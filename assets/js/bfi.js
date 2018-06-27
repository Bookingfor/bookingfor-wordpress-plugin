var bookingfor = new function() {
    this.version = "3.2.5";
	this.bsVersion = ( typeof jQuery.fn.typeahead !== 'undefined' ? 2 : 3 );
    this.offersLoaded = [];
    this.adsBlocked = false;
    this.adsBlockedChecked = false;
    this.loadedholydays = false;
    this.holydays = "";
    this.holydaysTitle = "";

	this.getDiscountAjaxInformations = function (discountId, hasRateplans) {
        var query = "discountId=" + discountId + "&hasRateplans=" + hasRateplans + "&language=en-gb&task=getDiscountDetails";
        jQuery.getJSON(bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + query, function(data) {

          var name = getXmlLanguage(data.Name, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);;
          name = nl2br(jQuery("<p>" + name + "</p>").text());
          jQuery("#divoffersTitle" + discountId).html(name);

          var descr = getXmlLanguage(data.Description, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);;
          descr = nl2br(jQuery("<p>" + descr + "</p>").text());
          jQuery("#divoffersDescr" + discountId).html(descr);
          jQuery("#divoffersDescr" + discountId).removeClass("com_bookingforconnector_loading");
        });

      };

    this.getRateplanAjaxInformations = function (rateplanId) {
        var query = "rateplanId=" + rateplanId + "&language=en-gb&task=getRateplanDetails";
        jQuery.getJSON(bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + query, function(data) {

          var name = getXmlLanguage(data.Name, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);;
          name = nl2br(jQuery("<p>" + name + "</p>").text());
          jQuery("#divrateplanTitle" + rateplanId).html(name);

          var descr = getXmlLanguage(data.Description, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);;
          descr = nl2br(jQuery("<p>" + descr + "</p>").text());
          jQuery("#divrateplanDescr" + rateplanId).html(descr);
          jQuery("#divrateplanDescr" + rateplanId).removeClass("com_bookingforconnector_loading");
        });

      };

    this.getData = function (urlCheck, query, elem, name, act) {
		query += '&simple=1';		
		if (typeof(ga) !== 'undefined' && !bookingfor.adsBlocked ) {
			ga('send', 'event', 'Bookingfor', act, name);
			ga(function(){
				jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
						jQuery(elem).parent().html(data);
						jQuery(elem).remove();
				});
			});
		}else{
			jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
					jQuery(elem).parent().html(data);
					jQuery(elem).remove();
			});
		}
	};

    this.getXmlLanguage = function (value, cultureCode, defaultcultureCode) {
		var ret = value;
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}
		if(value && value.indexOf("<languages>")>-1){
			var xmlValue = jQuery.parseXML(value);
			var jsonValue = jQuery.xml2json(xmlValue);
			try {
				if (jsonValue.language.hasOwnProperty("code")) {
					ret = (jsonValue.language.hasOwnProperty("text") ? jsonValue.language.text : "") ;
				} else {
					var defaultValue = '';
					jQuery.each(jsonValue.language, function (i, lang) {
						if (lang.code === cultureCode)
						{
							ret = (lang.hasOwnProperty("text") ? lang.text : "") ;
						}
						if (lang.code === defaultcultureCode)
						{
							defaultValue = (lang.hasOwnProperty("text") ? lang.text : "") ;
						}

					});
					if(ret===''){
						ret = defaultValue;
					}

				}
			}
			catch (e) {
			}
		}
		return ret;
	};

	this.make_slug = function ( str )
	{
		str = str.toLowerCase();
		str = str.replace(/\&+/g, 'and');
		str = str.replace(/[^a-z0-9]+/g, '-');
		str = str.replace(/^-|-$/g, '');
		return str;
	};

	this.nl2br = function (str, is_xhtml) {   
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
	};

	this.nomore1br = function (str) {   
		return (str + '').replace(new RegExp('(\n){2,}', 'gim') , '\n');
	};

	this.stripbbcode = function (str, is_xhtml) {   
		str = (str + '').replace(/\[(\w+)[^\]]*](.*?)\[\/\1]/g, '$2');
		str = str.replace(/(\[size\=[\d]\]|\[\/size\])+/g, '');
		str = str.replace(/(\[ul\]|\[\/ul\]|\[ol\]|\[\/ol\])+/g, '');
		return str;
	};

	this.parseODataDate = function(date) {
		return new Date(parseInt(date.match(/\/Date\(([0-9]+)(?:.*)\)\//)[1]));
	};

	this.priceFormat = function (number, decimals, dec_point, thousands_sep) {   
	  number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
	  var number = !isFinite(+number) ? 0 : +number,
		//conversion valuta;
		defaultcurrency = bfi_variable.defaultCurrency;//  bfi_get_defaultCurrency();
		currentcurrency = bfi_variable.currentCurrency;//  bfi_get_currentCurrency();

		if(defaultcurrency!=currentcurrency){
			//try to convert
			currencyExchanges =  bfi_variable.CurrencyExchanges;// BFCHelper::getCurrencyExchanges();
			if (currencyExchanges.hasOwnProperty(currentcurrency)) {
				number = number*currencyExchanges[currentcurrency];
			}
		}
		return bookingfor.number_format(number, decimals, dec_point, thousands_sep);
	};

	this.number_format = function (number, decimals, dec_point, thousands_sep) {
	  //  discuss at: http://phpjs.org/functions/number_format/
	  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: davook
	  // improved by: Brett Zamir (http://brett-zamir.me)
	  // improved by: Brett Zamir (http://brett-zamir.me)
	  // improved by: Theriault
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // bugfixed by: Michael White (http://getsprink.com)
	  // bugfixed by: Benjamin Lupton
	  // bugfixed by: Allan Jensen (http://www.winternet.no)
	  // bugfixed by: Howard Yeend
	  // bugfixed by: Diogo Resende
	  // bugfixed by: Rival
	  // bugfixed by: Brett Zamir (http://brett-zamir.me)
	  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  //  revised by: Luke Smith (http://lucassmith.name)
	  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
	  //    input by: Jay Klehr
	  //    input by: Amir Habibi (http://www.residence-mixte.com/)
	  //    input by: Amirouche
	  //   example 1: number_format(1234.56);
	  //   returns 1: '1,235'
	  //   example 2: number_format(1234.56, 2, ',', ' ');
	  //   returns 2: '1 234,56'
	  //   example 3: number_format(1234.5678, 2, '.', '');
	  //   returns 3: '1234.57'
	  //   example 4: number_format(67, 2, ',', '.');
	  //   returns 4: '67,00'
	  //   example 5: number_format(1000);
	  //   returns 5: '1,000'
	  //   example 6: number_format(67.311, 2);
	  //   returns 6: '67.31'
	  //   example 7: number_format(1000.55, 1);
	  //   returns 7: '1,000.6'
	  //   example 8: number_format(67000, 5, ',', '.');
	  //   returns 8: '67.000,00000'
	  //   example 9: number_format(0.9, 0);
	  //   returns 9: '1'
	  //  example 10: number_format('1.20', 2);
	  //  returns 10: '1.20'
	  //  example 11: number_format('1.20', 4);
	  //  returns 11: '1.2000'
	  //  example 12: number_format('1.2000', 3);
	  //  returns 12: '1.200'
	  //  example 13: number_format('1 000,50', 2, '.', ' ');
	  //  returns 13: '100 050.00'
	  //  example 14: number_format(1e-8, 8, '.', '');
	  //  returns 14: '0.00000001'

	  number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
	  var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
		  var k = Math.pow(10, prec);
		  return '' + (Math.round(n * k) / k)
			.toFixed(prec);
		};
	  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
	  if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	  }
	  if ((s[1] || '')
		.length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1)
		  .join('0');
	  }
	  return s.join(dec);
	};

	this.getUrlParameter = function (name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	};

	this.updateQueryStringParameter = function (uri, key, value) {
	  return uri
        .replace(new RegExp("([?&]"+key+"(?=[=&#]|$)[^#&]*|(?=#|$))"), "&"+key+"="+encodeURIComponent(value))
        .replace(/^([^?&]+)&/, "$1?");
//	  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
//	  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
//	  if (uri.match(re)) {
//		return uri.replace(re, '$1' + key + "=" + value + '$2');
//	  }
//	  else {
//		return uri + separator + key + "=" + value;
//	  }
	};



	this.waitBlockUI = function (msg1 ,msg2, img1){
	msg1 = msg1 ? msg1 : "";
	msg2 = msg2 ? msg2 : "";
	var msggeneral = jQuery.trim(msg1).length && jQuery.trim(msg2).length ? msg1 + '<br />' + msg2 : (jQuery.trim(msg1).length ? msg1 : msg2);
	jQuery.blockUI({
		message: (jQuery.trim(msggeneral).length ? '<h1 class="bfi-wait">'+msggeneral+'</h1><br />' : "") + '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		});
	};

	this.waitBlock = function (msg1 ,msg2, obj){
	obj.block({
		message: '<h1 class="bfi-wait">'+msg1+'<br />'+msg2+'</h1><br /><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
//		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		overlayCSS: {backgroundColor: '#1D668B', opacity: 0}  
		});
	};

	this.waitSimpleBlock = function (obj){
		obj.block({
			message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>',
			css: {border: '2px solid #1D668B', padding: '10px 20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
			overlayCSS: {backgroundColor: '#1D668B', opacity: .7}
		});
	};

	this.waitSimpleWhiteBlock = function (obj){
		obj.block({
			message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>',
			css: {border: 'none', width: '100%'},
			overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
		});
	};

	this.dateAdd = function(date, interval, units) {
		var ret = new Date(date); //don't change original date
		switch(interval.toLowerCase()) {
			case 'year'   :  ret.setFullYear(ret.getFullYear() + units);  break;
			case 'quarter':  ret.setMonth(ret.getMonth() + 3*units);  break;
			case 'month'  :  ret.setMonth(ret.getMonth() + units);  break;
			case 'week'   :  ret.setDate(ret.getDate() + 7*units);  break;
			case 'day'    :  ret.setDate(ret.getDate() + units);  break;
			case 'hour'   :  ret.setTime(ret.getTime() + units*3600000);  break;
			case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
			case 'second' :  ret.setTime(ret.getTime() + units*1000);  break;
			default       :  ret = undefined;  break;
		}
		return ret;
	}

	this.convertDateToInt = function(currDate) {
		var month = currDate.getMonth() + 1;
		var day = currDate.getDate();
		var year = currDate.getFullYear();
		var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
		var intDate = Number(datereformat);
		return (intDate)
	}
	this.convertDateToIta = function(currDate) {
		var month = currDate.getMonth() + 1;
		var day = currDate.getDate();
		var year = currDate.getFullYear();
		var datereformat = bookingfor.pad(day,2) + '/' + bookingfor.pad(month,2) + '/' + year;
		return (datereformat)
	}

	this.pad = function(str, max) {
		if (!str) {
			str = "";
		}
		str = str.toString();
		return str.length < max ? this.pad("0" + str, max) : str;
	}

	this.addToCart = function(objSource,gotoCart,resetCart,currResources) {
		gotoCart = (typeof gotoCart !== 'undefined') ?  gotoCart : 0;
		resetCart = (typeof resetCart !== 'undefined') ?  resetCart : 0;
		bookingfor.waitBlockUI();
//		jQuery.blockUI({ message: ''});
		var cart = jQuery('.bfi-shopping-cart').first();
		if (!jQuery(cart).length) {
			cart = jQuery('.bfi-content').first();
		}
		
		var recalculareOrder = 0;
		var orderDetailSummarytodrag = jQuery("#orderDetailSummary");
		if (jQuery(objSource).length) {
			orderDetailSummarytodrag = objSource;
			recalculateOrder = 1;
		}
		if (cart.length)
		{
			if (orderDetailSummarytodrag.length ) {
				var divClone = orderDetailSummarytodrag.clone().offset({
					top: orderDetailSummarytodrag.offset().top,
					left: orderDetailSummarytodrag.offset().left
				})
					.css({
						'opacity': '0.5',
						'width': orderDetailSummarytodrag.width() + "px",
						'height': orderDetailSummarytodrag.height() + "px",
						'position': 'absolute',
						'z-index': '100',
						'overflow': 'hidden'
					})
					.appendTo(jQuery('body'))
					.animate({
						'top': cart.offset().top + 10,
						'left': cart.offset().left,
						'width': 0,
						'height': 0
					}, 1000, 'easeInOutExpo', function () {
						jQuery(this).remove();
						//cartModel();
						var currData = {
							"hdnOrderData":jQuery("#hdnOrderDataCart").val(),
							"recalculateOrder":recalculateOrder,
							"hdnBookingType":jQuery("#hdnBookingType").val(),
							"bfiResetCart":resetCart
						}
						jQuery.ajax({
							cache: false,
							type: 'POST',
							url: bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=addToCart',
//							data: 'hdnOrderData=' + jQuery("#hdnOrderDataCart").val() + "&recalculateOrder=" + recalculateOrder +  '&hdnBookingType=' + jQuery("#hdnBookingType").val() +  '&bfiResetCart=' + resetCart,
							data: currData,
							success: function (data) {
	//							console.log(data);
								if (gotoCart==1)
								{
									window.location.assign(bfi_variable.bfi_carturl);
								}else{
									jQuery.unblockUI();  
									
									jQuery(".bfibadge").html(data);
									var currModalCart = jQuery(".bfimodalcart").first();
									var currTitle = currModalCart.find(".bfi-title").first().html();
									var currHtml = currModalCart.find(".bfi-body").first().html();
									var currFooter = currModalCart.find(".bfi-footer").first().html();

									var thisHtml = currHtml;
									jQuery(".bf-summary-body-resourcename").each(function () {
										var cuttTitle = $(this).find("strong").first();
										if (cuttTitle.length) {
											thisHtml += "<div>" + cuttTitle.html() + "</div>";
										}
									});
									thisHtml += currFooter;
									cart.webuiPopover({
										title : currTitle,
										content : thisHtml,
										container: document.body,
										cache: false,
										closeable:true,
										arrow: false,
										backdrop:true,
										placement:'auto-bottom',
										type:'html',
										style:'bfi-webuipopover'
									});
									jQuery('html,body').animate({
										scrollTop: cart.offset().top},
										'slow', function() {
											// Animation complete.
											cart.webuiPopover("show");
										});


	//								jQuery("#bfimodalcart").find(".modal-body").first().html(thisHtml);
	//								jQuery("#bfimodalcart").modal({ backdrop: 'static' });
								
								}

							}
						});
						//send data 

						//$("#LoginRegisterModel").html("");
						//$("#LoginRegisterModel").load(cartUrl);
						//$("#LoginRegisterModel").modal({ backdrop: 'static' });

					});

			}else{
				var currData = {
					"hdnOrderData":jQuery("#hdnOrderDataCart").val(),
					"recalculateOrder":recalculateOrder,
					"hdnBookingType":jQuery("#hdnBookingType").val(),
					"bfiResetCart":resetCart
				}
				jQuery.ajax({
					cache: false,
					type: 'POST',
					url: bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=addToCart',
//					data: 'hdnOrderData=' + jQuery("#hdnOrderDataCart").val() + "&recalculateOrder=" + recalculateOrder+  '&hdnBookingType=' + jQuery("#hdnBookingType").val() +  '&bfiResetCart=' + resetCart,
					data: currData,
					success: function (data) {
		//							console.log(data);
						if (gotoCart==1)
						{
							window.location.assign(bfi_variable.bfi_carturl);
						}else{
						
							jQuery.unblockUI();

							jQuery(".bfibadge").html(data);

							var currModalCart = jQuery(".bfimodalcart").first();
							var currTitle = currModalCart.find(".bfi-title").first().html();
							var currHtml = currModalCart.find(".bfi-body").first().html();
							var currFooter = currModalCart.find(".bfi-footer").first().html();

							var thisHtml = currHtml;
							thisHtml += currFooter;
							cart.webuiPopover({
								title : currTitle,
								content : thisHtml,
								container: document.body,
								cache: false,
								closeable:true,
								arrow: false,
								backdrop:true,
								placement:"auto",
								html :"true",
								style:'bfi-webuipopover'
							});
							jQuery('html,body').animate({
								scrollTop: cart.offset().top},
								'slow', function() {
									// Animation complete.
									cart.webuiPopover("show");
								});
						}
					}
				});
			}
		}


	}



	this.removeFromCart = function() {
		jQuery.ajax({
			cache: false,
			type: 'POST',
			url: removeFromCartUrl,
			beforeSend: function () {
				bookingfor.waitBlockUI();
				//blockui();
			},
			data: {
				cartOrderId: jQuery(this).attr("data-cartorderid")
			},
			success: function (data) {
				jQuery("#LoginRegisterModel").html(data);
				//$("#LoginRegisterModel").modal({ backdrop: 'static' });
				jQuery.unblockUI();
			}
		});
	}
	this.GetDiscountsInfo = function(discountIds,language, obj, fn) {
			var query = "discountIds=" + discountIds;
			var queryDiscount = "discountId=" + discountIds + "&language=" + language + "&task=getDiscountDetails";
			jQuery.post(bfi_variable.bfi_urlCheck, queryDiscount, function(data) {

				$html = '';
				jQuery.each(data || [], function (key, val) {
					var name = val.Name;
					var descr = val.Description;
					name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
					$html += '<p class="title">' + name + '</p>';
					descr = bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(descr) + "</p>").text());
					$html += '<p class="description ">' + descr + '</p>';
				});
				bookingfor.offersLoaded[discountIds] = $html;
				fn(obj, $html);
			},'json');
		}
	this.checkBookable = function(currSelect) {
		var isbookable = jQuery(currSelect).attr("data-isbookable");
		jQuery(".ddlrooms.ddlrooms-indipendent[data-isbookable!='" + isbookable+"']").each(function (index) {
			jQuery(this).val(0);
			bookingfor.checkMaxSelect(this);
		});
	}

	this.checkMaxSelect = function(currSelect) {
		var maxSelectable = Number(jQuery(currSelect).attr("data-availability")||0);
		var isbookable = jQuery(currSelect).attr("data-isbookable");
		var resourceId = jQuery(currSelect).attr("data-resid");
		if(jQuery(".ddlrooms-" + resourceId+"[data-isbookable='" + isbookable+"']").length>1){
			var occupancyResource = bookingfor.getOccupancy(resourceId,isbookable);
			var remainingResource = maxSelectable - occupancyResource;

			jQuery(".ddlrooms-" + resourceId+"[data-isbookable='" + isbookable+"']").each(function () {
				var currentValue = parseInt(jQuery(this).val());
				var maxValue = parseInt(jQuery(this).find("option:last-child").attr("value"));
				if((currentValue + remainingResource) < maxValue) {
					maxValue = currentValue + remainingResource;
				}
				var lastIndx = jQuery(this).find("option[value='" + maxValue + "']").index();
				/*var currentIndex = jQuery(this).find("option:selected").index();
				var maxIndex = lastIndx;
				var selIndx = jQuery(this).find("option[value='" + remainingResource + "']").index();
				var currSelect = jQuery(this).prop('selectedIndex');
				if (currSelect > selIndx)
				{
					selIndx += currSelect ;
				}*/
				jQuery(this).find('option:lt(' + lastIndx + ')').prop('disabled', false);
				jQuery(this).find('option:gt(' + lastIndx + ')').prop('disabled', true);
				jQuery(this).find('option:eq(' + lastIndx + ')').prop('disabled', false);
			});
		}

	}
	this.getOccupancy = function(resourceId,isbookable) {
        var occupancy = 0;
		jQuery(".ddlrooms-" + resourceId+"[data-isbookable='" + isbookable+"']").each(function () {
			occupancy += Number(jQuery(this).val()||0)
		});
		return occupancy;
	}

	this.checkListDisplay = function() {
		if (localStorage.getItem('display')) {
			if (localStorage.getItem('display') == 'list') {
				jQuery('#list-view').trigger('click');
			} else {
				jQuery('#grid-view').trigger('click');
			}
		} else {
			if (typeof bfi_variable === 'undefined' || bfi_variable.bfi_defaultdisplay === 'undefined') {
				jQuery('#list-view').trigger('click');
			} else {
				if (bfi_variable.bfi_defaultdisplay == '1') {
					jQuery('#grid-view').trigger('click');
				} else {
					jQuery('#list-view').trigger('click');
				}
			}
		}
	}

	this.easterForYear = function(year) {
		var a = year % 19;
		var b = Math.floor(year / 100);
		var c = year % 100;
		var d = Math.floor(b / 4); 
		var e = b % 4;
		var f = Math.floor((b + 8) / 25);
		var g = Math.floor((b - f + 1) / 3); 
		var h = (19 * a + b - d - g + 15) % 30;
		var i = Math.floor(c / 4);
		var k = c % 4;
		var l = (32 + 2 * e + 2 * i - h - k) % 7;
		var m = Math.floor((a + 11 * h + 22 * l) / 451);
		var n0 = (h + l + 7 * m + 114)
		var n = Math.floor(n0 / 31) - 1;
		var p = n0 % 31 + 1;
		var date = new Date(year,n,p);
		return date; 
	}

	this.loadHolidays = function() {
		if (!bookingfor.loadedholydays )
		{
			var cultureCode = bfi_variable.bfi_cultureCode;
			if (cultureCode.length>1)
			{
				cultureCode = cultureCode.substring(0, 2).toLowerCase();
			}
				bookingfor.holydaysTitle = ["New Year","Epiphany","Liberation","Labor Day","Republic Day","Mid-August","All saints","Immaculate Conception","Natale","St. Stephen","Easter","Easter Monday","Easter","Easter Monday"];
			if (cultureCode== 'it')
			{
				bookingfor.holydaysTitle = ["Capodanno","Epifania","Liberazione","Festa dei lavoratori","Festa della Repubblica","Ferragosto","Tutti Santi","Immacolata concezione","Natale","St. Stefano","Pasqua","Lunedì dell'angelo","Pasqua","Lunedì dell'angelo"];
			}
				bookingfor.holydays = ["0101","0601","2504","0105","0206","1508","0111","0812","2512","2612"];
				var date = new Date;
				// Set the timestamp to midnight.
				date.setHours( 0, 0, 0, 0 );
				var currYear =  date.getFullYear();
				var easterForCurrYear = bookingfor.easterForYear( currYear );
				var easterForNextYear = bookingfor.easterForYear( currYear+1 );
				
				bookingfor.holydays.push(("0" + easterForCurrYear.getDate()).slice(-2) + "" + ("0" + (easterForCurrYear.getMonth()+1)).slice(-2)+ easterForCurrYear.getFullYear()); 
				easterForCurrYear.setDate(easterForCurrYear.getDate() + 1);
				bookingfor.holydays.push(("0" + easterForCurrYear.getDate()).slice(-2) + "" + ("0" + (easterForCurrYear.getMonth()+1)).slice(-2)+ easterForCurrYear.getFullYear()); 
				bookingfor.holydays.push(("0" + easterForNextYear.getDate()).slice(-2) + "" + ("0" + (easterForNextYear.getMonth()+1)).slice(-2)+ easterForNextYear.getFullYear()); 
				easterForNextYear.setDate(easterForNextYear.getDate() + 1);
				bookingfor.holydays.push(("0" + easterForNextYear.getDate()).slice(-2) + "" + ("0" + (easterForNextYear.getMonth()+1)).slice(-2)+ easterForNextYear.getFullYear()); 
			bookingfor.loadedholydays =true;
		}
	}



	this.checkAdsBlocked = function() {
		if (!bookingfor.adsBlockedChecked )
		{
			this.isAdsBlocked();
			bookingfor.adsBlockedChecked =true;
		}
	}
	this.isFetchAPIsupported = function() {
		return 'fetch' in window;
	}
	this.isAdsBlocked = function() {
		if (typeof Request != 'undefined' && bookingfor.isFetchAPIsupported() ) {

			var testURL = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'

			var myInit = {
				method: 'HEAD',
				mode: 'no-cors'
			};

			var myRequest = new Request(testURL, myInit);

			fetch(myRequest).then(function(response) {
				return response;
			}).then(function(response) {
	//			console.log(response);
	//			callback(false)
				bookingfor.adsBlocked  = false;
			}).catch(function(e){
	//			console.log(e)
	//			callback(true)
				bookingfor.adsBlocked  = true;
			});
		}
	}

	this.BookNow = function() {
			//       debugger;
		var sendtocart = 0;

		var Order = { Resources: [], ExtraServices: [],SearchModel: {}, TotalAmount: 0, TotalDiscountedAmount: 0 };
		Order.SearchModel = jQuery('#bfi-calculatorForm').serializeObject();
		Order.SearchModel.MerchantId = bfi_currMerchantId;
		Order.SearchModel.AdultCount = new Number(Order.SearchModel.adults || 0);
		Order.SearchModel.ChildrenCount = new Number(Order.SearchModel.children || 0);
		Order.SearchModel.SeniorCount = new Number(Order.SearchModel.seniores || 0);
		Order.SearchModel.ChildAges = [Order.SearchModel.childages1, Order.SearchModel.childages2, Order.SearchModel.childages3, Order.SearchModel.childages4, Order.SearchModel.childages5];
		currPaxNumber = Order.SearchModel.AdultCount + Order.SearchModel.ChildrenCount + Order.SearchModel.SeniorCount;
		currPaxAges = new Array();
		for (i = 0; i < Order.SearchModel.AdultCount ; i++) {
			currPaxAges.push(bfi_currAdultsAge);
		}
		for (i = 0; i < Order.SearchModel.SeniorCount  ; i++) {
			currPaxAges.push(bfi_currSenioresAge);
		}
		for (i = 0; i < Order.SearchModel.ChildrenCount ; i++) {
			currPaxAges.push(Order.SearchModel.ChildAges[i]);
		}

		var FirstResourceId = 0;
		var ResetCart = 0;
		var currPolicy = [];
		jQuery(".ddlrooms-indipendent ").each(function (index, ddlroom) {
			var currResId = jQuery(this).attr('data-resid');
			var currRateplanId = new Number(jQuery(this).attr('data-ratePlanId') || 0);
			var currRateplanTypeId = new Number(jQuery(this).attr('data-ratePlanTypeId') || 0);
			var currQtSelected = jQuery(this).val();
			var currAvailabilityType = new Number(jQuery(this).attr('data-availabilitytype') || 1);

//			var currResetCart = new Number(jQuery(this).attr('data-resetCart') || 0);
//			if(currResetCart ==1){
//				ResetCart = 1;
//			}

			if (currQtSelected > 0) {
//				sendtocart = Number(jQuery(this).attr("data-isbookable") || 0);
//				if(bfi_variable.bfi_sendalltocart ==1 ){
//					sendtocart = 1;
//				}
				for (var i = 1; i <= currQtSelected; i++) {
					currPolicy.push(new Number(jQuery(this).attr('data-policyId') || 0));
					var currResourceRequest = {
						ResourceId: new Number(currResId || 0),
						Name: jQuery(this).attr('data-name'),
						Brand: jQuery(this).attr('data-brand'),
						ListName: jQuery(this).attr('data-lna'),
						Category: jQuery(this).attr('data-category'),
						FromDate:  jQuery(this).attr('data-checkin'),
						ToDate:  jQuery(this).attr('data-checkout'),
						PolicyId: new Number(jQuery(this).attr('data-policyId') || 0),
						IsBookable: new Number(jQuery(this).attr('data-isbookable') || 0),
						PaxNumber: currPaxNumber,
						PaxAges: currPaxAges,
						IncludedMeals: jQuery(this).attr('data-includedmeals'),
						TouristTaxValue: jQuery(this).attr('data-touristtaxvalue'),
						VATValue: jQuery(this).attr('data-vatvalue'),
						MerchantId: bfi_currMerchantId,
						RatePlanId: currRateplanId,
						RatePlanName: jQuery(this).attr('data-ratePlanName'),
						RatePlanTypeId: currRateplanTypeId,
						AvailabilityType: currAvailabilityType,
						SelectedQt: 1,
						TotalDiscounted: jQuery(this).attr('data-baseprice'),
						TotalAmount: jQuery(this).attr('data-basetotalprice'),
						AllVariations: jQuery(this).attr('data-allvariations'),
						PercentVariation: jQuery(this).attr('data-percentvariation'),
						MinPaxes: jQuery(this).attr('data-minpaxes'),
						MaxPaxes: jQuery(this).attr('data-maxpaxes'),
						ComputedPaxes: jQuery(this).attr('data-computedpaxes'),
						PricesExtraIncluded: JSON.stringify( (typeof pricesExtraIncluded[currRateplanId] !== 'undefined' && Object.keys(pricesExtraIncluded[currRateplanId]).length > 0)? pricesExtraIncluded[currRateplanId] : {}),
						PolicyValue: jQuery(this).attr('data-policy'),
						ExtraServices: []
					};

					if (currAvailabilityType == 2) {
						var currTr = jQuery("#bfi-timeperiod-" + currResId);
						currResourceRequest.TimeMinStart = currTr.attr("data-timeminstart");
						currResourceRequest.TimeMinEnd = currTr.attr("data-timeminend");
						currResourceRequest.CheckInTime = currTr.attr("data-checkintime");
						currResourceRequest.TimeDuration = currTr.attr("data-duration");
					}
					if (currAvailabilityType == 3) {
						var currTr = jQuery("#bfi-timeslot-" + currResId);
						currResourceRequest.FromDate = currTr.attr('data-checkin-ext');
						currResourceRequest.TimeSlotId = currTr.attr("data-timeslotid");
						currResourceRequest.TimeSlotStart = currTr.attr("data-timeslotstart");
						currResourceRequest.TimeSlotEnd = currTr.attr("data-timeslotend");
					}

					//--------recupero extras....

					jQuery("#services-room-" + i + "-" + currResId + "-" + currRateplanId).find(".ddlrooms").each(function (index, element) {
						var currValue = jQuery(this).val();

						var currPriceId = jQuery(this).attr("data-resid");
						var currPriceAvailabilityType = jQuery(this).attr("data-availabilityType");
						if (currValue != "0") {
							var extraValue = currPriceId + ":" + currValue;
							if (currPriceAvailabilityType == "2") {
								var currSelectData = jQuery(this).closest("tr").find(".bfi-timeperiod").first();
								extraValue += ":" + currSelectData.attr("data-checkin") + currSelectData.attr("data-timeminstart") + ":" + currSelectData.attr("data-duration") + "::::"
							}
							if (currPriceAvailabilityType == "3") {
								var currSelectData = jQuery(this).closest("tr").find(".bfi-timeslot").first();
								extraValue += ":::" + currSelectData.attr("data-timeslotid") + ":" + currSelectData.attr("data-timeslotstart") + ":" + currSelectData.attr("data-timeslotend") + ":" + currSelectData.attr("data-checkin") + "::::"
							}

							var currExtraService = {
								Value: extraValue,
								Name: jQuery(this).attr("data-name"),
								PriceId: currPriceId,
								CalculatedQt: currValue,
								ResourceId: currPriceId,
								TotalDiscounted: parseFloat(jQuery(this).attr('data-baseprice')) * currValue,
								TotalAmount: parseFloat(jQuery(this).attr('data-basetotalprice')) * currValue,
								Brand: jQuery(this).attr('data-brand'),
								ListName: jQuery(this).attr('data-lna'),
								Category: jQuery(this).attr('data-category'),
								RatePlanName: jQuery(this).attr('data-rateplanname'),
							}
							if (currPriceAvailabilityType == 2) {
								var currTr = jQuery(this).closest("tr").find(".bfi-timeperiod").first();
								currExtraService.TimeMinStart = currTr.attr("data-timeminstart");
								currExtraService.TimeMinEnd = currTr.attr("data-timeminend");
								currExtraService.CheckInTime = currTr.attr("data-checkintime");
								currExtraService.TimeDuration = currTr.attr("data-duration");
							}
							if (currPriceAvailabilityType == 3) {
								var currTr = jQuery(this).closest("tr").find(".bfi-timeslot").first();
								currExtraService.TimeSlotId = currTr.attr("data-timeslotid");
								currExtraService.TimeSlotStart = currTr.attr("data-timeslotstart");
								currExtraService.TimeSlotEnd = currTr.attr("data-timeslotend");
								var currDateint = currTr.attr("data-checkin");
								currExtraService.TimeSlotDate = currDateint.substr(6, 2) + "/" + currDateint.substr(4, 2) + "/" + currDateint.substr(0, 4);
							}
							var minSelectable = parseInt(jQuery(this).find('option:first').val());
							if (minSelectable > 0) {
								currResourceRequest.TotalDiscounted -= (parseFloat(jQuery(this).attr('data-baseprice'))) * minSelectable;
								currResourceRequest.TotalAmount -= (parseFloat(jQuery(this).attr('data-basetotalprice'))) * minSelectable;
							}

							currResourceRequest.ExtraServices.push(currExtraService);
							Order.ExtraServices.push(currExtraService);

							//							currResourceRequest.ExtraServices.push({
							//								Value:extraValue,
							//								PriceId: currPriceId,
							//								CalculatedQt: currValue,
							//								ResourceId: currResId, 
							//								TotalDiscounted:  jQuery(this).attr('data-baseprice'),
							//								TotalAmount:  jQuery(this).attr('data-basetotalprice'),
							//							});
						}
					});

					Order.Resources.push(currResourceRequest);

				}

			}
		});

		if (Order.Resources.length > 0) {
			FirstResourceId = Order.Resources[0].ResourceId;
			jQuery('#frm-order').html('');
			jQuery('#frm-order').empty();

                if (bfi_variable.bfi_eecenabled==1)
                {
					var currAllItems = jQuery.makeArray(jQuery.map(Order.Resources, function(elm, idx) {
							return {
								"id": elm.ResourceId + " - Resource",
								"name": elm.Name ,
								"category": elm.Category ,
								"brand": elm.Brand ,
								"price": elm.TotalDiscounted,
								"quantity": elm.SelectedQt,
								"variant": elm.RatePlanName.toUpperCase(),
								"list": elm.ListName,
							};
						}));
					if (typeof callAnalyticsEEc !== "undefined" )
					{
						var currListName = currAllItems[0].list;
						callAnalyticsEEc("addProduct", currAllItems, "addToCart", "",  {
								"step": 1,
								"list" : currListName
							},
							"Add to Cart"
						);
						callAnalyticsEEc("addProduct", jQuery.makeArray(jQuery.map(Order.ExtraServices, function(elm, idx) {
								return {
									"id": elm.PriceId + " - Service",
									"name": elm.Name ,
									"category": elm.Category ,
									"brand": elm.Brand ,
									"price": elm.TotalDiscounted,
									"quantity": elm.CalculatedQt,
									"variant": elm.RatePlanName.toUpperCase(),
								};
							})), "addToCart", "",  {
								"step": 2,
								"list" : currListName
							},
							"Add to Cart"
						);
					}
                }

				jQuery('#frm-order').prepend('<input id=\"hdnOrderDataCart\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order.Resources).replace(/'/g, "$$$") + "'" + '\>');
				jQuery('#frm-order').prepend('<input id=\"hdnBookingType\" name=\"hdnBookingType\" type=\"hidden\" value=' + "'" + jQuery('input[name="bookingType"]').val() + "'" + '\>');
			
//			console.log("ResetCart: ");
//			console.log(ResetCart);
//			if (sendtocart == 1) {
				bookingfor.addToCart(jQuery("#divcalculator"),bfi_variable.bfi_sendtocart,ResetCart,Order.Resources);
//			} else {
//				bookingfor.addToCart(jQuery("#divcalculator"),1,1);
//				jQuery('#frm-order').prepend('<input id=\"hdnOrderData\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order.Resources).replace(/'/g, "$$$") + "'" + '\>');
//				jQuery('#frm-order').prepend('<input id=\"hdnPolicyIds\" name=\"hdnPolicyIds\" type=\"hidden\" value=' + "'" + currPolicy.join(",") + "'" + '\>');
//				bookingfor.waitBlockUI();
//				jQuery('#frm-order').submit();
//			}
		} else {
			alert("Error, You must select a quantity!")
		}

	}



}





