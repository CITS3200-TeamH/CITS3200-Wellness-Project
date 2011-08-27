<?php
	include 'config.php';
	include 'connect.php';

	/**	Return the raw Physical Activity data along with profile information, in array form.
	*	i.e.	$data[0][0] is data for day 1 record 1
	*	
	*	data if of the format:
	*	"Date,Day,Day Count,Week Count,ID,Surname,First,DOB,Gender,Athletic,Sport,Level,Activity Type,Duration,METs,Cardio,Muscle,Flex,Body,Heading,Description,Intensity"
	*/
	function getPARaw($group) {
		//get the physical activity data
		$res = mysql_query('
							SELECT tr1.daydate as date,tr1.student_id as id, c.type as type, tr1.duration as duration, tr1.duration*c.mets as mets,c.cardio as cardio,
									c.muscle as muscle,c.flex as flex,c.body as body, c.heading as heading,c.description as description,c.intensity as intensity
							FROM training_records1 tr1,compcodes c
							WHERE c.compcode = tr1.compcode
							AND tr1.class="'.$group.'"
							ORDER BY tr1.daydate,tr1.student_id');
		$row = mysql_fetch_array($res);
					
		//get profile data		
		$subject_info = getSubjectIDS($group);
	
		//get the number of days in the project
		$date_info = getDateInfo($group);
		$ndays = $date_info[2];
	
		//get the number of subjects
		$nsubs = getNumSubjects($group);
		
		//populate the array
		for($i = 0; $i < $ndays; $i++) {
			//increment the date
			$date = date('Y-m-d',strtotime('+ '.$i.' day',strtotime($date_info[0])) );
			$day = date('D',strtotime($date));
			$week = getWeek($date,$date_info[0]);
			$k = 0;
			for($j = 0; $j < $nsubs; $j++) {
				if($subject_info[$j][0] == $row['id'] && $date == $row['date']) {
					//for adding multiple entries
					while($subject_info[$j][0] == $row['id'] && $date == $row['date']) {
						//add date and subject info
						$data[$i][$k] .= $date .','. $day .','. ($i+1) .','. $week .','. $subject_info[$j][0] .',"'.$subject_info[$j][1].'","'.$subject_info[$j][2]. '","'.$subject_info[$j][3].'","'.
								$subject_info[$j][4].'","'.$subject_info[$j][5].'","'.$subject_info[$j][6].'","'.$subject_info[$j][7].'"';
					
						//add exercise data
						$data[$i][$k] .= ',"'. $row['type'] .'",'. $row['duration'] .','. $row['mets'] .','. $row['cardio'] .','. $row['muscle'] .','. 
								$row['flex'] .','. $row['body'] .',"'. $row['heading'] .'","'. $row['description'] .'","'. $row['intensity'] .'"'. "\n";
						$row = mysql_fetch_array($res);
						$k++;
					}
				}else {
					//add date and subject info ONLY
					$data[$i][$k] .= $date .','. $day .','. ($i+1) .','. $week .','. $subject_info[$j][0] .',"'.$subject_info[$j][1].'","'.$subject_info[$j][2]. '","'.$subject_info[$j][3].'","'.
							$subject_info[$j][4].'","'.$subject_info[$j][5].'","'.$subject_info[$j][6].'","'.$subject_info[$j][7].'"'. "\n";
					$k++;
				}
			}
		}
		return $data;
	}


	/**	Returns the daily records of each member of the group, for every day of the project.
	*	Result is a 2D array layed out as follows.
	*	i.e.
	*	$data[0][0] = 	Week1	Monday	subject_1 	exercise_summary 	rating_item_summary
	*	$data[0][1] = 	Week1	Monday	subject_2	exercise_summary 	rating_item_summary
	*	$data[1][0] = 	Week1	Tuesday	subject_1	exercise_summary 	rating_item_summary
	*	etc.
	*/
	function getFullSummary($group) {
		//get group's dates and duration
		$date_info = getDateInfo($group);
		
		//get the number of subjects in the group
		$num_subjects = getNumSubjects($group);
		
		//get the existing exercise data
		$res = mysql_query('
							SELECT tr1.daydate as date, date_format(daydate,\'%W\') as dotw, tr1.student_id as id,s1.first as first,s1.last as last, 
									count(daydate) as activities, sum(mets* tr1.duration) as total_mets, sum(tr1.duration) as duration
							FROM training_records1 tr1,compcodes,student s1 
							WHERE compcodes.compcode = tr1.compcode 
							AND tr1.class="'.$group.'" 
							AND daydate<="'.$date_info[1].'" 
							AND s1.id=tr1.student_id 
							GROUP BY tr1.daydate,tr1.student_id 
							ORDER BY tr1.daydate,tr1.student_id');
		$row = mysql_fetch_array($res);
		
		//get the existing rating item data
		$res2 = mysql_query('
							SELECT tr2.daydate as date, tr2.student_id as id , tr2.heart_rate as heart_rate, tr2.sleep as sleep, tr2.health as health, tr2.ratings as ratings
							FROM training_records2 tr2
							WHERE tr2.class = "'.$group.'"
							ORDER BY daydate,student_id');
		$row2 = mysql_fetch_array($res2);
		
		//get the array of sorted subject information
		$subject_ids = getSubjectIDS($group);
		
		//create the array of all data
		//loop through days
		for($i = 0; $i < $date_info[2]; $i++) {
			//increment the date
			$date = date('Y-m-d',strtotime('+ '.$i.' day',strtotime($date_info[0])) );
			$day = date('D',strtotime($date));
			$week = getWeek($date,$date_info[0]);
				
			//loop through subjects
			for($j = 0; $j < $num_subjects; $j++) {
				//add time and subject profile data
				$data[$i][$j] = $date.','.$day.','.($i+1).','.$week.','.$subject_ids[$j][0].',"'.$subject_ids[$j][1].'","'.$subject_ids[$j][2]. '","'.
								$subject_ids[$j][3].'","'.$subject_ids[$j][4].'","'.$subject_ids[$j][5].'","'.$subject_ids[$j][6].'","'.$subject_ids[$j][7].'"';
							
				//handle the exercise data
				if($row['date'] == $date && $subject_ids[$j][0] == $row['id']) {
						$data[$i][$j] .= ','.$row[5] .','. $row[6] .','. $row[7];
						$row = mysql_fetch_array($res);
				}else {
						$data[$i][$j] .= ',0,0,0';
				}
				
				//handle the rating item data
				if($row2['date'] == $date && $subject_ids[$j][0] == $row2['id']) {
					$data[$i][$j] .= ',' . $row2['heart_rate'] . ',' . $row2['sleep'] .','. $row2['health'];
					$data[$i][$j] .= ',' . $row2['ratings'] . "\n";
					$row2 = mysql_fetch_array($res2);
				}else {
					$data[$i][$j] .= "\n";
				} 
			}
		}
		return $data;
	}
	
	/**	Exports the fitness test data for the given group in array form.
	*/
	function getFitnessTests($group) {
		$res = mysql_query('
							SELECT daydate,id,last,first,pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,bmi,bmi_rating,waist,hip,ratio,wh_rating 
							FROM fitness_test f,student s 
							WHERE f.group_id="'.$group.'" 
							AND s.id=f.subject_id  
							ORDER BY daydate,subject_id');

		$i = 0;
		while($row = mysql_fetch_array($res,MYSQL_ASSOC)) {
			foreach($row as &$val) {
				$arr[$i][] = $val;
			}
			$i++;
		}
		return $arr;
	}
	

	/**	Returns the start date, end date and number of days for the specified group in array form.
	*	arr[0] = start_date
	*	arr[1] = end_date
	*	arr[2] = n_days
	*/
	function getDateInfo($group) {
		$res = mysql_query('SELECT start,finish FROM class WHERE name="'.$group.'"');
		if($res) {
			$row = mysql_fetch_array($res);
			$arr[0] = $row['start'];
			$arr[1] = $row['finish'];
			$diff_qry = mysql_query('SELECT datediff("'.$arr[1].'","'.$arr[0].'")+1 as days');
			if($diff_qry) { 
				$diff_row = mysql_fetch_array($diff_qry); 
				$arr[2] = $diff_row['days']; 
			}
			return $arr;
		}
	}
	
	
	/**	Returns id,last,first,age,gender,athletic,sport,level as an array for the given group.
	*/
	function getSubjectIDS($group) {
		$res = mysql_query('SELECT s.id as id,s.last as last,s.first as first,s.age as age,s.gender as gender,s.athletic as athletic,s.sport as sport,s.level as level FROM student s,classmap c WHERE class_name="'.$group.'" and s.id=c.student_id ORDER BY s.id');
		if($res) {
			$i = 0;
			while($row = mysql_fetch_array($res)) {
				for($j=0;$j<8;$j++) {
					$ids[$i][$j] = $row[$j];
				}
				$i++;
			}
			return $ids;
		}
	}
	
	function getNumSubjects($group) {
		$res = mysql_query('SELECT * FROM classmap WHERE class_name="'.$group.'"');
		if($res) { return mysql_num_rows($res); }
	}
	
	function getWeek($date,$start) {
		$week_qry  = mysql_query('SELECT CEILING(DATEDIFF(\''.$date.'\',\''.$start.'\')/7+(1/10000000)) as wk');		
		$week_row = mysql_fetch_array($week_qry);
		return $week_row['wk'];
	}
	
	/**	Uploads a CSV class list in the approved format and returns the success string if specified.
	*	$fileinfo contains the basename(name) and basename(tmp_name) of the uploaded file.
	*/
	function uploadClassList($fileinfo, $group, $verbose) {
		//init upload info and counters
		$target_path = "uploads/";
		$target_path = $target_path . $fileinfo[0];
		$st_success = 0;
		$st_fail = 0;
		$cl_success = 0;
		$cl_fail = 0;
		$stfail_arr = array();
		$stsuccess_arr = array();
		
		//move the file to tmp
		if(move_uploaded_file($fileinfo[1], $target_path)) {
			//the file was successfully uploaded
		} else{
			//the file could not be uploaded
			return false;
		}
		
		//split file and insert students in db
		$myFile = $target_path;
		$fh = fopen($myFile, 'r');
		$group = stripslashes($group);
		$admin_id = $_SESSION['username'];

		while( ($data=fgets($fh,128)) != NULL) 
		{
			list($id,$first,$last) = split(",",$data,3);
			$pw = '';
			$last = trim($last);
			if(strlen($last) >= 4) {
				$pw = $last[0].$last[1].$last[2].$last[3].$id[4].$id[5].$id[6].$id[7];
			}else {
			
				for($i=0;$i<strlen($last);$i++) {
					$pw .= $last[$i];
				}
				$pw .= $id[4].$id[5].$id[6].$id[7];
			}
			//insert into students table
			$sql="INSERT INTO student(id,first,last,password,active) VALUES($id,'$first','$last','$pw',true)";
			if(mysql_query($sql) != 1) {
				$st_fail++;
				array_push($stfail_arr,$id);
				//echo "could not upload student: $first $last<br/>";
			}else {
				$st_success++;
				array_push($stsuccess_arr,$id);
				//echo "successfully uploaded student: $first $last<br/>";
			}

			//insert into classmap table
			$sql='INSERT INTO classmap(student_id, class_name) VALUES('.$id.',"'.$group.'")';
			if(mysql_query($sql) != 1) {
				$cl_fail++;
				//echo "could not upload student: $first $last to class $class<br/>";
			}else {
				$cl_success++;
				//echo "successfully uploaded student: $first $last to class $class<br/>";
			}
		}
		/*echo "<b style=\"color:red;\">Successfully uploaded subject list '" .$_FILES['uploadedfile']['name']. "'.</b><br/><br/>";
		echo "<b>Summary:</b><br/><br/>";
		echo "<b>" . $st_success . " subjects successfully uploaded.</b><br/>";
		echo "<b>" . $st_fail . " subjects were already present in the system.</b><br/><br/>";
		echo "<b>" . $cl_success . " subjects were assigned to the project group '" .$group. "'.</b><br/>";
		echo "<b>" . $cl_fail . " subjects were already present in the project group '" .$group. "'.</b><br/>";*/
		fclose($fh);
		unlink($myFile);
		if($verbose == true) {
			$info[0] = $fileinfo[0];		
			$info[1] = $st_success;
			$info[2] = $st_fail;
			$info[3] = $cl_success;
			$info[4] = $cl_fail;
			return $info;
		}
	}
	
	/**	Attempts to add the given subject details to the supplied group.
	*	Returns:
	*		0 on fail, 
	*		1 on success
	*		2 if added only to the group (already exists in subjects)
	*		3 if already present in both group and subjects
	*/
	function addSubject($id,$first,$last,$group) {
		$pass = genPassword($id,$last);
		$res = mysql_query('INSERT INTO student(id,first,last,password) values('.$id.',"'.$first.'","'.$last.'","'.$pass.'")');
		if($res) { 
			//added subject to student so now add to group
			$res2 = mysql_query('INSERT INTO classmap(student_id,class_name) values('.$id.',"'.$group.'")');
			if($res2) {
				return 1;
			}else {
				return 0;
			}
		}else {
			//not added
			$res3 = mysql_query('SELECT * FROM student WHERE id='.$id.'');
			//if exists already
			if(mysql_num_rows($res3) > 0) {
				//student already exists so we can try to add to the group
				$res2 = mysql_query('INSERT INTO classmap(student_id,class_name) values('.$id.',"'.$group.'")');
				if($res2) {
					return 2;
				}else {
					//not added to group either so check if they already exist
					$res = mysql_query('SELECT * FROM classmap WHERE class_name="'.$group.'" AND student_id='.$id);
					if(mysql_num_rows($res) > 0) {
						return 3;
					}else {
						return 0;
					}
				}
			}else {
				//not added to student and doesn't exist already, an error likely occurred.
				return 0;
			}
		}
	}
	
	function genPassword($id,$last) {
		$pw = '';
		$last = trim($last);
		if(strlen($last) >= 4) {
			$pw = $last[0].$last[1].$last[2].$last[3].$id[4].$id[5].$id[6].$id[7];
		}else {
			
			for($i=0;$i<strlen($last);$i++) {
				$pw .= $last[$i];
			}
			$pw .= $id[4].$id[5].$id[6].$id[7];
		}
		return $pw;
	}
?>
