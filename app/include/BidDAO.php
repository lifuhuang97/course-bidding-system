<?php

require_once 'common.php';

class BidDAO {


    public function add($bid) {
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $userid=$bid->getUserid();
        $amount=$bid->getAmount();
        $code=$bid->getCode();
        $section=$bid->getSection();

        // Prepare SQL
        $sql = "INSERT INTO BID (userid, amount, code, section) VALUES
        (:userid, :amount, :code, :section)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':code',$code,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        
        // Run Query
        $status = False;
        if ($stmt->execute()){
            $status=True;
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }


    public function removeAll() {
        // $sql = 'TRUNCATE TABLE BID';
        $sql = 'DELETE FROM BID';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }

    // public function getBidInfo() {

    // }
}

?>