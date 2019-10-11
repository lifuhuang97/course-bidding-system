<?php

require_once 'common.php';
require_once 'function.php';

class AdminRoundDAO {


    public function RetrieveRoundDetail() {
        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();

        $sql = "select * from ADMIN_ROUND";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $stmt->fetch() ) {
            $round = new adminRound( $row['adminID'], $row['adminPW'], $row['adminTK'], $row['roundID'], $row['roundStatus'], $row['r1Start'], $row['r1End'], $row['r2Start'], $row['r2End'] );

        $stmt = null;
        $conn = null;

        return $round;
    }
}


    public function startRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();

        $roundDetails = $this->RetrieveRoundDetail();

        $round = $roundDetails->getRoundID();

        $sql = "update ADMIN_ROUND set roundStatus = 'Started', r{$round}Start = CURRENT_TIMESTAMP"; 
        
        $stmt = $pdo->prepare($sql);

        $status = ($stmt->execute());

        $stmt->closeCursor();
        $pdo = null;

        return $status;
    }


    public function clearRoundBids(){
        
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();

        $bidDAO = new BidDAO();
        $sectDAO = new SectionDAO();
        $successBidsDAO = new StudentSectionDAO();

        $sections = $sectDAO->getAllSections();

        $bidDataTable = [];
        

        foreach($sections as $section){
            $conclude = false;
            $selected = $bidDAO->getAllBids($section);
            
            $totalBidCount = count($selected);

            if ($totalBidCount < $section[2]){
                foreach($selected as $bid){
                    $bidDataTable[] = "<tr><td>$bid[0]</td><td>$bid[1]</td><td>$bid[2]</td><td>$bid[3]</td><td>'Successful'</td></tr>";
                }
            }else{
                $bidStatus = "Successful";
                $vacancy = $section[2];
                $count = 1;
                $prevAmt = 0;
                $prevID = "";
                $clearingAmtCount = 0;
                $clearingAmt = 0;
                
                
                    foreach ($selected as $bid){

                        $bid = [$bid->getUserid(), $bid->getAmount(), $bid->getCode(), $bid->getSection()];
                            if($count == 0){

                                $prevAmt = $bid[1];
                                $prevID = $bid[0];

                            }elseif($bid[1] < $prevAmt){

                                $prevAmt = $bid[1];
                                $prevID = $bid[0];
                            }


                            if($count >= $vacancy){

                                if( $bid[1] < $clearingAmt){
                                    $bidStatus = "Unsuccessful";
                                }

                                if($bid[1] < $prevAmt){
                                    $clearingAmtCount += 1;
                                    $clearingAmt = $bid[1];
                                }
                                if($bid[1] == $clearingAmt){
                                    $clearingAmtCount += 1;
                                }
                            }
                            $count++; 
                            $bidDataTable[] = "<tr><td>$bid[0]</td><td>$bid[1]</td><td>$bid[2]</td><td>$bid[3]</td><td>$bidStatus</td></tr>";

                            if($bidStatus = "Successful" ){
                                var_dump($bid);
                                $successBidsDAO->addSuccessfulBid($bid[0],$bid[1],$bid[2],$bid[3]);

                            // }
                              
                        }
                    }
                }
                var_dump($bidDataTable);
                return $bidDataTable;
            }
        }
        

    public function clearRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();
        
        $successfulBids = new StudentSectionDAO();

        $roundDetails = $this->RetrieveRoundDetail();

        $round = $roundDetails->getRoundID();

        if ($round == 1){
            $addRound = 'roundID = 2, ';
            $newStatus = 'roundStatus = "Not Started", ';
        }elseif ($round == 2){
            $addRound = ' ';
            $newStatus = 'roundStatus = "Finished", ';
            $successfulBids->removeAll();
        }

        $sql = "update ADMIN_ROUND set {$addRound} {$newStatus} r{$round}End = CURRENT_TIMESTAMP"; 
        
        $stmt = $pdo->prepare($sql);

        $status = ($stmt->execute());

        $stmt->closeCursor();
        $pdo = null;

        return $status;
    }

    public function resetRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();

        $addRound = "roundID = 2,";

        $sql = "update ADMIN_ROUND set roundID = 1, roundStatus = 'Not Started', r1Start = null, r1End = null, r2Start = null, r2End = null"; 
        
        $stmt = $pdo->prepare($sql);

        $status = ($stmt->execute());

        $stmt->closeCursor();
        $pdo = null;

        return $status;
    }
}

?>