<?php

class Course {

    private $courseid;
    private $school;
    private $title;
    private $description;
    private $examDate;
    private $examStart;
    private $examEnd;
    
    
    public function __construct($courseid, $school,$title,$description,$examDate, $examStart, $examEnd) {
        $this->courseid = $courseid;
        $this->school = $school;
        $this->title = $title;
        $this->description = $description;
        $this->examDate = $examDate;
        $this->examStart = $examStart;
        $this->examEnd = $examEnd;
    }
    
    public function getCourseid() {
        return $this->courseid;
    }

    public function getSchool() {
        return $this->school;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getExamDate() {
        return $this->examDate;
    }

    public function getExamStart() {
        return $this->examStart;
    }

    public function getExamEnd() {
        return $this->examEnd;
    }


}

?>