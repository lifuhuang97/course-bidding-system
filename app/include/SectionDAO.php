<?php

require_once 'common.php';

class SectionDAO {
    public function add($Section) { // Adding a new section
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $coursesID=$Section->getCourseid();
        $sectionID=$Section->getSectionid();
        $day=$Section->getDay();
        $start=$Section->getStart();
        $end=$Section->getEnd();
        $instructor=$Section->getInstructor();
        $venue=$Section->getVenue();
        $size=$Section->getSize();

        // Prepare SQL
        $sql = "INSERT INTO SECTION (coursesID, sectionID, day, start, end, instructor, venue, size) VALUES
        (:coursesID, :sectionID, :day, :start, :end, :instructor, :venue, :size)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':coursesID',$coursesID,PDO::PARAM_STR);
        $stmt->bindParam(':sectionID',$sectionID,PDO::PARAM_STR);
        $stmt->bindParam(':day',$day,PDO::PARAM_STR);
        $stmt->bindParam(':start',$start,PDO::PARAM_STR);
        $stmt->bindParam(':end',$end,PDO::PARAM_STR);
        $stmt->bindParam(':instructor',$instructor,PDO::PARAM_STR);
        $stmt->bindParam(':venue',$venue,PDO::PARAM_STR);
        $stmt->bindParam(':size',$size,PDO::PARAM_STR);

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

    public function updateSectionMinBid($minbid,$course,$section) { // Adding a new section
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "UPDATE SECTION SET minbid=:minbid WHERE coursesID=:course and sectionID=:section"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':minbid',$minbid,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);

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

    public function resetSectionMinBid() { // Adding a new section
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "UPDATE SECTION SET minbid=NULL "; 
        $stmt=$conn->prepare($sql);

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

    public function viewMinBid($course,$section) { // Adding a new section
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "SELECT minbid from section WHERE coursesID=:course and sectionID=:section"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);

        // Run Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $minbid='-';
        while ($row = $stmt->fetch() ) {
            $minbid = $row['minbid'];
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $minbid; 
    }

    public function getAllSections(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        // limits selection to ONLY IS100
        $sql = "SELECT coursesID,sectionID,size from section";
        // $sql = "SELECT coursesID, sectionID from section";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $sections = [];
        while ($row = $stmt->fetch() ) {
            $sections[] = [$row['coursesID'],$row['sectionID'],$row['size']];
        }
        return $sections;

    }


    public function removeAll() { // Removing everything from Section
        // $sql = 'TRUNCATE TABLE SECTION';
        $sql = 'DELETE FROM SECTION';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }
    public function RetrieveAll(){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Write & Prepare SQL Query (take care of Param Binding if necessary)
    
        $sql = "SELECT * FROM SECTION ORDER BY coursesID, sectionID";
        $stmt = $conn->prepare($sql);
                
        //Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        //Retrieve Query Results (if any)
        $section=[];
        while ($row=$stmt->fetch()){
            $section[]=new Section($row['coursesID'],$row['sectionID'],$row['day'],$row['start'],$row['end'],$row['instructor'],$row['venue'],$row['size']);
        }
        
        // Clear Resources $stmt, $conn
        $stmt = null;
        $conn = null;
    
        // return (if any)
        return $section;
    }
}


?>