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
        
        if($successBidsDAO->getAllSuccessfulBids() == []){
        foreach($sections as $section){
            $conclude = false;
            $selected = $bidDAO->getAllBids($section);
            $totalBidCount = count($selected);
            if ($totalBidCount < $section[2]){
                foreach($selected as $bid){
                    $bidDataTable[] = "<tr><td>$bid[0]</td><td>$bid[1]</td><td>$bid[2]</td><td>$bid[3]</td><td>'Successful'</td></tr>";
                    $successBidsDAO->addSuccessfulBid($bid[0],$bid[1],$bid[2],$bid[3]);
                }
            }else{
                $bidStatus = "Successful";
                $vacancy = $section[2];
                $count = 0;
                $prevAmt = 0;
                $prevID = "";
                $clearingAmtCount = 0;
                $clearingAmt = 0;
                
                
                    foreach ($selected as $bid){
                        $count++; 
                        $bid = [$bid->getUserid(), $bid->getAmount(), $bid->getCode(), $bid->getSection()];
                        $bidID = $bid[0];
                        $bidAmt = $bid[1];
                        $bidCourse = $bid[2];
                        $bidSection = $bid[3];

                            if($count == 0){

                                $prevAmt = $bidAmt;
                                $prevID = $bidID;

                            }elseif($bidAmt < $prevAmt){

                                $prevAmt = $bidAmt;
                                $prevID = $bidID;
                            }


                            if($count >= $vacancy){

                                if($clearingAmt == 0){
                                    $clearingAmt = $bidAmt;
                                    $clearingAmtCount += 1;
                                }

                                if( $bidAmt < $clearingAmt){
                                    $prevAmt = $clearingAmt;
                                    $bidStatus = "Unsuccessful";
                                }else if($bidAmt == $clearingAmt && $clearingAmtCount == 2){
                                    $tempDataTable = $bidDataTable;
                                    $bidStatus = "Unsuccessful";
                                }
                                if($bidAmt == $clearingAmt){
                                    $clearingAmtCount += 1;
                            }
                        }
                            $bidDataTableArray[] = [$bidID,$bidAmt,$bidCourse,$bidSection,$bidStatus];
                        }
                         
                    }
                }

            if(isset($clearingAmtCount)){
                $counter = 0;
                foreach($bidDataTableArray as $bid){
                    $counter++;
                    if($bid[1] == $clearingAmt){
                        $changeState = array(4=>"Unsuccessful");
                        $bid = array_replace($bid, $changeState);
                        $bidDataTableArray[$counter-1] = $bid;
                    }
                }
            }
            if(isset($bidDataTableArray)){
            foreach($bidDataTableArray as $bid){

                if($bid[4] == "Unsuccessful"){
                    $tempbid = new Bid($bid[0],$bid[1],$bid[2],$bid[3]);
                    $studentDAO = new StudentDAO();
                    $theStudent = $studentDAO->retrieveStudent($bid[0]);
                    $oldDollar = $theStudent->getEdollar();
                    $newDollar = $oldDollar + $bid[1];
                    $studentDAO->updateDollar($bid[0], $newDollar);
                }

                $bidDataTable[] = "<tr><td>$bid[0]</td><td>$bid[1]</td><td>$bid[2]</td><td>$bid[3]</td><td>$bid[4]</td></tr>";
            
                $successBidsDAO->addBidResults($bid[0],$bid[1],$bid[2],$bid[3],$bid[4]);
            }
        }
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