jQuery(document).ready(function() {
//      jQuery(".variationlabel").click(
//        function() {
//          var discountId = jQuery(this).attr('rel');
//          var hasRateplans = jQuery(this).attr('rel1');
//          if (jQuery.inArray(discountId, offersLoaded) === -1) {
//            bookingfor.getDiscountAjaxInformations(discountId, hasRateplans);
//            offersLoaded.push(discountId);
//          }
//          jQuery("#divoffers" + discountId).slideToggle("slow");
//        }
//      );
//      jQuery(".rateplanslabel").click(
//        function() {
//          var rateplanId = jQuery(this).attr('rel');
//          if (jQuery.inArray(rateplanId, rateplansLoaded) === -1) {
//            getRateplanAjaxInformations(rateplanId);
//            rateplansLoaded.push(rateplanId);
//          }
//          jQuery("#divrateplan" + rateplanId).slideToggle("slow");
//        }
//      );

//	jQuery("#my-account-tabs").tabs();

	var start = jQuery('.checkincalendar').val();
	if (typeof start !== "undefined") {
		 date = jQuery.datepicker.parseDate('dd/mm/yy', start);
		 var dstart = new Date(date);
		 
		 var end = jQuery('.checkoutcalendar').val();
		 date = jQuery.datepicker.parseDate('dd/mm/yy', end);
		 var dend = new Date(date);
		 
		 var dendmin = new Date(dstart);
		 dendmin.setDate(dstart.getDate() + 1);

		jQuery('.checkincalendar').datepicker({
			dateFormat : 'dd/mm/yy',
			defaultDate: dstart,
			beforeShow: function(dateText, inst) { 
				jQuery(this).attr("disabled", true);
				jQuery(inst.dpDiv).addClass('bfi-calendar');
				jQuery(inst.dpDiv).attr('data-before',"");
				jQuery(inst.dpDiv).removeClass("bfi-checkin");
				jQuery(inst.dpDiv).removeClass("bfi-checkout");
			},
			onSelect: function(selectedDate) {
				instance = jQuery('.checkincalendar').data("datepicker");
				date = jQuery.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings);
				var d = new Date(date);
				d.setDate(d.getDate() + 1);
				jQuery(".checkoutcalendar").datepicker("option", "minDate", d);
			}
		});
		 
		jQuery('.checkoutcalendar').datepicker({
			beforeShow: function(dateText, inst) { 
				jQuery(this).attr("disabled", true);
				jQuery(inst.dpDiv).addClass('bfi-calendar');
				jQuery(inst.dpDiv).attr('data-before',"");
				jQuery(inst.dpDiv).removeClass("bfi-checkin");
				jQuery(inst.dpDiv).removeClass("bfi-checkout");
			},
			dateFormat : 'dd/mm/yy',
			defaultDate: dend,
			minDate: dendmin
		});
	}

	jQuery('a.boxedpopup').on('click', function (e) {
		var width = jQuery(window).width()*0.9;
		var height = jQuery(window).height()*0.9;
			if(width>800){width=870;}
			if(height>600){height=600;}

		e.preventDefault();
		var page = jQuery(this).attr("href")
//		var pagetitle = jQuery(this).attr("title")

		jQuery.post(page, function(data) {
			jQuery.unblockUI();
			var $dialog = jQuery('<div id="boxedpopupopen"></div>')
				.html(data)
			.dialog({
				autoOpen: false,
				modal: true,
				height:height,
				width: width,
				fluid: true, //new option
//				title: pagetitle
				dialogClass: 'bfi-dialog bfi-dialog-contact'
			});
			$dialog.dialog('open');
			if (typeof window.BFIInitReCaptcha2 === "function") { 
				// safe to use the function
				BFIInitReCaptcha2();
			}
		});
	});

		jQuery(window).resize(function() {
			var bpOpen = jQuery("#boxedpopupopen");
				var wWidth = jQuery(window).width();
				var dWidth = wWidth * 0.9;
				var wHeight = jQuery(window).height();
				var dHeight = wHeight * 0.9;
				if(dWidth>800){dWidth=870;}
				if(dHeight>600){dHeight=600;}
					bpOpen.dialog("option", "width", dWidth);
					bpOpen.dialog("option", "height", dHeight);
					bpOpen.dialog("option", "position", "center");

			jQuery("table.bfi-table-resources-sticked").each(function(){
				var existSticked = jQuery(this).find("thead.bfi-sticked").first();
				var $currDivBook = jQuery(this).find(".bfi-book-now").first();
				if(existSticked.length){existSticked.remove();};
				if($currDivBook.length){$currDivBook.removeClass("bfi-sticked");};
			});
		});

		jQuery(window).scroll(function(){
			jQuery("table.bfi-table-resources-sticked").each(function(){
				var existSticked = jQuery(this).find("thead.bfi-sticked").first();
				var $currDivBook = jQuery(this).find(".bfi-book-now").first();
					var corr= 0;
					if(jQuery(this).hasClass("bfi-table-selectableprice-container")){
						corr= 75;
					}
				if((jQuery(".bfi-result-list").offset().top+corr) <jQuery(window).scrollTop()){
					
					if(!$currDivBook.hasClass("bfi-sticked")){
						$currDivBook.addClass("bfi-sticked");
					}
					if(!existSticked.length){
						var $currthead = jQuery(this).find("thead").first();
						var newthead =  jQuery($currthead.clone());
						newthead.appendTo(jQuery(this));
						newthead.width(jQuery(this).width());
						newthead.css('top',0);
						newthead.addClass("bfi-sticked");
					}


					if((jQuery(".bfi-result-list").offset().top+jQuery(".bfi-result-list").height()) <(jQuery(window).scrollTop()+$currDivBook.height() )){
//						console.log("corr" +corr )
						$currDivBook.css('top',( (jQuery(".bfi-result-list").offset().top+jQuery(".bfi-result-list").height()) - (jQuery(window).scrollTop()+$currDivBook.height()))  + 'px');
					}else{
						$currDivBook.css('top','50px');
					}
					if((jQuery(".bfi-result-list").offset().top+jQuery(".bfi-result-list").height()) <jQuery(window).scrollTop()){
						existSticked.hide();
						$currDivBook.hide();
					}else{
						existSticked.show();
						$currDivBook.show();
					}
				}else{
					existSticked.remove();
					$currDivBook.removeClass("bfi-sticked");
				}
			});
		});

		bookingfor.checkAdsBlocked();
		bookingfor.loadHolidays();

});      
     
