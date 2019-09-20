<?php

require_once 'common.php';

class PrerequisiteDAO {
    public function add($Prerequisite) { // Adding a prerequisite
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $course=$Prerequisite->getCourse();
        $prerequisite=$Prerequisite->getPrerequisite();

        // Prepare SQL
        $sql = "INSERT INTO PREREQUISITE (course, prerequisite) VALUES (:course, :prerequisite)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':prerequisite',$prerequisite,PDO::PARAM_STR);

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


    public function removeAll() { // Removing everything from Prerequisite
        // $sql = 'TRUNCATE TABLE PREREQUISITE';
        $sql = 'DELETE FROM PREREQUISITE';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }
}


?>