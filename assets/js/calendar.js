var calTopCorr = 5;
jQuery(document).ready(function() {
    jQuery(".checking-container .ui-datepicker-trigger").click(function() {
        var currentForm = jQuery(this).parents('form:first').attr("id");
        currentForm = "#"+currentForm;
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                jQuery(currentForm+" .lastdate button.ui-datepicker-trigger").trigger("click");
                jQuery(currentForm+" .ui-datepicker-trigger").each(function() {
                    jQuery(this).addClass("activeclass");
                });
                jQuery(currentForm+" .checking-container .ui-datepicker-trigger").removeClass("activeclass");
                jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
                jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
                jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + calTopCorr + "px");
            }
        });
    })
    jQuery("input[name='form[CheckIn]']").focus(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                jQuery("input[name='form[CheckOut]']").focus();
            }
        });
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

    });
    jQuery(".ui-datepicker-trigger").click(function() {
        jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + calTopCorr + "px");
        jQuery(".ui-datepicker-trigger").each(function() {
            jQuery(this).removeClass("activeclass");
        });
        jQuery(this).addClass("activeclass");
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

    });
    jQuery("#ui-datepicker-div").click(function() {
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
    });
    setInterval(function() {
        if (jQuery("#ui-datepicker-div").is(":visible")) {
            jQuery("#ui-datepicker-div").css("max-width","500px");
            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
            if( jQuery(".ui-datepicker-trigger.activeclass").length){
				jQuery("#ui-datepicker-div").css("top", jQuery(".ui-datepicker-trigger.activeclass").offset().top + calTopCorr + "px");
			}
//            if (jQuery(".ui-datepicker-trigger").hasClass("activeclass") == false) {
//                jQuery(".ui-datepicker-trigger").addClass("activeclass");
//                jQuery(".checking-container button.ui-datepicker-trigger").removeClass("activeclass");
//            }
        } else {
            jQuery(".ui-datepicker-trigger").removeClass("activeclass");
        }
    }, 100);

    jQuery(".mod_bookingforsearch .flexalignend").hover(function(){
        jQuery(".ui-datepicker-trigger").click(function() {
            jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + calTopCorr + "px");
            jQuery(".ui-datepicker-trigger").each(function() {
                jQuery(this).removeClass("activeclass");
            });
            jQuery(this).addClass("activeclass");
            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

        });
    });
});