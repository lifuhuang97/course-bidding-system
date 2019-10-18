<?php
require_once 'common.php';
function doDump() {
    //course
    $CourseDAO=new CourseDAO();
    $courses=$CourseDAO->RetrieveAll();
    $courseList=[];
    foreach ($courses as $course){
        $courseList[]=["course"=> $course->getCourseid(),
                    "school"=> $course->getSchool(),
                    "title"=> $course->getTitle(),
                    "description"=>$course->getDescription(),
                    "exam date"=> $course->getExamDate(),
                    "exam start"=> $course->getExamStart(),
                    "exam end"=> $course->getExamEnd()];
    }
    //section
    $SectionDAO=new SectionDAO();
    $sections=$SectionDAO->RetrieveAll();
    $sectionList=[];
    foreach ($sections as $section){
        $sectionList[]=["course" => $section->getCourseid(),
                        "section" => $section->getSectionid(),
                        "day" => $section->getDay(),
                        "start" => $section->getStart(),
                        "end" => $section->getEnd(),
                        "instructor" => $section->getInstructor(),
                        "venue" => $section->getVenue(),
                        "size" => $section->getSize()];
    }
    //student
    $StudentDAO=new StudentDAO();
    $students=$StudentDAO->RetrieveAll();
    $studentList=[];
    foreach ($students as $student){
        $studentList[]=["userid" => $student->getUserid(),
                        "password" => $student->getPassword(),
                        "name" => $student->getName(),
                        "school" => $student->getSchool(),
                        "edollar" => $student->getEdollar()];
    }
    //prerequisite
    $prerequisiteDAO=new PrerequisiteDAO();
    $prerequisites=$prerequisiteDAO->RetrieveAll();
    $prerequisiteList=[];
    foreach ($prerequisites as $prerequisite){
        $prerequisiteList[]=["course" => $prerequisite->getCourse(),
                            "prerequisite" => $prerequisite->getPrerequisite()];
    }
    //bid
    $BidDAO=new BidDAO();
    $bids=$BidDAO->RetrieveAll();
    $bidList=[];
    foreach ($bids as $bid){
        $bidList[]=["userid" => $bid->getUserid(),
                    "amount" => $bid->getAmount(),
                    "course" => $bid->getCode(),
                    "section" => $bid->getSection()];
    }
    //completedCourse
    $CourseCompletedDAO=new CourseCompletedDAO();
    $CourseCompleteds=$CourseCompletedDAO->RetrieveAll();
    $completedCourseList=[];
    foreach ($CourseCompleteds as $CourseCompleted){
        $completedCourseList[]=["userid" => $CourseCompleted->getUserid(),
                                "course" => $CourseCompleted->getCode()];
    }
    //StudentSection
    $StudentSectionDAO=new StudentSectionDAO();
    $StudentSections=$StudentSectionDAO->RetrieveAll();
    $sectionStudentList=[];
    foreach ($StudentSections as $StudentSection){
        $sectionStudentList[]=["userid" => $StudentSection->getUserid(),
                                "course" => $StudentSection->getCourse(),
                                "section" => $StudentSection->getSection(),
                                "amount" => $StudentSection->getAmount()];
    }

    $result = [ 
        "status" => "success",
        "course" => $courseList,
        "section" => $sectionList,
        "student" => $studentList,
        "prerequisite" => $prerequisiteList,
        "bid" => $bidList,
        "completed-course" => $completedCourseList,
        "section-student" => $sectionStudentList
    ];
    return $result;
}
?>