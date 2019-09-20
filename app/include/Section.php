<?php

class Section {

    private $courseid;
    private $sectionid;
    private $day;
    private $start;
    private $end;
    private $instructor;
    private $venue;
    private $size;
    
    public function __construct($courseid, $sectionid, $day, $start, $end, $instructor, $venue, $size) {
        $this->courseid = $courseid;
        $this->sectionid = $sectionid;
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
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

    public function getVenue() {
        return $this->venue;
    }

    public function getSize() {
        return $this->size;
    }


}

?>