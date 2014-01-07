/************************************************************************
 * TASK VIEW 
 *
 * This is JavaScript for TaskView. It contains functions for time animation,
 * view switching, category adding, category removing, task manipulation etc.
 *
 * Created by: Samo Pajk 29.12.2011
 ************************************************************************/	


/************************************************************************
 * GLOBAL VARIABLES 
 *
 * This section includes global variables that are written and readed by 
 * many functions and they also serve for navigation forward/backwards
 * some of them will probably be loaded via AJAX on login
 ************************************************************************/	

var global = {
	selfGlobal		 : this,
	currentView      : null,
	selectedCategory : null,
	categories 		 : null,
	reminderTypes	 : null,
	categoryTypes    : null,
	priorities		 : null,
	taskReminders	 : null,
	repeats		     : null,
	task			 : null,
	pastTasks		 : false,
	stack 			 : new ControlableStack(30),
	push			 : function (action){
		//**(this);
		this.stack.add(action);
	},
	undo 			 : function (){
		var action = this.stack.previous();
		//execute previous action;
		//**(action);
		if(action){
			setTimeout(action.fn,0);
		}
	},
	redo			 : function (){
		var action = this.stack.next();
		if(action){
			setTimeout(action.fn,0);
		}
	} 
}

/************************************************************************
 * UNDO REDO STACK and ACTION
 * 
 * serves for undoing and redoing views
 ************************************************************************/	

/*
 * undo, redo object for particular action  
 */
function Action(name, fn){
	this.name = name;
	this.fn   = fn;
}

/*
 * Data structure that holds as many objects as capacity specifies
 * Structure allows adding data to it and moving forward and backwards 
 * between items.
 */
function ControlableStack(capacity){
	selfCtrStack 			= this;
	//private variables
	var maxsize 		    = capacity;
	var data				= [];
	var position			= -1;
	
	selfCtrStack.add = function(element){
		if(data.length > maxsize){
			data.splice(0,1);
		}else{
			position++;
		}
		data[position] = element;
		
		data.splice(position+1, maxsize-position-1)
	}
	
	selfCtrStack.next = function(){
		if(data[position+1] == null){
			return null;
		}
		position++;
		return data[position];
	}	
	
	selfCtrStack.previous = function(){
		if(data[position-1] == null){
			return null;
		}
		position--;
		return data[position];
	}

	selfCtrStack.hasNext  = function(){
		if(data[position+1] != null){
			return true
		}
		return false;
	}

	selfCtrStack.hasPrevious  = function(){
		if(data[position-1] != null){
			return true
		}
		return false;
	}
	
	selfCtrStack.size = function(){
		return maxsize;
	}
	
	selfCtrStack.changeSize = function(size){
		maxsize = size;
	}
}

/************************************************************************
 * INIT FUNCTION
 * 
 * Function by witch program starts
 ************************************************************************/	

$(document).ready(function(){
	//add clock
	clock(".clock");
	//set global AJAX settings
	$.ajaxSetup({ 
		dataType : "json",
		async : true,
		type : "POST",
		//set function for reporting ajax errors
		error : function(jqXHR, textStatus, thrownError){ 
			// redirect if session expires 
			if(jqXHR.status == 500) { 
				window.location = "./process.php"
			}
		}
	});
	//get global variables from server
	ajaxGetGlobals();
	//get categories for current user	
	ajaxGetCategories(false);
	//get tasks for thiw week
	ajaxGetAllTasks(false);
	//add first undo level to stack
	global.push(new Action("Login", "ajaxGetGlobals();ajaxGetCategories();ajaxGetAllTasks();"));
	//set Events for search
	$("input.taskSearch").bind("keyup", function(e){
		if($(e.target).val().length > 0){
			searchTasks($(e.target).val());
		}
		
	});
});

/*
 * clock() updates clock in header of the document each second...
 * Note that this function only starts the proces and in is not 
 * controling it...
 * 
 * @param: selector - jQuery selector for div or span in witch the clock will be shown
 */
function clock(selector){
	/*set timer that will update time each second*/
	setInterval(function(){
		var d = new Date();
		var h = (d.getHours()>9)? d.getHours() : "0" + d.getHours() ;
		var m = (d.getMinutes()>9)?d.getMinutes(): "0" + d.getMinutes();
		$(selector).html( h + " : " + m);
	},1000);
}

/************************************************************************
 * PAGE BUILDING 
 *
 * Functions in this section are used to build webpage
 ************************************************************************/	

/*
 * Function displays icons in header that are avalible for current
 * display/view
 * 
 * @param action - action from witch function detemins witch icons to show 
 */
function displayIcons(){
	var icons = "";
	//create li of icons
	if(global.currentView == "allTasks"){
		icons = createArrayOfLi(["<span class='left icon iconTrash' title='Remove selected tasks'/>", 
								 "<span class='left icon iconThick' title='Mark Selected tasks as completed'/>", 
								 //"<span class='left icon iconTimeline' title='Timeline view' />", 
								 "<span class='left icon iconSettings' title='Settings'/>"]);
								 
	}else if(global.currentView == "categoryTasks"){
		icons = createArrayOfLi(["<span class='left icon iconPlus' title='Add new tasks'/>",
								"<span class='left icon iconTrash' title='Remove selected tasks'/>", 
								 "<span class='left icon iconThick' title='Mark Selected tasks as completed'/>",  
								 "<span class='left icon iconSettings' title='Settings'/>"]);
								 
	}else if(global.currentView == "taskDetails"){
		icons = createArrayOfLi(["<span class='left icon iconSave' title='Save changes'/>",
								 "<span class='left icon iconSettings' title='Settings'/>"]);
	
	}else if(global.currentView == "settingsCategories"){
		icons = createArrayOfLi(["<span class='left icon iconTrash' title='Remove selected Categories'/>",
								 "<span class='left icon iconTasks' title='Tasks'/>"]);
								 
	}else if(global.currentView == "settingsUser" || global.currentView == "settingsReminders"){
		icons = createArrayOfLi(["<span class='left icon iconTasks' title='Tasks'/>"]);
	
	}else if(global.currentView == "searchView"){
		icons = createArrayOfLi(["<span class='left icon iconThick' title='Mark Selected tasks as completed'/>",  
								  "<span class='left icon iconTrash' title='Remove selected tasks'/>",
								  "<span class='left icon iconSettings' title='Settings'/>"]);
	}
	
	
	//remove previous completely and add new icons
	$("ul.headerIcons li").remove();
	$("ul.headerIcons").html(icons);
	//add events (note that if icons for event dont exist they will not be aded that's why there is no special checking if particular element exists)
	$("ul.headerIcons span.iconPlus").click(function(e){
		addNewTask();
	});
	$("ul.headerIcons span.iconTrash").click(function(e){
		if(global.currentView == "settingsCategories"){
			removeCategories();
		}else{
			removeTasks();	
		}
	});
	$("ul.headerIcons span.iconThick").click(function(e){
		markTasks();
	});
	$("ul.headerIcons span.iconSave").click(function(e){
		editTask();
	});
	$("ul.headerIcons span.iconTimeline").click(function(e){
		viewTimeline();
	});
	$("ul.headerIcons span.iconSettings").click(function(e){
		viewSettings();
	});
	
	$("ul.headerIcons span.iconTasks").click(function(e){
		ajaxGetCategories(false);
		ajaxGetAllTasks(false);
	});
}

/*
 * function sets left side as settings tabs
 */
