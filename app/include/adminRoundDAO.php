<?php

require_once 'common.php';

class adminRoundDAO {


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



    public function clearRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();
        
        $roundDetails = $this->RetrieveRoundDetail();

        $round = $roundDetails->getRoundID();

        if ($round == 1){
            $addRound = 'roundID = 2, ';
            $newStatus = 'roundStatus = "Not Started", ';
        }elseif ($round == 2){
            $addRound = ' ';
            $newStatus = 'roundStatus = "Finished", ';
        }
        var_dump($addRound);
        var_dump($newStatus);

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