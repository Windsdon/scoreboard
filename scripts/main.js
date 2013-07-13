var definitions = {
	wheelScrollAmount : 0.5
}

function searchSubmit(){
	alert("Hahahaha");
	return false;
}

$(document).ready(function(e) {
    $("a.keep").click(function(e) {
		var loc = $(this).attr("href");
		console.log(loc);
		window.history.pushState({page: loc}, "LOL", loc);
		return false;
    });
	
	$("#leftBarHolder")[0].onmousewheel = function(e){
		obj = $("#leftBarScroller");
		if(obj.is(":animated") || obj.is(".ui-draggable-dragging")){
			return;
		}
		if(e){
			obj.css("margin-top", "+="+(e.wheelDelta * definitions.wheelScrollAmount));
		}
		if(parseInt(obj.css("margin-top")) > 0){
			obj.animate({marginTop: 0}, 'fast');
			return;
		}
		if(parseInt(obj.css("margin-top")) + obj.height() < parseInt($("#leftBarHolder").height())){
			if(parseInt(obj.height()) > parseInt($("#leftBarHolder").height())){
				obj.animate({marginTop: (-parseInt(obj.height()) + parseInt($("#leftBarHolder").height())) + "px"}, 'fast');
				return;
			}else{
				obj.animate({marginTop: 0}, 'fast');
				return;
			}
		}
	}
	
	$("#leftBarScroller").draggable({ distance: 10, axis: "y", stop: function(e, obj){
		if(obj.helper.has(":animated")){
			return;
		}
		obj.helper.css("margin-top", "+=" + obj.position.top + "px");
		obj.helper.css("top", 0);
	}, drag: function(e, obj){
		if(parseInt(obj.helper.css("margin-top")) + obj.position.top > 0){
			obj.helper.css("margin-top", "+=" + obj.position.top + "px");
			obj.helper.css("top", 0);
			obj.helper.animate({marginTop: 0}, 'fast');
			return false;
		}
		
		if(parseInt(obj.helper.css("margin-top")) + obj.position.top + parseInt(obj.helper.height()) < parseInt($("#leftBarHolder").height())){
			obj.helper.css("margin-top", "+=" + obj.position.top + "px");
			obj.helper.css("top", 0);
			obj.helper.animate({marginTop: -parseInt(obj.helper.height()) + parseInt($("#leftBarHolder").height()) + "px"}, 'fast');
			console.log("stopping");
			return false;
		}
	}});
	
	$("#leftBarSearch").focus(function(e) {
        if(!$("#leftBarSearchText").hasClass("hidden")){
			$("#leftBarSearchText").addClass("hidden");
		}
    }).blur(function(e) {
        if(!$(this).val()){
			$("#leftBarSearchText").removeClass("hidden");
		}
    });
	
	$("#leftBarSearch").autocomplete({
		source: ["Default", "Destination", "General"],
		appendTo: "#leftBarScroller"
	});
	$("#leftBarSearchForm").submit(searchSubmit);
	$(".leftBarSearchButton").click(searchSubmit);
});