function settingsTabs(){
	//empty categories
	var leftSide = $("div.categories");
	$(leftSide).empty();
	var tabs = [["User Settings", "#49734b", "userSettings"], ["Categories", "#aeb48e", "categorySettings"], ["Reminders", "#efefd5", "remindersSettings"]];
	for(var i = 0; i < tabs.length; i++){
		//create div add its properties and append it to categories
		var div = document.createElement("div");
		$(div).html(tabs[i][0]);
		$(div).addClass("category " + tabs[i][2]);
		$(div).css({
			"z-index"			: tabs.length-i,
			"color"				: BlackOrWhite(tabs[i][1]),
			"background-color"  : tabs[i][1]
		});
		$(div).click(function(e){
			$("div.selectedCategory").removeClass("selectedCategory");
			$(this).addClass("selectedCategory");
			showSettingsTab($(e.target).html());
		});
		
		$("div.categories").append(div);
	}
	//add empty div for side edge enlargement
	var ext = document.createElement("div");
	$(ext).addClass("categoryEdge");
	$("div.categories").append(ext);
}

/*
 * function is called when clicked on tab when in settings and displays
 * categories 
 */
function showSettingsTab(tabName){
	//**(tabName);
	if(tabName == "User Settings"){
		global.currentView = "settingsUser";
		displayIcons();
		settingsUser();	
	}else if(tabName == "Categories"){
		global.currentView = "settingsCategories";
		displayIcons();
		settingsCategories();	
	}else if(tabName == "Reminders"){
		global.currentView = "settingsReminders";
		displayIcons();
		settingsReminders();	
	}
}

function settingsUser(){
	//**("settingsUser");
	$("div.details").empty();
	$("div.details").append("<span class='title'>User Settings</span>"+ 
						 	"<table id='userSettings' class='inputTable'>" +
						 	"<tr>" +
						 		"<td class='title' colspan='2'>Change your password:</td>" +
						 	"</tr>"+
						 	"<tr>"+
						 		"<td>Password old:</td><td><input class='form' id='passwordOld' type='password'/></td>"+
						 	"</tr>"+
						 	"<tr>"+
						 		"<td>Password new:</td><td><input class='form' id='passwordNew' type='password'/></td>"+
						 	"</tr>"+
						 	"<tr>"+
						 		"<td>Password new(repeat):</td><td><input class='form' id='passwordNewRepeat' type='password'/></td>"+
						 	"</tr>"+
						 	"<tr>"+
						 		"<td colspan='2' class='button'><input class='form button' id='changePassword' type='button' value='Change'/></td>"+
						 	"</tr>"+
						 	"</table>");
						 	
	$("#changePassword").click(function(e){
		var passOld = $("#passwordOld").val();
		var passNew = $("#passwordNew").val();
		var passNew2 = $("#passwordNewRepeat").val();
		if(passOld.length < 5){
			showInformation("Old password is too short.");
			return;
		}
		if(passNew.length < 5 || passNew2.length < 5){
			showInformation("New passwords are too short.");
			return;
		}
		if(passNew != passNew2){
			showInformation("New passwords do not match.");
			return;
		}
		if(passOld == passNew){
			showInformation("New and old password are identical.");
			return;
		}
		ajaxChangePassword(passOld, passNew);
	});
}

function settingsCategories(){
	$("div.details").empty();
	$("div.details").append("<span class='title'>Category Settings</span>"+ 
						 	"<table id='categorySettings' class='datatable'>" +
						 	"</table>");
	ajaxGetCategories(true,"edit");
					 	
}

function settingsReminders(){
	$("div.details").empty();
	$("div.details").append("<span class='title'>Reminders Settings</span>"+ 
						 	"<table id='reminderSettings' class='inputTable'>" +
						 	"</table>");
						 	
	ajaxGetReminderSettings();

	
}
/************************************************************************
 * EVENT FUNCTIONS
 *
 * Functions that are triggered by events  
 ************************************************************************/	

/*
 * Function is called when clicked on any category in right sidebar.
 * After click selected category is highligthed and proper data is displayed
 * in editor on left side that is gotten from ajax
 * 
 * @param e - event
 */
function selectCategory(e){
	var category = $(e.target).data("cid");
	
	if(category == null){//check if add button has benn pressed
		addNewCategory();
		return;
	}else if(category == "all"){ //check if category all tab has been pressed
		viewAllTasks();		
	}else{ //a category has been clicked
		viewCategoryTasks(category);
	}
	
	$("div.category").removeClass("selectedCategory");
	$(e.target).addClass("selectedCategory");
}

/************************************************************************
 * VIEW FUNCTIONS
 *
 * This functions serve for displaying certain view such as settings, 
 * categories, timeline etc.  
 ************************************************************************/	

/*
 * Function shows tasks happening in near future for category All
 */
function viewAllTasks(){
	ajaxGetAllTasks();
	global.push(new Action("View All Tasks", ajaxGetAllTasks));		
}

/*
 * Function shows tasks from certain category
 * 
 * @param cId - category ID of witch you want to show info from
 */
function viewCategoryTasks(cId){
	ajaxGetCategoryTasks(cId);
	global.push(new Action("View Category Tasks", "ajaxGetCategoryTasks("+cId+")"));
		
}

/*
 * Function shows details of a certain task
 */
function viewTaskDetails(tid){
	ajaxGetTaskDetails(tid);
	global.push(new Action("View Task Details", "ajaxGetTaskDetails("+tid+")"));
}

/*
 * Function shows settings window
 */

function viewSettings(){
	//set navigation tabs for settings
	settingsTabs();
	//show user settings as default
	showSettingsTab("User Settings");
}

/*
 * Function shows Timeline view
 */
function searchTasks(query){
	ajaxSearchTasks({"search" : query});
}

/************************************************************************
 * FUNCTIONS FOR ADDING TASKS AND CATEGORIES
 ************************************************************************/	

/*
 * function show popup for adding new task and adds it if user decides so
 */
function addNewTask(){
	//create html popup
	var newTask = "<span class='title'>New Task</span>";
	
	newTask += "<table class='newTask'>" +
		"<tr><td>Name:</td><td><input id='newName'/></td></tr>" +
		"<tr><td>Start:</td><td class='date'><input id='newDateStart'class='form'/><input id='newTimeStart' class='form' /></td></tr>" +
		"<tr><td>End:</td><td class='date'><input id='newDateEnd' class='form'/><input id='newTimeEnd'class='form' /></td></tr>"+
		"<tr><td>Category:</td><td><span id='newCategory'>" + (($("div.category.selectedCategory").html() != "all")?$("div.category.selectedCategory").html() : global.categories[0][0]) +"</span></td></tr>" + 
		"<tr><td>Priority:</td><td><span id='newPriority'>"+global.priorities[0]+"</span></td></tr>"+
		"<tr><td>SMS Reminder:</td><td><span id='newReminderSMS'>"+global.taskReminders[0]+"</span</td></tr>"+
		"<tr><td>Email Reminder:</td><td><span id='newReminderEmail'>"+global.taskReminders[0]+"</span</td></tr>"+
		"<tr><td>Repeat:</td><td><span id='newRepeat'>"+global.repeats[0]+"</span></td></tr>" +
		"<tr><td>Repeat ends:</td><td><input id='newRepeatEnd' class='form'/></td></tr>" +
		"<tr><td colspan='2'><button class='button'>Add</button></td></tr>" +
	"</table>";
	
	popup(newTask);
	
	//add datepickers
	$("#newDateStart, #newDateEnd, #newRepeatEnd").datepicker({
		showAnim: null, 
		dateFormat : "d.m.yy"
	});
	//for unknown reason datepicker is show and spread through the bottom of the page so it must be hidden
	$("#ui-datepicker-div").hide();
	//create time pickers
	timePicker("#newTimeStart");
	timePicker("#newTimeEnd");
	
	var cat = [];
	for(var i = 0; i<global.categories.length;i++){
		cat.push(global.categories[i][0]);
	}
	//create dropdowns
	dropDown("#newPriority", global.priorities);
	dropDown("#newReminderEmail", global.taskReminders);
	dropDown("#newReminderSMS", global.taskReminders);
	dropDown("#newCategory", cat);
	dropDown("#newRepeat", 	 global.repeats);
	
	//add button event
	$("table.newTask button").click(function(e){
		//get data from input fields and send it to server
		var data = {
			name    	    : $("#newName").val(),
			start  		  	: $("#newDateStart").val() + " " + $("#newTimeStart").val(),
			end    		  	: $("#newDateEnd").val() + " " + $("#newTimeEnd").val(),
			cid 			: getCidOfCategoryName($("#newCategory").html()),
			priority  		: $("#newPriority").html(),
			reminderSMS 	: $("#newReminderSMS").html(),
			reminderEmail 	: $("#newReminderEmail").html(),
			repeat    		: $("#newRepeat").html(),
			repeatEnd 		: $("#newRepeatEnd").val()
		}
		//**(data);
		//send data to server
		ajaxAddTask(data);
		//destroy popup
		popup("destroy");
	});
}

