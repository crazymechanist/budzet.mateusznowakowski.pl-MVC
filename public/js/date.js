let element1 = document.getElementById("limitSet");
let element2 = document.getElementById("limitNotSet");
let arr = [];
let arrName = [];

window.addEventListener('load', function() {
  assing();
  setTimeout("showLimitDialog()", 50);
}, true);

document.getElementById("inputCategory").onchange = function() {
  showLimitDialog();
};

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

function getTodayDate() {
  var today = new Date();
  var dd = today.getDate();
  var mm = today.getMonth()+1; //January is 0!
  var yyyy = today.getFullYear();

  if(dd<10) {
    dd = '0'+dd
  }

  if(mm<10) {
    mm = '0'+mm
  }

  today = yyyy + '-' + mm + '-' + dd;
  return today;
}

function showLimitAmount(){
  element1.classList.remove("d-block");
  element1.classList.add("d-none");
  element2.classList.remove("d-none");
  element2.classList.add("d-block");
}

function hideLimitAmount(){
  element1.classList.remove("d-none");
  element1.classList.add("d-block");
  element2.classList.remove("d-block");
  element2.classList.add("d-none");
}

const config = {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  },
}

async function getArr(){
  try{
    const res   =   await fetch("http://localhost/api/categories/");
    return await res.json();
  } catch (e) {
    console.log("ERROR",e);
    return "Error occurs";
  }
}

function assignNames(item, i){
  arrName[i] = item.name;
}

async function assing(){
  arr = await getArr();
  arr.forEach(assignNames);
}

function showLimitDialog(){
  const path = document.location.pathname;
  let category = document.getElementById("inputCategory").value;
  let index = arrName.indexOf(category);
  if(path.includes('expense')
  && arrName.length !== 0){
    if (index !== -1){
      document.getElementById("limitText").innerHTML=`The limit was set to ${arr[index].month_limit}`
      hideLimitAmount();
    } else {
      showLimitAmount();
    }
  }
}
