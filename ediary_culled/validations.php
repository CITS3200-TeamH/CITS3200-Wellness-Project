<script type="text/javascript">
	//could benefit from some use of focus in future 
	function validate_diary_form(form) {
		if(updateAction) {
			var msg = "";
			var valid = true;
			if(form.duration.value == "") {
				msg = "Please enter exercise duration.";
				valid = false;
			}else if(form.duration.value <= 0 || !isNumeric(form.duration.value)) {
				msg = "Duration must be a positive numeric value.";
				valid = false;
			}
			if(!valid) {
				$("#dialog").dialog({ hide: 'slide', close: function(event,ui) { $("#dialog").dialog("destroy"); }, 
					buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Form Incomplete', width: 400, height: 200 });
				document.getElementById("dialog").innerHTML = msg;
				document.getElementById("duration_label").innerHTML = " Duration (mins): <span style=\"color:red; font-weight:normal;\"> ( * ) </span>";			
				return false;
			}
			return true;
		}
	}
	
	function validateProfile(form) {
		var valid = true;
		var msg = "";
		var id = "";
		if(form.age.value == "" || form.age.value <= 0 || (form.age.value != "" && (!isInteger(form.age.value) || !isNumeric(form.age.value))) ) {
			valid = false;
			id = "age";
			msg = "Age must be a positive integer value.";
		}else if(!form.gender[0].checked && !form.gender[1].checked) {
			valid = false;
			id="gender";
			msg = "Please specify your gender.";
		}else if(form.athletic[0].checked && form.sport.value == "") {
			valid = false;
			msg = "Please list your chosen sport";
		}else if(form.athletic[0].checked && form.level.value == "") {
			valid = false;
			msg = "Please specify your level of competition";			
		}else if(form.athletic[1].checked && form.sport.value != "") {
			valid = false;
			msg = "You need to specify that you are athletic before listing a sport";
		}else if(form.athletic[1].checked && form.level.value != "") {
			valid = false;
			msg = "You need to specify that you are athletic before listing a competition level";
		}
		
		if(!valid) {
			$("#dialog").dialog({ hide: 'slide',position:[510,260], close: function(event,ui) { $("#dialog").dialog("destroy"); }, 
			buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Form Incomplete', width: 320, height: 160 });
			document.getElementById("dialog").innerHTML = msg;
			return false;
		}
		return true;
	}
	
	function validate_sleep_hr(form) {
		var msg = "";
		var valid = true;
		var id = "";
		if(form.resting_hr.value == "") {
			msg = "Please enter resting heart rate.";
			valid = false;
			id = "resting_hr";
		}else if(form.resting_hr.value <= 0 || !isInteger(form.resting_hr.value) ) {
			msg = "Resting heart rate must be a positive numeric value.";
			valid = false;
			id = "resting_hr";
		}else if(form.sleep_hrs.value == "") {
			msg = "Please enter number of sleep hours.";
			valid = false;
			id = "sleep_hrs";
		}else if(form.sleep_hrs.value < 0 || !isInteger(form.sleep_hrs.value)) {
			msg = "Sleep hours must be a positive numeric value.";
			valid = false;
			id = "sleep_hrs";
		}else if(form.sleep_hrs.value > 24) {
			msg = "Sleep hours can not exceed the number of hours in a day.";
			valid = false;
			id = "sleep_hrs";
		}
		
		if(!valid) {
			document.getElementById("notify").innerHTML = ''; $("#dialog").dialog({ hide: 'slide', close: function(event,ui) { $("#dialog").dialog("destroy"); document.getElementById(id).value= ''; document.getElementById(id).focus();}, 
				buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Form Incomplete', width: 400, height: 200 });
			document.getElementById("dialog").innerHTML = msg;
			return false;
		}
		return true;
	}
	
	function validate_fitness_test(form) {
		var msg = "";
		var valid = true;
		var none = false;
		var daydate = true;
		var id="";
		//formulate the js dates
		if(form.daydate.value == "") {
			daydate = false;
			valid = false;
			id = "daydate";
			msg = 'Please enter the date of the test.';
		}else {
			//field has something entered so check format
			var reg = /^[12][90][0-9][0-9]-[01]?[0-9]-[0-3]?[0-9]$/;			
			if(!form.daydate.value.match(reg)) { 
				daydate = false;
				valid = false;
				id = "daydate";				
				msg = 'Bad date format, please re-enter a valid date in the format \'yyyy-mm-dd\''; 
			}else {
				//valid rough format, so check days in detail
				//split the date into year month day here
				var dateString = form.daydate.value;
				var fields = dateString.split("-");
				//make a date object and test validity of the  fields by cross checking them. date will produce invalid date if date is incorrect.
				var myDate=new Date();
				myDate.setFullYear(fields[0],fields[1]-1,fields[2]);
				if(myDate.getFullYear() != fields[0] || myDate.getMonth()+1 != fields[1] || myDate.getDate() != fields[2] ) {
					daydate = false;
					valid = false;
					id = "daydate";
					msg = 'Invalid date, please re-enter a valid date in the format \'yyyy-mm-dd\''; 
				}else {
					//date matches format and is valid so check that it is inside the groups entry window (hidden form fields start_date and current_date)
					//formulate new date objects for the start and current dates passed in by the hidden form fields
					var startString = form.start_date.value;
					var startFields = startString.split("-");
					var currentString = form.current_date.value;
					var currentFields = currentString.split("-");
					var startDate = new Date();
					startDate.setFullYear(startFields[0],startFields[1]-1,startFields[2]);
					var currentDate = new Date();
					currentDate.setFullYear(currentFields[0],currentFields[1]-1,currentFields[2]);				
					if(myDate.getTime() > currentDate.getTime() || myDate.getTime() < startDate.getTime() ) {
						daydate = false;
						valid = false;
						id = "daydate";
						msg = "Date must be within the range of the project start and end dates";
					}
				}
			}
		}
		
		//only enter secondary checks if the date is set and valid
		if(daydate) {
		
			if(form.pushup.value != "" && (form.pushup.value < 0 || !isInteger(form.pushup.value))) {
				msg = "Number of push ups must be a positive integer value.";
				valid = false;
				id="pushup";
			}else if(form.situp.value != "" && (form.situp.value < 0 || !isInteger(form.situp.value)) ) {
				msg = "Number of sit ups must be a positive integer value.";
				valid = false;
				id="situp";
			}else if(form.chinup.value != "" && (form.chinup.value < 0 || !isInteger(form.chinup.value) )) {
				msg = "Number of chin ups must be a positive integer value.";
				valid = false;
				id="chinup";
			}else if(form.hang.value != "" && (form.hang.value < 0 || !isNumeric(form.hang.value)) ) {
				msg = "Hang duration must be a positive numeric value.";
				valid = false;
				id="hang";
			}else if(form.sitreach1.value != "" && (!isNumeric(form.sitreach1.value) || form.sitreach1.value < -30 || form.sitreach1.value > 30)) {
				msg = "Sit & reach value must be a numeric value between -30 & 30.";
				valid = false;
				id="sitreach1";
			}else if(form.sitreach2.value != "" && (!isNumeric(form.sitreach2.value) || form.sitreach2.value < -30 || form.sitreach2.value > 30)) {
				msg = "Sit & reach value must be a numeric value between -30 & 30.";
				valid = false;
				id="sitreach2";
			}else if(form.height.value != "" && (form.height.value < 0 || !isNumeric(form.height.value))) {
				msg = "Height must be a positive numeric value";
				valid = false;
				id="height";
			}else if(form.mass.value != "" && (form.mass.value < 0 || !isNumeric(form.mass.value))) {
				msg = "Mass must be a positive numeric value";
				valid = false;
				id="mass";
			}else if(form.waist.value != "" && (form.waist.value < 0 || !isNumeric(form.waist.value))) {
				msg = "Waist measurement must be a positive numeric value";
				valid = false;
				id="waist";
			}else if(form.hip.value != "" && (form.hip.value < 0 || !isNumeric(form.hip.value))) {
				msg = "Hip measurement must be a positive numeric value";
				valid = false;
				id="hip";
			}
		
			if(form.pushup.value == "" && form.chinup.value == "" && form.situp.value == "" && form.hang.value == "" && form.sitreach1.value == "" && form.sitreach2.value == "" && form.height.value == "" && form.mass.value == "" && form.waist.value == "" && form.hip.value == "") {
				msg = "At least one test field (other than date) must be completed";
				id = "pushup";
				none = true;
			} 
		}
		
		//clear all formatting/coloring of text fields
		var temp = form.elements;
		for(var i=0; i<temp.length; i++){
			if(temp[i].type == "text") {
				temp[i].style.backgroundColor = "white";
			}
		}
		
		if(!valid || none) {
			$("#dialog").dialog({ hide: 'slide', close: function(event,ui) { $("#dialog").dialog("destroy"); document.getElementById(id).value=''; document.getElementById(id).focus();}, 
				buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Form Incomplete', width: 400, height: 200 });
			document.getElementById("dialog").innerHTML = msg;
			document.getElementById(id).style.backgroundColor= "rgb(230,200,200)";
			return false;
		}
		return true;
	}
	
	function isNumeric(n) {
  		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	//does not do type comparison (eg. x === y) as the form input is string type
	function isInteger(s){
		return parseInt(s,10)==s;
	}

</script>
