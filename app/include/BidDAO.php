<?php

require_once 'common.php';

class BidDAO {

    public function drop($id, $courseid, $sectionid){
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "DELETE FROM bid WHERE userid=:id and code=:courseid and section=:sectionid"; 

        // Run Query
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
        $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }
    public function update($id, $courseid, $sectionid, $amount) {

        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Prepare SQL
        $sql = "UPDATE BID SET amount=:amount, section=:sectionid where userid=:id and code=:courseid"; 
        
        // Run Query
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        $stmt->bindParam(':courseid',$courseid,PDO::PARAM_STR);
        $stmt->bindParam(':sectionid',$sectionid,PDO::PARAM_STR);
        $status = False;

        if ($stmt->execute()){
            $status=True;
        }
        // Close Query/Connection
        $stmt = null;
        $conn = null;

        return $status; // Boolean True or False
    }

    public function add($bid) {
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $userid=$bid->getUserid();
        $amount=$bid->getAmount();
        $code=$bid->getCode();
        $section=$bid->getSection();

        // Prepare SQL
        $sql = "INSERT INTO BID (userid, amount, code, section) VALUES
        (:userid, :amount, :code, :section)"; 
        $stmt=$conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindParam(':amount',$amount,PDO::PARAM_STR);
        $stmt->bindParam(':code',$code,PDO::PARAM_STR);
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


    public function removeAll() {
        // $sql = 'TRUNCATE TABLE BID';
        $sql = 'DELETE FROM BID';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }

    public function getBidInfo($userid) {
        
        // Connect to Database
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        // Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = "SELECT * 
                FROM BID 
                WHERE   
                    userid=:userid
                ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid',$userid,PDO::PARAM_STR);
        
        // Execute SQL Query
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status=$stmt->execute();
        // check if query fail
        if (!$status){ //if ($status==False)
            //if there is error
            $err=$stmt->errorinfo();
            var_dump($err);
        }
        // Retrieve Query Results (if any)
        $mod=[];
        while ($row=$stmt->fetch()){
            $mod[]=new Bid($row['userid'],$row['amount'],$row['code'],$row['section']); 
        }
        
        // Clear Resources $stmt, $pdo
        $stmt = null;
        $conn = null;

        // Step 6 - Return (if any)
        return $mod;
    }

}

?>