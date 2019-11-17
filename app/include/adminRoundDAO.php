<?php

require_once 'common.php';
require_once 'function.php';

class AdminRoundDAO {

    // Get latest round status
    public function retrieveRoundDetail() {
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

    // update round status to reflect that round has started. Record time.
    public function startRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();

        $roundDetails = $this->retrieveRoundDetail();

        $round = $roundDetails->getRoundID();

        $sql = "update ADMIN_ROUND set roundStatus = 'Started', r{$round}Start = CURRENT_TIMESTAMP"; 
        
        $stmt = $pdo->prepare($sql);

        $status = ($stmt->execute());

        $stmt->closeCursor();
        $pdo = null;

        return $status;
    }

        // Update round status after round is cleared
    public function clearRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();
        
        $successfulBids = new StudentSectionDAO();

        $roundDetails = $this->retrieveRoundDetail();

        $round = $roundDetails->getRoundID();

        if ($round == 1){
            $addRound = 'roundID = 2, ';
            $newStatus = 'roundStatus = "Not Started", ';
        }elseif ($round == 2){
            $addRound = ' ';
            $newStatus = 'roundStatus = "Finished", ';
        }

        $sql = "update ADMIN_ROUND set {$addRound} {$newStatus} r{$round}End = CURRENT_TIMESTAMP"; 
        
        $stmt = $pdo->prepare($sql);

        $status = ($stmt->execute());

        $stmt->closeCursor();
        $pdo = null;

        return $status;
    }

    // Reset round status to clean slate
    public function resetRound() {
        $connMgr = new ConnectionManager();           
        $pdo = $connMgr->getConnection();
        $successfulBids = new StudentSectionDAO();
        $successfulBids->removeAll();

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