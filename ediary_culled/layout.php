<?php
function check_student() {
	//redirect if not a registered subject session
	session_start();
	if(!session_is_registered(myusername) || $_SESSION['user_type'] != "student" ){
		header("location:login.php");
	}
}

function check_admin() {
	//redirect if not a registered admin session
	session_start();
	if(!session_is_registered(myusername) || $_SESSION['user_type'] != "admin"){
		header("location:login.php");
	}
}

function draw_headerout() {	
	echo 
	"<!DOCTYPE html>
	<html>
	<head>
		<Title> Exercise eDiary ".date('Y')."</title>
		<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />
	</head>
	<body>
	"//<div id=\"wrap\">

	."<div id=\"header\">
		<div id=\"header_inner\">
			<img src=\"images/uwacrest.gif\" style=\"float:left; margin-top:15px; margin-left:10px;\"/>
			<img src=\"images/sports_logo.gif\" style=\"float:right; margin-top:45px; margin-right:20px;\"/> 
		</div>
		<div id=\"links-container\">
		<div id=\"links-inner\">				
			<a href=\"login.php\"><img src=\"images/bullet-link.gif\" style=\"border:none; margin-right:5px;\"/>Sign In</a>
			<a href=\"login_admin.php\"><img src=\"images/bullet-link.gif\" style=\"border:none; margin-right:5px; margin-left:15px;\"/>Admin</a>
		</div>		
		</div>	
	</div>";
}

