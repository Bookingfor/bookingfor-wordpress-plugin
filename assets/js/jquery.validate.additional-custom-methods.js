jQuery(document).ready(function() {
jQuery.validator.addMethod(
	"greaterThanDateITA",  
	function(value, element, param) {      
		var target = jQuery(param).val();
		var isValueNumeric = !isNaN(parseFloat(value)) && isFinite(value); 
		var isTargetNumeric = !isNaN(parseFloat(target)) && isFinite(target);
		if (isValueNumeric && isTargetNumeric) {
			 return Number(value) > Number(target); 
		}
		
		var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
		if( re.test(value) && re.test(target) ){
			var adata = value.split('/');
			var gg = parseInt(adata[0],10);
			var mm = parseInt(adata[1],10);
			var aaaa = parseInt(adata[2],10);
			var xdata = new Date(aaaa,mm-1,gg);
			
			var bdata = target.split('/');
			var bgg = parseInt(bdata[0],10);
			var bmm = parseInt(bdata[1],10);
			var baaaa = parseInt(bdata[2],10);
			var ydata = new Date(baaaa,bmm-1,bgg);
			if ( 
					( xdata.getFullYear() == aaaa ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == gg )
					&&
					( ydata.getFullYear() == baaaa ) && ( ydata.getMonth () == bmm - 1 ) && ( ydata.getDate() == bgg )
				){
					return (xdata > ydata); 
				}
			
		} 
		
		return false;
		
	},'Must be greater than {0}.');

jQuery.validator.addMethod('TwoDecimal', function(value, element) {
		return this.optional(element) || /^[0-9]+(\.\d{0,2})?$/.test(value); 
	}, "Please enter a correct number, format xxxx.xx");

jQuery.validator.addMethod('MinDecimal', function(value, element) {
	var val = parseFloat(value);
	return this.optional(element) || (val != 'NaN' && val > 0); 
}, "Please enter a correct number, format xxxx.xx");

jQuery.validator.addMethod("notEqual", function(value, element, param) {
  return this.optional(element) || value != param;
}, "Please specify a different (non-default) value");
 
jQuery.validator.addMethod("vatCode", function (value, element) {
	value = value.replace(/\s+/g, "").toUpperCase();
	jQuery(element).val(value);
	return this.optional(element) ||
		(
			value.length == 16 &&
			value.match(/^[a-zA-Z]{6}\d{2}[a-zA-Z]\d{2}[a-zA-Z]\d{3}[a-zA-Z]$/) &&
			bficodiceFISCALE(value)
		);
});

function bficodiceFISCALE(cfins) {
	 var cf = cfins.toUpperCase();
	 var cfReg = /^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/;
	 if (!cfReg.test(cf))
	 return false;

	 var set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	 var set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
	 var setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	 var setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
	 var s = 0;
	 for( i = 1; i <= 13; i += 2 )
	 s += setpari.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
	 for( i = 0; i <= 14; i += 2 )
	 s += setdisp.indexOf( set2.charAt( set1.indexOf( cf.charAt(i) )));
	 if ( s%26 != cf.charCodeAt(15)-'A'.charCodeAt(0) )
	 return false;
	 return true;
}
});      
