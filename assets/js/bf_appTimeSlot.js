
function initDatepickerTimeSlot() {
    jQuery(".ChkAvailibilityFromDateTimeSlot").datepicker({
        numberOfMonths: 1,
        defaultDate: "+0d",
        dateFormat: "dd/mm/yy",
        minDate: strAlternativeDateToSearch,
        maxDate: strEndDate,
        onSelect: function (date) {
            dateTimeSlotChanged(jQuery(this));
        },
        showOn: "button",
        beforeShowDay: function (date) {
            return enableSpecificDatesTimeSlot(date, 1, daysToEnableTimeSlot[jQuery(this).attr("data-resid")]);
        },
        buttonText: strbuttonTextTimeSlot,
        firstDay: 1,
		beforeShow: function( input, inst){
			jQuery(this).attr("disabled", true);
			jQuery(inst.dpDiv).addClass('bfi-calendar');
			jQuery(inst.dpDiv).attr('data-before',"");
			jQuery(inst.dpDiv).removeClass("bfi-checkin");
			jQuery(inst.dpDiv).removeClass("bfi-checkout");
			setTimeout(function() {
				jQuery("#ui-datepicker-div div.bfi-title").remove();
				jQuery("#ui-datepicker-div").prepend( "<div class=\"bfi-title\">Check-in</div>" );
			}, 1);

		}
    });


}
function dateTimeSlotChanged(obj){
    var currTr = jQuery("#bfimodaltimeslot");
	bookingfor.waitSimpleBlock(currTr);
	var currDate = obj.datepicker('getDate');
    var intDate = bookingfor.convertDateToInt(currDate);
    updateTimeSlotRange(intDate);
	obj.next().html(jQuery.datepicker.formatDate("D d M yy", currDate));
    jQuery(currTr).unblock();
}


