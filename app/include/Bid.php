<?php

class Bid {

    private $userid;
    private $amount;
    private $code;
    private $section;
    
    public function __construct($userid, $amount, $code, $section) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->code = $code;
        $this->section = $section;
    }

    public function getUserid() {
        return $this->userid;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCode() {
        return $this->code;
    }

    public function getSection() {
        return $this->section;
    }

    public function getCourseDetailsByCourseSection(){
        $courseDAO= new CourseDAO();
        $course=$courseDAO->retrieveAllCourseDetail($this->code,$this->section);
        return $course[0]; // cuz only 1 item
    }

}

?>