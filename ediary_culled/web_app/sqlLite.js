var dataBase;

//##############################################################
//
// Database creation and insertion functions
//
//##############################################################

function openDB(){
	dataBase = openDatabase('eDiary','2.0','Exercise eDiary',2*1024*1024);
	dataBase.transaction(function (tx) {
		tx.executeSql('Create Table If Not Exists Student(id int,first,last,password,age,gender,height,mass,athletic,sport,level,loggedOn bool,time date,uploaded bool,token)');
		tx.executeSql('Create Table If Not Exists Fitness_Test(id integer primary key,student_id int,group_id,daydate date,pushup int,situp int,chinup int,hang double,sitreach1 double,sitreach2 double,height double,mass double,waist double,hip double,uploaded bool)');
		tx.executeSql('Create Table If Not Exists training_Records2(student_id int,class,daydate date,heart_rate int,sleep int,health int,ratings text, uploaded bool)');
		tx.executeSql('Create Table If Not Exists Training_Records1(student_id int, daydate date, compcode, duration,class, start, end, time_of_day, uploaded bool)');
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
	title.innerHTML = '<div id="title">Rating Items</div><div id="leftnav" onclick="document.location='+"'"+'Day.html?date='+d.getTime()+"'"+'"><a>'+d.toDateString()+'</a></div>';
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
									page.innerHTML += '<span class="graytitle">'+rr['description']+'</span><ul class="pageitem"><li class="select"><select id="'+rr['description']+'"><option value="5">5 - Excellent</option><option value="4">4 - Good</option><option value="3">3 - Ok</option><option value="2">2 - Poor</option><option value="1">1 - Awful</option></select><span class="arrow"></ span></li></ul>';
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
								//alert(ratingItems[i]);
								data += ','+document.getElementById(ratingItems[i]).value;
							}
							data = data.substring(1);
							var classname = r.rows.item(0)['name'];
							tx.executeSql('Select * From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
								if(r.rows.length==1){
									tx.executeSql('Update training_records2 Set ratings=?, uploaded=? Where daydate=? And class=? And student_id=?',[data,false,d.getTime(),classname,studentid,],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								} else {
									tx.executeSql('Insert Into training_records2(ratings,daydate,class,student_id,uploaded) values(?,?,?,?,?)',[data,d.getTime(),classname,studentid,false],function (t, r) {
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

function loadOtherActivities(){
if(dataBase==null){
		openDB();
	}
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
							var classname = r.rows.item(0)['name'];
							tx.executeSql('Select * From training_records1 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
								if(r.rows.length>0){
									var content = document.getElementById("list");
									for(var i=0;i<r.rows.length;i++){
										var timeofday = r.rows.item(i)["time_of_day"];
										var compcode = r.rows.item(i)["compcode"];
										tx.executeSql('Select * From compcodes Where compcode=?',[compcode],function (t, r) {
											if(r.rows.length==1){
												var rr = r.rows.item(0);
												content.innerHTML += "<li class='textbox'><p><b>"+rr["heading"]+"</b>"+rr["description"]+", "+timeofday+"</p>";
											}
										},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});	
									}
								} else {
									
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

function loadFitnessCategories(){
	if(dataBase==null){
		openDB();
	}
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	var title = document.getElementById('topbar');
	title.innerHTML = '<div id="title">Exercise Data</div><div id="leftnav" onclick="document.location='+"'"+'Day.html?date='+d.getTime()+"'"+'"><a>'+d.toDateString()+'</a></div>';
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var fitnesscategory = document.getElementById("fitnesscategory");
				fitnesscategory.innerHTML = '<option value="All types">All Types</option>';
				tx.executeSql('Select DISTINCT type From compcodes',[],function (t, r) {
					for(var i=0;i<r.rows.length;i++){
						fitnesscategory.innerHTML += '<option value="'+r.rows.item(i)['type']+'">'+r.rows.item(i)['type']+'</option>';
					}
					loadActivities();
				},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
	loadEndTimes();
	loadOtherActivities();
}

function loadActivities(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var fitnesscategory = document.getElementById("fitnesscategory").value;
				var activity = document.getElementById("activity");
				activity.innerHTML = '';
				if(fitnesscategory=="All types"){
					tx.executeSql('Select DISTINCT heading From compcodes',[],function (t, r) {
						for(var i=0;i<r.rows.length;i++){
							activity.innerHTML += '<option value="'+r.rows.item(i)['heading']+'">'+r.rows.item(i)['heading']+'</option>';
						}
						loadDescriptions();
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
				} else {
					tx.executeSql('Select DISTINCT heading From compcodes Where type=?',[fitnesscategory],function (t, r) {
						for(var i=0;i<r.rows.length;i++){
							activity.innerHTML += '<option value="'+r.rows.item(i)['heading']+'">'+r.rows.item(i)['heading']+'</option>';
						}
						loadDescriptions();
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
				}
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadDescriptions(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var fitnesscategory = document.getElementById("fitnesscategory").value;
				var activity = document.getElementById("activity").value;
				var description = document.getElementById("description");
				description.innerHTML = '';
					tx.executeSql('Select compcode,description,mets From compcodes Where heading=?',[activity],function (t, r) {
						for(var i=0;i<r.rows.length;i++){
							description.innerHTML += '<option value="'+r.rows.item(i)['compcode']+'">'+r.rows.item(i)['description']+' - '+r.rows.item(i)['mets']+' METs</option>';
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function loadEndTimes(){
	var starttime = document.getElementById("starttime").value;
	var endtime = document.getElementById("endtime");
	endtime.innerHTML = "";
	for(var i=0;i<24;i++){
		for(var j=0;j<60;j=j+15){
			var ii = ""+i;
			if(ii.length==1) ii = "0"+ii;
			var jj = ""+j;
			if(jj.length==1) jj = "0"+jj;
			ii = ii+":"+jj;
			if(starttime<ii){
				endtime.innerHTML += '<option value="'+ii+'">'+ii+'</option>';
			}
		}
	}
	endtime.innerHTML += '<option value="23:59">23:59</option>';
}

function saveExerciseLog(){
	if(dataBase==null){
		openDB();
	}
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
							var compcode = document.getElementById('description').value;
							var start = document.getElementById('starttime').value;
							var end = document.getElementById('endtime').value;
							//var comments = document.getElementById('comments').value;
							var classname = r.rows.item(0)['name'];
							var timeofday = "Night";
							if(start>="21:00"){
								timeofday = "Night";
							} else if(start>="18:00"){
								timeofday = "Evening";
							} else if(start>="16:00"){
								timeofday = "Late Afternoon";
							} else if(start>="14:00"){
								timeofday = "Early Afternoon";
							} else if(start>="11:30"){
								timeofday = "Midday";
							} else if(start>="09:00"){
								timeofday = "Mid Morning";
							} else if(start>="06:00"){
								timeofday = "Morning";
							} else if(start>="04:00"){
								timeofday = "Early Morning";
							}
							tx.executeSql('Select * From training_records1 Where daydate=? And class=? And student_id=? And compcode=? And time_of_day=?',[d.getTime(),classname,studentid,compcode,timeofday],function (t, r) {
								if(r.rows.length==1){
								document.location = "Day.html?date="+d.getTime();
								} else {
									tx.executeSql('Insert Into training_records1(daydate,class,student_id,start,end,time_of_day,compcode,uploaded) values(?,?,?,?,?,?,?,?)',[d.getTime(),classname,studentid,start,end,timeofday,compcode,false],function (t, r) {
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

function loadWellnessPage(){
	if(dataBase==null){
		openDB();
	}
	var dateValue = document.location.toString();
	dateValue = dateValue.substring(dateValue.indexOf("date=")+5);
	var d = new Date();
	d.setTime(dateValue);
	var title = document.getElementById('topbar');
	title.innerHTML = '<div id="title">Wellness Data</div><div id="leftnav" onclick="document.location='+"'"+'Day.html?date='+d.getTime()+"'"+'"><a>'+d.toDateString()+'</a></div>';
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
	if(dataBase==null){
		openDB();
	}
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
									tx.executeSql('Update training_records2 Set heart_rate=?, sleep=?, health=?, uploaded=? Where daydate=? And class=? And student_id=?',[heartrate,sleep,health,false,d.getTime(),classname,studentid],function (t, r) {
										document.location = "Day.html?date="+d.getTime();
									},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
								} else {
									tx.executeSql('Insert Into training_records2(heart_rate,sleep,health,daydate,class,student_id,uploaded) values(?,?,?,?,?,?,?)',[heartrate,sleep,health,d.getTime(),classname,studentid,false],function (t, r) {
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
						tx.executeSql('Select * From training_records1 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
							if(r.rows.length>0){
								list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Exercise.html?date="+dateValue+'"'+"'><a><img src='images/ExerciseCompleted.jpg'/><span class='name'>Exercise Data Completed";
							} else {
								
							list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Exercise.html?date="+dateValue+'"'+"'><a><img src='images/ExerciseNotCompleted.jpg'/><span class='name'>Exercise Data Incomplete";
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						list.innerHTML += "</span><span class='arrow'/></a></li>";
						tx.executeSql('Select * From training_records2 Where daydate=? And class=? And student_id=?',[d.getTime(),classname,studentid],function (t, r) {
							if(r.rows.length==1){
								if(r.rows.item(0)['ratings']!=null&&r.rows.item(0)['ratings'].toString().length>0){
									list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Ratings.html?date="+dateValue+'"'+"'><a><img src='images/RatingCompleted.png'/><span class='name'>Rating Items Completed";
								} else {
									list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Ratings.html?date="+dateValue+'"'+"'><a><img src='images/RatingNotCompleted.png'/><span class='name'>Rating Items Incomplete";
								}
								list.innerHTML += "</span><span class='arrow'/></a></li>";
								if(r.rows.item(0)['health']!=null&&r.rows.item(0)['health'].toString()>0){
									list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Wellness.html?date="+dateValue+'"'+"'><a><img src='images/WellnessCompleted.jpg'/><span class='name'>Wellness Data Completed";
								} else {
									list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Wellness.html?date="+dateValue+'"'+"'><a><img src='images/WellnessNotCompleted.jpg'/><span class='name'>Wellness Data Incomplete";
								}
								list.innerHTML += "</span><span class='arrow'/></a></li>";
							} else {
								list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Ratings.html?date="+dateValue+'"'+"'><a><img src='images/RatingNotCompleted.png'/><span class='name'>Rating Items Incomplete";
								list.innerHTML += "</span><span class='arrow'/></a></li>";
								list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Wellness.html?date="+dateValue+'"'+"'><a><img src='images/WellnessNotCompleted.jpg'/><span class='name'>Wellness Data Incomplete";
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
							list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"Day.html?date="+d.getTime()+'"'+"'><a><span class='name'>"+d.toDateString()+"</span><span class='arrow'><a></li>";
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
				tx.executeSql('Update Student Set age=?,gender=?,athletic=?,sport=?,level=?,uploaded=? Where id=?',[age,gender,athlete,sport,level,false,studentid], function (t, r) {
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
						list.innerHTML += "<li class='menu' onclick='document.location="+'"'+"OldFitness.html?test="+rr['id']+'"'+"'><a><span class='name'>"+d.toDateString()+"</span><span class='arrow'><a></li>"
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
			var content = document.getElementById("list");
			var rr = r.rows.item(0);
			var currentDate = new Date();
			currentDate.setTime(rr['daydate']);
			content.innerHTML = "<span class='header'>Date:</span><p>"+currentDate.toDateString()+"</p>";
			content.innerHTML += "<span class='header'>Push Up's in 30 seconds:</span><p>"+rr['pushup']+"</p>";
			content.innerHTML += "<span class='header'>Sit Up Candance Test:</span><p>"+rr['situp']+"</p>";
			content.innerHTML += "<span class='header'>Number of Chin Up's:</span><p>"+rr['chinup']+"</p>";
			content.innerHTML += "<span class='header'>Hang Duration:</span><p>"+rr['hang']+" seconds</p>";
			content.innerHTML += "<span class='header'>Sit & Reach Trial 1:</span><p>"+rr['sitreach1']+" cm</p>";
			content.innerHTML += "<span class='header'>Sit & Reach Trial 2:</span><p>"+rr['sitreach2']+" cm</p>";
			content.innerHTML += "<span class='header'>Height:</span><p>"+rr['height']+" cm</p>";
			content.innerHTML += "<span class='header'>Mass:</span><p>"+rr['mass']+" kg</p>";
			content.innerHTML += "<span class='header'>Waist Measurement:</span><p>"+rr['waist']+" cm</p>";
			content.innerHTML += "<span class='header'>Hip Measurement:</span><p>"+rr['hip']+" cm</p>";
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
					t.executeSql('Select * From class Where start<? And finish>? And (Select count(student_id) From classmap Where student_id = ? And class_name=class.name)>0',[currentTime.getTime(),currentTime.getTime(),studentid],function (t, r) {
						if(r.rows.length==1){
							//alert();
							var classname = r.rows.item(0)['name'];
							t.executeSql('Insert Into Fitness_Test(student_id,group_id,daydate,pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,waist,hip,uploaded) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[studentid,classname,daydate.getTime(),pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,waist,hip,false],
								function (t, r) {
									document.location = "Fitness.html";
								},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
							//alert();
						} else {
							alert("You are currently enrolled in multiple classes or no classes. Sorry this application cannot handle this event.");
						}
					},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
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
	if(dataBase==null){
		openDB();
	}
	if(navigator.onLine){
		var form = document.getElementById('form');
		var xmlhttp;

		xmlHttp=new XMLHttpRequest();
		var username = document.getElementById("username").value;
		var password = document.getElementById("password").value;
		var url="../api_auth.php";

		xmlHttp.open("POST",url,false);
		xmlHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xmlHttp.send("username="+username+"&password="+password);
		//alert(xmlHttp.responseText);
		//convert xmlHttp.responseText so so I can get the data I need
		var token = xmlHttp.responseText;
		if(token.indexOf("&lt;token&gt;")!=-1){
			token = token.substring(token.indexOf("&lt;token&gt;")+13,token.indexOf("&lt;/token&gt;"));
			dataBase.transaction(function (t) {
				t.executeSql('Select Count(*) As d From Student Where id=?', [username], function (t, r) {
					t.executeSql('Update Student Set loggedOn = ?',[false], function (t, r) {},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
					var currentTime = new Date();
					if(r.rows.item(0).d>0){
						t.executeSql('Update Student Set loggedOn = ?, time=?, password=?, token=? Where id=?',[true,currentTime.getTime()+900000,password,token,username], function (t, r) {
							document.location = "Upload.html";
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
					} else {
						t.executeSql('Insert Into Student(id,password,loggedOn,time,token) values(?,?,?,?,?)',[username,password,true,currentTime.getTime()+900000,token], function (t, r) {
							document.location = "Upload.html";
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
					}
				},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			});
		} else {
			document.location = "InvalidLogon.html";//Need to create page
		}
	} else {
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

var insertDump;

function downloadData(){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select id,token From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
			if(r.rows.length==1){
				var studentid = r.rows.item(0)['id'];
				var xmlhttp;
				xmlHttp=new XMLHttpRequest();
				var url="Download.php";
				xmlHttp.open("POST",url,false);
				xmlHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				xmlHttp.send("token="+r.rows.item(0)["token"]);
				var json = jQuery.parseJSON(xmlHttp.responseText);
				var content;
				
				insertDump = json["student"].length+json["class"].length+json["compcodes"].length+json["classmap"].length+json["training_records1"].length+json["training_records2"].length+json["fitness_test"].length+json["rating_item_map"].length+json["rating_item"].length;
				
					if(json["compcodes"].length>0){
						tx.executeSql('Delete From compcodes', [], function (t, r) {
							for(var i=0;i<json["compcodes"].length;i++){
								insertCompcodes(json["compcodes"][i]);
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					}
					//if(json["training_records1"].length>0){
						tx.executeSql('Delete From training_records1 Where student_id=?', [studentid], function (t, r) {
							for(var i=0;i<json["training_records1"].length;i++){
								insertTrainingRecords1(json["training_records1"][i]);
							}
				//document.getElementById("content").innerHTML += "Training 1<br>";
				},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					//}
					//if(json["training_records2"].length>0){
						tx.executeSql('Delete From training_records2 Where student_id=?', [studentid], function (t, r) {
							for(var i=0;i<json["training_records2"].length;i++){
								insertTrainingRecords2(json["training_records2"][i]);
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					//}
					//if(json["fitness_test"].length>0){
						tx.executeSql('Delete From fitness_test Where student_id=?', [studentid], function (t, r) {
							for(var i=0;i<json["fitness_test"].length;i++){
								insertFitnessTest(json["fitness_test"][i]);
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					//}
					if(json["rating_item_map"].length>0){
						tx.executeSql('Delete From rating_item_map Where groupname=?', [json["rating_item_map"][0]["groupname"]], function (t, r) {
							for(var i=0;i<json["rating_item_map"].length;i++){
								insertRatingItemMap(json["rating_item_map"][i]);
							}
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					}
					tx.executeSql('Select id From Student', [], function (t, r) {
						for(var i=0;i<json["rating_item"].length;i++){
							insertRatingItem(json["rating_item"][i]);
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					tx.executeSql('Select id From Student', [], function (t, r) {
						for(var i=0;i<json["student"].length;i++){
							insertStudent(json["student"][i]);
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					tx.executeSql('Select id From Student', [], function (t, r) {
						for(var i=0;i<json["class"].length;i++){
							insertClass(json["class"][i]);
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
					tx.executeSql('Select id From Student', [], function (t, r) {
						for(var i=0;i<json["classmap"].length;i++){
							insertClassmap(json["classmap"][i]);
						}
					},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});
}

function insertStudent(student){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select * From Student Where id=?', [student["id"]], function (t, r) {
			if(r.rows.length==1){
				tx.executeSql('Update Student Set first=?,last=?,password=?,age=?,gender=?,athletic=?,sport=?,level=? Where id=?',[student["first"],student["last"],student["password"],student["age"],student["gender"],student["athlete"],student["sport"],student["level"],student["id"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				tx.executeSql('Insert Into Student(id,first,last,password,age,gender,athletic,sport,level) values(?,?,?,?,?,?,?,?,?)',[student["id"],student["first"],student["last"],student["password"],student["age"],student["gender"],student["athlete"],student["sport"],student["level"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertClass(clas){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select * From class Where name=?', [clas["name"]], function (t, r) {
			if(r.rows.length==1){
				tx.executeSql('Update class Set start=?,finish=?,window=? Where name=?',[clas["start"],clas["finish"],clas["window"],clas["name"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				tx.executeSql('Insert Into class(name,start,finish,window) values(?,?,?,?)',[clas["name"],clas["start"],clas["finish"],clas["window"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertClassmap(classmap){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		var currentTime = new Date();
		tx.executeSql('Select * From classmap Where student_id=?', [classmap["student_id"]], function (t, r) {
			if(r.rows.length==1){
				tx.executeSql('Update classmap Set class_name=? Where student_id=?',[classmap["class_name"],classmap["student_id"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				tx.executeSql('Insert Into classmap(student_id,class_name) values(?,?)',[classmap["student_id"],classmap["class_name"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertCompcodes(compcodes){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into compcodes(compcode,mets,type,heading,description) values(?,?,?,?,?)',[compcodes["compcode"],compcodes["mets"],compcodes["type"],compcodes["heading"],compcodes["description"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertTrainingRecords1(trainingrecords1){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into training_records1(daydate,compcode,duration,start,end,student_id,time_of_day) values(?,?,?,?,?,?,?)',[trainingrecords1["daydate"],trainingrecords1["compcode"],trainingrecords1["duration"],trainingrecords1["start"],trainingrecords1["end"],trainingrecords1["student_id"],trainingrecords1["time_of_day"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertTrainingRecords2(trainingrecords2){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into training_records2(daydate,student_id,class,heart_rate,sleep,health,ratings) values(?,?,?,?,?,?,?)',[trainingrecords2["daydate"],trainingrecords2["student_id"],trainingrecords2["class"],trainingrecords2["heart_rate"],trainingrecords2["sleep"],trainingrecords2["health"],trainingrecords2["ratings"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertFitnessTest(fitnesstest){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into fitness_test(student_id,group_id,daydate,id,pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,waist,hip) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[fitnesstest["subject_id"],fitnesstest["group_id"],fitnesstest["daydate"],fitnesstest["test_num"],fitnesstest["pushup"],fitnesstest["situp"],fitnesstest["chinup"],fitnesstest["hang"],fitnesstest["sitreach1"],fitnesstest["sitreach2"],fitnesstest["height"],fitnesstest["mass"],fitnesstest["waist"],fitnesstest["hip"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertRatingItem(ratingitem){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Select * From rating_item Where id=?', [ratingitem["id"]], function (t, r) {
			if(r.rows.length==1){
				tx.executeSql('Update rating_item Set description=?,summary=? Where id=?',[ratingitem["description"],ratingitem["summary"],ratingitem["id"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				tx.executeSql('Insert Into rating_item(id,description,summary) values(?,?,?)',[ratingitem["id"],ratingitem["description"],ratingitem["summary"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
			}
		},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

function insertRatingItemMap(ratingitemmap){
	if(dataBase==null){
		openDB();
	}
	dataBase.transaction(function (tx) {
		tx.executeSql('Insert Into rating_item_map(groupname,id) values(?,?)',[ratingitemmap["groupname"],ratingitemmap["id"]], function (t, r) {insertedData();},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
	});	
}

var locationAfterDownload;

function insertedData(){
	insertDump--;
	//document.getElementById("content").innerHTML += insertDump+"<br>";
	if(insertDump==0){
		//alert("done");
		if(locationAfterDownload!=null){
			document.location = locationAfterDownload;
		} else {
			document.location = "Home.html";
		}
	}
}

var uploadDump;

function uploadPage(){
	var locationstring = document.location.toString();
	if(locationstring.indexOf("nextpage")!=-1){
		locationAfterDownload = locationstring.substring(locationstring.indexOf("nextpage")+9)+".html";
	} else {
		locationAfterDownload = "Home.html";
	}
	uploadData();
}

function uploadData(){
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
						var JSON = {"student":[],"training_records1":[],"training_records2":[],"fitness_test":[]};
						uploadDump = 4;
						tx.executeSql('Select * From training_records2 Where class=? And student_id=? And Uploaded=?',[classname,studentid,false],function (t,r) {
							for(var i =0;i<r.rows.length;i++){
								var rr = r.rows.item(i);
								JSON["training_records2"][i] = {"daydate":rr["daydate"],"student_id":rr["student_id"],"class":rr["class"],"heart_rate":rr["heart_rate"],"sleep":rr["sleep"],"health":rr["health"],"ratings":rr["ratings"]};
							}
							extractedData(JSON);
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						tx.executeSql('Select * From training_records1 Where class=? And student_id=? And Uploaded=?',[classname,studentid,false],function (t,r) {
							for(var i =0;i<r.rows.length;i++){
								var rr = r.rows.item(i);
								JSON["training_records1"][i] = {"daydate":rr["daydate"],"student_id":rr["student_id"],"class":rr["class"],"compcode":rr["compcode"],"start":rr["start"],"end":rr["end"],"time_of_day":rr["time_of_day"]};
							}
							extractedData(JSON);
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						tx.executeSql('Select * From fitness_test Where group_id=? And student_id=? And Uploaded=?',[classname,studentid,false],function (t,r) {
							for(var i =0;i<r.rows.length;i++){
								var rr = r.rows.item(i);
								JSON["fitness_test"][i] = {"subject_id":rr["student_id"],"group_id":rr["group_id"],"daydate":rr["daydate"],"pushup":rr["pushup"],"situp":rr["situp"],"chinup":rr["chinup"],"hang":rr["hang"],"sitreach1":rr["sitreach2"],"height":rr["height"],"mass":rr["mass"],"waist":rr["waist"],"hip":rr["hip"]};
							}
							extractedData(JSON);
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
						tx.executeSql('Select * From student Where id=? And Uploaded=?',[studentid,false],function (t,r) {
							for(var i =0;i<r.rows.length;i++){
								var rr = r.rows.item(i);
								JSON["student"][i] = {"id":rr["id"],"first":rr["first"],"last":rr["last"],"active":rr["active"],"age":rr["age"],"gender":rr["gender"],"athletic":rr["athletic"],"sport":rr["sport"]};
							}
							extractedData(JSON);
						},function (t, error) {alert('Error: '+error.message+' (Code '+error.code+')');;});
					} else {
						uploadDump = 1;
						extractedData(JSON);
					}
				},function (t, error) {alert('Obtaining Class Error: '+error.message+' (Code '+error.code+')');;});
			} else {
				document.location = "LoginTimedOut.html";
			}
		},function (t, error) {alert('Obtaining Student Error: '+error.message+' (Code '+error.code+')');;});
	});
}

// implement JSON.stringify serialization  
function tostring(obj) {  
    var t = typeof (obj);  
    if (t != "object" || obj === null) {  
        // simple data type  
        if (t == "string") obj = '"'+obj+'"';  
        return String(obj);  
    }  
    else {  
        // recurse array or object  
        var n, v, json = [], arr = (obj && obj.constructor == Array);  
        for (n in obj) {  
            v = obj[n]; t = typeof(v);  
            if (t == "string") v = '"'+v+'"';  
            else if (t == "object" && v !== null) v = tostring(v);  
            json.push((arr ? "" : '"' + n + '":') + String(v));  
        }  
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");  
    }  
};  

function extractedData(JSON){
	uploadDump--;
	//document.getElementById("content").innerHTML += uploadDump+"<br>";
	if(uploadDump==0){
	//document.getElementById("content").innerHTML += tostring(JSON)+"<br>";
		//alert("done");
		if(dataBase==null){
			openDB();
		}
		dataBase.transaction(function (tx) {
			var currentTime = new Date();
			tx.executeSql('Select id,token From Student Where loggedOn = ? and time>?', [true,currentTime.getTime()], function (t, r) {
				if(r.rows.length==1){
					var studentid = r.rows.item(0)['id'];
					var xmlhttp;
					xmlHttp=new XMLHttpRequest();
					var url="Upload.php";
					xmlHttp.open("POST",url,false);
					xmlHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
					xmlHttp.send("token="+r.rows.item(0)["token"]+"&data="+tostring(JSON));
					//document.getElementById("content").innerHTML += tostring(JSON)+"<br>";
					alert(tostring(JSON));
					var response = xmlHttp.responseText;
					//var json = jQuery.parseJSON(xmlHttp.responseText);
					//if(response=="success"){
						downloadData();
					//} else {
					//	alert("Sorry. We had trouble uploading your data. Please log in again.");
					//	document.location = "Logon.html";
					//}
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