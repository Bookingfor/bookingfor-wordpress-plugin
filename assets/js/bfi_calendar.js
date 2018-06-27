var calTopCorr = 5;
function bfiCalendarCheck() {
        if (jQuery("#ui-datepicker-div.bfi-calendar").is(":visible")) {
            jQuery("#ui-datepicker-div.bfi-calendar").css("max-width","500px");
            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
            if( jQuery(".ui-datepicker-trigger.activeclass").length){
				jQuery("#ui-datepicker-div.bfi-calendar").css("top", jQuery(".ui-datepicker-trigger.activeclass").offset().top + calTopCorr + "px");
			}
        } else {
            jQuery(".ui-datepicker-trigger").removeClass("activeclass");
        }
}

//jQuery(document).ready(function() {
//
//
////    jQuery(".ui-datepicker-trigger").click(function() {
////        jQuery("#ui-datepicker-div.bfi-calendar").css("top", jQuery(this).offset().top + calTopCorr + "px");
////        jQuery(".ui-datepicker-trigger").each(function() {
////            jQuery(this).removeClass("activeclass");
////        });
////        jQuery(this).addClass("activeclass");
////        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
////        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
////
////    });
////    jQuery("#ui-datepicker-div.bfi-calendar").click(function() {
////        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
////        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
////    });
//    setInterval(function() {
//        if (jQuery("#ui-datepicker-div.bfi-calendar").is(":visible")) {
//            jQuery("#ui-datepicker-div.bfi-calendar").css("max-width","500px");
//            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
//            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
//            if( jQuery(".ui-datepicker-trigger.activeclass").length){
//				jQuery("#ui-datepicker-div.bfi-calendar").css("top", jQuery(".ui-datepicker-trigger.activeclass").offset().top + calTopCorr + "px");
//			}
////            if (jQuery(".ui-datepicker-trigger").hasClass("activeclass") == false) {
////                jQuery(".ui-datepicker-trigger").addClass("activeclass");
////                jQuery(".checking-container button.ui-datepicker-trigger").removeClass("activeclass");
////            }
//        } else {
//            jQuery(".ui-datepicker-trigger").removeClass("activeclass");
//        }
//    }, 1);
//
//});