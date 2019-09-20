<?php

require_once 'common.php';

class CourseDAO {

    public function add($Course) { // Adding a new course
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $courseID=$Course->getCourseid();
        $school=$Course->getSchool();
        $title=$Course->getTitle();
        $description =$Course->getDescription();
        $examDate=$Course->getExamDate();
        $examStart=$Course->getExamStart();
        $examEnd=$Course->getExamEnd();
        
        // Prepare SQL
        $sql = "INSERT INTO COURSE (courseID, school, title, description, examDate, examStart, examEnd) VALUES
        (:courseID, :school, :title, :description, :examDate, :examStart, :examEnd)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':courseID',$courseID,PDO::PARAM_STR);
        $stmt->bindParam(':school',$school,PDO::PARAM_STR);
        $stmt->bindParam(':title',$title,PDO::PARAM_STR);
        $stmt->bindParam(':description',$description,PDO::PARAM_STR);
        $stmt->bindParam(':examDate',$examDate,PDO::PARAM_STR);
        $stmt->bindParam(':examStart',$examStart,PDO::PARAM_STR);
        $stmt->bindParam(':examEnd',$examEnd,PDO::PARAM_STR);

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

    public function removeAll() { // remove everything from Course
        // $sql = 'TRUNCATE TABLE COURSE';
        $sql = 'DELETE FROM COURSE';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }
}


?>