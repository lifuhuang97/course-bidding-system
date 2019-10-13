<?php

require_once 'common.php';

class StudentSectionDAO {
    // Get whether a bid is success or fail

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
    
    // Add bid result record

    public function addBidResults($userid,$amount,$course,$section,$bidstatus, $bidround) {

        if(!isset($bidstatus)){
            $bidstatus = "Pending";
        }

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        // Prepare SQL
        $sql = "INSERT INTO STUDENT_SECTION (userid, amount, course, section, bidstatus, bidround) VALUES
        (:userid, :amount, :course, :section, :bidstatus, :bidround)"; 

        $stmt=$conn->prepare($sql);
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->bindParam(':bidstatus',$bidstatus,PDO::PARAM_STR);
        $stmt->bindParam(':bidround',$bidround,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }

    // Get all successful records [to be removed as inferior to bottom]
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
    
    // Get all successful results with bid status
    public function getAllBidsWithStatus(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT userid, amount, course, section,bidstatus from STUDENT_SECTION order by bidstatus asc, amount desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus']];
        }
        return $bids;

    }

    // Get successful results with bid status according to user id

    public function getSuccessfulBidsByID($userid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from STUDENT_SECTION where userid = :userid and bidstatus = 'Success' order by bidstatus asc, amount desc";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch()) {
            $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'], $row['bidround']];
        }
        return $bids;

    }

    // Wipe table

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

    function DropSection($userid,$courseid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Prepare SQL
        $sql = "DELETE FROM STUDENT_SECTION WHERE userid=:userid and course=:courseid"; 
    
        // Run Query
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
    
        $status = False;
    
        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;
    
        return $status; // Boolean True or False
    
    }
    
    public function RetrieveAllStudentByCourseSection($course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from STUDENT_SECTION WHERE course=:course AND section=:section order by userid asc";
        // $sql = "SELECT coursesID, sectionID from section";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $studentList = [];
        while ($row = $stmt->fetch() ) {
            $studentList[] = new StudentSection($row['userid'],$row['amount'],$row['course'],$row['section']);
        }

        $stmt = null;
        $conn = null;
        return $studentList;

    }

    public function RetrieveAll(){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Write & Prepare SQL Query (take care of Param Binding if necessary)
    
        $sql = "SELECT * FROM STUDENT_SECTION ORDER BY course,userid";
        $stmt = $conn->prepare($sql);
                
        //Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        //Retrieve Query Results (if any)
        $students=[];
        while ($row=$stmt->fetch()){
            $students[]=new StudentSection($row['userid'],$row['amount'],$row['course'],$row['section']);
        }
        
        // Clear Resources $stmt, $conn
        $stmt = null;
        $conn = null;
    
        // return (if any)
        return $students;
    }

}

?>