if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}   

if (typeof jQuery.fn.serializeObject !== 'function') {
	jQuery.fn.serializeObject = function()
	{
	   var o = {};
	   var a = this.serializeArray();
	   jQuery.each(a, function() {
		   if (o[this.name]) {
			   if (!o[this.name].push) {
				   o[this.name] = [o[this.name]];
			   }
			   o[this.name].push(this.value || '');
		   } else {
			   o[this.name] = this.value || '';
		   }
	   });
	   return o;
	};
}   


function bfi_quoteCalculatorServiceChanged(el){
	
	var selectedExtra = parseInt(jQuery(el).val());
	var currProdRelatedId = jQuery(el).attr("data-resid");
	var currMaxAvailability = servicesAvailability[currProdRelatedId];
	jQuery(".ddlrooms-"+currProdRelatedId).each(function(){
		var currselectableprice = jQuery(this).val();
		currMaxAvailability -= parseInt(currselectableprice);
	});

	//rebuild ddl
	jQuery(".ddlrooms"+currProdRelatedId).not(this).each(function(){
		var currMaxValue = jQuery(this).children("option:last").val();
		var currValue = parseInt(jQuery(this).val());

		jQuery(this).children("option").prop('disabled',false);
		var maxValue = pcurrValue+currMaxAvailability;

		if(currMaxValue>maxValue){
			var prodRelatedId = jQuery(this).attr("data-resid");
			var selIndx  = jQuery(this).children("option").index( jQuery(this).children("option[value='" +maxValue +"']"));
			if(selIndx>-1){
				jQuery(this).children("option:gt("+selIndx+")").prop('disabled',true);

			}
		}
	});

	bfi_getcompleterateplansstaybyrateplanid(jQuery(el));
//	console.log("Recalc");
}

