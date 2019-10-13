<?php

require_once 'common.php';

class StudentDAO {

    public function authenticate($userid, $password) { // Authenticate Student Login
        
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = "SELECT * FROM STUDENT WHERE userid=:userid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        
        // Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        // Retrieve Query Results (if any)
        $return_message = 'Invalid username!';
        if ($row=$stmt->fetch()){
            if ($password===$row['password']){
                $return_message= 'SUCCESS';
            }
            else{
                $return_message='Password is incorrect!';
            }
        }
        
        // Clear Resources $stmt, $pdo
        $stmt = null;
        $conn = null;

        // Return (if any)
        return $return_message;
    }

    public function retrieveStudent($userid) { // Retrieve Student Information
        
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = "SELECT * FROM STUDENT WHERE userid=:userid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        
        // Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        // Retrieve Query Results (if any)
        // $student=[];
        $student = false;
        if ($row=$stmt->fetch()){
            $student = new Student($row['userid'], $row['password'], $row['name'], $row['school'], $row['edollar']);
        }
        
        // Clear Resources $stmt, $pdo
        $stmt = null;
        $conn = null;

        // Return (if any)
        return $student;
    }

    public function updateDollar($userid,$eDollar) {
        
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "UPDATE STUDENT SET edollar=:edollar where userid=:userid "; 
        
        // Run Query
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':edollar',$eDollar,PDO::PARAM_STR);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

        $status = False;

        if ($stmt->execute()){
            $status=True;
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }

    public function add($Student) { // Adding in new student information
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $userid=$Student->getUserid();
        $password=$Student->getPassword();
        $name=$Student->getName();
        $school=$Student->getSchool();
        $edollar=$Student->getEdollar();

        // Prepare SQL
        $sql = "INSERT INTO STUDENT (userid, password, name, school, edollar) VALUES
        (:userid, :password, :name, :school, :edollar)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':password',$password,PDO::PARAM_STR);
        $stmt->bindParam(':name',$name,PDO::PARAM_STR);
        $stmt->bindParam(':school',$school,PDO::PARAM_STR);
        $stmt->bindParam(':edollar',$edollar,PDO::PARAM_STR);

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
    
    public function removeAll() { // Remove everything from student table
        // $sql = 'TRUNCATE TABLE STUDENT'; 
        $sql = 'DELETE FROM STUDENT';
        
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
    
        $sql = "SELECT * FROM STUDENT ORDER BY userid";
        $stmt = $conn->prepare($sql);
                
        //Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        //Retrieve Query Results (if any)
        $student=[];
        while ($row=$stmt->fetch()){
            $student[]=new Student($row['userid'],$row['password'],$row['name'],$row['school'],$row['edollar']);
        }
        
        // Clear Resources $stmt, $conn
        $stmt = null;
        $conn = null;
    
        // return (if any)
        return $student;
    }
}


?>