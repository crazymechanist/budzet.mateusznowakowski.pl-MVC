/**
	* Add jQuery Validation of item (money flow) object
	*
*/
$(document).ready(function(){
	
	$("#formItem").validate({
		rules: {
			sDate: {
				dateISO: true
			},
			eDate: {
				dateISO: true,
				endDate: true
			},
			min: {
				number: true,
				min: 0.0
			},
			max: {
				number: true,
				min: 0.0,
				maxValue: true
			}
		}
	});
	
	var startDateValue = '';
	var endDateValue = '';
	var minValue = '';
	var maxValue = '';
	
	$( "#sDate" ).change(function() {
		startDateValue = $('#sDate').val();
		endDateValue = $('#eDate').val();
		$("#formItem").validate().element("#eDate");
	});
	
	$( "#eDate" ).change(function() {
		startDateValue = $('#sDate').val();
		endDateValue = $('#eDate').val();
		$("#formItem").validate().element("#eDate");
	});
	
	$( "#period" ).change(function() {
		startDateValue = $('#sDate').val();
		endDateValue = $('#eDate').val();
		$("#formItem").validate().element("#eDate");
	});
	
	$.validator.addMethod("endDate", function(value, element){
		if(startDateValue.length === 0) return true;
		if(endDateValue.length === 0) return true;
		return Date.parse(startDateValue) <= Date.parse(value);
	}, 
	'End Date should be greater or equal to Start Date.'
	);
	
 	$( "#inputMinAmount" ).change(function() {
		minValue = $('#inputMinAmount').val();
		maxValue = $('#inputMaxAmount').val();
		$("#formItem").validate().element("#inputMaxAmount");
	});
	
	$( "#inputMaxAmount" ).change(function() {
		minValue = $('#inputMinAmount').val();
		maxValue = $('#inputMaxAmount').val();
		$("#formItem").validate().element("#inputMaxAmount");
	}); 
	
	$.validator.addMethod("maxValue", function(value, element){
		if(minValue.length === 0) return true;
		if(maxValue.length === 0) return true;
		return minValue <= value;
	}, 
	'Max value should be  greater or equal to Min Amount.'
	);
});