/*
 * Function adds mew category without tasks 
 */
function addNewCategory(){
	//build html form for adding new category
	//title
	var newCategory = "<span class='title'>New Category</span>";
	//input fields	
	newCategory += "<table class='newCategory'>" +
		"<tr><td>Category name: </td><td><input class='form newCategoryName' type='text'/></td></tr>" +
		//"<tr><td>Type of category: </td><td><span class='newCategoryType'>"+global.categoryTypes[0]+"</span></td></tr>" + 
		// "<tr><td>Reminders:</td> <td><span class='newCategoryReminders'>"+global.reminderTypes[0]+"</span></td></tr>" +
		"<tr><td>Color: </td><td><span class='color newCategoryColor'/></td></tr>" +
		"<tr><td colspan='2'><button class='button'>Add</button></td></tr>" +
	"</table>";
	
	//add current color to
	$(".newCategoryColor").data("color", "#000000");
	//add form to popup and display it
	popup(newCategory);
	
	//add events to form
	// dropDown(".newCategoryType", global.categoryTypes);
	dropDown(".newCategoryReminders", global.reminderTypes);
	
	//colorpicker
	colorpicker(".newCategoryColor", function(color){
		$(".newCategoryColor").data("color", color);
	});
	
	$("table.newCategory button").click(function(e){
		//TODO check for data integrity
		var name = $(".newCategoryName").val();
		if(name.length == 0){return;}
		data = {
			name      : name,
			color     : $(".newCategoryColor").data("color"),
			reminders : "None"
		}
	 	ajaxAddCategory(data);
		popup("destroy");
	});
}

/************************************************************************
 * FUNCTIONS FOR REMOVING TASKS AND CATEGORIES
 ************************************************************************/

/*
 * Function removes selected Tasks or current task if in task detailsView
 */
function removeTasks(){
	//get all thicks
	var selectedTasks = $("div.details table td span.icon.iconBorderBoxThicked");
	//check if we got any selected tasks
	if(selectedTasks.length == 0){
		return;
	}
	
	var tIds = [];
	//get tids that are saved in row data
	for(var i = 0; i < selectedTasks.length; i++){
		tIds.push($(selectedTasks[i]).parent().parent().data("taskId"));
	}
	
	//deselect - not neccesarry
	$(selectedTasks).removeClass("iconBorderBoxThicked");
	$(selectedTasks).addClass("iconBorderBox");
	//remove selected tasks
	showInformation("Removed " + tIds.length + " tasks.");
	//TODO uncomment this when ajax will be working
	ajaxRemoveTasks({tids : tIds});
	//TODO refresh from server data
	
}

/*
 * Function removes category and its tasks 
 */
function removeCategories(){
	//get all thicks
	var selectedTasks = $("div.details table td span.icon.iconBorderBoxThicked");
	//check if we got any selected tasks
	if(selectedTasks.length == 0){
		return;
	}
	
	var cid;
	var cids = [];
	var avalibleCids = global.categories.slice(0);
	//get cids that are saved in row data
	for(var i = 0; i < selectedTasks.length; i++){
		//**($(selectedTasks[i]).parent().parent().data("cid"));
		cid = $(selectedTasks[i]).parent().parent().data("cid");
		cids.push(cid);
		for(var j = 0; j < avalibleCids.length; j++){
			if(cid == avalibleCids[j][1]){
				avalibleCids.splice(j,1);
				break;
			}
		}
	}
	
	//**(cids, avalibleCids);
	if(avalibleCids.length > 0){
		//ask where to move tasks after deletion
		var catChange = "<span class='title'>Move tasks?</span>"+
						"<table class='moveTask'>" +
							"<tr><td colspan='2' class='informationText'>If you would like to move tasks from deleted category(s) "+
							"to another one, please select one below..</td></tr>" +
							"<tr><td class='verticalSpace'></td></tr>" +
							"<tr><td>Move to:</td><td><span id='moveCategory'>"+avalibleCids[0][0]+"</span></td></tr>" +
							"<tr><td class='verticalSpace'></td></tr>" + 
							"<tr><td colspan='2'><button id='cancelMove' class='button'>Cancel</button><button id='applyMove' class='button'>Move</button><button id='removeAll' class='button'>Remove both</button></td></tr>" +
						"</table>";
		//create popup
		popup(catChange);
		//create dropdown for category chose
		var dd = [];
		for(var i = 0; i < avalibleCids.length; i++){
			dd.push(avalibleCids[i][0]);
		}
		dropDown("#moveCategory", dd);
		
		$("#cancelMove").click(function(){
			popup("destroy");
		});
		
		$("#applyMove, #removeAll").click(function(e){
			//**(cids);
			var newCategory;
			if($(e.target).attr("id") != "removeAll"){
				for(var j = 0; j < avalibleCids.length; j++){
					if(avalibleCids[j][0] == $("#moveCategory").html()){
						newCategory = avalibleCids[j][1];
						break;
					}
				}
			}
			$(selectedTasks).removeClass("iconBorderBoxThicked");
			$(selectedTasks).addClass("iconBorderBox");
			//remove selected tasks
			showInformation("Removed " + cids.length + " tasks.");
			//TODO uncomment this when ajax will be working
			ajaxRemoveCategories({
									newCategory : newCategory,
									cids : cids
								  });
			popup("destroy");
		});
	}else{
		ajaxRemoveCategories({cids : cids});
	}
}

/************************************************************************
 * FUNCTIONS FOR EDITING TASKS AND CATEGORIES
 ************************************************************************/

/*
 * Function marks tasks as completed
 */
function markTasks(){
	var selectedTasks = $("div.details table td span.icon.iconBorderBoxThicked");
	
	if(selectedTasks.length == 0){
		return;
	}
	
	var tIds = [];
	//get tids that are saved in row data
	for(var i = 0; i < selectedTasks.length; i++){
		tIds.push($(selectedTasks[i]).parent().parent().data("taskId"));
	}
	//deselect - not neccesarry
	$(selectedTasks).removeClass("iconBorderBoxThicked");
	$(selectedTasks).addClass("iconBorderBox");
	//mark selected tasks
	// showInformation(tIds.length + " tasks marked as completed.");
	//**(tIds);
	ajaxMarkTasks({tids : tIds});
	//TODO refresh from server data
}

/*
 * function checks for changes and if there are any it sends them to server
 */
function editTask(){
	var changes = checkTaskValues();
	var numChanges = 0;
	for(change in changes){
		numChanges++;
		//**(changes[change])
	}
	if(numChanges>0){
		changes.tid = global.task.tid;
		ajaxEditTask(changes);
	}else{
		showInformation("No changes found for this task");
	}
	
}

/*
 * checks weather it came to some changes with current task
 */
function hasTaskChanged(){
	if(!global.task){return;}
	var changes = checkTaskValues();
	for(change in changes){
		if(change){
			return true;
		}
	}
	return false;
}

/*
 * gets all task changes and puts them in object
 */ 
