let elLimitSet = document.getElementById("limitSet");
let elLimitNotSet = document.getElementById("limitNotSet");
let switchCategory = document.getElementById("inputCategory");
let switchDate = document.getElementById("inputDate");
let inputAmount = document.getElementById("inputAmount");
let hideInput = document.getElementById("type");
let createButton = document.getElementById("createButton");
let arr = [];
let arrName = [];
let error = '';
let leftToSpend =0;

window.addEventListener('load', async function() {
  let today = await getTodayDate();
  await assing(today);
  await showLimitDialog(0);

}, true)

switchCategory.onchange = function() {
  createButton.disabled = false;
  showLimitDialog();
}

switchDate.onchange = async function() {
  let date = switchDate.value;
  createButton.disabled = false;
  await assing(date);
  await showLimitDialog();
}

inputAmount.onchange = function() {
  createButton.disabled = false;
  showLimitDialog();
}

function getYYYYMMDDstring(d , type='') {
  let date = new Date(d);

  if (type == 'first'){
    date = new Date(date.getFullYear(), date.getMonth(), 1);
  }
  if (type == 'last'){
    date = new Date(date.getFullYear(), date.getMonth()+ 1, 0);
  }

  let day = date.getDate();
  if (day<10) day = "0" +day;
  let month = date.getMonth()+1;
  if (month<10) month = "0" +month;
  let year = date.getFullYear();
  if (year<10) year = "0" +year;
  return year + '-' + month + '-' + day;
}

function getTodayDate() {
  let today = new Date();
  today = getYYYYMMDDstring(today);
  return today;
}

function showLimitAmount(){
  elLimitSet.classList.remove("d-block");
  elLimitSet.classList.add("d-none");
  elLimitNotSet.classList.remove("d-none");
  elLimitNotSet.classList.add("d-block");
}

function hideLimitAmount(){
  elLimitSet.classList.remove("d-none");
  elLimitSet.classList.add("d-block");
  elLimitNotSet.classList.remove("d-block");
  elLimitNotSet.classList.add("d-none");
}

const config = {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  },
}

async function getArr(date){
  let sDate = await getYYYYMMDDstring(date , type='first');
  let eDate = await getYYYYMMDDstring(date , type='last');
  try{
    let res = await fetch(`http://localhost/api/categories/${sDate}and${eDate}`);
      return await res.json();
    } catch (e) {
      console.log("ERROR",e);
      error = e;
    }
  }

  function assignNames(item, i){
    arrName[i] = item.name;
  }

  async function assing(date){
    try{
      arr = await getArr(date);
      arr.forEach(assignNames);
    } catch (e) {
      console.log("ERROR",e);
      error = e;
    }
  }

  function showLimitDialog(){
    const type = hideInput.value;
    let category = switchCategory.value;
    let index = arrName.indexOf(category);
    if(type.includes('expense')
    && arrName.length !== 0){
      if (index !== -1){
        leftToSpend = arr[index].limit-arr[index].sum-inputAmount.value;
        let text = `The limit was set to ${arr[index].limit}`;
        if(leftToSpend>=0){
          text += `, there are ${leftToSpend.toFixed(2)} left to spend.`;
        } else {
          let debt = -leftToSpend;
          text += `, there are nothing left to spend. ${debt.toFixed(2)} above limit`;
          createButton.disabled = true;
        }
        document.getElementById("limitText").innerHTML=text;
        hideLimitAmount();
      } else {
        showLimitAmount();
      }
    }
    if (error != '') {
      document.getElementById("limitSet").innerHTML="Error occured!";
      hideLimitAmount();
    }
    if (error =='' && arrName.length === 0) {
      showLimitAmount();
    }
  }
