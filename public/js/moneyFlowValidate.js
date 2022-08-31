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
				dateISO: true
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
});