function checkTaskValues(){
	var changes = {};
	var start 		  = $("#detailsDateStart").val()+ " " +  $("#detailsTimeStart").val();
	var end 	 	  = $("#detailsDateEnd").val()+ " " +  $("#detailsTimeEnd").val();
	var priority 	  = $("#detailsPriority").html();
	var reminderSMS	  = $("#detailsReminderSMS").html();
	var reminderEmail = $("#detailsReminderEmail").html();
	var category	  = $("#detailsCategory").html();
	var repeat		  = $("#detailsRepeat").html();
	var repeatEnds 	  = $("#detailsRepeatEnd").val();
	var notes 		  = $("textarea.notesEditor").val()
	var name 	      = $("input.taskName").val()
	
	if(start && start != global.task.start){
		changes.start = start;	
	}
	if(end && end != global.task.end){
		changes.end = end;	
	}
	if(priority && priority != global.task.priority){
		changes.priority = priority;	
	}
	if(reminderSMS && reminderSMS != global.task.reminderSMS){
		changes.reminderSMS = reminderSMS;	
	}
	if(reminderEmail && reminderEmail != global.task.reminderEmail){
		changes.reminderEmail = reminderEmail;	
	}
	if(category && category != global.task.category){
		var cid;
		//get cid of chosen category
		for(cat in global.categories){
			if(global.categories[cat][0] == category){
				cid = global.categories[cat][1];
				break;
			}
		}
		if(cid){
			changes.cid = cid;	
		}
	}
	if(repeat && repeat != global.task.repeat){
		changes.repeat = repeat;	
	}
	if(repeatEnds && repeatEnds != global.task.repeatEnds){
		changes.repeatEnds = repeatEnds;	
	}
	if(notes && notes != global.task.notes){
		changes.notes = notes;	
	}
	if(name && name != global.task.name){
		changes.name = name;	
	}
	return changes;
}

/************************************************************************
 * DATATABLES
 *
 * This functions are used for table creation and filing them 
 * with data from ajax. For table creation jquery datatables plugin is 
 * used. You can find it at www.datatable.net. Note that datatables provide 
 * lots of futures for sortin paging and displaying data and since we only 
 * need just basic table 
 ************************************************************************/	

/*
 * Function creates a table and fills it with data
 * 
 * @param type 	   - what kind of datatatable to create (allTasks, categories...)
 * @param selector - jquery selector for selecting table that will be filled with data
 * @param data     - usually multidimensional array of data that will be used to fill the table 
 */
function datatable(type, selector, data){
	////**(data);
	if(type == "taskAll"){ //create table for viewAllTasks
		//**("filling table with all tasks");
		$(selector).dataTable({
			"bFilter"   : false,
			"bInfo"     : false,
			"bPaginate" : false,
			"bSort"     : false,
			"aaData"    : data,
			"sDom": 'T<"clear">t',
			"oTableTools": {
            	"sSwfPath": "../swf/copy_cvs_xls_pdf.swf",
            	"aButtons": [
		            {
		                "sExtends": "xls",
		                "sButtonText": "",
		                "mColumns": [2, 3]
		            },
		            {
		                "sExtends": "pdf",
		                "sButtonText": "",
		                "mColumns": [2, 3]
		            },
		            {
		                "sExtends": "csv",
		                "sButtonText": "",
		                "mColumns": [2, 3]
		            },
		        ]
        	},
			"aoColumns" : [
				{"sTitle" : "", "sClass" : "icon", "sWidth" : "110px", "bSortable" : false}, //icons column
				{"sTitle" : ""}, //grouping column
				{"sTitle" : "", "sClass" : "task"}, //task name column
				{"sTitle" : "", "sClass" : "date"} //date column
			],
			"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				//for each row do some processing
				//save task id and infomation weather task has been completed into row
				// //**(aData);
				$(nRow).data("completed", aData[4]);
				$(nRow).data("taskId", aData[5]);
				//add special class to task that indicates that task is completed
				if(aData[4] == true){
					$('td:eq(2)', nRow).addClass("completed");	
				}
				//sometimes this function is called multiple times but events still exists so they must be detached
				$('td:eq(0) span, td:eq(2)', nRow).unbind();
				//add event for marking tasks
	            $('td:eq(0) span', nRow).click(function(e){
	            	//mark or unmark task for "changing
	            	if($(e.target).hasClass("iconBorderBox")){
	            		$(e.target).removeClass("iconBorderBox");
	            		$(e.target).addClass("iconBorderBoxThicked")
	            	}else{
	            		$(e.target).removeClass("iconBorderBoxThicked")
	            		$(e.target).addClass("iconBorderBox");
	            	}
	            });
				//attach click event to task name column that will show details about task
	            $('td:eq(2)', nRow).click(function(e){
	            	// $(".selectedCategory").removeClass("selectedCategory");
	            	// $("#category"+aData[7]).addClass("selectedCategory");
	            	ajaxGetTaskDetails(aData[5]);
	            });
	            
	            return nRow;
        	},
			"oLanguage" : {
				"sZeroRecords": "No upcomming tasks."
			}
		}).rowGrouping({
			"iGroupingColumnIndex" : 1
		});
	}else if(type == "tasksCategory"){
		$(selector).dataTable({
			"bFilter"   : false,
			"bInfo"     : false,
			"bPaginate" : false,
			"bSort"     : true,
			"aaData"    : data,
			"sDom": 'T<"clear">t',
			"oTableTools": {
            	"sSwfPath": "../swf/copy_cvs_xls_pdf.swf",
            	"aButtons": [
		            {
		                "sExtends": "xls",
		                "sButtonText": "",
		                "mColumns": [1,2, 3]
		            },
		            {
		                "sExtends": "pdf",
		                "sButtonText": "",
		                "mColumns": [1,2, 3]
		            },
		            {
		                "sExtends": "csv",
		                "sButtonText": "",
		                "mColumns": [1,2, 3]
		            },
		        ]
        	},
			"aoColumns" : [
				{"sTitle" : "", "sClass" : "icon", "sWidth" : "110px", "bSortable" : false}, //icons column
				{"sTitle" : "Task", "sClass" : "task"}, //task name column
				{"sTitle" : "Priority", "sClass" : "priority", "sWidth" : "70px"}, //priority column
				{"sTitle" : "Date", "sClass" : "date"}, //date column
			],
			"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				//for each row do some processing
				//save task id and infomation weather task has been completed into row
				$(nRow).data("completed", aData[4]);
				$(nRow).data("taskId", aData[5]);
				//remove previous events
				$('td:eq(0) span, td:eq(1)', nRow).unbind();
				//add special class to task that indicates that task is completed
				if(aData[4] == true){
					$(nRow).addClass("completed");	
				}
				//add event for marking tasks
	            $('td:eq(0) span', nRow).click(function(e){
	            	//mark or unmark task for "changing"
	            	//**($(e.target).parent().parent().data("taskId"));
	            	if($(e.target).hasClass("iconBorderBox")){
	            		$(e.target).removeClass("iconBorderBox");
	            		$(e.target).addClass("iconBorderBoxThicked")
	            	}else{
	            		$(e.target).removeClass("iconBorderBoxThicked")
	            		$(e.target).addClass("iconBorderBox");
	            	}
	            });
				//atach click event to task name column that will show details about task
	            $('td:eq(1)', nRow).click(function(e){
	            	//**("click", aData[5]);
	            	viewTaskDetails(aData[5]);
	            	
	            });
	            return nRow;
        	},
			"oLanguage" : {
				"sZeroRecords": "No tasks under this category."
			}
		});
	}else if(type == "editCategories"){ //create table for editing categories
		//**("filling table with categories");
		$(selector).dataTable({
			"bFilter"   : false,
			"bInfo"     : false,
			"bPaginate" : false,
			"bSort"     : false,
			"aaData"    : data,
			"aoColumns" : [
				{"sTitle" : "", "sClass" : "icon", "sWidth" : "110px", "bSortable" : false}, //icons column
				{"sTitle" : "Category Name","sClass" : "name"},
				{"sTitle" : "Color", "sClass" : "color" }, 
				// {"sTitle" : "Reminders", "sClass" : "reminders"},
				// {"sTitle" : "", "sClass" : "remove"} 
			],"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				//save category id to row
				$(nRow).data("cid", aData[3]);
				
				//remove previous events
				$('td:eq(0) span, td:eq(1), td:eq(2), td:eq(3)', nRow).unbind();
				
				//add event for marking categories
	            $('td:eq(0) span', nRow).click(function(e){
	            	//mark or unmark task for "changing"
	            	if($(e.target).hasClass("iconBorderBox")){
	            		$(e.target).removeClass("iconBorderBox");
	            		$(e.target).addClass("iconBorderBoxThicked")
	            	}else{
	            		$(e.target).removeClass("iconBorderBoxThicked")
	            		$(e.target).addClass("iconBorderBox");
	            	}
	            });
				
				//create span for color presentation add him a color and add it to table
				var span = document.createElement("span");
				$(span).addClass("categoryColor");
				
				// //**(aData[1]);
				$(span).css("background-color", aData[2]);
				$("td:eq(2)", nRow).html(span);
				//create onclick colorpicker for span 
				colorpicker(span, function(color){
					//**("sending color", color);
					ajaxEditCategory({
						cid : aData[3],
						color : color
					});
				});
				
				//create editable areas for changable fields
				$("td:eq(1)", nRow).editable(function(val, settings){

					var cid = $(this).parent().data("cid");
					//**("editing category name", cid, val);

					ajaxEditCategory({
						cid : aData[3],
						name : val
					});
				},{
					tooltip : "Click to Edit",
					type  	: "text"
				});
			
				return nRow;
			},
			"oLanguage" : {
				"sZeroRecords": "No categories defined."
			}
		});
	}
}

