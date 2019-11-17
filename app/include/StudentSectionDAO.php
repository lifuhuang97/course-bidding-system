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
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = 'DELETE FROM STUDENT_SECTION';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt = null;
        $conn = null; 
    }

    // Drop an enrolled section
    function dropSection($userid,$courseid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "DELETE FROM STUDENT_SECTION WHERE userid=:userid and course=:courseid"; 
    
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
    
        $status = False;
    
        if ($stmt->execute()){
            $status=True;
        }
        $stmt = null;
        $conn = null;
    
        return $status; 
    
    }

    //Get all enrolled sections
    public function retrieveAllByUser($userid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = "SELECT * FROM STUDENT_SECTION where userid=:userid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = [];
        while ($row = $stmt->fetch() ) {
            $result[] = [$row['amount'],$row['course'],$row['section']];
        }

        $stmt = null;
        $conn = null;        
        
        return $result;
    }

    //Get enrolled student list by course & section
    public function retrieveAllStudentByCourseSection($course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = "SELECT * from STUDENT_SECTION WHERE course=:course AND section=:section order by userid asc";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $studentList = [];
        while ($row = $stmt->fetch() ) {
            $studentList[]=new StudentSection($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }

        $stmt = null;
        $conn = null;
        return $studentList;

    }

    //Get all information
    public function RetrieveAll(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = "SELECT * FROM STUDENT_SECTION ORDER BY course,userid";
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();
        $students=[];
        while ($row=$stmt->fetch()){
            $students[]=new StudentSection($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $students;
    }

}

?>