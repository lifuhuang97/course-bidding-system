<?php
require_once 'common.php';
function CheckForCompletedPrerequisites($userid,$course){
    $prerequisiteDAO = new PrerequisiteDAO();
    $course_completedDAO=new CourseCompletedDAO();
    $preCourses=$prerequisiteDAO->checkPrerequisite($course);
    foreach($preCourses as $preCourse){
        if (!$course_completedDAO->checkCourseComplete($userid, $preCourse)){
            return FALSE;
        }
    }
    return TRUE;// return true if userid completed all prerequisite 
}

function CheckForOwnSchool($userid,$courseid){
    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Prepare SQL
    $sql = "SELECT c.school as cSchool, s.school as sSchool FROM course c, student s where s.userid=:userid and c.courseID=:courseid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);

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
        if ($row!=NULL && $row['cSchool']==$row['sSchool']){
            $status=TRUE;
        }
    }

    // Close Query/Connection
    $stmt = null;
    $conn = null;
    
    return $status;// return true if course from own school
}

function CheckClassTimeTable($userid,$courseid,$sectionid){
    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Prepare SQL
    //retrieve day, start and end from the incoming course
    $sql = "SELECT * FROM section where coursesID=:courseid and sectionID=:sectionid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
    $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }
    $info=[];
    if ($row=$stmt->fetch()){
        $info=['day'=>$row['day'],'start'=>$row['start'],'end'=>$row['end']];
    }

    // Prepare SQL
    $sql = "SELECT * FROM bid b, section s where b.code=s.coursesID and b.section=s.sectionID and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }
    $status=TRUE;
    while ($row=$stmt->fetch()){
        if ($row['code']==$courseid && $row['section']==$sectionid){
            return TRUE;
        }
        if ($row['code']!=$courseid && $row['day']==$info['day']){
            if ($row['start']>=$info['start'] and $row['end']<=$info['end']){
                // if the incoming fall inbetween the existing timetable
                $status=FALSE;
            }
            elseif ($row['start']<=$info['end'] and $row['end']>=$info['end']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['start']<=$info['start'] and $row['end']>=$info['start']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
        }
    }

    // Close Query/Connection
    $stmt = null;
    $conn = null;
    
    return $status;// return true if no clash of timetable
}

function CheckExamTimeTable($userid,$courseid){
    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Prepare SQL
    //retrieve day, start and end from the incoming course
    $sql = "SELECT * FROM course where courseID=:courseid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }
    $info=[];
    if ($row=$stmt->fetch()){
        $info=['examDate'=>$row['examDate'],'examStart'=>$row['examStart'],'examEnd'=>$row['examEnd']];
    }

    // Prepare SQL
    $sql = "SELECT * FROM bid b, course c where  b.code=c.courseid and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }

    $status=TRUE;
    while ($row=$stmt->fetch()){
        if ($row['code']==$courseid){
            return TRUE;
        }
        if ($row['examDate']==$info['examDate']){
            if ($row['examStart']>=$info['examStart'] and $row['examEnd']<=$info['examEnd']){
                // if the incoming fall inbetween the existing exam timetable
                $status=FALSE;
            }
            elseif ($row['examStart']<=$info['examEnd'] and $row['examEnd']>=$info['examEnd']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['examStart']<=$info['examStart'] and $row['examEnd']>=$info['examStart']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
        } 
    }

    // Close Query/Connection
    $stmt = null;
    $conn = null;
    
    return $status;// return true if no clash of examtimetable
}

function CheckForCompletedCourse($userid,$course){
    
    $course_completedDAO=new CourseCompletedDAO();
    return $course_completedDAO->checkCourseComplete($userid, $course);//return true if already completed course

}

function CheckForExceedOfBidSection($userid,$course){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Prepare SQL
        $sql = "SELECT *  FROM bid where userid=:userid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

        // Run Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();
    
        // check if query fail
        if (!$status){ //if ($status==False)
            //if there is error
            $err=$stmt->errorinfo();
        }
        $status=FALSE;
        $count=0;
        while ($row=$stmt->fetch()){
            if ($row['code']!=$course){
                $count++;
            }
        }

        // Close Query/Connection
        $stmt = null;
        $conn = null;
        
        return $count<5;// return True if didnt exceed
}

function CheckForEdollar($userid, $amount, $course, $retrieveValue=FALSE){
    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Prepare SQL
    //retrieve exisiting course bid
    $sql = "SELECT *  FROM  bid where userid=:userid  and code=:course"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }
    $amt=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $amt=$row['amount'];
        }
    }
    // Prepare SQL
    //retrieve student current edollar
    $sql = "SELECT *  FROM  student where userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ //if ($status==False)
        //if there is error
        $err=$stmt->errorinfo();
    }
    $userEdollar=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $userEdollar=$row['edollar'];
        }
    }

    // Close Query/Connection
    $stmt = null;
    $conn = null;

    $TotalAmt=$userEdollar+$amt;
    if ($retrieveValue){// retrieve total amount
        $inBidList=FALSE;
        if ($amt!=0){
            $inBidList=TRUE;
        }
        return [$TotalAmt,$inBidList];
    }

    return $TotalAmt >=$amount;// return True have enough bid 
}

function ChangeBidUpdateEdollar($bid, $action=''){
    $list=CheckForEdollar($bid->getUserid(),$bid->getAmount(),$bid->getCode(),TRUE);
    $TotalAmt=$list[0];
    $bidDAO= new BidDAO();
    if ($action=="Drop"){
        //drop bid
        $status=$bidDAO->drop($bid->getUserid(),$bid->getCode(), $bid->getSection());
    }elseif($list[1]){
        //update bid
        $status=$bidDAO->update($bid->getUserid(),$bid->getCode(), $bid->getSection(),$bid->getAmount());
        $TotalAmt-=$bid->getAmount();
    }else{
        //add bid
        $TotalAmt-=$bid->getAmount();
        $status=$bidDAO->add($bid);
    }
    
    if ($status){
        $studentDAO=new StudentDAO();
        $status=$studentDAO->updateDollar($bid->getUserid(),$TotalAmt);
    }
    
    return $status;
}

?>