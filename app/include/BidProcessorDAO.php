<?php

require_once 'common.php';

class bidProcessorDAO {
    
    // Add bid result record
    public function addBidResults($userid,$amount,$course,$section,$bidstatus, $bidround) {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "INSERT INTO BID_PROCESSOR (userid, amount, course, section, bidstatus, bidround) VALUES
        (:userid, :amount, :course, :section, :bidstatus, :bidround)"; 

        $stmt=$conn->prepare($sql);
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_INT);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->bindParam(':bidstatus',$bidstatus,PDO::PARAM_STR);
        $stmt->bindParam(':bidround',$bidround,PDO::PARAM_INT);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }

// Get all successful records [to be removed as inferior to bottom]
    public function getAllSuccessfulBids(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT userid, amount, course, section from BID_PROCESSOR order by amount desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            // $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section']];
            $bids[] = new Bid($row['userid'],$row['amount'],$row['course'],$row['section']);
        }
        return $bids;

    }



    // Get whether a bid is pending, success or fail

    public function getBidStatus($userid,$amount,$course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT bidstatus FROM BID_PROCESSOR where userid=:userid and amount=:amount and course=:course and section=:section"; 
        
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = [];

        while ($row = $stmt->fetch() ) {
            $result[] = $row['bidstatus'];
        }

        $stmt = null;
        $conn = null;        
        
        return $result;
    }


    // Get all existing bids with bid status
    public function getAllBidsWithStatus($bidround = ''){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $roundchooser = '';

        if($bidround != ''){
          if($bidround == 1){
              $roundchooser = 'where bidround = 1';
          }elseif($bidround == 2){
              $roundchooser = 'where bidround = 2';
          }
        }
        
        $sql = "SELECT userid, amount, course, section, bidstatus, bidround from BID_PROCESSOR {$roundchooser} order by bidstatus desc, amount desc, course asc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            $bids[] = new BidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }
        return $bids;

    }



    public function RetrieveAllStudentByCourseSection($course,$section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from BID_PROCESSOR WHERE course=:course AND section=:section order by userid asc";
        // $sql = "SELECT coursesID, sectionID from section";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $studentList = [];
        while ($row = $stmt->fetch() ) {
            $studentList[]=new bidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }

        $stmt = null;
        $conn = null;
        return $studentList;

    }

    public function RetrieveAll(){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        // Write & Prepare SQL Query (take care of Param Binding if necessary)
    
        $sql = "SELECT * FROM BID_PROCESSOR ORDER BY amount desc, course,userid";
        $stmt = $conn->prepare($sql);
                
        //Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        //Retrieve Query Results (if any)
        $students=[];
        while ($row=$stmt->fetch()){
            $students[]=new bidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'],$row['bidround']);
        }
        
        // Clear Resources $stmt, $conn
        $stmt = null;
        $conn = null;
    
        // return (if any)
        return $students;
    }

      // Get successful results with bid status according to user id

      public function getBidsByID($userid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * from BID_PROCESSOR where userid = :userid order by bidstatus asc, amount desc";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch()) {
            $bids[] = [$row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidstatus'], $row['bidround']];
        }
        return $bids;

    }

    // Update bid status of successful / failed bids

    public function updateBidStatus($userid, $amount, $course, $section, $passed = false){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        if($passed == true){
            $status = 'Success';
        }elseif($passed == false){
            $status = 'Fail';
        }

        $sql = "UPDATE BID_PROCESSOR set bidstatus = '{$status}' where userid=:userid and amount=:amount and course=:course and section=:section";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_INT);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        
            
        $status = $stmt->execute();
    
        $stmt = null;
        $conn = null; 

        return $status;
    }

    // // Clear all bids & store their success/fail in new table
    // public function clearRoundBids(){
    //    /** check minimum bid using ty/yl's function 
    //     * apply clearing to bid by comparing against min bid
    //     * if higher than min bid, success
    //     * if lower than min bid, fail
    //     * update accordingly
    //    */ 
    //     $adminRoundDAO = new adminRoundDAO();
    //     $roundNumber = $round->getRoundID();

    //     // get all courses & sections pairs in array

    //     foreach($courseSections as $courseSection){
    //         $course = $courseSection[0];
    //         $section = $courseSection[1];

    //         $minbid = CheckMinBid($course,$section);

    //         // get all bids from this course section
    //         //
    //         // for each bid, compare bid amount to min bid, update accordingly

    //         foreach($bids as $bid){
    //             $userid = $bid->getUserid();
    //             $amount = $bid->getAmount();

    //             if($amount <= $minbid){
    //                 updateBidStatus($userid, $amount, $course, $section, false);
    //             }elseif ($amount > $minbid){
    //                 updateBidStatus($userid, $amount, $course, $section, true);
    //             }
    //         }
    //     }

    //     $bidsList = ProcessAllSuccessfulBids($roundNumber);

    //     foreach($bidsList as $bid){
    //         $userid = $bid->getUserid();
    //         $amount = $bid->getAmount();
    //         $course = $bid->getCourse();
    //         $section = $bid->getSection();

    //         }

    //     // pass all successful bids into student section using a function

    // }
        

    // Transfer all successful bids to student section

    public function ProcessAllSuccessfulBids($bidround = ''){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $roundchooser = '';

        if($bidround != ''){
          if($bidround == 1){
              $roundchooser = 'and bidround = 1';
          }elseif($bidround == 2){
              $roundchooser = 'and bidround = 2';
          }
        }

        $sql = "SELECT userid, amount, course, section, bidround from BID_PROCESSOR where bidstatus = 'success' {$bidround}";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            $bids[] = new BidProcessor($row['userid'],$row['amount'],$row['course'],$row['section'],$row['bidround']);
        }
        
        $stusecDAO = new StudentSectionDAO();

        return $bids;

    }

        // Wipe table

        public function removeAll() {
            // $sql = 'TRUNCATE TABLE BID';
            $sql = 'DELETE FROM BID_PROCESSOR';
            
            $connMgr = new ConnectionManager();
            $conn = $connMgr->getConnection();
            
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
    
            $stmt = null;
            $conn = null; 
        }

}


?>