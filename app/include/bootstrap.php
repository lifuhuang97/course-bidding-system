<?php
require_once 'common.php';
require_once 'function.php';
require_once 'DoRestart.php';
require_once 'DoStart.php';

function doBootstrap() {
    doRestart();	
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
				$inputRowError=array();
                # for the project, the full error list is listed in the wiki
                // Student
                $header =fgetcsv($student);
                $line=2;
                $useridList=[];
                while (($data=fgetcsv($student))!==false){
                    //$data[0]=>userid, $data[1]=>password, $data[2]=>name, $data[3]=>school, $data[4]=>edollar
                    $message=[];
                    for ($i=0;$i<=4;$i++){
                        if($i == 3){
                            $data[$i]=strtoupper(trim($data[$i]));
                            }
                            $data[$i]=trim($data[$i]);
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(strlen($data[0])>128){
                            //check if length of text is more than 128
                            $message[]="invalid userid";
                        }elseif(in_array($data[0],$useridList)){
                            $message[]="duplicate userid";
                        }
                        if(strlen($data[1])>128){
                            //check if length of text is more than 128
                            $message[]="invalid password";
                        }
                        if(strlen($data[2])>100){
                            //check if length of text is more than 100
                            $message[]="invalid name";
                        }
                        
                        if(!is_numeric($data[4]) || ($data[4]<0) || $data[4]!=number_format($data[4],2,'.','')){
                            //check if is numeric value, positive value and not more 2 decimal point
                            $message[]="invalid e-dollar";
                        }
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"student.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;                        
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
                $header =fgetcsv($course);
                $line=2;
                $courseList=[];
                while (($data=fgetcsv($course))!==false){
                    //$data[0]=>Course, $data[1]=>School, $data[2]=>Title, $data[3]=>Description, $data[4]=>ExamDate, $data[5]=>ExamStart, $data[6]=>ExamEnd
                    $message=[];
                    for ($i=0;$i<=6;$i++){
                        if($i == 0 or $i == 1){
                        $data[$i]=strtoupper(trim($data[$i]));
                        }
                        $data[$i]=trim($data[$i]);
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(strlen($data[2])>100){
                            //check if length of text is more than 100
                            $message[]="invalid title";
                        }
                        if(strlen($data[3])>1000){
                            //check if length of text is more than 1000
                            $message[]="invalid description";
                        }
                        if($data[4]!=date("Ymd",strtotime($data[4]))){
                            //check if the date format is in YYYYMMDD eg 20191029
                            $message[]="invalid exam date";
                        }
                        $dateFormat=TRUE;
                        if($data[5]!=date("G:i",strtotime($data[5])) and $data[5]!=date("H:i",strtotime($data[5]))){
                            //Hours with no leading 0 use G 
                            //Hours with leading 0 use H 
                            //check if time format is in HH:MM eg 15:30
                            $message[]="invalid exam start";
                            $dateFormat=False;
                        }
                        if($data[6]!=date("G:i",strtotime($data[6])) and $data[6]!=date("H:i",strtotime($data[6]))){
                            //Hours with no leading 0 use G
                            //Hours with leading 0 use H 
                            //check if time format is in HH:MM eg 15:30
                            $message[]="invalid exam end";
                            $dateFormat=False;
                        }
                        if($dateFormat and (strtotime($data[6])-strtotime($data[5]))<=0){
                            //Check if date is in right format and different between end and start is positive
                            $message[]="invalid exam end";
                        }
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"course.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;
                        
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
                $header =fgetcsv($section);
                $line=2;
                $sectionList=[];
                while (($data=fgetcsv($section))!==false){
                    //$data[0]=>Course, $data[1]=>Section, $data[2]=>Day, $data[3]=>Start, $data[4]=>End, $data[5]=>Instructor, $data[6]=>Venue, $data[7]=>Size
                    $message=[];
                    for ($i=0;$i<=7;$i++){
                        if($i == 0 or $i == 1){
                            $data[$i]=strtoupper(trim($data[$i]));
                            }
                            $data[$i]=trim($data[$i]);
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(!in_array($data[0],$courseList)){
                            //check if course exist in course.csv
                            $message[]="invalid course";
                        }
                        if($data[1][0]!="S" || !is_numeric(substr($data[1],1)) || substr($data[1],1)<1 || substr($data[1],1)>99){
                            //check if first character is not a "S" and check numbers is between 1 to 99
                            $message[]="invalid section";
                        }
                        if($data[2]<1 ||$data[2]>7){
                            $message[]="invalid day";
                        }
                        $dateFormat= True;
                        if($data[3]!=date("G:i",strtotime($data[3])) and $data[3]!=date("H:i",strtotime($data[3]))){
                            //Hours with no leading 0 use G 
                            //Hours with leading 0 use H 
                            //check if time format is in HH:MM eg 15:30
                            $message[]="invalid start";
                            $dateFormat=False;
                        }
                        if($data[4]!=date("G:i",strtotime($data[4])) and $data[4]!=date("H:i",strtotime($data[4]))){
                            //Hours with no leading 0 use G 
                            //Hours with leading 0 use H 
                            //check if time format is in HH:MM eg 15:30
                            $message[]="invalid end";
                            $dateFormat=False;
                        }
                        if($dateFormat and (strtotime($data[4])-strtotime($data[3]))<=0){
                            //Check if date is in right format and different between end and start is positive
                            $message[]="invalid end";
                        }

                        if(strlen($data[5])>100){
                            //check if length of text is more than 100
                            $message[]="invalid instructor";
                        }

                        if(strlen($data[6])>100){
                            //check if length of text is more than 100
                            $message[]="invalid venue";
                        }
                        if($data[7]<1){
                            //check if variable is positive
                            $message[]="invalid size";
                        }
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"section.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;
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
                $header =fgetcsv($prerequisite);

                while (($data=fgetcsv($prerequisite))!==false){
                    //$data[0]=>course, $data[1]=>prerequisite
                    $message=[];
                    for ($i=0;$i<=1;$i++){
                        $data[$i]=strtoupper(trim($data[$i]));
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(!in_array($data[0],$courseList)){
                            //check for course in course.csv
                            $message[]="invalid course";
                        }
                        if(!in_array($data[1],$courseList)){
                            //check for prerequisite in course.csv
                            $message[]="invalid prerequisite";
                        }
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"prerequisite.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;
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
                $header =fgetcsv($course_completed);
                $line=2;
                
                while (($data=fgetcsv($course_completed))!==false){
                    //$data[0]=>userid, $data[1]=>code
                    $message=[];
                    for ($i=0;$i<=1;$i++){
                        if($i == 1){
                            $data[$i]=strtoupper(trim($data[$i]));
                            }
                            $data[$i]=trim($data[$i]);
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(!in_array($data[0],$useridList)){
                            //check userid exist in student.csv
                            $message[]="invalid userid";
                        }
                        if(!in_array($data[1],$courseList)){
                            //check course exist in course.csv
                            $message[]="invalid course";
                        }
                    }
                    if (isEmpty($message) and !CheckForCompletedPrerequisites($data[0],$data[1])){
                        $message[]="invalid course completed";
                    }

                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"course_completed.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;
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
                $header =fgetcsv($bid);
                $line=2;
                
                while (($data=fgetcsv($bid))!==false){
                    //$data[0]=>userid, $data[1]=>amount, $data[2]=>code, $data[3]=>section
                    $message=[];
                    for ($i=0;$i<=3;$i++){
                        if($i == 2 or $i == 3){
                            $data[$i]=strtoupper(trim($data[$i]));
                            }
                            $data[$i]=trim($data[$i]);
                        if (strlen($data[$i])==0){
                            //check for empty cell
                            $message[]="blank $header[$i]";
                        }
                    }
                    if (isEmpty($message)){
                        if(!in_array($data[0],$useridList)){
                            // check if userid exist in student.csv
                            $message[]="invalid userid";
                        }
                        if(!is_numeric($data[1]) || ($data[1]<10) || $data[1]!=number_format($data[1],2,'.','')){
                            //check if is numeric value, value less than 10  and not more 2 decimal point
                            $message[]="invalid amount";
                        }
                        $courseValid=TRUE;
                        if(!in_array($data[2],$courseList)){
                            // check if code exist in student.csv
                            $message[]="invalid course";
                            $courseValid=FALSE;
                        }
                        if($courseValid && (!array_key_exists($data[2],$sectionList) || !in_array($data[3],$sectionList[$data[2]]))){                            // check if code exist in student.csv
                            $message[]="invalid section";
                        }
                    }
                    if (isEmpty($message)){
                        if (!CheckForOwnSchool($data[0],$data[2])){
                            $message[]="not own school course";
                        }
                        if (!CheckClassTimeTable($data[0],$data[2],$data[3])){
                            $message[]="class timetable clash";
                        }
                        if (!CheckExamTimeTable($data[0],$data[2])){
                            $message[]="exam timetable clash";
                        }
                        if (!CheckForCompletedPrerequisites($data[0],$data[2])){
                            $message[]="incomplete prerequisites";
                        }
                        if (CheckForCompletedCourse($data[0],$data[2])){
                            $message[]="course completed";
                        }
                        if (!CheckForExceedOfBidSection($data[0],$data[2])){
                            $message[]="section limit reached";
                        }
                        if (!CheckForEdollar($data[0],$data[1],$data[2])){
                            $message[]="not enough e-dollar";
                        }
                        
                    }
                    if (!isEmpty($message)){
                        $lineError=
                            ["file"=>"bid.csv",
                            "line"=>$line,
                            "message"=>$message
                            ]
                        ;
                        $inputRowError[]=$lineError;
                    }else{
                        ChangeBidUpdateEdollar(new Bid($data[0],$data[1],$data[2],$data[3]));
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

	if (!isEmpty($errors))
	{	
		$sortclass = new Sort();
		$errors = $sortclass->sort_it($errors,"bootstrap");
		$result = [ 
			"status" => "error",
			"message" => $errors
		];
	}
	else
	{	
		$result = [ 
			"status" => "success",
			"num-record-loaded" => [
				["bid.csv" => $bid_processed],
				["course.csv" => $course_processed],
				["course_completed.csv" => $course_completed_processed],
                ["prerequisite.csv" => $prerequisite_processed],
                ["section.csv" => $section_processed],
				["student.csv" => $student_processed]
			]
        ];
        if (!isEmpty($inputRowError)){
            $file=array_column($inputRowError,"file");
            $lines=array_column($inputRowError,"line");
            array_multisort($file,SORT_ASC,$lines,SORT_ASC,$inputRowError);
            $result["status"]="error";
            $result["error"]=$inputRowError;
        }
    }
    doStart();
    return $result;
}
?>