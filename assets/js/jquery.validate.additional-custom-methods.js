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
 
