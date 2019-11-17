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

    //get all courses completed (optional to fill in userid)
    public function getAllCourseComplete($userid='') {
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $sql = 'SELECT * FROM course_completed where userid =:userid ';
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);


        $status=$stmt->execute();

        $course1=[];
        while($row=$stmt->fetch()){
            $course1[]=new CourseCompleted($row['userid'],$row['code']);
        }
 
        $stmt = null;
        $conn = null;
        
        return $course1;

    }

    //Check course completed by userID and courseID
    public function checkCourseComplete($userid,$code){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * FROM course_completed where userid=:userid and code=:code"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':code',$code,PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();


        if (!$status){ 
            $err=$stmt->errorinfo();
        }
        $status=FALSE;
        if ($row=$stmt->fetch()){
            if ($row!=NULL){
                $status=TRUE;
            }
        }


        $stmt = null;
        $conn = null;
        
        return $status;
    }

    //Get everything from course completed
    public function RetrieveAll(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "SELECT * FROM course_completed ORDER BY code,userid";
        $stmt = $conn->prepare($sql);
                
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $CourseCompleted=[];
        while ($row=$stmt->fetch()){
            $CourseCompleted[]=new CourseCompleted($row['userid'],$row['code']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $CourseCompleted;
    }
}


?>