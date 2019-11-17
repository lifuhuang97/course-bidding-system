<?php
require_once 'common.php';

//Check if a student exists in database
function CheckStudentExist($userid){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM STUDENT WHERE userid=:userid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    $stmt = null;
    $conn = null;

    return $status; //return true if student exist
}

//Check if a course exists in database
function CheckCourseExist($courseid){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM COURSE WHERE courseID=:courseid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    $stmt = null;
    $conn = null;

    return $status; //return true if course exist
}

//Check if a section exists in database
function CheckSectionExist($courseid,$sectionid){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM SECTION WHERE coursesID=:courseid AND sectionID=:sectionid";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid ,PDO::PARAM_STR);
    $stmt->bindParam(':sectionid',$sectionid ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=TRUE;
    }
    
    $stmt = null;
    $conn = null;

    return $status; // return true if section exist
}

//Check if bid exists in database
function CheckBidExist($userid,$course){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM BID WHERE userid=:userid AND code=:course";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=$row['amount'];
    }
    
    $stmt = null;
    $conn = null;

    return $status;// return bid amount of the course that used bidded
}

//Check if course has been enrolled
function CheckCourseEnrolled($userid,$course){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM STUDENT_SECTION WHERE userid=:userid AND course=:course";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userid',$userid ,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status=$stmt->execute();

    $status=FALSE;
    if ($row=$stmt->fetch()){
        $status=$row['amount'];
    }
    
    $stmt = null;
    $conn = null;

    return $status;// return amount if course enrolled
}

//Check vacancy in section
function CheckVacancy($course,$section,$retrieveValue=FALSE){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT * FROM SECTION WHERE coursesID=:course AND sectionID=:section";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section ,PDO::PARAM_STR);
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

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
      
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    if ($row=$stmt->fetch()){
        $size=$size-$row['remain'];
    }
    
    $stmt = null;
    $conn = null;

    if ($retrieveValue){
        return $size;
    }else{
        return $size>0;// return true if there is vacancy
    } 
}

//Check min bid from bidding results
function CheckMinBidFromBiddingResult($course,$section,$round){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT min(amount) as minbid FROM STUDENT_SECTION WHERE course=:course AND section=:section AND bidround=:round";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course',$course ,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section ,PDO::PARAM_STR);
    $stmt->bindParam(':round',$round ,PDO::PARAM_STR);
        
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    $minBid=10;
    if ($row=$stmt->fetch()){
        $minBid=$row['minbid'];
    }
    
    $stmt = null;
    $conn = null;

    return $minBid;
}


//Check min bid value
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
        if ($user){
            return $valuearray[$vacancy-1]+1;
        }else{
            return $valuearray[$vacancy-1];
        }
    }
    return $value;
}

//Update credits after dropping
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

//Check pre-requisite
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


//Check if is from own school
function CheckForOwnSchool($userid,$courseid){
 
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    $sql = "SELECT c.school as cSchool, s.school as sSchool FROM course c, student s where s.userid=:userid and c.courseID=:courseid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    if (!$status){ 
        $err=$stmt->errorinfo();
    }
    $status=FALSE;
    if ($row=$stmt->fetch()){
        if ($row!=NULL && $row['cSchool']==$row['sSchool']){
            $status=TRUE;
        }
    }

    $stmt = null;
    $conn = null;
    
    return $status;// return true if course from own school
}

//Check for timetable clash
function CheckClassTimeTable($userid,$courseid,$sectionid){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    //retrieve day, start and end from the incoming course
    $sql = "SELECT * FROM section where coursesID=:courseid and sectionID=:sectionid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
    $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    $info=[];
    if ($row=$stmt->fetch()){
        $info=['day'=>$row['day'],'start'=>$row['start'],'end'=>$row['end']];
    }

    $sql = "SELECT * FROM bid b, section s where b.code=s.coursesID and b.section=s.sectionID and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

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
    $sql = "SELECT * FROM student_section ss, section s where  ss.course=s.coursesID and ss.section=s.sectionID and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

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
    $stmt = null;
    $conn = null;
    
    return $status;// return true if no clash of timetable
}

//Check for exam timetable clash
function CheckExamTimeTable($userid,$courseid){

    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    //retrieve day, start and end from the incoming course
    $sql = "SELECT * FROM course where courseID=:courseid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    $info=[];
    if ($row=$stmt->fetch()){
        $info=['examDate'=>$row['examDate'],'examStart'=>$row['examStart'],'examEnd'=>$row['examEnd']];
    }

    $sql = "SELECT * FROM bid b, course c where  b.code=c.courseid and userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

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
    $sql = "SELECT * FROM student_section s, course c where  s.course=c.courseid and userid=:userid;"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

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

    $stmt = null;
    $conn = null;
    
    return $status;// return true if no clash of examtimetable
}

//Check if course has been completed
function CheckForCompletedCourse($userid,$course){
    
    $course_completedDAO=new CourseCompletedDAO();
    return $course_completedDAO->checkCourseComplete($userid, $course);//return true if already completed course

}

//Check if user already bid for more than 5 mods
function CheckForExceedOfBidSection($userid,$course){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "SELECT *  FROM bid where userid=:userid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();
    
        if (!$status){ 
            $err=$stmt->errorinfo();
        }
        $count=0;
        while ($row=$stmt->fetch()){
            if ($row['code']!=$course){
                $count++;
            }
        }

        $sql = "SELECT * FROM student_section where userid=:userid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();
    
        if (!$status){ 
            $err=$stmt->errorinfo();
        }
        while ($row=$stmt->fetch()){
            $count++;
        }

        $stmt = null;
        $conn = null;
        
        return $count<5;// return True if didnt exceed
}

//Check if user has enough edollar to bid
function CheckForEdollar($userid, $amount, $course, $retrieveValue=FALSE){
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    //retrieve exisiting course bid
    $sql = "SELECT *  FROM  bid where userid=:userid  and code=:course"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
    $stmt->bindParam(':course',$course,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    if (!$status){
        $err=$stmt->errorinfo();
    }
    $amt=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $amt=$row['amount'];
        }
    }
    $sql = "SELECT *  FROM  student where userid=:userid"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();

    // check if query fail
    if (!$status){ 
        $err=$stmt->errorinfo();
    }
    $userEdollar=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $userEdollar=$row['edollar'];
        }
    }

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

//Update / delete bid amount in database
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

//Count the number of bids with the same amount
function noOfSameMinBid($course,$section,$amount){
    
    $connMgr = new ConnectionManager();
    $conn = $connMgr->getConnection();

    //retrieve existing course bid
    $sql = "SELECT count(*) as numberSameBid  FROM  BID where code=:course and section=:section and amount=:amount"; 
    $stmt=$conn->prepare($sql);
    $stmt->bindParam(':course',$course,PDO::PARAM_STR);
    $stmt->bindParam(':section',$section,PDO::PARAM_STR);
    $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $status = $stmt->execute();
    $Total=0;
    if ($row=$stmt->fetch()){
        if ($row!=NULL){
            $Total=$row['numberSameBid'];
        }
    }

    $stmt = null;
    $conn = null;

    return $Total;
}
?>