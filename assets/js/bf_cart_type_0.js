        function UpdateQuote(isService)
        {
            //jQuery("[id^='ddlrooms-']").each(function(index,objDdl){debugger
            //    var resId = jQuery(objDdl).attr('id').split('-').pop();

            //    if(jQuery(el).attr('id') != jQuery(objDdl).attr('id')){
            //        jQuery(objDdl).val(0);
            //        jQuery('#dvTotal-' + resId).hide();
            //    }
            //    else
            //        jQuery('#dvTotal-' + resId).show();
            //});

            jQuery("tr[id^=data-id-]").each(function(index,obj){
                var totalRooms = 0;
                var totalQuote = 0;
                var totalQuoteDiscount = 0;
                var ddlroom = jQuery(obj).find(".ddlrooms");
                var resId = jQuery(ddlroom).attr('id').split('-').pop();
//                var txtTotalForNights = '@Res.MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS';
                totalRooms += parseInt(ddlroom.val());

                var rate = jQuery(obj).find("span.bfi_merchantdetails-resourcelist-stay-total").attr("data-value");

                var discountRate = 0;
                if(jQuery(obj).find('td:eq(3)').find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").length > 0)
                {
                    discountRate = jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").attr("data-value");
                    totalQuoteDiscount += (parseInt(ddlroom.val()) * parseFloat(discountRate));
                }
                else
                    totalQuoteDiscount += (parseInt(ddlroom.val()) * parseFloat(rate));

                totalQuote += (parseInt(ddlroom.val()) * parseFloat(rate));

                if(discountRate > 0)
                {
                    jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").show();
                    jQuery(obj).find("span.bfi_merchantdetails-resourcelist-stay-total").addClass("red-color");
                    //jQuery(".totalQuoteDiscount").html("&euro;&nbsp;" + bookingfor.number_format(totalQuoteDiscount, 2, '.', ''));
                    //jQuery(".totalQuoteDiscountWithServices").html("&euro;&nbsp;" + bookingfor.number_format(totalQuoteDiscount, 2, '.', ''));
                }
                else
                    jQuery(obj).find("span.bfi_merchantdetails-resourcelist-stay-total").removeClass("red-color");

                if(ddlroom.val() > 0)
                {
                    if(!jQuery(obj).find(".resourceimage").find("span").hasClass("com_bookingforconnector_resourcelist-resourcename-blue"))
                    {
                        jQuery(obj).find(".resourceimage").find("span").addClass("com_bookingforconnector_resourcelist-resourcename-blue");
                        jQuery(obj).find(".resourceimage").find("span").removeClass("com_bookingforconnector_resourcelist-resourcename-grey");
                        jQuery(obj).find(".fa-check-circle").show();
                    }
//                    for(var i=1; i<=ddlroom.val(); i++){
//                        jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:eq(0)').find('th:eq(2)').html(txtTotalForNights.replace('{0}',@DateDiff));
//                    }
                }
                else
                {
                    jQuery(obj).find(".resourceimage").find("span").removeClass("com_bookingforconnector_resourcelist-resourcename-blue");
                    jQuery(obj).find(".resourceimage").find("span").addClass("com_bookingforconnector_resourcelist-resourcename-grey");
                    jQuery(obj).find(".fa-check-circle").hide();
                }

                jQuery("#data-id-"+resId+" .lblLodging span").html(totalRooms);
                jQuery("#data-id-"+resId+" .totalQuote").html(bookingfor.number_format(totalQuote, 2, ',', '.') );
                jQuery("#data-id-"+resId+" .totalQuoteDiscount").html(bookingfor.number_format(totalQuoteDiscount, 2, ',', '.') );


                if(totalRooms == 0)
                {
                    jQuery("#data-id-"+resId+" .book-now").addClass("hidden");
                }
                else{
                    jQuery("#data-id-"+resId+" .book-now").removeClass("hidden");
                }

                if(totalQuoteDiscount == 0 || totalQuoteDiscount == totalQuote){
                    jQuery("#data-id-"+resId+" .totalQuoteDiscount").addClass("hidden");
                    jQuery("#data-id-"+resId+" .totalQuote").removeClass("red-color");
                }
                else{
                    jQuery("#data-id-"+resId+" .totalQuoteDiscount").removeClass("hidden");
                    jQuery("#data-id-"+resId+" .totalQuote").addClass("red-color");
                }


                if(isService){
                    jQuery(".data-sel-id-"+resId+" .lblLodging span").html(totalRooms);
                    jQuery(".data-sel-id-"+resId+" .totalQuote").html(bookingfor.number_format(totalQuote, 2, ',', '.') );
                    jQuery(".data-sel-id-"+resId+" .totalQuoteDiscount").html(bookingfor.number_format(totalQuoteDiscount, 2, ',', '.') );
                    if(totalRooms == 0)
                    {
                        jQuery(".data-sel-id-"+resId+" .book-now").addClass("hidden");
                    }
                    else{
                        jQuery(".data-sel-id-"+resId+" .book-now").removeClass("hidden");
                    }

                    if(totalQuoteDiscount == 0 || totalQuoteDiscount == totalQuote){
                        jQuery(".data-sel-id-"+resId+" .totalQuoteDiscount").addClass("hidden");
                        jQuery(".data-sel-id-"+resId+" .totalQuote").removeClass("red-color");
                    }
                    else{
                        jQuery(".data-sel-id-"+resId+" .totalQuoteDiscount").removeClass("hidden");
                        jQuery(".data-sel-id-"+resId+" .totalQuote").addClass("red-color");
                    }
                }
            });
        }

        function ChangeVariation(obj)
        {
            var showServices = false;
            var noResources = 0;
            var objDdl = jQuery("#ddlrooms-" + jQuery(obj).attr('data-resid'));
            UpdateQuote(true); //set service price default value

            var resId = objDdl.attr('id').split("-").pop();
            if(objDdl.val() > 0 ){

                if(jQuery("#services-room-1-" + resId).length){
                    var title = jQuery("#services-room-1-" + resId).find('h5.titleform').first().html();
                    var firstResourceServices = jQuery("#services-room-1" + '-' + resId)[0].outerHTML;

                    for (var i = 1; i <= objDdl.val(); i++) {
                        jQuery("#services-room-" + i + '-' + resId).find('h5.titleform').first().html(title + ' ' + i);
                        if(i!=objDdl.val()){
                            var nextservice = jQuery(firstResourceServices);
                            nextservice.attr('id',"services-room-" + (i + 1) + '-' + resId);

                            //var totalextraselectid = nextservice.find("[class^='totalextrasselect-']").first().attr('class').split('-');
                            //nextservice.find("[class^='totalextrasselect-']").first().attr('class',totalextraselectid[0]+'-'+(i+1)+'-'+totalextraselectid[2]+'-'+totalextraselectid[3]);
                            nextservice.find("[class^='totalextrasselect-']").each(function(){
                                var totalextraselectid = jQuery(this).attr('class').split('-');
                                jQuery(this).attr('class',totalextraselectid[0]+'-'+(i+1)+'-'+totalextraselectid[2]+'-'+totalextraselectid[3]);
                            });


                            //var extrasselectclass = nextservice.find("[class^='extrasselect-']").first().attr('class').split('-');
                            //nextservice.find("[class^='extrasselect-']").first().attr('class',extrasselectclass[0]+'-'+(i+1)+'-'+extrasselectclass[2]);

                            nextservice.find("[class^='extrasselect-']").each(function(){
                                var extrasselectclass = jQuery(this).attr('class').split('-');
                                jQuery(this).attr('class',extrasselectclass[0]+'-'+(i+1)+'-'+extrasselectclass[2] + ' ddlrooms inputmini');
                            });

                            nextservice.find("[id^='extras-']").each(function(){
                                var extrasselectid = jQuery(this).attr('id').split('-');
                                jQuery(this).attr('id',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                                jQuery(this).attr('name',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                            });

                            var selectablenameclass = nextservice.find("[class^='name-selectableprice-']").first().attr('class').replace('name-selectableprice-1-', 'name-selectableprice-'+(i+1) + '-');
                            nextservice.find("[class^='name-selectableprice-']").first().attr('class',selectablenameclass);
                            // update id for all datapicker
                            var currCounterId = 0;
                            nextservice.find(".ChkAvailibilityFromDateTimePeriod,.ChkAvailibilityFromDateTimeSlot").each(function(){
                                jQuery(this).removeClass("hasDatepicker");
                                jQuery(this).next("button").remove();
                                currCounterId +=1;
                                jQuery(this).attr("id",  jQuery(this).attr("id") + '-' + (i + 1) + '-' +currCounterId);
                            });
//                            nextservice.find("select.selectpickerTimeSlotRange,select.selectpickerTimePeriodStart,select.selectpickerTimePeriodEnd").each(function(){
//                                //var newSelectpicker = jQuery(this).clone(true).append();
////								if(bookingfor.bsVersion ==3){
////									jQuery(this).closest("td").append(jQuery(this).clone(true));
////								}
//                                jQuery(this).closest("div").remove();
//                            });
                            nextservice.insertAfter("#services-room-" + i + '-' + resId);
                        }

                        if(jQuery("#services-room-" + i + '-' + resId).length)
                        {
                            showServices = true;
                            jQuery("#services-room-" + i + '-' + resId).show();
                            if(noResources == 0)//show price and rooms text in last td
                            {
                                jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:first').find('th:last').html('Booking');
                                jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:eq(1)').find('td:eq(4)').find('div').show();
                            }
                            noResources++;
                        }
                    }

                }

                jQuery('#data-id-' + resId).attr('IsSelected','true');
            }

            if(showServices){
                jQuery(".bfi-table-resources").hide();
                jQuery(".bfi-hideonextra").hide();
                jQuery(".div-selectableprice").show();
                if (typeof daysToEnableTimeSlot !== 'undefined' && typeof strAlternativeDateToSearch !== 'undefined' && typeof initDatepickerTimeSlot !== 'undefined' && jQuery.isFunction(initDatepickerTimeSlot)) {
                    initDatepickerTimeSlot();
                }
                if (typeof daysToEnableTimePeriod !== 'undefined' && typeof initDatepickerTimePeriod !== 'undefined' && jQuery.isFunction(initDatepickerTimePeriod)) {
                    initDatepickerTimePeriod();
					jQuery(".ChkAvailibilityFromDateTimePeriod.extraprice").each(function(){
						var currDateTimePeriod = jQuery(this).datepicker("getDate");
						updateTimePeriodRange(jQuery.datepicker.formatDate("yymmdd", currDateTimePeriod), jQuery(this).attr("data-id"), jQuery(this));
					});
                }
            }
            else
            {
                BookNow();
            }
        }