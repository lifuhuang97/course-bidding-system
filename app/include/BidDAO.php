<?php

require_once 'common.php';

class BidDAO {

    // Drop a bid
    public function drop($id, $courseid, $sectionid){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $sql = "DELETE FROM bid WHERE userid=:id and code=:courseid and section=:sectionid"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
        $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        $stmt = null;
        $conn = null;

        return $status; 
    }

    // Update a bid
    public function update($id, $courseid, $sectionid, $amount) {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "UPDATE BID SET amount=:amount, section=:sectionid where userid=:id and code=:courseid"; 
        
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
        $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        $stmt = null;
        $conn = null;

        return $status; 
    }

    //Retrieve All Bids by course & section
    public function getAllBids($section){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $coursesID=$section[0];
        $sectionID=$section[1];

        $sql = "select * from bid where code = '$coursesID' and section = '$sectionID' order by amount desc, userid;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $bids = [];
        while ($row = $stmt->fetch() ) {
            $bids[] = new Bid ( $row['userid'], $row['amount'], $row['code'], $row['section']);
        }
        return $bids;

    }

    // Add a bid
    public function add($bid) {
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $userid=$bid->getUserid();
        $amount=$bid->getAmount();
        $code=$bid->getCode();
        $section=$bid->getSection();

        $sql = "INSERT INTO BID (userid, amount, code, section) VALUES
        (:userid, :amount, :code, :section)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':code',$code,PDO::PARAM_STR);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        
        $status = False;
        if ($stmt->execute()){
            $status=True;
        }

        $stmt = null;
        $conn = null;

        return $status; 
    }

    //Clear Bid Table
    public function removeAll() {
        $sql = 'DELETE FROM BID';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }

    //Retrieve bids by userID
    public function getBidInfo($userid) {
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = "SELECT * 
                FROM BID 
                WHERE   
                    userid=:userid
                ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();
        if (!$status){ 
            $err=$stmt->errorinfo();
        }
        $mod=[];
        while ($row=$stmt->fetch()){
            $mod[]=new Bid($row['userid'],$row['amount'],$row['code'],$row['section']); 
        }
        
        $stmt = null;
        $conn = null;

        return $mod;
    }

    //Retrieve all bids
    public function RetrieveAll(){
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $sql = "SELECT * FROM BID ORDER BY code,section,amount desc,userid";
        $stmt = $conn->prepare($sql);
                
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();

        $bid=[];
        while ($row=$stmt->fetch()){
            $bid[]=new Bid($row['userid'],$row['amount'],$row['code'],$row['section']);
        }
        
        $stmt = null;
        $conn = null;
    
        return $bid;
    }
}
?>