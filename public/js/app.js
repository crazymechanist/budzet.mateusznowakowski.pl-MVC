	/**
	* Add jQuery Validation plugin method dor a valid password
	*
	*
	* Valid password contain at least one letter and one number
	*/
	$.validator.addMethod('validPassword',
		function(value, element, param) {
			if(value, element, param) {
				if(value.length === 0){
					return true;
				}
				if(value.match(/.*[a-z]+.*/i) == null){
					return false;
				}
				if(value.match(/.*\d+.*/) == null){
					return false;
				}
			}
			return true;
		},
		'Must contain at least one letter and one number'
	);