/************************************************************************
 * CONSISTENCY CHECK FUNCTIONS
 *
 * Functions for checking consistency of input fields dates and forms
 ************************************************************************/	

/*
 * Function compares 2 dates
 */
function compareDates(dateStart, dateEnd){
	
}

/************************************************************************
 * OTHER FUNCTIONS
 *
 * There are misc functions
 ************************************************************************/	

/*
 * returns category id for category name or null if it's not there
 * 
 * @param name - category name
 */
function getCidOfCategoryName(name){
	var cid;
	for(cat in global.categories){
		if(global.categories[cat][0] == name){
			cid = global.categories[cat][1];
			break;
		}
	}
	return cid;
}
/*
 * function puts focus on a object by creating a full invisible window size diw and
 * putting it underheat the focused diw aloowing only focused content to be edited
 * 
 * @param focusedElement - selector for element to be focused or string "destroy" to remove focus from element 
 * @param callback 		 - function that is called if clicked outside of selected element
 */
function focus(focusedElement, callback){
	//check for input parameters
	//**("focus");
	if(focusedElement == null){
		return;
	}
	if(focusedElement == "destroy"){
		//**("destroy focus");
		focusedElement = $(".focusFader").data("focusedElement");
		var previousZindex = $(focusedElement).data("previousZindex");
		//**(previousZindex);
		$(focusedElement).css("z-index", previousZindex);
		$(".focusFader").remove();
		return;
	}
	//check if element that we are focusing exists
	if($(".focusFader").length > 0 || $(focusedElement).length == 0 ){
		return;
	}
	
	//create element for defocus, set proper css and save data witch element is it focusing
	var div = document.createElement("div");
	$(div).addClass("focusFader");
	$(div).css({
		"position" : "fixed",
		"top" : "0px",
		"left" : "0px",
		"width" : "100%",
		"height" : "100%",
		"z-index" : 25000
	});
	$(div).data("focusedElement", focusedElement);
	
	//**("focused element z-index", $(focusedElement).css("z-index"));
	//save previous z-index and set new one higher than focus element
	$(focusedElement).data("previousZindex", $(focusedElement).css("z-index"));
	$(focusedElement).css({
		"z-index" : 25001
	});
	//add focus element to body
	$("body").prepend(div);
	
	if(callback){
		$(div).click(function(e){
			callback();		
		});
	}
	return div;
}

/*
 * adds color picker to chosen selector when clicked on
 */
function colorpicker(selector, callback){
	$(selector).click(function(e){
		var div = document.createElement("div");
		$(div).addClass("colorpicker shadow");
		//$(div).attr("tabindex", "5");
		$("body").append(div);
		$(div).position({
			of : selector,
			my : "center top",
			at : "center bottom"
		});
		
		var cp = $.farbtastic(div,function(color){
			$(selector).css("background-color", color);
		});
		
		var clr = $(selector).css("background-color");
		//**(cp);
		cp.setColor(rgb2hex(clr));
		
		//put focus on colorpicker
		focus(div, function(){
			if(callback){
				////**("colorpicker callback", cp.color);
				callback(cp.color);
			}
			$(div).remove();
			focus("destroy");
		});
	});
}

/*
 * converts color from rgb to hex
 */
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}


/*
 * adds functionality to set time by buttons
 * 
 * @param selector - selector for input field that holds time in format hh:mm
 */
function timePicker(selector, callback){	
	$(selector).click(function(e){
		if($("div.timepicker").length>0){
			return;
		}
		var div = document.createElement("div");
		$(div).addClass("timepicker borderBL borderBR");
		$(div).attr("tabindex", 5);
		$(div).css({
			"position"  : "absolute",
			"top"		: ($(selector).offset().top + $(selector).height()),
			"left"		: $(selector).offset().left,
			"display"   : "block"
		});
		
		$(div).append("<ul class='hours'><li><span class='hours iconSmall iconSmallUp'/></li><li><span class='hours iconSmall iconSmallDown'/></li></ul>");
		$(div).append("<ul class='minutes'><li><span class='minutes iconSmall iconSmallUp'/></li><li><span class='minutes iconSmall iconSmallDown'/></li></ul>");
		//append to body
		$("body").append(div);
		
		//add series of event that will help to keep focus on helper or input for as long as there is at least one component that has focus
		$(selector).focus(function(e){
			$(this).addClass("focus");	
		});
		$(selector).blur(function(e){
			$(selector).removeClass("focus");
			if(!$(div).hasClass("focus")){
				$(div).remove();
				if(callback){
					callback($(selector).html());
				}
			}
		});
		
		$(div).blur(function(e){
			$(div).removeClass("focus");
			if(!$(selector).hasClass("focus")){
				$(div).remove();
				if(callback){
					callback($(selector).html());
				}
			}
		});
		
		$(div).mousedown(function(){
			$(div).addClass("focus");
		});

		//add events for changeing hours and minutes
		$("span.iconSmallUp, span.iconSmallDown").click(function(e){
			//get current time
			var hh = ($(selector).val().split(":")[0])*1;
			var mm = ($(selector).val().split(":")[1])*1;
			if(!hh){hh == 0}
			if(!mm){mm == 0}
			//set time depending on witch button was clicked
			if($(e.target).hasClass("hours")){
				if($(e.target).hasClass("iconSmallUp")){
					hh = (hh+1 <= 24)?hh+1 : 0;
				}else{
					hh = (hh-1 >= 0)?hh-1 : 24;
				}
			}else{
				if($(e.target).hasClass("iconSmallUp")){
					mm = (mm+5 <= 59)?mm+5 : 0;
				}else{
					mm = (mm-5 >= 0)?mm-5 : 55;
				}
			}
			//add time back to input field
			$(selector).val(hh+":" + ((mm < 9)?"0"+mm : mm));
		});
	});
}

