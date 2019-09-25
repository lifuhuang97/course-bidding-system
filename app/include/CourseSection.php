<?php

class CourseSection {

    private $courseid;
    private $sectionid;
    private $day;
    private $start;
    private $end;
    private $instructor;
    private $venue;
    private $size;
    private $school;
    private $title;
    private $description;
    private $examDate;
    private $examStart;
    private $examEnd;
    
    
    public function __construct($courseid, $sectionid, $day, $start, $end, $instructor, $venue, $size, $school,$title,$description,$examDate, $examStart, $examEnd) {
        $this->courseid = $courseid;
        $this->sectionid = $sectionid;
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
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

    public function getSectionid() {
        return $this->sectionid;
    }

    public function getDay() {
        return $this->day;
    }

    public function getStart() {
        return $this->start;
    }

    public function getEnd() {
        return $this->end;
    }

    public function getInstructor() {
        return $this->instructor;
    }
    
    public function getSize() {
        return $this->size;
    }

    public function getVenue() {
        return $this->venue;
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