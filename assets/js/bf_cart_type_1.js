		var bfi_totalRooms = 0;
		var bfi_totalQuote = 0;
		var bfi_totalQuoteDiscount = 0;

		function UpdateQuote(isService) //multiresource
		{
			var totalRooms = 0;
			var totalQuote = 0;
			var totalQuoteDiscount = 0;
			var onlybookable = 0;
			
			jQuery(".bfi-book-now").hide();
			jQuery(".bfi-request-now").hide();
			jQuery(".bfi-btn-book-now").hide();

			jQuery("tr[id^=data-id-]").each(function(index,obj){
				var ddlroom = jQuery(obj).find(".ddlrooms");
				var nRoom = parseInt(ddlroom.val());
				var resId = jQuery(ddlroom).attr("data-resid");
				var discountRate = parseFloat(jQuery(ddlroom).attr("data-totalprice"));
				var rate = parseFloat(jQuery(ddlroom).attr("data-price"));
				totalRooms += nRoom;
				totalQuote += nRoom * rate;
				totalQuoteDiscount += nRoom * discountRate;
				jQuery(this).attr('IsSelected',(nRoom>0));
				if(nRoom>0){
					onlybookable = Number(jQuery(ddlroom).attr("data-isbookable")||0);
				}
			});

			bfi_totalRooms = totalRooms;
			bfi_totalQuote = totalQuote;
			bfi_totalQuoteDiscount = totalQuoteDiscount;

			jQuery(".bfi-resource-total span").html(bfi_totalRooms);
			jQuery(".bfi-price-total").html(bookingfor.number_format(bfi_totalQuote, 2, ',', '.') );
			jQuery(".bfi-discounted-price-total").html(bookingfor.number_format(bfi_totalQuoteDiscount, 2, ',', '.') );

			
			if(bfi_totalRooms > 0){
				jQuery(".bfi-book-now").show();
				if (onlybookable==1)
				{
					jQuery(".bfi-btn-book-now").show();
					jQuery(".bfi-request-now").hide();
				}else{
					jQuery(".bfi-btn-book-now").hide();
					jQuery(".bfi-request-now").show();
				}
			}

			if(bfi_totalQuoteDiscount <= bfi_totalQuote){
				jQuery(".bfi-discounted-price-total").hide();
				jQuery(".bfi-price-total").removeClass("bfi-red");
			}else{
				jQuery(".bfi-discounted-price-total").show();
				jQuery(".bfi-price-total").addClass("bfi-red");
			}

		}

        function ChangeVariation(obj) //multiresource
        {
            UpdateQuote(); //set service price default value

            var showServices = false;
            var noResources = 0
            jQuery(".ddlrooms-indipendent ").each(function(index,objDdl){
                var currResId = jQuery(this).attr('data-resid');
                var currRateplanId = jQuery(this).attr('data-ratePlanId');
				var currQtSelected = jQuery(this).val();
				if( currQtSelected > 0 && jQuery("#services-room-1-" + currResId + "-" + currRateplanId).length){
					
//					console.log("exist extras");
                    var firstResourceServices = jQuery("#services-room-1-" + currResId + "-" + currRateplanId);
                    var currTitle = firstResourceServices.find('.bfi-resname-extra').first().html();

                    for (var i = 1; i <= currQtSelected; i++) {

//                        jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).find('.bfi-resname-extra').first().html(currTitle + ' ' + i);
                        jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).find('.bfi-resname-extra').first().html((noResources+1) + ') ' + currTitle);
                        
						if(i!=jQuery(this).val() && jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).length ){

                            var nextservice = firstResourceServices.clone();

                            nextservice.attr('id',"services-room-" + (i + 1) + '-' + currResId + "-" + currRateplanId);
							nextservice.find(".bfi-timeslot").attr('data-sourceid',"services-room-" + (i + 1) + '-' + currResId + "-" + currRateplanId);
							nextservice.find(".ddlrooms").attr('data-sourceid',"services-room-" + (i + 1) + '-' + currResId + "-" + currRateplanId);

                            //nextservice.find('.ddlrooms').first();
                            nextservice.insertAfter("#services-room-" + i + '-' + currResId + "-" + currRateplanId);
                        }
                        else{

                        }
                        if(jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).length)
                        {
                            showServices = true;
                            jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).show();
//                            if(noResources == 0)//show price and rooms text in last td
//                            {
////                                jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).find('table').find('tr:first').find('th:last').html('Booking');
//                                jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).find('table').find('tr:eq(1)').find('td:last').find('div').show();
//                            }else{
//                                jQuery("#services-room-" + i + '-' + currResId + "-" + currRateplanId).find('table').find('tr:eq(1)').find('td:last').html("");
//							}
                            noResources++;

                        }
                    }
                }
            });

            if(showServices){
				jQuery(".bfi-menu-booking a:eq(1)").removeClass(" bfi-alternative3"); //set menu to "Extra service"
				jQuery(".bfi-table-resources").not( ".bfi-table-selectableprice" ).hide();
                jQuery(".bfi-hideonextra").hide();
                jQuery(".div-selectableprice").show();
				
				//
//                if (bfi_variable.bfi_eecenabled==1)
//                {
//					callAnalyticsEEc("addImpression", jQuery.makeArray(jQuery.map(jQuery(".ddlextras:visible"), function(svc, idx) {
//						return {
//							"id": "" + jQuery(svc).attr('data-priceid') + " - Service",
//							"category": "Services",
//							"name": jQuery(svc).attr('data-name'),
//							"brand": jQuery(svc).attr('data-brand'),
//							"variant": jQuery(svc).attr('data-resourcename'),
//							"position": idx
//						};
//					})), "list", "Services List");
//                }

                if (typeof daysToEnableTimeSlot !== 'undefined' && typeof strAlternativeDateToSearch !== 'undefined' && typeof initDatepickerTimeSlot !== 'undefined' && jQuery.isFunction(initDatepickerTimeSlot)) {
                    initDatepickerTimeSlot();
                }
                if (typeof daysToEnableTimePeriod !== 'undefined' && typeof initDatepickerTimePeriod !== 'undefined' && jQuery.isFunction(initDatepickerTimePeriod)) {
                    initDatepickerTimePeriod();
                }
				var currTotalExtras = jQuery(".totalextrasstay");
				if(!currTotalExtras.is(":visible")){
					var currTableVisible = jQuery(".bfi-table-selectableprice:visible").first();
					currTableVisible.find('tr:eq(1)').find('td:last').append(currTotalExtras.clone(true));
					currTotalExtras.remove();
				}
				UpdateQuote(); //set service price default value

				bfi_updateQuoteService();
            }
            else
            {
                bookingfor.BookNow();
            }
        }