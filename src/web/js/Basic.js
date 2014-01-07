/************************************************************************
 * GLOBAL JavaScript
 *
 * This is JavaScript for all pages. It contains functions for sizing 
 content to users view,
 ************************************************************************/	


$(document).ready(function(){
	setInterval(function(){
		resize();
	}, 500);
});


/*
 * function check if content needs resizing and resizes it if needed
 */
function resize(){
	
	var defaultWidth = 800; //default width for todo list
	//check height
	// console.log($(window).width(),$(window).height());
	
	if($(window).height() - $("div.wbook").offset().top - 20 !=  $("div.wbook").height()){
		$("div.wbook").css({
			"height" : ($(window).height() - $("div.wbook").offset().top -20)
		});
		
		if($("div.editorWrapper").length == 1 && $("div.editorWrapper").width() != $("div.wbook").height()-$("div.wbookHeader").height()){
			$("div.editorWrapper").css({
				"height" : (($(window).height() - $("div.wbook").offset().top -20)-$("div.wbookHeader").height()),
			});
		}
	}
	//check width
	var w = $(window).width()-30
	if($(window).width()-30 <=  $("div.wbook").width()){
		$("div.wbook, div.stiches").css({
			"width" : w
		});

		$("div.details").css({
			"width" : w-$("div.categories").outerWidth()
		});
		
	}else{
		$("div.wbook, div.stiches").css({
			"width" : defaultWidth
		});
					
		$("div.details").css({
			"width" : defaultWidth-$("div.categories").outerWidth()
		});
	}
	//resize notes editor to proper size
	if($("div.taskDetails").length != 0){
		var h = $("div.wbook").height()-($("div.notesScroll").offset().top - $("div.wbook").offset().top) - $("div.taskDetails").height();
		// console.log(h); 
		$("div.notesScroll").css("height", h);
	}
}

/*
 * Function determins witch color(black or white) is better 
 * agains background color "clr"
 * 
 * @param clr - background color
 */
function BlackOrWhite(clr){
    (clr.length == 7)?clr = clr.substr(1, 6):"";
    var r = parseInt(clr.substr(0,2),16);
    var g = parseInt(clr.substr(2,2),16);
    var b = parseInt(clr.substr(4,2),16);
    var diff = ((r*299)+(g*587)+(b*114))/1000;
    return (diff >= 128) ? '#000000' : '#FFFFFF';
}