function bfi_getcompleterateplansstaybyrateplanid($el) {
	//console.log("calcolo prezzo per id: " + priceId);
	
//	debugger;
	var selectedExtra = parseInt($el.val());
	var priceId = $el.attr("data-resid");
	var resId = $el.attr("data-bindingproductid");
	var rateplanId = $el.attr("data-rateplanid");
	currTable = $el.closest("table");
    bookingfor.waitSimpleWhiteBlock(currTable);

	var extrasselect = [];
	jQuery(currTable).find(".ddlrooms").each( function( index, element ){
		var currValue = jQuery(this).val();
		var currResId = jQuery(this).attr("data-resid");
		var currAvailabilityType = jQuery(this).attr("data-availabilityType");
		if(currValue!="0"){
			var extraValue = currResId + ":1"; // + currValue;
			if(currAvailabilityType =="2"){
				var currSelectData = jQuery(this).closest("tr").find(".bfi-timeperiod").first();				
				
				extraValue += ":" + currSelectData.attr("data-checkin") + currSelectData.attr("data-timeminstart") + ":" + currSelectData.attr("data-duration") + "::::"
			}
			if(currAvailabilityType =="3"){
				var currSelectData = jQuery(this).closest("tr").find(".bfi-timeslot").first();	
				extraValue += ":::" + currSelectData.attr("data-timeslotid")  + ":" + currSelectData.attr("data-timeslotstart") + ":" + currSelectData.attr("data-timeslotend") + ":" + currSelectData.attr("data-checkin") + "::::"
			}

			extrasselect.push(extraValue);
		}
	});

	obj = jQuery("tr[id^=data-id-"+resId+"-"+rateplanId +"]");
	var ddlroom = jQuery(obj).find(".ddlrooms");

	var searchModel = jQuery('#bfi-calculatorForm').serializeObject();
	var dataarray = jQuery('#bfi-calculatorForm').serializeArray();
	dataarray.push({name: 'resourceId', value: resId});
	dataarray.push({name: 'id', value: resId});

	var accomodation = {
		ResourceId: resId,
		RatePlanId: rateplanId,
		AvailabilityType:ddlroom.attr("data-availabilityType"),
		TimeMinStart:0,
		TimeMinEnd:0,
		FromDate:"",
		ExtraServices: extrasselect
	};
	
	if(ddlroom.attr("data-availabilityType")==2){

		var currTr = jQuery("#bfi-timeperiod-"+resId);
		dataarray.push({name: 'timeMinStart', value: currTr.attr("data-timeminstart")});
		dataarray.push({name: 'timeMinEnd', value: currTr.attr("data-timeminend")});
		dataarray.push({name: 'CheckInTime', value: currTr.attr("data-checkintime")});
		dataarray.push({name: 'duration', value: currTr.attr("data-duration")});
	}
	if(ddlroom.attr("data-availabilityType")==3){
		var currTr = jQuery("#bfi-timeslot-"+resId);
		accomodation.TimeSlotId = currTr.attr("data-timeslotid");
		accomodation.TimeSlotStart = currTr.attr("data-timeslotstart");
		accomodation.TimeSlotEnd = currTr.attr("data-timeslotend");
	}

	dataarray.push({name: 'pricetype', value:  accomodation.RatePlanId});
	dataarray.push({name: 'rateplanid', value: accomodation.RatePlanId});
//	dataarray.push({name: 'timeMinStart', value: accomodation.TimeMinStart});
//	dataarray.push({name: 'timeMinEnd', value: accomodation.TimeMinEnd});
	dataarray.push({name: 'selectableprices', value: accomodation.ExtraServices.join("|")});
	dataarray.push({name: 'availabilitytype', value: accomodation.AvailabilityType});
	dataarray.push({name: 'searchModel', value: searchModel});

	var jqxhr = jQuery.ajax({
		url: bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + 'task=getCompleteRateplansStay',
		type: "POST",
		dataType: "json",
		data : dataarray
//            data: {
//                id: resId,
//                rateplanid: accomodation.RatePlanId,
//                timeMinStart: accomodation.TimeMinStart,
//                timeMinEnd: accomodation.TimeMinEnd,
//                selectableprices: accomodation.ExtraServices.join("|"),
//                productAvailabilityType : accomodation.AvailabilityType,
//                searchModel: searchModel
//            }
	});

	jqxhr.done(function(result, textStatus, jqXHR)
	{
		if (result) {

			 UpdateQuote();

			if(result.length > 0)
			{
//                    debugger;
				var currResult = jQuery.grep(result, function (rs) {
					return (rs.RatePlanId == parseInt(accomodation.RatePlanId));
				});

				currStay = currResult[0].SuggestedStay;
				var currTr = $el.closest("tr");
				var CalculatedPrices = JSON.parse(currResult[0].CalculatedPricesString);
//                    console.log(CalculatedPrices)
				var showPrice = false;

				var currentDivPrice = jQuery(currTr).find(".bfi-totalextrasselect");
				currentDivPrice.hide();

				var currTotalPriceDivPrice=0;
				var currDiscountedPriceDivPrice=0;
				var simpleDiscountIdsDivPrice = "";


				CalculatedPrices.forEach(function (cprice) {
					//if (cprice.PriceId == priceId) {
					if (cprice.RelatedProductId == priceId) {

//                            console.log("Visualizzo prezzo id: " + priceId);

						showPrice = true;
//						cprice.TotalPrice = cprice.TotalAmount;
//						cprice.DiscountedPrice = cprice.TotalDiscounted;
						currTotalPriceDivPrice += cprice.TotalAmount;
						currDiscountedPriceDivPrice += cprice.TotalDiscounted;
						var simpleDiscountIds = [];
						cprice.Variations.forEach(function (variation) {
							simpleDiscountIds.push(variation.VariationPlanId);
						});
//						cprice.SimpleDiscountIds = simpleDiscountIds.join(",");
						simpleDiscountIdsDivPrice +=simpleDiscountIds.join(",");
//						var curr_bfi_price = currTr.find(".bfi-price");
//						var curr_bfi_discounted_price = currTr.find(".bfi-discounted-price");
//						var curr_percent_discount = currTr.find(".bfi-percent-discount");
//
//						var ddlroom = currTr.find(".ddlrooms");
//						ddlroom.attr("data-baseprice",bookingfor.number_format(cprice.DiscountedPrice, 2, '.', '') );
//						ddlroom.attr("data-basetotalprice",bookingfor.number_format(cprice.TotalPrice, 2, '.', '') );
//						ddlroom.attr("data-price",bookingfor.priceFormat(cprice.DiscountedPrice, 2, '.', '') );
//						ddlroom.attr("data-totalprice",bookingfor.priceFormat(cprice.TotalPrice, 2, '.', '') );
//
//						curr_bfi_price.html(bookingfor.priceFormat(cprice.DiscountedPrice, 2, ',', '.') );
//						curr_bfi_discounted_price.html(bookingfor.priceFormat(cprice.TotalPrice, 2, ',', '.') );
//
//						if(cprice.DiscountedPrice >= cprice.TotalPrice) {
//							//curr_bfi_price.html(bookingfor.priceFormat(cprice.DiscountedPrice, 2, ',', '.') );
//							curr_bfi_price.removeClass("bfi-red");
//							curr_bfi_discounted_price.hide();
//							curr_percent_discount.hide();
//						} else {
//							curr_bfi_price.addClass("bfi-red");
//							curr_bfi_discounted_price.show();
//							curr_percent_discount.show();
//							curr_percent_discount.attr("rel", cprice.SimpleDiscountIds);
//							var variationPercent = cprice.TotalPrice > 0 ? parseInt(((cprice.DiscountedPrice - cprice.TotalPrice) * 100) / cprice.TotalPrice) : 0;
//							curr_percent_discount.find(".bfi-percent").html(variationPercent);
//						}
					}
				});

				if(showPrice){
					var curr_bfi_price = currTr.find(".bfi-price");
					var curr_bfi_discounted_price = currTr.find(".bfi-discounted-price");
					var curr_percent_discount = currTr.find(".bfi-percent-discount");

					var ddlroom = currTr.find(".ddlrooms");
					ddlroom.attr("data-baseprice",bookingfor.number_format(currDiscountedPriceDivPrice, 2, '.', '') );
					ddlroom.attr("data-basetotalprice",bookingfor.number_format(currTotalPriceDivPrice, 2, '.', '') );
					ddlroom.attr("data-price",bookingfor.priceFormat(currDiscountedPriceDivPrice, 2, '.', '') );
					ddlroom.attr("data-totalprice",bookingfor.priceFormat(currTotalPriceDivPrice, 2, '.', '') );

					curr_bfi_price.html(bookingfor.priceFormat(currDiscountedPriceDivPrice, 2, ',', '.') );
					curr_bfi_discounted_price.html(bookingfor.priceFormat(currTotalPriceDivPrice, 2, ',', '.') );

					if(currDiscountedPriceDivPrice>= currTotalPriceDivPrice) {
						//curr_bfi_price.html(bookingfor.priceFormat(cprice.DiscountedPrice, 2, ',', '.') );
						curr_bfi_price.removeClass("bfi-red");
						curr_bfi_discounted_price.hide();
						curr_percent_discount.hide();
					} else {
						curr_bfi_price.addClass("bfi-red");
						curr_bfi_discounted_price.show();
						curr_percent_discount.show();
						curr_percent_discount.attr("rel", simpleDiscountIdsDivPrice);
						var variationPercent = currTotalPriceDivPrice> 0 ? parseInt(((currDiscountedPriceDivPrice- currTotalPriceDivPrice) * 100) / currTotalPriceDivPrice) : 0;
						curr_percent_discount.find(".bfi-percent").html(variationPercent);
					}
					currentDivPrice.show();
				}
				
				bfi_updateQuoteService();

			}
		}
		$el.unblock();

	});


	jqxhr.always(function() {
		jQuery(currTable).unblock();
	});
}

