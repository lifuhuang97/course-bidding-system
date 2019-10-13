<?php

class StudentSection {

    private $userid;
    private $amount;
    private $course;
    private $section;
    private $bidstatus;
    private $bidround;
    
    
    public function __construct($userid, $amount, $course, $section, $bidstatus, $bidround) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;
        $this->bidstatus = $bidstatus;
        $this->bidround = $bidround;
    }

    public function getUserid() {
        return $this->userid;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getSection() {
        return $this->section;
    }

    public function getBidStatus() {
        return $this->bidstatus;
    }

}

?>