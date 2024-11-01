jQuery(function($){
	"use script";
	
	$(".timeline-custom-js").each(function( index ) {
		var custom_css = $(this).attr("data-timeline-custom-css");
		$( custom_css ).appendTo( "head" );
	});
	
});	

var timelineHandler = function($scope, $) {
	$(".timeline-custom-js").each(function( index ) {
		var custom_css = $(this).attr("data-timeline-custom-css");
		$( custom_css ).appendTo( "head" );
	});
};	

jQuery(window).on("elementor/frontend/init", function() {
	"use strict";
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/timeline.default",
		 timelineHandler
    );
});	