/*
 * function create dropdown menu at the bottom or top of current element
 * with provided element list
 * 
 * @param selector - selector for element that will need dropdown
 * @param list     - list of elements to shose from
 */
function dropDown(selector, data, callback){
	// //**($(selector));
	// //**(data);
	$(selector).click(function(e){
		if($("div.dropdown").length>0){
			return;
		}
		
		var div = document.createElement("div");
		$(div).addClass("dropdown borderBL borderBR");
		// $(div).attr("tabindex", 10);
		
		$(div).append("<ul class='dropdownList'>" + createArrayOfLi(data) + "</ul>");
		$("body").append(div);
		$("li", div).click(function(e){
			if(callback){
				callback($(e.target).html());
			}
			$(selector).html($(e.target).html());
			focus("destroy");
			$(div).remove();
		});
		
		//put focus on dropdown
		focus(div, function(){
			$(div).remove();
			focus("destroy");
		});
		// $(div).focus();
		// $(div).blur(function(e){
			// // $(div).remove();
		// });
		
		//**($(selector).offset().top, $(selector).height());
		$(div).position({
			of : selector,
			my : "left top",
			at : "left bottom"
		});
	});
}

/*
 * function clears left side witch is used for editing
 */
function clearEditor(){
	$($("div.details").children()).remove();
	$("div.details").empty();
}

/*
 * function showss whatever text information you want in taskbar
 * 
 * @param info - text data to show
 */
function showInformation(info, time){
	//set default time of 5 seconds if not set
	time = (time)?time: 5;
	//hide information div
	$("div.information").hide();
	// position information div
	var w = $("div.wbook").width() - ($("ul.headerIcons").width()*2.5);
	var h = 20;
	// $("div.information").addClass("shadow");
	// $("div.information").css({ 
		// "width"    : w, 
		// "height"   : h,
	// });
	//set html and display 
	$("div.information").html(info);
	$("div.information").fadeIn();
	setTimeout(function(){
		$("div.information").fadeOut();	
	}, time*1000);
}


/*
 * Shows popup with given data (used for making new categories 
 * and showing messages)
 * 
 * @param data - data to be put into popup or action to be executed
 */
function popup(data){
	if(typeof data == "string" && data == "destroy"){
		$("div.popup").remove();
		$("div.dimmer").remove();
		return;
	}
	var dimmer = document.createElement("div");
	var popup = document.createElement("div");
	
	$(dimmer).addClass("dimmer");
	$(popup).addClass("popup shadow");	
	
	$(popup).html(data);
	
	$(dimmer).append(popup);
	$("body").append(dimmer);
	$(popup).css({
		"position" : "absolute",
		"top" : "50%",
		"left" : "50%",
		"margin-left" : -$(popup).width()/2+"px",
		"margin-top" : -$(popup).height()/2+"px"
	});
	$(dimmer).click(function(e){
		if(e.target == dimmer){
			$([dimmer, popup]).remove();
		}
	});
}

/*
 * returns string of elements wrapped in <li> tag
 * Elements are taken from input array of data
 */
function createArrayOfLi(data){
	var s = "";
	if(data== null || data.length == 0){return;}
	for(var i = 0; i < data.length; i++){
		s += "<li>"+ data[i] +"</li>";
	}
	return s;
}



/************************************************************************
 * AJAX REQUESTS
 *
 * This functions serve for getting and posting data on server
 * Here are url-s that will be used for sending and receving 
 * information about 
 * URLs:
 * 	/ajax/getCategories.php
 *  /ajax/getAllTasks.php?
 *	/ajax/getCategoryTasks.php?cid=xxx
 *  /ajax/getTask.php?tid=xxx
 *  /ajax/getSettings.php
 *  /ajax/addCategory.php [JSON]
 *  /ajax/addTask.php [JSON]
 *  /ajax/editTask.php [JSON]
 *  /ajax/editCategory.php [JSON]
 *  /ajax/removeTask.php?tid=xxx
 *  /ajax/removeCategory.php?tid=xxx
 *  /ajax/editSettings.php [JSON]
 ************************************************************************/	
/*
 * gets global data regarding possible values for input fields and dropdowns etc.
 */

function ajaxGetGlobals(){
	$.ajax({
		type    : "GET",
		url 	: "../ajax/getGlobals.php",
		success : function(json){
			parseGlobals(json); 
		}
	});
}

/*
 * function gets user categories and displays them on right side
 * 
 * @param asnc - asynhronious ajax
 * @param  
 */
function ajaxGetCategories(async, type){
	$.ajax({
		url 	: "../ajax/getCategories.php",
		async   : (async)?async : true,
		data 	: (global.pastTasks)?{history :  true}:{},
		success : function(json){
			if(type == null || type == "show"){
				showCategories(json);
			}else if(type != null || type == "edit"){
				parseResponseEditCategories(json);
			}
		}
	});
}

/*
 * get summary of tasks in near future in format
 */
function ajaxGetAllTasks(){
	$.ajax({
		url 	: "../ajax/getAllTasks.php",
		data 	: {},
		success : function(json){
			showAllTasks(json);
		}
	});
}


 /*
  * Function gets category tasks for category specified by cId
  * function should get JSON object in format
  * 
  * @param cId - category ID for witch we want the tasks
  */
function ajaxGetCategoryTasks(cId){
	$.ajax({
		type    : "GET",
		url 	: "../ajax/getCategoryTasks.php",
		data 	: {
			history : global.pastTasks,
			cid : cId
		},
		success : function(json){
			showCategoryTasks(json);
		}
	});
}

/*
 * function gets data about particular task from server
 * 
 * @param tId - task ID for witch you want information 
 */
function ajaxGetTaskDetails(tId){
	$.ajax({
		type    : "GET",
		url 	: "../ajax/getTask.php",
		data 	: {
			tid : tId
		},
		success : function(json){
			showTaskDetails(json);
		}
	});
}

/*
 * function sends data about newly created task to server
 * 
 * @param data - data about new task that will be send to server
 */
function ajaxAddTask(data){
	$.ajax({
		type    : "POST",
		url 	: "../ajax/addTask.php",
		data 	: data,
		success : function(json){
			parseResponseAddTask(json);
		}
	});
}

/*
 * function sends newly created category data to server
 * And before this, category is examined to be unique not equal to others
 * 
 * @param data - data about new category that will be send to server 
 */ 
function ajaxAddCategory(data){
	$.ajax({
		type    : "POST",
		url 	: "../ajax/addCategory.php",
		data 	: {
			name	  : data.name,
			color     : data.color,
			reminders : data.reminders
		},
		success : function(json){
			parseResponseAddCategory(json);
		}
	});
}

/*
 * function sends data about edited task to server
 * 
 * @param data - data about edited task 
 */
function ajaxEditTask(data){
	$.ajax({
		type    : "POST",
		url 	: "../ajax/editTask.php",
		data 	: data,
		success : function(json){
			parseResponseEditTask(json);
		}
	});
}

/*
 * function sends data about edited category to server
 *
 * @param data - data about edited category 
 */
function ajaxEditCategory(data){
	$.ajax({
		type    : "POST",
		url 	: "../ajax/editCategory.php",
		data 	: data,
		success : function(json){
			parseResponseEditCategory(json);
		}
	});
}

 
/*
 * function sends tid's of selected tasks
 * 
 * @param data - tid's of tasks to be deleted 
 */
function ajaxRemoveTasks(data){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/removeTasks.php",
		"data"    : data,
		"success" : function(json){
			parseResponseRemoveTasks(json);
		}
	});
}

/*
 * function sends cid of categories to be removed
 *
 * @param data - cid's of tasks to be removed 
 */
function ajaxRemoveCategories(data){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/removeCategory.php",
		"data"    : data,
		"success" : function(json){
			parseResponseRemoveCategories(json);
		}
	});
}

