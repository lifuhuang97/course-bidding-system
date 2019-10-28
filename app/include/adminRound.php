<?php

class AdminRound {
    private $adminID;
    private $adminPW;
    private $adminTK;
    private $roundID;
    private $roundStatus;
    private $r1Start;
    private $r1End;
    private $r2Start;
    private $r2End;

    public function __construct ($adminID, $adminPW, $adminTK, $roundID, $roundStatus, $r1Start, $r1End, $r2Start, $r2End){
        $this->adminID = $adminID;
        $this->adminPW = $adminPW;
        $this->adminTK = $adminTK;
        $this->roundID = $roundID;
        $this->roundStatus = $roundStatus;
        $this->r1Start = $r1Start;
        $this->r1End = $r1End;
        $this->r2Start = $r2Start;
        $this->r2End = $r2End;
    }

    public function getRoundID(){
        return $this->roundID;
    }

    public function getRoundStatus(){
        return $this->roundStatus;
    }

    public function getR1Start(){
        return $this->r1Start;
    }

    public function getR1End(){
        return $this->r1End;
    }

    public function getR2Start(){
        return $this->r2Start;
    }

    public function getR2End(){
        return $this->r2End;
    }
    
}

?>