function bfi_updateQuoteService() {
				var totalServices = 0;
				var currTotalServices = 0;
				var currTotalNotDiscoutedServices = 0;
				jQuery(".ddlextras:visible").each( function( index, element ){
					var nExtras = parseInt(jQuery(this).val());
					if(nExtras>0)
					{
						var minSelectable =  parseInt(jQuery(this).find('option:first').val());

						totalServices+=nExtras;
						var currTr = jQuery(this).closest("tr");

						currTotalServices += ( parseFloat( currTr.find(".bfi-price").html().replace(".","").replace(",",".")) * nExtras);
						currTotalNotDiscoutedServices += ( parseFloat( currTr.find(".bfi-discounted-price").html().replace(".","").replace(",",".")) *nExtras);
						if(minSelectable>0){
							currTotalServices -= ( parseFloat( currTr.find(".bfi-price").html().replace(".","").replace(",",".")) * minSelectable);
							currTotalNotDiscoutedServices -= ( parseFloat( currTr.find(".bfi-discounted-price").html().replace(".","").replace(",",".")) *minSelectable);
						}
					}
				});

				jQuery(".bfi-extras-total span").html(totalServices);
				if(totalServices > 0){
					jQuery(".bfi-extras-total").show();
				}else{
					jQuery(".bfi-extras-total").hide();
				}

				jQuery(".bfi-price-total").html(bookingfor.number_format(bfi_totalQuote + currTotalServices, 2, ',', '.') );
				jQuery(".bfi-discounted-price-total").html(bookingfor.number_format(bfi_totalQuoteDiscount + currTotalNotDiscoutedServices, 2, ',', '.') );
				jQuery(".bfi-discounted-price-total").hide();
				if((bfi_totalQuoteDiscount + currTotalNotDiscoutedServices)<= (bfi_totalQuote + currTotalServices)){
					jQuery(".bfi-discounted-price-total").hide();
					jQuery(".bfi-price-total").removeClass("bfi-red");
				}else{
					jQuery(".bfi-discounted-price-total").show();
					jQuery(".bfi-price-total").addClass("bfi-red");
				}


}
/* jQuery UI dialog clickoutside */

/*
The MIT License (MIT)

Copyright (c) 2013 - AGENCE WEB COHERACTIO

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
jQuery.widget( 'ui.dialog', jQuery.ui.dialog, {
    options: {
        // Determine if clicking outside the dialog shall close it
        clickOutside: false,
        // Element (id or class) that triggers the dialog opening 
        clickOutsideTrigger: ''
    },
      open: function() {
          var clickOutsideTriggerEl = jQuery( this.options.clickOutsideTrigger ),
              that = this;
            if (this.options.clickOutside){
                  // Add document wide click handler for the current dialog namespace
                  jQuery(document).on( 'click.ui.dialogClickOutside' + that.eventNamespace, function(event){
                      var $target = jQuery(event.target);
                      if ( $target.closest(jQuery(clickOutsideTriggerEl)).length === 0 &&
                           $target.closest(jQuery(that.uiDialog)).length === 0){
                        that.close();
                      }
                  });
            }
            // Invoke parent open method
            this._super();
      },
      close: function() {
        // Remove document wide click handler for the current dialog
        jQuery(document).off( 'click.ui.dialogClickOutside' + this.eventNamespace );
        // Invoke parent close method 
        this._super();
      },  
});