/*
 * function changes old password
 *
 * @param passOld 	  - old passwoed
 * @param passwordNew - new passwoed
 */
function ajaxChangePassword(passOld, passwordNew){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/changePassword.php",
		"data"    : {
			oldPassword : passOld,
			newPassword : passwordNew 
		},
		"success" : function(json){
			parseResponseChangePassword(json);
		}
	});
}

/*
 * function gets current settings for reminders
 */
function ajaxGetReminderSettings(){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/getRemindersSettings.php",
		"success" : function(json){
			parseResponseGetRemindersSettings(json);
		}
	});
}

/*
 * function changes reminder settings
 * 
 * @param data - new settings
 */
function ajaxChangeReminders(data){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/changeReminders.php",
		"data"    : data,
		"success" : function(json){
			parseResponseChangeReminders(json);
		}
	});
}

/*
 * function marks tasks as completed 
 * 
 * @param data - tid's of tasks to mark
 */
function ajaxMarkTasks(data){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/markTasks.php",
		"data"    : data,
		"success" : function(json){
			parseResponseMarkTasks(json);
		}
	});
}

function ajaxSearchTasks(data){
	$.ajax({
		"type"    : "POST",
		"url"     : "../ajax/searchTasks.php",
		"data"    : data,
		"success" : function(json){
			showSearchResults(json);
		}
	});
}

/************************************************************************
 * AJAX CALLBACK
 *
 * This functions are called when data ajax data is receved and needs to be
 * put somwhere
 ************************************************************************/

/*
 * puts global settings about avalible options data in global scope
 */
function parseGlobals(data){
	global.reminderTypes 	 = data.reminderTypes;
	global.categoryTypes     = data.categoryTypes;
	global.priorities		 = data.priorities;	
	global.taskReminders	 = data.taskReminders; 
	global.repeats		     = data.repeats;
}

/*
 * function that adds given categories to div
 * 
 * @param categories - categories in 2d array like so [["name", "property", "cId"...], ["name", "property"...], ...] 
 */
function showCategories(data){
	//global.currentView = "allTasks";
	//**("addingCategories");
	$("div.categories").empty();
	global.categories = [];
	var w = $("div.categories").width();
	var h = 120;
	//add category all to first place
	data.unshift(["All", "#571b27", "all"]);
	//add all categories from ajax
	for(var i = 0; i < data.length; i++){
		//Save each categoryId and name to global array for later access
		if(i != 0){
			global.categories.push([data[i][0], data[i][2]]);
		}
		//create div add its properties and append it to categories
		var div = document.createElement("div");
		$(div).html(data[i][0]);
		$(div).addClass("category");
		$(div).data("cid", data[i][2]);
		$(div).attr("id", "category" + data[i][2]);
		$(div).css({
			"z-index"			: data.length-i,
			"color"				: BlackOrWhite(data[i][1]),
			"background-color"  : data[i][1]  
		});
		$(div).click(function(e){
			selectCategory(e);
		});
		
		$("div.categories").append(div);
	}
	//add plus icon div for adding new category
	var div = document.createElement("div");
	$(div).html("<span class='icon iconPlus'></span>");
	$(div).addClass("category categoryAdd");
	$("div.categories").append(div);
	
	$(div).click(function(e){
		addNewCategory();
	});
	
	//add empty div for side edge enlargement
	var ext = document.createElement("div");
	$(ext).addClass("categoryEdge");
	$("div.categories").append(ext);
}


/*
 * function creates tables for tab "All"
 * 
 * @param data - ajax data that will be processed and put into tables
 */
function showAllTasks(data){
	global.currentView = "allTasks";
	//**("show all tasks");
	//first we must edit data by adding first column that has icons
	for(row in data){
		data[row].unshift("<span class='thick icon iconBorderBox'></span>");
	}
	//remove editor data
	clearEditor();
	
	//create title and table
	$("div.details").append("<span class='title'>Upcomming tasks</span><table id='todayTable' class='datatable'></table>");
	
	//fill tables
	datatable("taskAll", "#todayTable", data);
	//display proper icons for current taskView
	displayIcons("taskViewAll");
	//show proper icons
	displayIcons();
	//highlight all tab
	$(".selectedCategory").removeClass("selectedCategory");
	$("#categoryall").addClass("selectedCategory");
}	

/*
 * function creates table for category Specified in 
 * 
 * @param data - ajax data that will be processed and put into tables
 */
function showCategoryTasks(data){
	global.currentView = "categoryTasks";
	//**("show category tasks");
	//first we must edit data by adding first column that has icons
	var tasks 	 = data.tasks;
	var cid   	 = data.cid;
	var catName  = data.category;
	
	for(row in tasks){
		tasks[row].unshift("<span class='thick icon iconBorderBox'></span>");
	}
	//remove editor data
	clearEditor();
	
	//create title and table
	$("div.details").append("<input class='categoryName title' value='"+ catName +"'/><table id='todayTable' class='datatable'></table>");
	
	$("input.categoryName").change(function(e){
		ajaxEditCategory({
							"cid" : cid,
							"name" : $("input.categoryName").val()	
						});
		ajaxGetCategories();
		ajaxGetCategoryTasks(cid);
	});
	
	//fill tables
	datatable("tasksCategory", "#todayTable", tasks);
	//show proper icons
	displayIcons();
	//highlight current tab
	$(".selectedCategory").removeClass("selectedCategory");
	$("#category" + data.cid).addClass("selectedCategory");		
}



/*
 * function shows details about particular task
 * 
 * @param data - ajax data from witch we get the information
 */
function showTaskDetails(data){
	global.currentView = "taskDetails";
	//clear editor
	clearEditor();
	//check parameters
	if(!data.notes){
		data.notes = "";
	}
	
	//add title
	$("div.details").append("<input class='taskName title"+ ((data.completed)?" completed":"") +"' value='"+data.name+"'/>");
	//add notes area notesScrool serves for scrool notesWrapper for proper margin and notesEditor for editing text
	$("div.details").append("<div class='notesScroll'><div class='notesWrapper'><textarea class='notesEditor'>"+ data.notes +"</textarea></div></div>");
	//add automatic resizing of text area
	$("textarea.notesEditor").autoResize({
		"animate" : false
	});
	
	//add yellow paper title wrappers etc.
	$("div.details").append("<div class='taskDetails'><div class='title'>Task Details</div><div class='taskDetailsContent'></div></div>");
	//fill yellow div with data
	$("div.taskDetailsContent").append("<table class='taskDetailsTable'>" +
		"<tr><td>Start:</td><td class='date'><input id='detailsDateStart' value='" + data.start.split(" ")[0] + "'/><input id='detailsTimeStart' value='"+ data.start.split(" ")[1] +"'/></td><td>Category:</td><td><span id='detailsCategory'>" 	  + data.category 	 + "</span></td></tr>"+
		"<tr><td>End:</td><td class='date'><input id='detailsDateEnd' value='"    + data.end.split(" ")[0]   + "'/><input id='detailsTimeEnd' value='"  + data.end.split(" ")[1]   +"'/></td><td>Repeat:</td><td><span id='detailsRepeat'>"      + data.repeat 	 + "</span></td></tr>"+
		"<tr><td>Priority:</td><td><span id='detailsPriority'>"  + data.priority + "</span/<td><td>Repeat ends:</td><td><input id='detailsRepeatEnd' value='" + ((data.repeatEnds)?data.repeatEnds : "never")  + "'/></td></tr>"+
		"<tr><td>SMS Reminder:</td><td><span id='detailsReminderSMS'>" + data.reminderSMS + "</span</td><td>Email Reminder:</td><td><span id='detailsReminderEmail'>" + data.reminderEmail + "</span</td></tr>"+
	"</table>");
	//create datepickersa
	$("#detailsDateStart, #detailsDateEnd, #detailsRepeatEnd").datepicker({
		showAnim: null, 
		dateFormat : "d.m.yy",
		onClose : function(e,ui){
			console.log(e, ui);
		}
	});
	//for unknown reason datepicker is show and spread throught the bottom of the page so it must be hidden
	$("#ui-datepicker-div").hide();
	//create time pickers
	timePicker("#detailsTimeStart");
	timePicker("#detailsTimeEnd");
	
	var cat = [];
	for(var i = 0; i<global.categories.length;i++){
		cat.push(global.categories[i][0]);
	}
	//create dropdowns
	dropDown("#detailsPriority", global.priorities);
	dropDown("#detailsReminderSMS", global.taskReminders);
	dropDown("#detailsReminderEmail", global.taskReminders);
	dropDown("#detailsCategory", cat);
	dropDown("#detailsRepeat", 	 global.repeats);
	//show proper icons
	displayIcons();
	//select proper side category
	$(".selectedCategory").removeClass("selectedCategory");
	$("#category"+data.cid).addClass("selectedCategory");
	//save current task to global vars for later use when checking if two tasks are the same
	global.task = data;
}

