<?php
require_once 'common.php';

function doBootstrap() {
		

	$errors = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Get temp dir on system for uploading
	$temp_dir = sys_get_temp_dir();

	# keep track of number of lines successfully processed for each file
	$bid_processed=0;
	$course_processed=0;
    $course_completed_processed=0;
    $prerequisite_processed=0;
    $section_processed=0;
    $student_processed=0;

	# check file size
	if ($_FILES["bootstrap-file"]["size"] <= 0)
		$errors[] = "input files not found";

	else {
		
		$zip = new ZipArchive;
		$res = $zip->open($zip_file);

		if ($res === TRUE) {
			$zip->extractTo($temp_dir);
			$zip->close();
		
			$bid_path = "$temp_dir/bid.csv";
			$course_path = "$temp_dir/course.csv";
            $course_completed_path = "$temp_dir/course_completed.csv";
            $prerequisite_path = "$temp_dir/prerequisite.csv";
            $section_path = "$temp_dir/section.csv";
            $student_path = "$temp_dir/student.csv";
			
			$bid = @fopen($bid_path, "r");
			$course = @fopen($course_path, "r");
            $course_completed = @fopen($course_completed_path, "r");
            $prerequisite = @fopen($prerequisite_path, "r");
            $section = @fopen($section_path, "r");
			$student = @fopen($student_path, "r");
			
			if (empty($bid) || empty($course) || empty($course_completed) || empty($prerequisite) || empty($section) || empty($student)){
				$errors[] = "input files not found";
				if (!empty($bid)){
					fclose($bid);
					@unlink($bid_path);
				} 
                if (!empty($course_completed)) {
					fclose($course_completed);
					@unlink($course_completed_path);
                }
                if (!empty($prerequisite)) {
					fclose($prerequisite);
					@unlink($prerequisite_path);
                }
                if (!empty($section)) {
					fclose($section);
					@unlink($section_path);
                }
				if (!empty($course)) {
					fclose($course);
					@unlink($course_path);
				}
   
                if (!empty($student)) {
					fclose($student);
					@unlink($student_path);
				}	
				
			}
			else {
				$connMgr = new ConnectionManager();
				$conn = $connMgr->getConnection();

				# start processing
				
                # truncate current SQL tables
                $bidDAO= new BidDAO();
                $bidDAO->removeAll();

                $course_completedDAO= new CourseCompletedDAO();
                $course_completedDAO->removeAll();

                $prerequisiteDAO= new PrerequisiteDAO();
                $prerequisiteDAO->removeAll();

                $sectionDAO= new SectionDAO();
                $sectionDAO->removeAll();

                $courseDAO= new CourseDAO();
                $courseDAO->removeAll();

                $studentDAO= new StudentDAO();
                $studentDAO->removeAll();

				# then read each csv file line by line (remember to skip the header)
				# $data = fgetcsv($file) gets you the next line of the CSV file which will be stored 
				# in the array $data
				# $data[0] is the first element in the csv row, $data[1] is the 2nd, ....
				
				# process each line and check for errors
				
				# for this lab, assume the only error you should check for is that each CSV field 
				# must not be blank 
				
				# for the project, the full error list is listed in the wiki
                // Student
                $data =fgetcsv($student);
                $line=2;
                $useridList=[];
                while (($data=fgetcsv($student))!==false){
                    //userid password name school edollar
                    $message=[];
                    
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank userid";
                    }elseif(strlen($data[0])>128){
                        //check if length of text is more than 128
                        $message[]="invalid userid";
                    }elseif(in_array($data[0],$useridList)){
                        $message[]="duplicate userid";
                    }
                    if (strlen(trim($data[1]))==0){
                        //check for empty cell
                        $message[]="blank password";
                    }elseif(strlen($data[1])>128){
                        //check if length of text is more than 128
                        $message[]="invalid password";
                    }
                    if (strlen(trim($data[2]))==0){
                        //check for empty cell
                        $message[]="blank name";
                    }elseif(strlen($data[2])>100){
                        //check if length of text is more than 100
                        $message[]="invalid name";
                    }
                    if (strlen(trim($data[3]))==0){
                        //check for empty cell
                        $message[]="blank school";
                    }
                    if (strlen(trim($data[4]))==0){
                        //check for empty cell
                        $message[]="blank e-dollar";
                    }elseif(!is_numeric($data[4]) || ($data[4]<0) || $data[4]!=number_format($data[4],2,'.','')){
                        //check if is numeric value, positive value and not more 2 decimal point
                        $message[]="invalid e-dollar";
                    }

                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"student.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $studentDAO->add(new Student($data[0],$data[1],$data[2],$data[3],$data[4]));
                        $student_processed++;
                        $useridList[]=$data[0];
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors

                // clean up
                fclose($student);
                @unlink($student_path);

				// Course 
                $data =fgetcsv($course);
                $line=2;
                $courseList=[];
                while (($data=fgetcsv($course))!==false){
                    $message=[];
                    //check if blank
                    //check order of upload
                    //set error=0
                    //add 1 if there is error
                    //add course if no error
                    //row number to indicate error
                    //Course School Title Description ExamDate ExamStart ExamEnd
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank course";
                    }
                    if (strlen(trim($data[1]))==0){
                        //check for empty cell
                        $message[]="blank school";
                    }
                    if (strlen(trim($data[2]))==0){
                        //check for empty cell
                        $message[]="blank title";
                    }elseif(strlen($data[2])>100){
                        //check if length of text is more than 100
                        $message[]="invalid title";
                    }
                    if (strlen(trim($data[3]))==0){
                        //check for empty cell
                        $message[]="blank description";
                    }elseif(strlen($data[3])>1000){
                        //check if length of text is more than 1000
                        $message[]="invalid description";
                    }
                    if (strlen(trim($data[4]))==0){
                        //check for empty cell
                        $message[]="blank examDate";
                    }elseif($data[4]!=date("Ymd",strtotime($data[4]))){
                        //check if the date format is in YYYYMMDD eg 20191029
                        $message[]="invalid exam date";
                    }
                    $dateFormat=TRUE;
                    if (strlen(trim($data[5]))==0){
                        $message[]="blank examStart";
                        //check for empty cell
                    }elseif($data[5]!=date("G:i",strtotime($data[5])) ){
                        //Hours with no leading 0 use G 
                        //check if time format is in HH:MM eg 15:30
                        $message[]="invalid exam start";
                        $dateFormat=False;
                    }
                    if (strlen(trim($data[6]))==0){
                        //check for empty cell
                        $message[]="blank examEnd";
                    }elseif($data[6]!=date("G:i",strtotime($data[6])) ){
                        //Hours with no leading 0 use G 
                        //check if time format is in HH:MM eg 15:30
                        $message[]="invalid exam end";
                        $dateFormat=False;
                    }
                    if($dateFormat and (strtotime($data[6])-strtotime($data[5]))<=0){
                        //Check if date is in right format and different between end and start is positive
                        $message[]="invalid exam end";
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"course.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $courseDAO->add(new Course($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6]));
                        $course_processed++;
                        $courseList[]=$data[0];
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors
                
				// clean up
                fclose($course);
                @unlink($course_path);

				// Section
                $data =fgetcsv($section);
                $line=2;
                $sectionList=[];
                while (($data=fgetcsv($section))!==false){
                    $message=[];
                    //Course Section Day Start End Instructor Venue Size
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank course";
                    }elseif(!in_array($data[0],$courseList)){
                        //check if course exist in course.csv
                        $message[]="invalid course";
                    }
                    if (strlen(trim($data[1]))==0){
                        //check for empty cell
                        $message[]="blank section";
                    }elseif($data[1][0]!="S" || substr($data[1],1)<1 || substr($data[1],1)>99){
                        //check if first character is not a "S" and check numbers is between 1 to 99
                        $message[]="invalid section";
                    }
                    if (strlen(trim($data[2]))==0){
                        //check for empty cell
                        $message[]="blank day";
                    }elseif($data[2]<1 ||$data[2]>7){
                        $message[]="invalid day";
                    }
                    $dateFormat= True;
                    if (strlen(trim($data[3]))==0){
                        //check for empty cell
                        $message[]="blank start";
                    }elseif($data[3]!=date("G:i",strtotime($data[3])) ){
                        //Hours with no leading 0 use G 
                        //check if time format is in HH:MM eg 15:30
                        $message[]="invalid start";
                        $dateFormat=False;
                    }
                    if (strlen(trim($data[4]))==0){
                        //check for empty cell
                        $message[]="blank end";
                    }elseif($data[4]!=date("G:i",strtotime($data[4])) ){
                        //Hours with no leading 0 use G 
                        //check if time format is in HH:MM eg 15:30
                        $message[]="invalid end";
                        $dateFormat=False;
                    }
                    if($dateFormat and (strtotime($data[4])-strtotime($data[3]))<=0){
                        //Check if date is in right format and different between end and start is positive
                        $message[]="invalid end";
                    }

                    if (strlen(trim($data[5]))==0){
                        //check for empty cell
                        $message[]="blank instructor";
                    }elseif(strlen($data[5])>100){
                        //check if length of text is more than 100
                        $message[]="invalid instructor";
                    }

                    if (strlen(trim($data[6]))==0){
                        //check for empty cell
                        $message[]="blank venue";
                    }elseif(strlen($data[6])>100){
                        //check if length of text is more than 100
                        $message[]="invalid venue";
                    }
                    if (strlen(trim($data[7]))==0){
                        //check for empty cell and variable not equal to 0
                        $message[]="blank size";
                    }elseif($data[7]<1){
                        //check if variable is positive
                        $message[]="invalid size";
                    }
                    
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"section.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $sectionDAO->add(new Section($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7]));
                        $section_processed++;
                        if (array_key_exists($data[0],$sectionList)){
                            $sectionList[$data[0]][]=$data[1];
                        }else{
                            $sectionList[$data[0]]=[$data[1]];
                        }
                        
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors
                // clean up
                fclose($section);
                @unlink($section_path);

                // Prerequisite
                $line=2;
                $data =fgetcsv($prerequisite);

                while (($data=fgetcsv($prerequisite))!==false){
                    //course prerequisite
                    $message=[];
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank course";
                    }elseif(!in_array($data[0],$courseList)){
                        //check for course in course.csv
                        $message[]="invalid course";
                    }
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank prerequisite";
                    }elseif(!in_array($data[1],$courseList)){
                        //check for prerequisite in course.csv
                        $message[]="invalid prerequisite";
                    }

                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"prerequisite.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $prerequisiteDAO->add(new Prerequisite($data[0],$data[1]));
                        $prerequisite_processed++;
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors
                
                // clean up
                fclose($prerequisite);
                @unlink($prerequisite_path);

                // course_completed
                $data =fgetcsv($course_completed);
                $line=2;
                //userid and code
                while (($data=fgetcsv($course_completed))!==false){
                    $message=[];
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank userid";
                    }elseif(!in_array($data[0],$useridList)){
                        //check userid exist in student.csv
                        $message[]="invalid userid";
                    }
                    if (strlen(trim($data[1]))==0){
                        //check for empty cell
                        $message[]="blank course";
                    }elseif(!in_array($data[1],$courseList)){
                        //check course exist in course.csv
                        $message[]="invalid course";
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"course_completed.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $course_completedDAO->add(new CourseCompleted($data[0],$data[1]));
                        $course_completed_processed++;
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors
                
                // clean up
                fclose($course_completed);
                @unlink($course_completed_path);

                // Bid
                $data =fgetcsv($bid);
                $line=2;
                //userid amount code section
                while (($data=fgetcsv($bid))!==false){
                    $message=[];
                    if (strlen(trim($data[0]))==0){
                        //check for empty cell
                        $message[]="blank userid";
                    }elseif(!in_array($data[0],$useridList)){
                        // check if userid exist in student.csv
                        $message[]="invalid userid";
                    }
                    if (strlen(trim($data[1]))==0){
                        //check for empty cell
                        $message[]="blank amount";
                    }elseif(!is_numeric($data[1]) || ($data[1]<10) || $data[1]!=number_format($data[1],2,'.','')){
                        //check if is numeric value, value less than 10  and not more 2 decimal point
                        $message[]="invalid amount";
                    }
                    $courseValid=TRUE;
                    if (strlen(trim($data[2]))==0){
                        //check for empty cell
                        $message[]="blank course";
                        $courseValid=FALSE;
                    }elseif(!in_array($data[2],$courseList)){
                        // check if code exist in student.csv
                        $message[]="invalid course";
                        $courseValid=FALSE;
                    }
                    if (strlen(trim($data[3]))==0){
                        //check for empty cell
                        $message[]="blank section";
                    }
                    if($courseValid && !in_array($data[3],$sectionList[$data[2]])){
                        // check if code exist in student.csv
                        $message[]="invalid section";
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"bid.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $errors["error"][]=$lineError;
                    }else{
                        $bidDAO->add(new Bid($data[0],$data[1],$data[2],$data[3]));
                        $bid_processed++;
                    }
                    $line++;
                }
				// process each line, check for errors, then insert if no errors
                
                // clean up
                fclose($bid);
                @unlink($bid_path);

                
			}
		}
	}

	# Sample code for returning JSON format errors. remember this is only for the JSON API. Humans should not get JSON errors.

	if (count($errors)!=0)
	{	
		$sortclass = new Sort();
		$errors = $sortclass->sort_it($errors,"bootstrap");
		$result = [ 
			"status" => "error",
			"messages" => $errors
		];
	}

	else
	{	
		$result = [ 
			"status" => "success",
			"num-record-loaded" => [
				"bid.csv" => $bid_processed,
				"course.csv" => $course_processed,
				"course_completed.csv" => $course_completed_processed,
                "prerequisite.csv" => $prerequisite_processed,
                "section.csv" => $section_processed,
				"student.csv" => $student_processed
			]
		];
	}
	header('Content-Type: application/json');
	echo json_encode($result, JSON_PRETTY_PRINT);

}
?>