<?php

require_once 'common.php';

class StudentSectionDAO {
    
    public function addSuccessfulBid($userid,$amount,$course,$section) {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "INSERT INTO STUDENT_SECTION (userid, amount, course, section) VALUES
        (:userid, :amount, :course, :section)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);

        $status = False;
        if ($stmt->execute()){
            $status=True;
        }

        $stmt = null;
        $conn = null;

        return ($status); // Boolean True or False
    }

    public function getAllSuccessfulBids(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from STUDENT_SECTION order by amount desc";
        // $sql = "SELECT coursesID, sectionID from section";
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