function bfi_selecttimeslot(currEl){
    var currContainer = jQuery("#bfimodaltimeslot");
    var currDiv = jQuery(currEl).closest(".bfi-timeslot-change");
	var currFromDate =currContainer.find(".ChkAvailibilityFromDateTimeSlot").first();
	var currTimeSlotSelect = currContainer.find("#selectpickerTimeSlotRange option:selected");
	var currSelect = currTimeSlotSelect.text().split(" - ");
	var currTimeSlotId =currTimeSlotSelect.val();
    
	var resourceId = currEl.getAttribute("data-resid");
	var sourceId = currEl.getAttribute("data-sourceid");

    jQuery.unblockUI();
	updateTotalSelectable(jQuery(currEl));
	
	var curr =currContainer.find("#selectpickerTimeSlotRange");

	var mcurrFromDate = jQuery(currFromDate).datepicker( "getDate" );
	var fromDate = jQuery.datepicker.formatDate("yy-mm-dd", jQuery(currFromDate).datepicker( "getDate" ));
	var newValStart = new Date(fromDate + "T" + currSelect[0] + ":00Z" );
	var newValEnd = new Date(fromDate + "T" + currSelect[1] + ":00Z" );
	var diffMs = (newValEnd - newValStart);
	var duration =  Math.round(Math.floor((diffMs/1000)/60/60) * 100) / 100;

//	var currTr = jQuery('.bfi-timeslot[data-sourceid="'+sourceId+'"]');
    var currTr = jQuery(bfi_currTRselected).find(".bfi-timeslot");
	var currCheckin =currTr.find(".bfi-time-checkin").first();
	var currCheckinhours =currTr.find(".bfi-time-checkin-hours").first();
	var currCheckout =currTr.find(".bfi-time-checkout").first();
	var currCheckouthours =currTr.find(".bfi-time-checkout-hours").first();
	var currduration =currTr.find(".bfi-total-duration").first();
	currTr.attr("data-checkin",jQuery.datepicker.formatDate("yymmdd", mcurrFromDate));
	currTr.attr("data-checkin-ext",jQuery.datepicker.formatDate("dd/mm/yy", mcurrFromDate));
	currCheckin.html(jQuery.datepicker.formatDate("D d M yy", mcurrFromDate));
	currCheckinhours.html(currSelect[0]);
	currCheckout.html(jQuery.datepicker.formatDate("D d M yy", mcurrFromDate));
	currCheckouthours.html(currSelect[1]);
	currduration.html(duration);
	
	currTr.attr("data-timeslotid",currTimeSlotSelect.val());
	currTr.attr("data-timeslotstart",currTimeSlotSelect.attr("data-timeslotstart"));
	currTr.attr("data-timeslotend",currTimeSlotSelect.attr("data-timeslotend"));

	dialogTimeslot.dialog( "close" );

}

    function updateTimeSlotRange(currDate) {
        var slotToEnableTimeSlot = [];
        var currSel = jQuery('#selectpickerTimeSlotRange')
        .find('option')
        .remove()
        .end();
		var currProdId = currSel.attr("data-resid");
		var copyarray = jQuery.extend(true, [], daysToEnableTimeSlot[currProdId]);
		slotToEnableTimeSlot = jQuery.grep(copyarray, function (ts) {
			return ts.StartDate == currDate;
		});
		slotToEnableTimeSlot.sort(function (a, b) { return a.TimeSlotStart - b.TimeSlotStart });
		jQuery.each(slotToEnableTimeSlot, function (i, currTimeSlot) {
			var tmpDate = new Date();
			tmpDate.setHours(0,0,0,0);
			var newTmpDateStart = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeSlotStart));
			var newTmpDateEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeSlotEnd));
			var newValStart = bookingfor.pad(newTmpDateStart.getHours(),2) + ":" + bookingfor.pad(newTmpDateStart.getMinutes(),2);
			var newValEnd =  bookingfor.pad(newTmpDateEnd.getHours(),2) + ":" + bookingfor.pad(newTmpDateEnd.getMinutes(),2);                
			var currOpt = jQuery('<option>').text(newValStart + " - " + newValEnd).attr('value', currTimeSlot.ProductId);
			jQuery(currOpt).attr("data-startdate", currTimeSlot.StartDate);
			jQuery(currOpt).attr("data-timeslotstart", currTimeSlot.TimeSlotStart);
			jQuery(currOpt).attr("data-timeslotend", currTimeSlot.TimeSlotEnd);
			jQuery(currOpt).attr("data-availability", currTimeSlot.Availability);
			currSel.append(currOpt);
			currTimeSlotDisp[currTimeSlot.ProductId] = currTimeSlot.Availability;
		});
    }

    function updateTotalSelectable(currEl) {
		var currTr = currEl.closest("div.bfi-timeslot-change");
		var resid = currEl.data("resid");
		var rateplanid = currEl.data("rateplanid");
//		var currSel = jQuery("#ddlrooms-"+resid+"-"+rateplanid).first();
		
//		var sourceId = currEl.data("sourceid");
		var sourceId =  jQuery(currEl).attr("data-sourceid");

		var currSel = jQuery('.ddlrooms[data-sourceid="'+sourceId+'"]').first();

        var currentSelection = currSel.val();
        //debugger;
        jQuery(currSel)
        .find('option')
        .remove()
        .end();
        var currentTimeOpt = currTr.find(".selectpickerTimeSlotRange option:selected");
        var currentTime = currentTimeOpt.val();
		var maxSelectable = Math.min(bfi_MaxQtSelectable,currTimeSlotDisp[currentTime]);

        var correction = 1;
        if (jQuery(currSel).hasClass('ddlextras')) {
//           currentSelection = parseInt(currentSelection.split(":")[1]);
           for (var i = 0; i <= maxSelectable; i++) {
                //var opt = jQuery('<option>').text(i).attr('value', id + ":" + i + ":::" + currentTime + ":" + currentTimeOpt.attr("data-timeslotstart") + ":" + currentTimeOpt.attr("data-timeslotend") + ":" + currentTimeOpt.attr("data-startdate"));
				var opt = jQuery('<option>').text(i).attr('value', i);
//                if (currentSelection == i) { opt.attr("selected", "selected"); }
					if (i == 0) { opt.attr("selected", "selected"); }
                currSel.append(opt);
            }
        } else {
			jQuery.each(jQuery(".ddlrooms-" + resid), function(j, itm) {
				jQuery(itm).find('option').remove();
				jQuery(itm).attr("data-availability", currTimeSlotDisp[currentTime]);
				for (var i = 0; i <= maxSelectable; i++) {
					var opt = jQuery('<option>').text(i).attr('value', i);
					if (i == 0) { opt.attr("selected", "selected"); }
					jQuery(itm).append(opt);
				}
			});
			/*
            for (var i = 0; i <= maxSelectable; i++) {
                var opt = jQuery('<option>').text(i).attr('value', i);
                if (currentSelection == i) { opt.attr("selected", "selected"); }
                currSel.append(opt);
            }
			*/
        }

            UpdateQuote(); //set service price default value
			bfi_updateQuoteService();

	}

    function enableSpecificDatesTimeSlot(date, offset, enableDays) {
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var year = date.getFullYear();
        var copyarray = jQuery.extend(true, [], enableDays);
        var listDays = jQuery.map(copyarray, function (n, i) {
            return (n.StartDate);
        });
        listDays = jQuery.unique(listDays);
        var listDaysunique = listDays.filter(function (elem, index, self) {
            return index == self.indexOf(elem);
        })
        for (var i = 0; i < offset; i++)
            listDaysunique.pop();
        var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
        if (jQuery.inArray(Number(datereformat), listDaysunique) != -1) {
            return [true, 'greenDay'];
        }
        return [false, 'redDay'];
    }