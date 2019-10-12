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

    public function getAllSections(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        // limits selection to ONLY IS100
        $sql = "SELECT coursesID,sectionID,size from section LIMIT 2,1";
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
}


?>