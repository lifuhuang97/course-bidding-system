<?php

class StudentSection {

    private $userid;
    private $amount;
    private $course;
    private $section;
    
    public function __construct($userid, $amount, $course, $section) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;
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

}

?>