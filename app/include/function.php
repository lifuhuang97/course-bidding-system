<?php
require_once 'common.php';
function CheckStudentExist($userid){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM STUDENT WHERE userid=:userid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    //Retrieve Query Results (if any)
    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    return $status; //return true if student exist
}
function CheckCourseExist($courseid){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM COURSE WHERE courseID=:courseid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    //Retrieve Query Results (if any)
    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    return $status; //return true if course exist
}

function CheckSectionExist($courseid,$sectionid){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM SECTION WHERE coursesID=:courseid AND sectionID=:sectionid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid ,PDO::PARAM_STR);
    $stmt->bindParam(':sectionid',$sectionid ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    //Retrieve Query Results (if any)
    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    return $status; // return true if section exist
}
function CheckBidExist($userid,$course){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM BID WHERE userid=:userid AND code=:course";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    //Retrieve Query Results (if any)
    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=$row['amount'];
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    return $status;// return bid amount of the course that used bidded
}
function CheckCourseEnrolled($userid,$course){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM STUDENT_SECTION WHERE userid=:userid AND course=:course";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    //Retrieve Query Results (if any)
    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=$row['amount'];
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    return $status;// return amount if course enrolled
}
function CheckVacancy($course,$section,$retrieveValue=FALSE){

    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT * FROM SECTION WHERE coursesID=:course AND sectionID=:section";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    //Retrieve Query Results (if any)
    $size=0;
    if ($row=$stmt->fetch()){
        $size=$row['size'];
    }else{
        return 'No record found.';
    }

    $sql = "SELECT count(*) as remain FROM student_section WHERE course=:course AND section=:section";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section ,PDO::PARAM_STR);
      
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    //Retrieve Query Results (if any)
    if ($row=$stmt->fetch()){
        $size=$size-$row['remain'];
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    // return (if any)
    if ($retrieveValue){
        return $size;
    }else{
        return $size>0;// return true if there is vanancy
    } 
}

function CheckMinBidFromBiddingResult($course,$section,$round){
        // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Write & Prepare SQL Query (take care of Param Binding if necessary)
    $sql = "SELECT min(amount) as minbid FROM STUDENT_SECTION WHERE course=:course AND section=:section AND bidround=:round";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section ,PDO::PARAM_STR);
    $stmt->bindParam(':round',$round ,PDO::PARAM_STR);
        
    //Execute SQL Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    //Retrieve Query Results (if any)
    $minBid=10;
    if ($row=$stmt->fetch()){
        $minBid=$row['minbid'];
    }
    
    // Clear Resources $stmt, $conn
    $stmt = null;
    $conn = null;

    return $minBid;
}

function CheckMinBid($course,$section,$user=TRUE){
    $vacancy=CheckVacancy($course,$section,TRUE);
    if ($vacancy==0){
        return '-';
    }
    $bidDAO= new BidDAO();
    $allBid=$bidDAO->getAllBids([$course,$section]);
    $value = 10.00;
    if ($vacancy > count($allBid)){
        return $value;
    }
    if ($vacancy == count($allBid)){
        $count=0;
        $valuearray = [];
        while($count<count($allBid)){
            array_push($valuearray,$allBid[$count]->getAmount());
            $count +=1;
        }
        if ($user){
            return $valuearray[$vacancy-1]+1;
        }else{
            return $valuearray[$vacancy-1];
        }
    }
    if ($vacancy < count($allBid)){
        $count=0;
        $valuearray = [];
        while($count<count($allBid)){
            array_push($valuearray,$allBid[$count]->getAmount());
            $count +=1;
        }
    }
    if ($valuearray[$vacancy-1] == $valuearray[$vacancy]){
        if ($user){
            return $valuearray[$vacancy-1]+1;
        }else{
            return $valuearray[$vacancy-1]+0.01;
        }
    }else{
        //$valuearray[$vacancy-1] > $valuearray[$vacancy]
        if ($user){
            return $valuearray[$vacancy-1]+1;
        }else{
            return $valuearray[$vacancy-1];
        }
    }
    return $value;
}

