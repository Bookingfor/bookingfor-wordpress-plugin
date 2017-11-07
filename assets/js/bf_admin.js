jQuery(document).ready(function() {
	jQuery(document).ajaxSuccess(function(e, xhr, settings) {
		var widget_id_base = 'bookingfor_booking_search';
		if(settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id_base) != -1) {
			var widgetid = bfi_getParameterByName('widget-id', settings.data);
			var cForm = jQuery('input[value="' + widgetid + '"]').parents("form");
			bfi_adminInit(cForm); 
		}
	});
	jQuery(document).on('click','.bfitabsearch input',function(){
		bfi_ShowHideoptSearch(jQuery(this))
	});
	bfi_adminInit(null);
});

function bfi_adminInit(currForm){
	if(!jQuery("#g5-container").length){
		if(currForm!= null && jQuery(currForm).length){
			jQuery(currForm).find(".select2").select2();
			jQuery(currForm).find(".select2full").select2({ width: '100%' });
		}else{
			jQuery(".select2").not('[name*="__i__"]').select2();
			jQuery(".select2full").not('[name*="__i__"]').select2({ width: '100%' });
		}
	}
//	jQuery(".bfitabsearch input").click(function() {
//		bfi_ShowHideoptSearch(jQuery(this))
//	});
	jQuery(".bfitabsearch input").each(function() {
		bfi_ShowHideoptSearch(jQuery(this))
	});
}

function bfi_ShowHideoptSearch(obj){
	if(jQuery(obj).closest("p").find(".bfickbbooking input:checked").length){
		jQuery(obj).closest("p").siblings(".bookingoptions").show();
	}else{
		jQuery(obj).closest("p").siblings(".bookingoptions").hide();
	}
	if(jQuery(obj).closest("p").find(".bfickbrealestate input:checked").length){
		jQuery(obj).closest("p").siblings(".realestateoptions").show();
	}else{
		jQuery(obj).closest("p").siblings(".realestateoptions").hide();
	}
	if(jQuery(obj).attr("checked")) {
		jQuery(obj).closest("p").siblings(".bfitabsearch"+jQuery(obj).val()).show();
	}else{
		jQuery(obj).closest("p").siblings(".bfitabsearch"+jQuery(obj).val()).hide();
	}

}

function bfi_getParameterByName(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}