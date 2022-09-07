/**
	* Add jQuery Validation of item (money flow) object
	*
*/
$(document).ready(function(){
	
	$("#formItem").validate({
		rules: {
			name: "required",
			date: {
				required: true,
				dateISO: true,
				dateRange: true
			},
			category: {
				required: true,
				remote: '/category/validate-category'
			},
			amount: {
				required: true,
				number: true,
				min: 0.01
			}
		},
		messages: {
			category: {
				remote: 'This category is not set'	
			}
		}
	});
	
	var fromDate = new Date("2000-01-01");
	var toDate = new Date("2030-12-31");
	
	$.validator.addMethod('dateRange',
		function(value, element, param) {
			var date = new Date(value);
			if(value, element, param) {
				if(date >= fromDate && date <= toDate) return true;
			}
			return false;
		},
		'Date must be from ' + fromDate.toISOString().split('T')[0] + ' to ' + toDate.toISOString().split('T')[0]
	);
	
});	