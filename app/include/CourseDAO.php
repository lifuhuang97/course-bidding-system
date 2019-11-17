<?php

require_once 'common.php';

class CourseDAO {

    public function add($Course) { // Adding a new course

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $courseID=$Course->getCourseid();
        $school=$Course->getSchool();
        $title=$Course->getTitle();
        $description =$Course->getDescription();
        $examDate=$Course->getExamDate();
        $examStart=$Course->getExamStart();
        $examEnd=$Course->getExamEnd();
        

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


        $status = False;
        if ($stmt->execute()){
            $status=True;
        }


        $stmt = null;
        $conn = null;
        
        return $status;
    }

    public function removeAll() { // remove everything from Course

        $sql = 'DELETE FROM COURSE';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    //Get all course details (filter accordingly)
    public function retrieveAllCourseDetail($courseid='',$sectionid='',$school=''){

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        if (strlen($courseid)!=0 && strlen($sectionid)!=0){
            $sql = "SELECT * 
                FROM COURSE c, SECTION s
                WHERE 
                    c.courseID=s.coursesID AND
                    c.courseID=:courseid AND
                    sectionID=:sectionid;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':courseid',$courseid ,PDO::PARAM_STR);
            $stmt->bindParam(':sectionid',$sectionid ,PDO::PARAM_STR);
        }elseif(strlen($school)!=0){
            $sql = "SELECT * 
                FROM COURSE c, SECTION s
                WHERE 
                    c.courseID=s.coursesID AND
                    school=:school;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':school',$school ,PDO::PARAM_STR);
        }else{
            $sql = "SELECT * 
                FROM COURSE c, SECTION s
                WHERE 
                    c.courseID=s.coursesID;
            ";
            $stmt = $conn->prepare($sql);
        }
                
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $course=[];
        while ($row=$stmt->fetch()){
            $course[]=new CourseSection($row['courseID'],$row['sectionID'],$row['day'],$row['start'],$row['end'],$row['instructor'],$row['venue'],$row['size'],$row['school'],$row['title'],$row['description'],$row['examDate'],$row['examStart'],$row['examEnd']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $course;
    }
    
    public function RetrieveAll(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "SELECT * FROM COURSE ORDER BY courseID";
        $stmt = $conn->prepare($sql);
                
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $course=[];
        while ($row=$stmt->fetch()){
            $course[]=new Course($row['courseID'],$row['school'],$row['title'],$row['description'],$row['examDate'],$row['examStart'],$row['examEnd']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $course;
    }

    public function RetrieveCourseName($courseID){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "SELECT * FROM COURSE where courseID=:courseID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':courseID',$courseID ,PDO::PARAM_STR);
            
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $courseName='';
        while ($row=$stmt->fetch()){
            $courseName=$row['title'];
        }
        
        $stmt = null;
        $conn = null;
    
        return $courseName;
    }
}


?>