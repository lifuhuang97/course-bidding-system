<?php

require_once 'common.php';

class PrerequisiteDAO {
    public function checkPrerequisite($courseid){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "SELECT prerequisite FROM prerequisite where course=:courseid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);

        // Run Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();

        // check if query fail
        if (!$status){ //if ($status==False)
            //if there is error
            $err=$stmt->errorinfo();
            var_dump($err);
        }
        $prerequisite=[];
        while ($row=$stmt->fetch()){
            $prerequisite[]= $row['prerequisite'];
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $prerequisite;

    }

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
    public function RetrieveAll(){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Write & Prepare SQL Query (take care of Param Binding if necessary)
    
        $sql = "SELECT * FROM PREREQUISITE ORDER BY course, prerequisite";
        $stmt = $conn->prepare($sql);
                
        //Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        //Retrieve Query Results (if any)
        $prerequisite=[];
        while ($row=$stmt->fetch()){
            $prerequisite[]=new Prerequisite($row['course'],$row['prerequisite']);
        }
        
        // Clear Resources $stmt, $conn
        $stmt = null;
        $conn = null;
    
        // return (if any)
        return $prerequisite;
    }
}


?>