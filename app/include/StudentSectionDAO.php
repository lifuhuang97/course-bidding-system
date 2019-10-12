<?php

require_once 'common.php';

class StudentSectionDAO {
    
    public function getBidStatus($userid,$amount,$course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT bidstatus FROM STUDENT_SECTION where userid=:userid and amount=:amount and course=:course and section=:section"; 
        
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);

       

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = [];

        while ($row = $stmt->fetch() ) {
            $result[] = $row['bidstatus'];
        }

        $stmt = null;
        $conn = null;        
        
        return $result;
    }
    


    public function addBidResults($userid,$amount,$course,$section,$bidstatus) {

        if(!isset($bidstatus)){
            $bidstatus = "Pending";
        }

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        // Prepare SQL
        $sql = "INSERT INTO STUDENT_SECTION (userid, amount, course, section, bidstatus) VALUES
        (:userid, :amount, :course, :section, :bidstatus)"; 

        $stmt=$conn->prepare($sql);
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->bindParam(':bidstatus',$bidstatus,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }

    public function getAllSuccessfulBids(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT userid, amount, course, section from STUDENT_SECTION order by amount desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section']];
        }
        return $bids;

    }
    
    public function removeAll() {
        // $sql = 'TRUNCATE TABLE BID';
        $sql = 'DELETE FROM STUDENT_SECTION';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }

}

?>