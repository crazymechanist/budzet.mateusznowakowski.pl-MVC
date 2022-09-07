function getYYYYMMDD(date) {
	var day = date.getDate();
	var month = date.getMonth()+1;
	var year = date.getFullYear();
	
	if (day<10) day = "0" +day;
	if (month<10) month = "0" +month;
	if (year<10) year = "0" +seconds;
	return year + '-' + month + '-' + day;
}

var date = new Date();
var firstDayOfCurrentMonth = new Date(date.getFullYear(), date.getMonth(), 1);
var lastDayOfCurrentMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
var firstDayOfLastMonth = new Date(date.getFullYear(), date.getMonth()-1, 1);
var lastDayOfLastMonth = new Date(date.getFullYear(), date.getMonth()-1 + 1, 0);

$("#period").change(function() {
	if($('#period').val()=="none"){
		$('#sDate').val("");
		$('#eDate').val("");
	}
	if($('#period').val()=="this"){
		$('#sDate').val(getYYYYMMDD(firstDayOfCurrentMonth));
		$('#eDate').val(getYYYYMMDD(lastDayOfCurrentMonth));
	}
	if($('#period').val()=="last"){
		$('#sDate').val(getYYYYMMDD(firstDayOfLastMonth));
		$('#eDate').val(getYYYYMMDD(lastDayOfLastMonth));
	}
});

$("#page").change(function() {
	$( "#formItem" ).submit();
});

$("#page").change(function() {
	$( "#formItem" ).submit();
});

$("#clear").change(function() {
	$( "#formItem" ).submit();
});

$("#period").click(function() {
	$("#sort").val("date-asc");
	$("#page").val("1");
});

$("#sDate").change(function() {
	if($('#sDate').val == getYYYYMMDD(firstDayOfCurrentMonth) && $('#eDate').val == getYYYYMMDD(lastDayOfCurrentMonth)){
		$('#period').val("this");
	}
});

$("#eDate").change(function() {
	if( $('#eDate').val() == getYYYYMMDD(lastDayOfCurrentMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfCurrentMonth)){
		$('#period').val("this");
	}
	else if( $('#eDate').val() == getYYYYMMDD(lastDayOfLastMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfLastMonth)){
		$('#period').val("last");
	} else {
		$('#period').val("none");
	}
});

$("#sDate").change(function() {
	if( $('#eDate').val() == getYYYYMMDD(lastDayOfCurrentMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfCurrentMonth)){
		$('#period').val("this");
	}
	else if( $('#eDate').val() == getYYYYMMDD(lastDayOfLastMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfLastMonth)){
		$('#period').val("last");
	} else {
		$('#period').val("none");
	}
});

$(document).ready(function() {
	if( $('#eDate').val() == getYYYYMMDD(lastDayOfCurrentMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfCurrentMonth)){
		$('#period').val("this");
	}
	else if( $('#eDate').val() == getYYYYMMDD(lastDayOfLastMonth) && $('#sDate').val() == getYYYYMMDD(firstDayOfLastMonth)){
		$('#period').val("last");
	} else {
		$('#period').val("none");
	}
});	