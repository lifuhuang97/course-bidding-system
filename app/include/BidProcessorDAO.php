<?php

require_once 'common.php';

class BidProcessorDAO {
    
    // Add bid result record
    public function addBidResults($userid,$amount,$course,$section,$bidstatus, $bidround) {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "INSERT INTO BID_PROCESSOR (userid, amount, course, section, bidstatus, bidround) VALUES
        (:userid, :amount, :course, :section, :bidstatus, :bidround)"; 

        $stmt=$conn->prepare($sql);
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_INT);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->bindParam(':bidstatus',$bidstatus,PDO::PARAM_STR);
        $stmt->bindParam(':bidround',$bidround,PDO::PARAM_INT);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }


    // Get whether a bid is pending, success or fail
    public function getBidStatus($userid,$amount,$course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT bidstatus FROM BID_PROCESSOR where userid=:userid and amount=:amount and course=:course and section=:section"; 
        
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

    //To get students' bids by course & section
    public function retrieveAllStudentByCourseSection($course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from BID_PROCESSOR WHERE course=:course AND section=:section order by userid asc";
        // $sql = "SELECT coursesID, sectionID from section";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $studentList = [];
        while ($row = $stmt->fetch() ) {
            $studentList[]=new bidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }

        $stmt = null;
        $conn = null;
        return $studentList;

    }

    // Get all processed bids
    public function RetrieveAll(){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = "SELECT * FROM BID_PROCESSOR ORDER BY amount desc, course,userid";
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $students=[];
        while ($row=$stmt->fetch()){
            $students[]=new bidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $students;
    }

      // Get successful results with bid status according to user id
      public function getBidsByID($userid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from BID_PROCESSOR where userid = :userid order by bidstatus asc, amount desc";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch()) {
            $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'], $row['bidround']];
        }

        $stmt = null;
        $conn = null;

        return $bids;

    }

    
        // Wipe table
        public function removeAll() {
            $connMgr = new ConnectionManager();
            $conn = $connMgr->getConnection();
            $sql = 'DELETE FROM BID_PROCESSOR';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $stmt = null;
            $conn = null; 
        }
}
?>