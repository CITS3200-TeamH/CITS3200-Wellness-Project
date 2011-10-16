var dataBase;

//##############################################################
//
// Database creation and insertion functions
//
//##############################################################

function openDB(){
	dataBase = openDatabase('eDiary','2.0','Exercise eDiary',2*1024*1024);
	dataBase.transaction(function (tx) {
		tx.executeSql('Create Table If Not Exists Student(id int,first,last,password,age,gender,height,mass,athletic,sport,level,loggedOn bool,time date,uploaded bool)');
		tx.executeSql('Create Table If Not Exists Fitness_Test(id integer primary key,student_id int,daydate date,pushup int,situp int,chinup int,hang double,sitreach1 double,sitreach2 double,height double,mass double,waist double,hip double,uploaded bool)');
		tx.executeSql('Create Table If Not Exists training_Records2(student_id int,class,daydate date,heart_rate int,sleep int,health int,ratings text, uploaded bool)');
		tx.executeSql('Create Table If Not Exists Training_Records1(student_id int, daydate date, copmcode, duration, start, end, time_of_day, uploaded bool)');
		tx.executeSql('Create Table If Not Exists CompCodes(compcode,mets,type,cardio bool,muscle bool,flex bool,body bool,heading,description,intensity)');
		tx.executeSql('Create Table If Not Exists rating_item(id int, description, summary)');
		tx.executeSql('Create Table If Not Exists rating_item_map(groupname,id)');
		tx.executeSql('Create Table If Not Exists class(name,start date, finish date, window)');
		tx.executeSql('Create Table If Not Exists classmap(student_id,class_name)');
	},function (t, error) {alert('Error: '+error+' (Code '+error+')');;});}





//##############################################################
//
// Functions for other pages
//
//##############################################################

var ratingItems;