function draw_headerin() {	
	echo 
	"<!DOCTYPE html>
	<html>
	<head>
		<Title> Exercise eDiary ".date('Y')."</title>
		<link href=\"style.css\" rel=\"Stylesheet\" type=\"text/css\" />
		<link rel=\"Stylesheet\" href=\"jquery-ui-1.7.3.custom.css\" type=\"text/css\" /> 
		<link rel=\"Stylesheet\" href=\"ui.selectmenu.css\" type=\"text/css\" /> 
		<script type=\"text/javascript\" src=\"jquery-1.4.4.js\"></script>
		<script type=\"text/javascript\" src=\"jquery-ui-1.7.3.custom.min.js\"></script>
		<script type=\"text/javascript\" src=\"ui.selectmenu.js\"></script>   
	</head>
	<body>"
	//<div id=\"wrap\">

	."<div id=\"header\">
		<div id=\"header_inner\">
			<img src=\"images/uwacrest.gif\" style=\"float:left; margin-top:15px; margin-left:10px;\"/> 
			<img src=\"images/sports_logo.gif\" style=\"float:right; margin-top:45px; margin-right:20px;\"/>
		</div>
		<div id=\"links-container\">
		<div id=\"links-inner\">
			<span style=\"margin-left:-20px; border:solid 1px rgb(210,210,210); border-width:0px 0px 0px 0px; padding:5px 5px 5px 20px;\">Welcome, ".$_SESSION['first']." ".$_SESSION['last']."</span>						
			(<a href=\"logout.php\">logout</a>)
			<span style=\"float:right; margin-right:0px; padding:0px;\">
				<a style=\"margin-right:20px;\" href=\"http://www.science.uwa.edu.au/\"><img src=\"images/bullet-link.gif\" style=\"margin-right:5px; border:none;\"/>Faculty Home</a>
				<a href=\"http://www.sseh.uwa.edu.au/\"><img src=\"images/bullet-link.gif\" style=\" margin-right:5px; border:none;\"/>School Home</a>
			</span>
		</div>		
		</div>	
	</div>";
}

function drawHeaderAdmin() {	
	echo 
	"<!DOCTYPE html>
	<html>
	<head>
		<Title> Exercise eDiary ".date('Y')."</title>
		<link href=\"style.css\" rel=\"Stylesheet\" type=\"text/css\" />
		<link rel=\"Stylesheet\" href=\"jquery-ui-1.7.3.custom.css\" type=\"text/css\" /> 
		<link rel=\"Stylesheet\" href=\"ui.selectmenu.css\" type=\"text/css\" /> 
		<script type=\"text/javascript\" src=\"jquery-1.4.4.js\"></script>
		<script type=\"text/javascript\" src=\"jquery-ui-1.7.3.custom.min.js\"></script>
		<script type=\"text/javascript\" src=\"jquery.confirm-1.3.js\"></script>
		<script type=\"text/javascript\" src=\"jquery.alerts.js\"></script>
		<script type=\"text/javascript\" src=\"jquery.easy-confirm-dialog.js\"</script>
		<link rel=\"Stylesheet\" href=\"jquery.alerts.css\" type=\"text/css\" /> 
		<script type=\"text/javascript\" src=\"ui.selectmenu.js\"></script>     		          		
	</head>
	<body>"
	//<div id=\"wrap\">

	."<div id=\"header\">
		<div id=\"header_inner\">
			<img src=\"images/uwacrest.gif\" style=\"float:left; margin-top:15px; margin-left:10px;\"/> 
			<img src=\"images/sports_logo.gif\" style=\"float:right; margin-top:45px; margin-right:20px;\"/> 
		</div>
		<div id=\"links-container\">
		<div id=\"links-inner\">				
			Welcome, ".$_SESSION['first']." ".$_SESSION['last']." (<a href=\"logout.php\">logout</a>)
		</div>
		</div>	
	</div>";
}

function draw_navout() {
	/*echo	
	"<div id=\"inner-wrap\">*/
		echo "<div id=\"left_pane\">
			<a href=\"home.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Home</a>
			<hr style=\"height:1px; border:none; background-color:rgb(210,210,210)\">
			<a href=\"aboutpage\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">About the eDiary</a>
			<hr style=\"height:1px; border:none; background-color:rgb(210,210,210)\">
		</div>";
}

function draw_navin() {
	/*echo	
	"<div id=\"inner-wrap\">*/
		echo"<div id=\"left_pane\">
				<a href=\"home.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Home</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"about.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">About the eDiary</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"edit_profile.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Edit Profile</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"change_pw.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Change Password</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"fitness_test.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Fitness Tests</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"query_group.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Change Group</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
				<a href=\"subject_select_reports.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">View Reports</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
			</div>";
}

function drawNavAdmin() {
		echo"<div id=\"left_pane\">
				<a href=\"home_admin.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Home</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\"/>
				<a href=\"admin_change_pw.php\" style=\"background:url(images/nav-arrow.gif) no-repeat left; padding-left:15px;\">Change Password</a>
				<hr style=\"height:1px; border:none; background-color:rgb(190,190,190)\">
			</div>";
}

function draw_footer() {
	/*echo
		"</div>*/
		echo '<div id="footer">
		<div id="footer_inner">
			<div class="footer_col">
				<p style="width:135px; margin:0px; color:#464646; font:100%/1.1 Georgia,serif;">The University of Western Australia</p>
				<p style="margin:2px 0px 0px 0px; font-size:8pt; color:#5C5C5C; line-height:1.75;">
				> <a class="footer_small_link" href="http://www.uwa.edu.au/">UWA Home</a><br/>
				> <a class="footer_small_link" href="http://studyat.uwa.edu.au/">Future UWA Students</a><br/>
				> <a class="footer_small_link" href="http://www.uwa.edu.au/current">Current UWA Students</a><br/>
				> <a class="footer_small_link" href="http://uwa.edu.au/staff">UWA Staff</a><br/>
				> <a class="footer_small_link" href="http://uwa.edu.au/business">Business and Industry</a><br/>
				> <a class="footer_small_link" href="http://uwa.edu.au/alumni">Alumni and Friends</a><br/>
				> <a class="footer_small_link" href="http://uwa.edu.au/media">Media</a><br/>
				</p>
			</div>
			<div class="footer_col" style="margin-left:20px;">
				<p style="width:135px; font-size:8pt; font-weight:bold; margin:0px 0px 0px 0px; color:#464646; font-family:Arial,Helvetica,sans-serif;">
				School of Sport Science, Exercise and Health
				</p>
				<p style="margin:8px 0px 0px 0px; font-size:8pt; color:#5C5C5C; line-height:1.75;">
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/courses">Courses</a><br/>
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/research">Research</a><br/>
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/alumni">Alumni</a><br/>
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/community">Community</a><br/>
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/students">Current Students</a><br/>

				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/staff">Staff</a><br/>
				> <a class="footer_small_link" href="http://www.sseh.uwa.edu.au/contact">Contact Us</a><br/>
				</p>
			</div>
			<div class="footer_col" style="margin-left:20px;">
				<p style="width:135px; font-size:8pt; margin:0px 0px 0px 0px; color:#464646; font-family:Arial,Helvetica,sans-serif;">
				<b>University Information</b><br/>
				CRICOS Code: 00126G
				</p>
				<p style="margin:8px 0px 0px 0px; font-size:8pt; color:#5C5C5C; line-height:1.75;">
				> <a class="footer_small_link" href="http://www.uwa.edu.au/accessibility">Accessibility</a><br/>
				> <a class="footer_small_link" href="http://www.uwa.edu.au/campus_map">Campus Map</a><br/>
				> <a class="footer_small_link" href="http://www.uwa.edu.au/contact">Contact UWA</a><br/>
				> <a class="footer_small_link" href="http://www.uwa.edu.au/indigenous_commitment">Indigenous Commitment</a><br/>
				> <a class="footer_small_link" href="http://www.uwa.edu.au/terms_of_use">Privacy and terms of use</a><br/>				
				</p>
			</div>
			<div class="footer_col" style="margin-left:0px; border:none;">
				<p style="width:135px; font-size:8pt; margin:0px; color:#464646; font-family:Arial,Helvetica,sans-serif;">
				<img src="images/footer-infoarrow.gif" style="float:left;"/>
				<b>This Page</b>
				</p>
				<p style="margin:2px 0px 0px 20px; font-size:8pt; color:#464646; line-height:1.75;">
				Website Feedback:<br/><br/>
				Nat Benjanuvatra:<br/>
				
				<a class="footer_small_link" href="mailto:nat.benjanuvatra@uwa.edu.au">nat.benjanuvatra@uwa.edu.au</a>			
				</p>
			</div>
		</div>
		</div>'
	//</div>
	.'</body>
	</html>';
}

function escape_data($data) {
	global $conn;
	if(ini_get('magic_quotes_gpc')) {
		$data = stripslashes($data);
	}
	return mysql_real_escape_string (trim($data), $conn);
}

function check_iso_date($date)
{
    if(!preg_match('/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/', $date, $matches))
    {
        return false;
    }
    return checkdate($matches[2], $matches[3], $matches[1]);
}

//used to validate integer from form input
function is_int_val($data) {
	if (is_int($data) == true) {
		return true;
	}elseif (is_string($data) == true && is_numeric($data) == true) {
		return (strpos($data, '.') == false);
	}
	return false;
}

?>
