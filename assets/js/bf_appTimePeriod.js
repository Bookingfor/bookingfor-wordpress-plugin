var timeloadedItems = {};

function initDatepickerTimePeriod() {
	var evntSelect = "change";
    jQuery('.selectpickerTimePeriodStart').on('change', function(){
        var optSelected = jQuery(this).find("option:selected");
        var selected = jQuery(this).attr("data-resid");
        var selIndx  = jQuery(this).prop('selectedIndex');
        var minstay = Number(jQuery(optSelected).attr("data-minstay"));
        var maxIndex = selIndx + Number(jQuery(optSelected).attr("data-maxstay"));
        selIndx += minstay - 1;
        var currTr = jQuery(this).closest("div.bfi-timeperiod-change");
        var selectpickerTimePeriodEnd = currTr.find('.selectpickerTimePeriodEnd').first();

        currTr.find('.selectpickerTimePeriodEnd option:lt(' + selIndx + ')').prop('disabled', true)
        currTr.find('.selectpickerTimePeriodEnd option:gt(' + selIndx + ')').prop('disabled', false);
        currTr.find('.selectpickerTimePeriodEnd option:gt(' + (maxIndex - 1) + ')').prop('disabled', true);
        var selEnd = selectpickerTimePeriodEnd.find('option').eq(selIndx).first();
        if(selEnd.length){
            selEnd.prop('selected', true);
            selectpickerTimePeriodEnd.trigger('change');
            selectpickerTimePeriodEnd.focus();
        }


    });
    
    var previous_selectedIndex;

    jQuery('.selectpickerTimePeriodEnd').on(evntSelect, function(e) {
        previous_selectedIndex = jQuery(this).prop('selectedIndex');
    }).change(function() {
        var selected = jQuery(this).find("option:selected").val();
        var currContainer = jQuery("#bfimodaltimeperiod");
        var optSelected = currContainer.find('.selectpickerTimePeriodStart option:selected');
        var maxstay = Number(jQuery(optSelected).attr("data-maxstay"));
        var PeriodStart_selectedIndex = currContainer.find('.selectpickerTimePeriodStart').first().prop('selectedIndex');
        var PeriodEnd_selectedIndex = jQuery(this).prop('selectedIndex');
        if((PeriodEnd_selectedIndex - PeriodStart_selectedIndex) > maxstay){
            currContainer.find('.selectpickerTimePeriodEnd option:eq(' + previous_selectedIndex + ')').first().prop('selected', true)
            return;
        }
        if (jQuery(this).prop('selectedIndex') < PeriodStart_selectedIndex) {
            currContainer.find('.selectpickerTimePeriodStart').trigger('change');
            return;
        }
        previous_selectedIndex = jQuery(this).prop('selectedIndex');
//        updateTotalSelectablePeriod(jQuery(this), timeloadedItems.hasOwnProperty(jQuery(this).attr("data-resid") + "-" + jQuery(this).attr("data-bindingproductid")) && timeloadedItems[jQuery(this).attr("data-resid") + "-" + jQuery(this).attr("data-bindingproductid")]);
    });

    jQuery(".ChkAvailibilityFromDateTimePeriod").datepicker({
        numberOfMonths: 1,
        defaultDate: "+0d",
        dateFormat: "dd/mm/yy",
        minDate: strAlternativeDateToSearch,
        maxDate: strEndDate,
        onSelect: function(date) {
            dateTimePeriodChanged(jQuery(this));
        },
        showOn: "button",
        beforeShowDay: function(date) {
            return enableSpecificDatesTimePeriod(date, 1, daysToEnableTimePeriod[jQuery(this).attr("data-resid")]);
        },
        buttonText: strbuttonTextTimePeriod,
        firstDay: 1,
		beforeShow: function(dateText, inst) { 
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

function enableSpecificDatesTimePeriod(date, offset, enableDays) {
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var year = date.getFullYear();
    var copyarray = jQuery.extend(true, [], enableDays);
    var listDays = jQuery.map(copyarray,function( n, i ) {
        return ( n.StartDate );
    });
    listDays = jQuery.unique( listDays );
    var listDaysunique = listDays.filter(function(elem, index, self) {
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

function dateTimePeriodChanged(obj){
    var currTr = jQuery("#bfimodaltimeperiod");
	bookingfor.waitSimpleWhiteBlock(currTr);
	var currProdId = obj.attr("data-resid");
	var currDate = obj.datepicker('getDate');
//	var maxDate = obj.datepicker("option", "maxDate");
//	var dateFormat = obj.datepicker("option", "dateFormat");
//	var currMaxDate = jQuery.datepicker.parseDate(dateFormat, maxDate );
//	var timeDiff = Math.abs(currMaxDate.getTime() - currDate.getTime());
//	var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; 
    var diffDays = 1;
	var intDate = bookingfor.convertDateToInt(currDate);
    updateTimePeriodRange(intDate, currProdId, obj,diffDays);
	obj.next().html(jQuery.datepicker.formatDate("D d M yy", currDate));
}

function updateTimePeriodRange(currDate,currProdId,obj,maxDays){
    var currTr = jQuery("#bfimodaltimeperiod");
    var curSelStart = currTr.find('.selectpickerTimePeriodStart').first()
    .find('option')
    .remove()
    .end();
    var curSelEnd = currTr.find('.selectpickerTimePeriodEnd').first()
    .find('option')
    .remove()
    .end();
    var jqxhr = jQuery.ajax({
        url: urlGetListCheckInDayPerTimes,
        type: "GET",
        dataType: "json",
        data: { resourceId: currProdId, fromDate: currDate,limitTotDays:maxDays }
    }).done(updateOptTimePeriodRange(curSelStart, curSelEnd));

}

var updateOptTimePeriodRange = function (curSelStart, curSelEnd) {
    return function (result, textStatus, jqXHR) {
        if (result) {
            if (result.length > 0) {
//                debugger;

                jQuery.each(result, function (i, currTimeSlot) {
					var newValStart = new Date(1,1,1);
					var tmpCorrTimeStart = bookingfor.pad(currTimeSlot.TimeMinStart, 6);
					newValStart.setHours(Number(tmpCorrTimeStart.substring(0, 2)),Number(tmpCorrTimeStart.substring(2, 4)),0,0);
					var newValEnd = new Date(1,1,1);
					var tmpCorrTimeEnd = bookingfor.pad(currTimeSlot.TimeMinEnd, 6);
					newValEnd.setHours(Number(tmpCorrTimeEnd.substring(0, 2)),Number(tmpCorrTimeEnd.substring(2, 4)),0,0);

                    var currOptStart = jQuery('<option>').text(bookingfor.pad(newValStart.getHours(), 2) + ":" + bookingfor.pad(newValStart.getMinutes(), 2)).attr('value', currTimeSlot.ProductId);
                    jQuery(currOptStart).attr("data-TimeMinStart", currTimeSlot.TimeMinStart);
                    jQuery(currOptStart).attr("data-availability", currTimeSlot.Availability);
                    jQuery(currOptStart).attr("data-minstay", currTimeSlot.MinStay);
                    jQuery(currOptStart).attr("data-maxstay", currTimeSlot.MaxStay);

                    var currOptEnd = jQuery('<option>').text(bookingfor.pad(newValEnd.getHours(), 2) + ":" + bookingfor.pad(newValEnd.getMinutes(), 2)).attr('value', currTimeSlot.ProductId);
                    jQuery(currOptEnd).attr("data-TimeMinEnd", currTimeSlot.TimeMinEnd);
                    jQuery(currOptStart).attr("class", "bfi-hourdenabled");
                    jQuery(currOptEnd).attr("class", "bfi-hourdenabled");
                    jQuery(currOptEnd).attr("data-availability", currTimeSlot.Availability);

                    if (currTimeSlot.Availability == 0) {
                        jQuery(currOptStart).attr("disabled", "disabled");
                        jQuery(currOptEnd).attr("disabled", "disabled");
                        jQuery(currOptStart).attr("class", "bfi-hourdisabled");
                        jQuery(currOptEnd).attr("class", "bfi-hourdisabled");
                    }
                    curSelStart.append(currOptStart);
                    curSelEnd.append(currOptEnd);

                });

                jQuery(curSelStart).trigger('change');
				jQuery(jQuery("#bfimodaltimeperiod")).unblock();

            }
        }
    };
};

function updateTotalSelectablePeriod(currEl, updateQuote) {
   var dialogDiv = currEl.closest("div.bfi-timeperiod-change");
// 	var currTr = jQuery(bfi_currTRselected).find(".bfi-timeperiod-change");
	var resid = currEl.data("resid");
    var rateplanid = currEl.data("rateplanid");
//	var currSel = jQuery("#ddlrooms-"+resid+"-"+rateplanid).first();
    var currSel = jQuery(bfi_currTRselected).find(".ddlrooms");
    var currentSelection = currSel.val();
   
    //debugger;
    jQuery(currSel)
    .find('option')
    .remove()
    .end();
    var isSelectable = true;
    var maxSelectable = 0;

    var selectStart = dialogDiv.find(".selectpickerTimePeriodStart").first();
    var selectEnd = dialogDiv.find(".selectpickerTimePeriodEnd").first();
    for (var i = selectStart.prop('selectedIndex'); i <= selectEnd.prop('selectedIndex'); i++) {
        var currOption = dialogDiv.find('.selectpickerTimePeriodStart option').eq(i);
        var currAvailability =  Number(jQuery(currOption).attr('data-availability') );
        if(currAvailability==0){
            isSelectable = false;
            break;
        }
        if (currAvailability < maxSelectable || i == selectStart.prop('selectedIndex')) {
            maxSelectable = currAvailability;
        }
    }
	var singleMaxSelectable = Math.min(bfi_MaxQtSelectable, maxSelectable);
    if(isSelectable){
        currSel.show();
//        jQuery("#btnBookNow").show();

//        if (jQuery(currSel).is('[class^="extrasselect"]')) {
//            var currentSelectedQt = parseInt(currentSelection.split(":")[1]);
//            var currFromDate = currTr.find(".ChkAvailibilityFromDateTimePeriod").first();
//            
//			var mcurrFromDate = bookingfor.convertDateToInt(jQuery(currFromDate).datepicker( "getDate" ));
//
//            
//            var fromDate = jQuery.datepicker.formatDate("yy-mm-dd", jQuery(currFromDate).datepicker( "getDate" ));
//			var currentTimeStart = selectStart.find("option:selected");
//            var currentTimeEnd = selectEnd.find("option:selected");
//            
//			
//			var newValStart = new Date(fromDate + "T" + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2:$3') + "Z" );
//			var newValEnd = new Date(fromDate + "T" + bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2:$3') + "Z" );
//			var diffMs = (newValEnd - newValStart);
//			var duration =  Math.floor((diffMs/1000)/60);
//
//
//            for (var i = parseInt(currSel.attr("data-minvalue")) ; i <= parseInt(currSel.attr("data-maxvalue")); i++) {
//                var opt = jQuery('<option>').text(i).attr('value', resid + ":" + i + ":" + mcurrFromDate + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6) + ":" + duration + "::::");
//                if (currentSelectedQt == i) { opt.attr("selected", "selected"); }
//                currSel.append(opt);
//            }
//            if (currentSelectedQt > 0 && updateQuote) {
//                quoteCalculatorServiceChanged(currSel, timeloadedItems[jQuery(currSel).attr("data-resid") + "-" + jQuery(currSel).attr("data-bindingproductid")]);
//            }
//            timeloadedItems[jQuery(currSel).attr("data-resid") + "-" + jQuery(currSel).attr("data-bindingproductid")] = true;
//        } else {
			if(jQuery(".ddlrooms-" + resid).first().hasClass("ddlrooms-indipendent")){
				jQuery.each(jQuery(".ddlrooms-" + resid), function(j, itm) {
					jQuery(itm).find('option').remove();
					jQuery(itm).attr("data-availability", maxSelectable);
					for (var i = 0; i <= singleMaxSelectable; i++) {
                var opt = jQuery('<option>').text(i).attr('value', i);
						if (i == 0) { opt.attr("selected", "selected"); }
						jQuery(itm).append(opt);
					}
				});
			} else {
				var currentSelectedQt = parseInt(currentSelection);
				if(currentSelectedQt == 0){currentSelectedQt = 1;}
				for (var i = 0; i <= maxSelectable; i++) {
					var opt = jQuery('<option>').text(i).attr('value', i);
                if (currentSelectedQt == i) { opt.attr("selected", "selected"); }
                currSel.append(opt);
            }
			}
//           bfi_quoteCalculatorServiceChanged(currSel);
//        }

    }else{
		
        currSel.hide();
//        jQuery("#btnBookNow").hide();
    }
//	UpdateQuote();
}
//function quoteCalculatorPeriodChanged(currEl, updateQuote) {
//    var currTr = currEl.closest("div.bfi-timeperiod-change");
//    var id = currEl.data("resid");
//
//    //debugger;
//    var currentTimeStart = currTr.find(".selectpickerTimePeriodStart option:selected").val();
//    var currentTimeEnd= currTr.find(".selectpickerTimePeriodEnd option:selected").val();
//}

function bfi_selecttimeperiod(currEl){
    var currContainer = jQuery("#bfimodaltimeperiod");
//    var currDiv = jQuery(currEl).closest(".bfi-timeperiod-change");
//    var currDiv = jQuery(bfi_currTRselected).find(".bfi-timeperiod-change");

	var currFromDate =currContainer.find(".ChkAvailibilityFromDateTimePeriod").first();
    var resourceId = currEl.getAttribute("data-resid");
    jQuery.unblockUI();
	
	updateTotalSelectablePeriod(jQuery(currEl), true);
	var currdDlrooms = jQuery(bfi_currTRselected).find(".ddlrooms").first();
	if(currdDlrooms.length==0){
		currdDlrooms = jQuery("#ddlrooms-" + resourceId + "-0");
	}
	getcompleterateplansstaybyidPerTime(resourceId, currdDlrooms);
	
//	if (currdDlrooms.hasClass("ddlrooms-indipendent")) // if is a extra...
//	{
//	}else{
//		bfi_quoteCalculatorServiceChanged(currdDlrooms);
//	}

	dialogTimeperiod.dialog( "close" );
}

function getcompleterateplansstaybyidPerTime(resourceId, currdDlrooms) {
    //debugger;
//    var currTr = jQuery('#bfi-timeperiod-'+resourceId);
    var currTr = jQuery(bfi_currTRselected).find(".bfi-timeperiod");
    
	currTr.find(".bfi-hide").removeClass("bfi-hide");
	
	var currContainer = jQuery("#bfimodaltimeperiod");


	var currFromDate =currContainer.find(".ChkAvailibilityFromDateTimePeriod").first();
    var currentTimeStart = currContainer.find(".selectpickerTimePeriodStart option:selected");
    var currentTimeEnd= currContainer.find(".selectpickerTimePeriodEnd option:selected");


	var currObjToLoock = jQuery(bfi_currTRselected).find("table"); //".bfi-table-resources";
	if(currObjToLoock.length==0){
		currObjToLoock = jQuery(bfi_currTRselected).closest("table");
	}
    bookingfor.waitSimpleWhiteBlock(currObjToLoock);

	var mcurrFromDate = jQuery(currFromDate).datepicker( "getDate" );
	var checkInTime = jQuery.datepicker.formatDate("yymmdd", mcurrFromDate) + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);

	var fromDate = jQuery.datepicker.formatDate("yy-mm-dd", jQuery(currFromDate).datepicker( "getDate" ));
	var newValStart = new Date(fromDate + "T" + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2:$3') + "Z" );
	var newValEnd = new Date(fromDate + "T" + bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2:$3') + "Z" );
	
	var diffMs = (newValEnd - newValStart);
	var duration =  Math.floor((diffMs/1000)/60);
	
	var currCheckin =currTr.find(".bfi-time-checkin").first();
	var currCheckinhours =currTr.find(".bfi-time-checkin-hours").first();
	var currCheckout =currTr.find(".bfi-time-checkout").first();
	var currCheckouthours =currTr.find(".bfi-time-checkout-hours").first();
	var currduration =currTr.find(".bfi-total-duration").first();
	currTr.attr("data-checkin",jQuery.datepicker.formatDate("yymmdd", mcurrFromDate));
	currTr.attr("data-checkintime",checkInTime);
	currTr.attr("data-timeminstart",bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6));
	currTr.attr("data-timeminend",bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6));
	currTr.attr("data-duration",duration);

	currCheckin.html(jQuery.datepicker.formatDate("D d M yy", mcurrFromDate));
	currCheckinhours.html(bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2') );
	currCheckout.html(jQuery.datepicker.formatDate("D d M yy", mcurrFromDate));
	currCheckouthours.html( bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6).replace(/(.{2})(.{2})(.{2})/,'$1:$2') );
	currduration.html(Math.round(duration/60 * 100) / 100);

	if (jQuery(".ddlrooms-" + resourceId).first().hasClass("ddlrooms-indipendent")) // if is a extra...
	{
		var searchModel = jQuery('#bfi-calculatorForm').serializeObject();
		var dataarray = jQuery('#bfi-calculatorForm').serializeArray();
		dataarray.push({name: 'resourceId', value: resourceId});
		dataarray.push({name: 'timeMinStart', value: currentTimeStart.attr("data-TimeMinStart")});
		dataarray.push({name: 'timeMinEnd', value: currentTimeEnd.attr("data-TimeMinEnd")});
		dataarray.push({name: 'CheckInTime', value: checkInTime});
		dataarray.push({name: 'searchModel', value: searchModel});

		dataarray.push({name: 'availabilitytype', value: 2});
		dataarray.push({name: 'duration', value: duration});

		var jqxhr = jQuery.ajax({
			url: urlGetCompleteRatePlansStay,
			type: "POST",
			dataType: "json",
			data : dataarray
		});

		jqxhr.done(function(result, textStatus, jqXHR)
		{
			if (result) {
				if(result.length > 0)
				{
					jQuery.each(result, function(i, st) {
					//debugger
						currStay = st.SuggestedStay;
						var currTrRateplan = jQuery("#data-id-" + resourceId + "-" + st.RatePlanId);
					var currDivPrice = currTrRateplan.find(".bfi-price");
					var currDivTotalPrice = currTrRateplan.find(".bfi-discounted-price");
					var currDivPercentDiscount = currTrRateplan.find(".bfi-percent-discount");
					var currSel = currTrRateplan.find(".ddlrooms");

					currDivPrice.html(bookingfor.number_format(currStay.DiscountedPrice, 2, '.', ''))
						.attr("data-value",currStay.DiscountedPrice)
						.removeClass("red-color");
					
						currSel.attr("data-baseprice",bookingfor.number_format(currStay.DiscountedPrice, 2, '.', '') );
						currSel.attr("data-basetotalprice",bookingfor.number_format(currStay.TotalPrice, 2, '.', '') );
						currSel.attr("data-price",bookingfor.priceFormat(currStay.DiscountedPrice, 2, '.', '') );
						currSel.attr("data-totalprice",bookingfor.priceFormat(currStay.TotalPrice, 2, '.', '') );

//					currSel.attr("data-price",currStay.DiscountedPrice)
//						.attr("data-totalprice",currStay.TotalPrice);

					currDivTotalPrice.hide();
					currDivPercentDiscount.hide();
					if (currStay.DiscountedPrice < currStay.TotalPrice) {
						currDivTotalPrice.html(bookingfor.number_format(currStay.TotalPrice, 2, '.', ''))
							.attr("data-value",currStay.TotalPrice)
							.show();
						currDivPrice.addClass("red-color");
						currDivTotalPrice.attr("rel", currStay.SimpleDiscountIds);
						currDivTotalPrice.find(".bfi-percent").html(currStay.VariationPercent);
					}

//					if (updateQuote) {
						UpdateQuote();
//					}
					});

				}
			}
		});


		jqxhr.always(function() {
			jQuery(currObjToLoock).unblock();
		});
	}else{
		bfi_quoteCalculatorServiceChanged(currdDlrooms);
	}

}