/*
 * parses response when searching on server 
 * 
 * @param data - response
 */
function showSearchResults(data){
	if(!data || data.length == 0){
		showInformation("No results found.");
		return;
	}
	global.currentView = "searchView";
	//**("search");
	var tasks = data.tasks;
	
	for(row in tasks){
		tasks[row].unshift("<span class='thick icon iconBorderBox'></span>");
	}
	//remove editor data
	clearEditor();
	
	//create title and table
	$("div.details").append("<span class='title'>Search results</span><table id='searchTable' class='datatable'></table>");
	
	//fill tables
	datatable("tasksCategory", "#searchTable", tasks);
	//show proper icons
	displayIcons();
	//remove highlights
	$(".selectedCategory").removeClass("selectedCategory");
}

/*
 * function parses response from server witch tells weather the task was added 
 * to category or not
 * 
 * @param data - json response from server 
 */
function parseResponseAddTask(data){
	if(data && data.response == true){
		showInformation("New task added successfully.");
		//get current category from selected tab
		var cid = $("div.category.selectedCategory").data("cid");
		//refresh data from server for current category
		ajaxGetCategoryTasks(cid);
	}else{
		showInformation("There was a problem adding new task.");
		//do nothing
	}
}

/*
 * function parses response from server witch tells weather the category was added or not
 * 
 * @param data - json response from server 
 */
function parseResponseAddCategory(data){
	if(data && data.response == true){
		showInformation("New category added successfully.");
		//get current category from selected tab
		var cid = $("div.category.selectedCategory").data("cid");
		//refresh categories
		ajaxGetCategories();
	}else{
		showInformation("There was a problem adding new Category.");
		//do nothing
	}
}

/*
 * function parses response about removed category
 */
function parseResponseRemoveTasks(data){
	if(data && data.response == true){
		showInformation("Task(s) removed successfully.");
		//get current category from selected tab
		var cid = $("div.category.selectedCategory").data("cid");
		//refresh data from server for current category
		if(!cid || cid == "all"){
			ajaxGetAllTasks();
		}else{
			ajaxGetCategoryTasks(cid);
		}
	}else{
		showInformation("There was a problem with removing task.");
		//do nothing
	}
}

/*
 * function parses response from server witch tells weather the category was removed or not
 * 
 * @param data - json response from server 
 */
function parseResponseRemoveCategories(data){
	if(data && data.response == true){
		showInformation("Categories removed successfully.");
		//get current category from selected tab
		//refresh categories by triggering category tab
		$(".categorySettings").trigger("click");
	}else{
		showInformation("There was a problem removing a category.");
		//do nothing
	}
}

/*
 * function parses response from server witch tells weather the category was edited
 * @param data - json response from server 
 */
function parseResponseEditTask(data){
	if(data && data.response == true){
		showInformation("Changes saved successfully.");
		ajaxGetTaskDetails(global.task.tid);
	}else{
		showInformation("There was a problem editing task.");
		//do nothing
	}
}

/*
 * function parses response from server witch tells weather the category was edited
 * @param data - json response from server 
 */
function parseResponseEditCategory(data){
	if(data && data.response == true){
		// showInformation("Category removed successfully.");
		$(".categorySettings").trigger("click");
	}else{
		showInformation("There was a problem editing a category.");
		//do nothing
	}
}

/*
 * function parses response from server witch tells weather the tasks have been marked
 * 
 * @param data - json response from server 
 */
function parseResponseMarkTasks(data){
	if(data != null && data.response == true){
		showInformation("Tasks marked successfully.");
		var cid = $("div.category.selectedCategory").data("cid");
		if(!cid || cid == "all"){
			ajaxGetAllTasks();
		}else{
			ajaxGetCategoryTasks(cid);
		}
	}else{
		showInformation("There was a problem with marking tasks as completed.");
	}
}

/*
 * function parses response from server witch tells weather the password was changed successfuly
 * 
 * @param data - json response from server 
 */
function parseResponseChangePassword(data){
	if(data && data.response == true){
		showInformation("Password changed successfully.");
	}else{
		showInformation("Old password is incorrect.");
		
	}
}

/*
 * function parses response from server about category tasks
 * 
 * @param data - json response from server 
 */
function parseResponseEditCategories(data){
	for(row in data){
		data[row].unshift("<span class='thick icon iconBorderBox'></span>");
	}
	datatable("editCategories", "#categorySettings", data);
}


/*
 * function parses response from server about reminders and its settings
 * 
 * @param data - json response from server 
 */
function parseResponseChangeReminders(data){
	if(data && data.response == true){
		showInformation("Settings saved succesfully.");
	}else{
		showInformation("There was problem saving your settings.");
	}
}


/*
 * function parses response from server about category tasks
 * 
 * @param data - json response from server 
 */
function parseResponseGetRemindersSettings(data){
	if(data){
		if(!data.phone){
			data.phone = "";
		}
		if(!data.email){
			data.email = "";
		}
		$("#reminderSettings").html("<tr>" +
		 	"<td class='title' colspan='2'>Set your username and password for sms reminders.</td>" +
		 	"</tr>"+
		 	"<tr>"+
		 		"<td>Phone number:</td><td><input class='form' id='phoneNumberSMS' type='text' value='"+data.phone+"'/></td>"+
		 	"</tr>"+
		 	"<tr>"+
		 		"<td>Password:</td><td><input class='form' id='passwordSMS' type='password'/></td>"+
		 	"</tr>"+
		 	"<tr>" +
		 		"<td class='title' colspan='2'>Set default e-mail for e-mail reminders.</td>" +
		 	"</tr>"+
		 	"<tr>"+
		 		"<td>E-mail:</td><td><input class='form' id='emailReminders' type='text' value='"+data.email+"'/></td>"+
		 	"</tr>"+
		 	"<tr>"+
		 		"<td colspan='2' class='button'><input class='form button' id='changeReminders' type='button' value='Save changes'/></td>"+
		 	"</tr>"
		);
			
		$("#changeReminders").click(function(e){
			var phone = $("#phoneNumberSMS").val();
			var smsPass = $("#passwordSMS").val();
			var email = $("#emailReminders").val();
			
			if(phone.length == 0 || !phone.replace(/\s/g).match(/[0-9]{9}/)){
				showInformation("Wrong phone number");
				return;
			}
			if(smsPass.length == 0){
				showInformation("SMS password not set");
				return;
			}
			if(email.length == 0){
				showInformation("E-mail not set");
				return;
			}
			if(!email.match(/^([0-9a-zA-Z]([-\.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/)){
				showInformation("Email is not valid.");
				return;
			}
			ajaxChangeReminders({
				phone : phone,
				pass  : smsPass,
				email : email
			});
		});
	}else{
		showInformation("Error occurred on this page");
	}
}