function loadRatingsPage(){
	if(dataBase==null){
		openDB();
	}
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	var title = document.getElementById('topbar');
	title.innerHTML = '<div id="title">Rating Items</div><div id="leftnav"><a href="Home.html"/><img src="images/home.png"/></a><a href="Day.html?date='+d.getTime()+'">'+d.toDateString()+'</a></div>';
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				tx.executeSql('Select name From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
					if(r.rows.length==1){
						var classname = r.rows.item(0)['name'];
						tx.executeSql('Select * From rating_item Where (Select count(id) From rating_item_map Where groupname=? And rating_item.id=rating_item_map.id)>0 Order By id ASC',[classname],function (t, r) {
							if(r.rows.length>0){
								ratingItems = new Array();
								var page = document.getElementById("contents");
								for(var i=0;i<r.rows.length;i++){
									var rr = r.rows.item(i);
									ratingItems[i] = rr['description'];
									page.innerHTML += '<span class="graytitle">'+rr['description']+'</span><ul class="pageitem"><li class="select"><select id='+rr['description']+'><option value="5">5 - Excellent</option><option value="4">4 - Good</option><option value="3">3 - Ok</option><option value="2">2 - Poor</option><option value="1">1 - Awful</option></select><span class="arrow"></ span></li></ul>';
								}
								page.innerHTML += '<ul class="pageitem"><li class="button"><input name="Submit" type="button" value="Submit" onClick="saveRatingItems()"/></li></ul>';
								tx.executeSql('Select ratings From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
									if(r.rows.length==1){
										var data = r.rows.item(0)['ratings'];
										for(var i=0;i<ratingItems.length;i++){
											document.getElementById(ratingItems[i]).value = data.substring(0,1);
											data = data.substring(2);
										}
									}
								},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
							} else {
								//There are no reating items for this class
								//I dont think any error message is needed
							}
						},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
					} else {
						alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
					}
				},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function saveRatingItems(){
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				if(ratingItems!=null){
					var studentid = r.rows.item(0)['id'];
					tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
					tx.executeSql('Select name From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
						if(r.rows.length==1){
							var data = "";
							for(var i=0;i<ratingItems.length;i++){
								data += ','+document.getElementById(ratingItems[i]).value;
							}
							data = data.substring(1);
							var classname = r.rows.item(0)['name'];
							tx.executeSql('Select * From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
								if(r.rows.length==1){
									tx.executeSql('Update training_records2 Set ratings=? Where daydate=? And class=? And student_id=?',[data,d.getTime(),classname,studentid],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								} else {
									tx.executeSql('Insert Into training_records2(ratings,daydate,class,student_id) values(?,?,?,?)',[data,d.getTime(),classname,studentid],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								}
							},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						} else {
							alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
						}
					},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			
				} else {
					alert('Nothing to save');
					document.location = "Day.html?date="+d.getTime();
				}
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadWellnessPage(){
	if(dataBase==null){
		openDB();
	}
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	var title = document.getElementById('topbar');
	title.innerHTML = '<div id="title">Wellness Data</div><div id="leftnav"><a href="Home.html"/><img src="images/home.png"/></a><a href="Day.html?date='+d.getTime()+'">'+d.toDateString()+'</a></div>';
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				tx.executeSql('Select name From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
					if(r.rows.length==1){
						var classname = r.rows.item(0)['name'];
						tx.executeSql('Select heart_rate, sleep, health From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t,r) {
							var heartrate = "";
							var sleep = "";
							var health = "5";
							if(r.rows.length==1){
								var rr = r.rows.item(0);
								if(rr['heart_rate']!=null&&rr['heart_rate'].toString().length>0){
									heartrate = rr['heart_rate'];
								}
								if(rr['sleep']!=null&&rr['sleep'].toString().length>0){
									sleep = rr['sleep'];
								}
								if(rr['health']!=null&&rr['health'].toString().length>0){
									health = rr['health'];
								}
							}
							var page = document.getElementById("contents");
							page.innerHTML += '<span class="graytitle">Resting Heart Rate</span><ul class="pageitem"><li class="bigfield"><input id="heart_rate" type="text" value="'+heartrate+'"/></li></ul>';
							page.innerHTML += '<span class="graytitle">Sleep Hours</span><ul class="pageitem"><li class="bigfield"><input id="sleep" type="text" value="'+sleep+'"/></li></ul>';
							page.innerHTML += '<span class="graytitle">Health</span><ul class="pageitem"><li class="select"><select id="health"><option value="5">5 - Excellent</option><option value="4">4 - Good</option><option value="3">3 - Ok</option><option value="2">2 - Poor</option><option value="1">1 - Awful</option></select><span class="arrow"></ span></li></ul>';
							page.innerHTML += '<ul class="pageitem"><li class="button"><input name="Submit" type="button" value="Submit" onClick="saveWellnessData()"/></li></ul>';
							document.getElementById('health').value = health;
						},function (t, error) {alert('Obtaining Wellness Data Error: '+error.message+' (Code '+error.code+')');;});
					} else {
						alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
					}
				},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function saveWellnessData(){
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
					var studentid = r.rows.item(0)['id'];
					tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
					tx.executeSql('Select name From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
						if(r.rows.length==1){
							var heartrate = document.getElementById('heart_rate').value;
							var sleep = document.getElementById('sleep').value;
							var health = document.getElementById('health').value;
							var classname = r.rows.item(0)['name'];
							tx.executeSql('Select * From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
								if(r.rows.length==1){
									tx.executeSql('Update training_records2 Set heart_rate=?, sleep=?, health=? Where daydate=? And class=? And student_id=?',[heartrate,sleep,health,d.getTime(),classname,studentid],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								} else {
									tx.executeSql('Insert Into training_records2(heart_rate,sleep,health,daydate,class,student_id) values(?,?,?,?,?,?)',[heartrate,sleep,health,d.getTime(),classname,studentid],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								}
							},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						} else {
							alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
						}
					},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadDay(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				tx.executeSql('Select name From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
					if(r.rows.length==1){
						var classname = r.rows.item(0)['name'];
						var list = document.getElementById("List");
						var dateValue = document.location.toString();
						dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
						var d = new Date();
						d.setTime(dateValue);
						document.getElementById("title").innerHTML = d.toDateString();
						var ec = Math.floor(Math.random()*2);
						list.innerHTML += "";
						if(ec==0){
							list.innerHTML += "<li class='menu'><a href='Exercise.html?date="+dateValue+"'><img src='images/ExerciseCompleted.jpg'/><span class='name'>Exercise Data Completed";
						} else {
							list.innerHTML += "<li class='menu'><a href='Exercise.html?date="+dateValue+"'><img src='images/ExerciseNotCompleted.jpg'/><span class='name'>Exercise Data Incomplete";
						}
						list.innerHTML += "</span><span class='arrow'/></a></li>";
						tx.executeSql('Select * From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
							if(r.rows.length==1){
								if(r.rows.item(0)['ratings']!=null&&r.rows.item(0)['ratings'].toString().length>0){
									list.innerHTML += "<li class='menu'><a href='Ratings.html?date="+dateValue+"'><img src='images/RatingCompleted.png'/><span class='name'>Rating Items Completed";
								} else {
									list.innerHTML += "<li class='menu'><a href='Ratings.html?date="+dateValue+"'><img src='images/RatingNotCompleted.png'/><span class='name'>Rating Items Incomplete";
								}
								list.innerHTML += "</span><span class='arrow'/></a></li>";
								if(r.rows.item(0)['health']!=null&&r.rows.item(0)['health'].toString()>0){
									list.innerHTML += "<li class='menu'><a href='Wellness.html?date="+dateValue+"'><img src='images/WellnessCompleted.jpg'/><span class='name'>Wellness Data Completed";
								} else {
									list.innerHTML += "<li class='menu'><a href='Wellness.html?date="+dateValue+"'><img src='images/WellnessNotCompleted.jpg'/><span class='name'>Wellness Data Incomplete";
								}
								list.innerHTML += "</span><span class='arrow'/></a></li>";
							} else {
								list.innerHTML += "<li class='menu'><a href='Ratings.html?date="+dateValue+"'><img src='images/RatingNotCompleted.png'/><span class='name'>Rating Items Incomplete";
								list.innerHTML += "</span><span class='arrow'/></a></li>";
								list.innerHTML += "<li class='menu'><a href='Wellness.html?date="+dateValue+"'><img src='images/WellnessNotCompleted.jpg'/><span class='name'>Wellness Data Incomplete";
								list.innerHTML += "</span><span class='arrow'/></a></li>";
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
					} else {
						alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
					}
				},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadCalendar(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				tx.executeSql('Select window From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
					if(r.rows.length==1){
						var list = document.getElementById("List");
						var d = new Date();
						d.setHours(0);
						d.setMinutes(0);
						d.setSeconds(0);
						d.setMilliseconds(0);
						for(var i=0;i<r.rows.item(0)['window'];i++){
							list.innerHTML += "<li class='menu'><a href='Day.html?date="+d.getTime()+"'><span class='name'>"+d.toDateString()+"</span><span class='arrow'><a></li>";
							d.setDate(d.getDate()-1);
						}
					} else {
						alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
					}
				},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadEditProfile(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select * From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var rr = r.rows.item(0);
				var studentid = rr['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				document.getElementById('age').value = rr['age'];
				if(rr['gender'].toString().length=1){
					document.getElementById('gender'+rr['gender']).checked = true;
				}
				if(rr['athletic']=="true"){
					document.getElementById('athleteT').checked = true;
				} else {
					document.getElementById('athleteF').checked = true;
				}
				document.getElementById('sport').value = rr['sport'];
				document.getElementById('level').value = rr['level'];
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function saveProfile(){
if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select * From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var rr = r.rows.item(0);
				var studentid = rr['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				var age = document.getElementById('age').value;
				var gender;
				if(document.getElementById('genderF').checked){
					gender = 'F';
				} else if(document.getElementById('genderM').checked){
					gender = 'M';
				}
				var athlete;
				if(document.getElementById('athleteT').checked){
					athlete = true;
				} else if(document.getElementById('athleteF').checked){
					athlete = false;
				}
				//alert(athelete);
				var sport = document.getElementById('sport').value;
				var level = document.getElementById('level').value;
				tx.executeSql('Update Student Set age=?,gender=?,athletic=?,sport=?,level=? Where id=?',[age,gender,athlete,sport,level,studentid], function (t, r) {
					document.location = 'Home.html';
				},function (t, error) {alert('Setting Student Profile Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});	

}

function listFitnessTests(){
	if(dataBase==null){
		openDB();
	}
	var data = new Array();
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				tx.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
				tx.executeSql('Select id,daydate From Fitness_Test Where student_id = ? Order By daydate DESC',[r.rows.item(0)['id']],function (t, r) {
					var list = document.getElementById("List");
					for(var i=0;i<r.rows.length;i++){
						var rr = r.rows.item(i);
						var d = new Date();
						d.setTime(rr['daydate']);
						list.innerHTML += "<li class='menu'><a href='OldFitness.html?test="+rr['id']+"'><span class='name'>"+d.toDateString()+"</span><span class='arrow'><a></li>"
					}
				},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadTest(){
	if(dataBase==null){
		openDB();
	}
	var testid= document.location.toString();
	testid = testid.substring(testid.indexOf("test=")+5);
	dataBase.transaction(function (tx) {
		tx.executeSql('Select * From Fitness_Test Where id=?',[testid],function (t, r) {
			var content = document.getElementById("content");
			var rr = r.rows.item(0);
			var currentDate = new Date();
			currentDate.setTime(rr['daydate']);
			content.innerHTML = "Date: "+currentDate.toDateString()+"<br>";
			content.innerHTML += "Push Up's in 30 seconds: "+rr['pushup']+"<br>";
			content.innerHTML += "Sit Up Candance Test: "+rr['situp']+"<br>";
			content.innerHTML += "Number of Chin Up's: "+rr['chinup']+"<br>";
			content.innerHTML += "Hang Duration: "+rr['hang']+" seconds<br>";
			content.innerHTML += "Sit & Reach Trial 1: "+rr['sitreach1']+" cm<br>";
			content.innerHTML += "Sit & Reach Trial 2: "+rr['sitreach2']+" cm<br>";
			content.innerHTML += "Height: "+rr['height']+" cm<br>";
			content.innerHTML += "Mass: "+rr['mass']+" kg<br>";
			content.innerHTML += "Waist Measurement: "+rr['waist']+" cm<br>";
			content.innerHTML += "Hip Measurement: "+rr['hip']+" cm<br>";
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function createNewFitnessTest(){
	if(dataBase==null){
		openDB();
	}
	var daydateString = document.getElementById("daydate").value;
	var dateValid = true;
	var day;
	var month;
	var year;
	if(daydateString.indexOf("/")!=-1){
		day = daydateString.substr(0,daydateString.indexOf("/"));
		daydateString = daydateString.substr(daydateString.indexOf("/")+1);
	} else {
		dateValid = false;
	}
	if(daydateString.indexOf("/")!=-1){
		month = daydateString.substr(0,daydateString.indexOf("/"));
		daydateString = daydateString.substr(daydateString.indexOf("/")+1);
	} else {
		dateValid = false;
	}
	if(daydateString.length>0){
		year = daydateString;
	} else {
		dateValid = false;
	}
	if(dateValid){
		var daydate = new Date();
		daydate.setTime(0);
		daydate.setFullYear(year);
		daydate.setMonth(month-1);
		daydate.setDate(day);
		var pushup = document.getElementById("pushup").value;
		var situp = document.getElementById("situp").value;
		var chinup = document.getElementById("chinup").value;
		var hang = document.getElementById("hang").value;
		var sitreach1 = document.getElementById("sitreach1").value;
		var sitreach2 = document.getElementById("sitreach2").value;
		var height = document.getElementById("height").value;
		var mass = document.getElementById("mass").value;
		var waist = document.getElementById("waist").value;
		var hip = document.getElementById("hip").value;
		var currentTime = new Date();
		dataBase.transaction(function (t) {
			t.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
				if(r.rows.length==1){
					var studentid = r.rows.item(0)['id'];
					t.executeSql('Update Student Set time=? Where id=?', [currentTime.getTime()+900000,studentid], function (t, r) {},function (t, error) {alert('Obtaining Rating Items Error: '+error.message+' (Code '+error.code+')');;});
					t.executeSql('Insert Into Fitness_Test(student_id,daydate,pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,waist,hip,uploaded) values(?,?,?,?,?,?,?,?,?,?,?,?,?)',[studentid,daydate.getTime(),pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,waist,hip,false],
						function (t, r) {
							document.location = "Fitness.html";
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
				} else {
					document.location = "LoginTimedOut.html";
				}
			},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
		});
	}
}

    function callComplete(result){
    
        document.getElementById("c").innerHTML = result.value;
    }

function attemptLogon(){
	if(navigator.onLine){
		var form = document.getElementById('form');
		alert('some alert.. to be removed.');
		//form.action = "http://www.foota.org/CITS3200-Wellness-Project/ediary_culled/api_auth.php";
		//form.submit();
		//service.useService(http://www.foota.org/CITS3200-Wellness-Project/ediary_culled/api_auth.php,
		//	"");
		//service.GetDateTimeService.callService('<?xml version="1.0"?><username>1234567</username><password>lolcano</password>');
		//Send logon details to server and wait for response
		
		
		//alert(1);
var xmlhttp;


if (window.XMLHttpRequest)

  {
	//alert(7);
  xmlHttp=new XMLHttpRequest();

  }

else // for older IE 5/6

  {

  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");

  }
  //alert(4);

//var url="xml.xml";
//var url="http://www.foota.org/CITS3200-Wellness-Project/ediary_culled/api_auth.php?username=1234567&password=lolcano";
var url="api_auth.php?username=1234567&password=lolcano";

//alert(5);

xmlHttp.open("GET",url,false);

//alert(6);

xmlHttp.send("");
//alert(3);

alert(xmlHttp.responseText);

	} else {
		if(dataBase==null){
			openDB();
		}
		var stnumber = document.getElementById("username").value;
		var pword = document.getElementById("password").value;
		dataBase.transaction(function (t) {
			t.executeSql('Select Count(*) As c From Student', [], function (t, r) {
				if(r.rows.item(0).c>0){
					t.executeSql('Select Count(*) As d From Student Where id=? And password=?', [stnumber,pword], function (t, r) {
						if(r.rows.item(0).d>0){
							t.executeSql('Update Student Set loggedOn = ?',[false], function (t, r) {},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
							var currentTime = new Date();
							t.executeSql('Update Student Set loggedOn = ?, time=? Where id=?',[true,currentTime.getTime()+900000,stnumber], function (t, r) {
								document.location = "Home.html";
							},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						} else {
							document.location = "InvalidLogon.html";//Need to create page
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
				} else {
					document.location = "NoLogons.html";//Need to create page
				}
			},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			
		});
	}
}






//##############################################################
//
// Admin Database Testing functionality. Delete on release
//
//##############################################################

function isworking(){
	if(dataBase==null){
		openDB();
	}
	var content = document.getElementById("content");
	content.innerHTML = "Working";
}


function initiateDatabaseForTesting(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into Student(id,first,last,password,age,gender,height,mass,athletic,sport) values(?,?,?,?,?,?,?,?,?,?)',[12345678,"Bob","Down","temppass",18,"M",162,80,false,"frog hopping"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into class(name,start,finish,window) values(?,?,?,?)',["Class1",0,9999999999999,5],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into classmap(student_id,class_name) values(?,?)',[12345678,"Class1"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item(id,description,summary) values(?,?,?)',[1,"Tired","How tired are you?"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item(id,description,summary) values(?,?,?)',[2,"Hungry","How hungry are you?"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item(id,description,summary) values(?,?,?)',[3,"Sleepy","How sleepy are you?"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item_map(id,groupname) values(?,?)',[3,"Class1"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item_map(id,groupname) values(?,?)',[2,"Class1"],function (t, r) {},function (t, r) {alert(r);});
		tx.executeSql('Insert Into rating_item_map(id,groupname) values(?,?)',[1,"Class1"],function (t, r) {},function (t, r) {alert(r);});
	});
	
}

function viewFitnesTestsTable(){
	if(dataBase==null){
		openDB();
	}
	var data = new Array();
	dataBase.transaction(function (tx) {
		tx.executeSql('Select * From Fitness_Test',[],
			function (t, r) {
				for(var i=0;i<r.rows.length;i++){
					var rr = r.rows.item(i);
					data[i] = new Array();
					data[i][0] = rr['student_id'];
					data[i][1] = rr['daydate'];
					data[i][2] = rr['pushup'];
					data[i][3] = rr['situp'];
					data[i][4] = rr['chinup'];
					data[i][5] = rr['hang'];
					data[i][6] = rr['sitreach1'];
					data[i][7] = rr['sitreach2'];
					data[i][8] = rr['height'];
					data[i][9] = rr['mass'];
					data[i][10] = rr['waist'];
					data[i][11] = rr['hip'];
				}
				var content = document.getElementById("content");
				content.innerHTML = "Table: Fitness_Test<br>";
				content.innerHTML += "<br>";
				content.innerHTML += "<table>";
				for(var i=0; i<data.length; i++){
					content.innerHTML += "<tr>";
					for(var j=0; j<data[i].length; j++){
						content.innerHTML += "<td>"+data[i][j]+"-"+"<td>";
					}
					content.innerHTML += "</tr>";
				}
				content.innerHTML += "</table>";
			},function (t, r) {alert(r);}
		);
	});
}

function sendTests(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Select * From Fitness_Test Where uploaded=?',[false],function (t, r) {
			var content = document.getElementById("content");
			var array = new Array();
			var jjj = {"database": {"Fitness_Test":[]}};
			jjj['database']['Fitness_Test'][0] = {"pushup":"1"}
			for(var i=0;i<r.rows.length;i++){
				var rr = r.rows.item(i);
				jjj['database']['Fitness_Test'][i] = {"daydate":rr['daydate'],"pushup":rr['pushup'],"situp":rr['situp'],"chinup":rr['chinup'],"hang":rr['hang'],"sitreach1":rr['sitreach1'],"sitreach2":rr['sitreach2'],"height":rr['height'],"mass":rr['mass'],"waist":rr['waist'],"hip":rr['hip']};
			}
			content.innerHTML += "-"+jjj['database']['Fitness_Test'][0]['daydate']+"-";
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});
}