function DropSectionUpdateEdollar($userid,$course,$dollar){
    $studentDAO=new StudentDAO();
    $student=$studentDAO->retrieveStudent($userid);
    $eDollar=$student->getEdollar();
    $studentSectionDAO= new StudentSectionDAO();
    $status=$studentSectionDAO->dropSection($userid,$course);
    if ($status){
        $TotalAmt=$eDollar+$dollar;
        $status=$studentDAO->updateDollar($userid,$TotalAmt);
    }
    return $status;

}

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
    $stmt->execute();

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
    $stmt->execute();

    $status=TRUE;
    while ($row=$stmt->fetch()){
        if ($row['code']==$courseid && $row['section']==$sectionid){
            return TRUE;
        }
        if ($row['code']!=$courseid && $row['day']==$info['day']){
            if ($row['start']>$info['start'] and $row['end']<$info['end']){
                // if the incoming fall inbetween the existing timetable
                $status=FALSE;
            }
            elseif ($row['start']<$info['end'] and $row['end']>$info['end']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['start']<$info['start'] and $row['end']>$info['start']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
            elseif ($row['start']==$info['start'] and $row['end']==$info['end']){
                // same timing
                $status=FALSE;
            }
        }
    }

    //check student section
    // Prepare SQL
    $sql = "SELECT * FROM student_section ss, section s where  ss.course=s.coursesID and ss.section=s.sectionID and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    while ($row=$stmt->fetch()){
        if ($row['day']==$info['day']){
            if ($row['start']>$info['start'] and $row['end']<$info['end']){
                // if the incoming fall inbetween the existing timetable
                $status=FALSE;
            }
            elseif ($row['start']<$info['end'] and $row['end']>$info['end']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['start']<$info['start'] and $row['end']>$info['start']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
            elseif ($row['start']==$info['start'] and $row['end']==$info['end']){
                // same timing
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
    $stmt->execute();

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
    $stmt->execute();

    $status=TRUE;
    while ($row=$stmt->fetch()){
        if ($row['code']==$courseid){
            return TRUE;
        }
        if ($row['examDate']==$info['examDate']){
            if ($row['examStart']>$info['examStart'] and $row['examEnd']<$info['examEnd']){
                // if the incoming fall inbetween the existing exam timetable
                $status=FALSE;
            }
            elseif ($row['examStart']<$info['examEnd'] and $row['examEnd']>$info['examEnd']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['examStart']<$info['examStart'] and $row['examEnd']>$info['examStart']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
            elseif ($row['examStart']==$info['examStart'] and $row['examEnd']==$info['examEnd']){
                // same timing
                $status=FALSE;
            }
        } 
    }

    //check student section
    // Prepare SQL
    $sql = "SELECT * FROM student_section s, course c where  s.course=c.courseid and userid=:userid;"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    while ($row=$stmt->fetch()){
        if ($row['examDate']==$info['examDate']){
            if ($row['examStart']>$info['examStart'] and $row['examEnd']<$info['examEnd']){
                // if the incoming fall inbetween the existing exam timetable
                $status=FALSE;
            }
            elseif ($row['examStart']<$info['examEnd'] and $row['examEnd']>$info['examEnd']){
                // if the incoming timetable clashes with incomingStart->existingStart->incomingEnd->existingEnd
                $status=FALSE;
            }
            elseif ($row['examStart']<$info['examStart'] and $row['examEnd']>$info['examStart']){
                // if the incoming timetable clashes with existingStart->incomingStart->existingEnd->incomingEnd
                $status=FALSE;
            }
            elseif ($row['examStart']==$info['examStart'] and $row['examEnd']==$info['examEnd']){
                // same timing
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
        $count=0;
        while ($row=$stmt->fetch()){
            if ($row['code']!=$course){
                $count++;
            }
        }

        // Prepare SQL
        $sql = "SELECT *  FROM student_section where userid=:userid"; 
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
        while ($row=$stmt->fetch()){
            $count++;
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

function noOfSameMinBid($course,$section,$amount){
    // Connect to Database
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    // Prepare SQL
    //retrieve exisiting course bid
    $sql = "SELECT count(*) as numberSameBid  FROM  BID where code=:course and section=:section and amount=:amount"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':course',$course,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section,PDO::PARAM_STR);
    $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);

    // Run Query
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();
    $Total=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $Total=$row['numberSameBid'];
        }
    }

    // Close Query/Connection
    $stmt = null;
    $conn = null;

    return $Total;
}
?>