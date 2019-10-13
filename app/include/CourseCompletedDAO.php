<?php

require_once 'common.php';

class CourseCompletedDAO {

    public function add($courseCompleted) { // Adding new CourseCompleted
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $userid=$courseCompleted->getUserid();
        $code=$courseCompleted->getCode();

        // Prepare SQL
        $sql = "INSERT INTO COURSE_COMPLETED (userid, code) VALUES
        (:id, :courseid)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':id',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$code,PDO::PARAM_STR);

        // Run Query
        $status = False;
        if ($stmt->execute()){
            $status=True;
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $status; // Return True or False
    }

    public function removeAll() { // Removing everything from CourseCompleted
        // $sql = 'TRUNCATE TABLE COURSE_COMPLETED';
        $sql = 'DELETE FROM COURSE_COMPLETED';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }
    public function getallcoursecomplete($userid='') {
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = 'SELECT * FROM course_completed where userid =:userid ';
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

        //Execute SQL Query
        //$stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $course1=[];
        while($row=$stmt->fetch()){
            $course1[]=new CourseCompleted($row['userid'],$row['code']);
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $course1;

    }
    public function checkCourseComplete($userid,$code){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "SELECT * FROM course_completed where userid=:userid and code=:code"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':code',$code,PDO::PARAM_STR);
        // Run Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();

        // check if query fail
        if (!$status){ //if ($status==False)
            //if there is error
            $err=$stmt->errorinfo();
        }
        $status=FALSE;
        if ($row=$stmt->fetch()){
            if ($row!=NULL){
                $status=TRUE;
            }
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $status;
    }
}


?>