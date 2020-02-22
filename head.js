function doPopUp(Base,Pre,Suf)
{
  if (Pre == 0) Pre = '';
  if (Suf == 0) Suf = '';
  url = "itemstat.php?base="+ Base+"&prefix=" + Pre +"&suffix=" + Suf;
  popUp(url);
}

function popUp(URL)
{
  day = new Date();
  id = day.getTime();
  eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=275,height=375,left = 412,top = 234');");
}

function popUp2(URL)
{
  day = new Date();
  id = day.getTime();
  eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=716,height=500,left = 412,top = 234');");
}
function popUp850(URL)
{
  day = new Date();
  id = day.getTime();
  eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=850,height=500,left = 412,top = 234');");
}

function setInfo(y)
{
  document.getElementById('infoBar').innerHTML="<font class='littletext'><font color='6D7795'><b>"+y+"<\/b>";
}

function UpdateTop(message,gold)
{
  if (message) setInfo(message);
  //document.getElementById('goldshow').innerHTML='<font class=foottext><center>Gold: '+gold;
}

// -------------------------------------- FAKE POPUP BOX ------------------------------------------

function timerTime(wTime)
{
  setTimeout('hideMe();',wTime);
}

var isIE=document.all;
var isNN=!document.all&&document.getElementById;
var isN4=document.layers;
var isHot=false;

function hideMe(){
  var theLayer=document.getElementById("theLayer");
  if (isIE||isNN) theLayer.style.visibility="hidden";
  else if (isN4) document.theLayer.visibility="hide";
}

function showMe(aText,aTime){
  var winW = 1000, winH = 600, yOff=0;
  
  if (parseInt(navigator.appVersion)>3) {
    if (navigator.appName=="Netscape") {
      winW = window.innerWidth;
      winH = window.innerHeight;
      yOff = window.pageYOffset;
    }
    if (navigator.appName.indexOf("Microsoft")!=-1) {
      winW = document.body.offsetWidth;
      winH = document.body.offsetHeight;
      yOff = document.body.scrollTop;
    }
  }

  aText = aText.replace('-ap-',"&#39;").replace('-ap-',"&#39;").replace('-ap-',"&#39;");
  var theLayer=document.getElementById("theLayer");
  document.getElementById('popText').innerHTML="<center>"+aText;

  if (isIE||isNN) {
    theLayer.style.visibility="visible";
    theLayer.style.top=winH/2-75+yOff;
    theLayer.style.left=winW/2-125;
  }
  else if (isN4) {
    document.theLayer.visibility="show";
    document.theLayer.top=winH/2-75+yOff;
    document.theLayer.left=winW/2-125;

  }
  if (aTime!=0) timerTime(1100);
}

function popConfirm(aText,aLink)
{
  var buttons="<form><input class='popper' type='button' onClick='window.location="+'"'+aLink+'"'+";' value='Yes'>&nbsp;&nbsp;&nbsp;<input class='popper' type='button' onClick='hideMe();' value='No'/><\/form>";
  showMe("<center>"+aText+"<br/><br/>"+buttons,0);
}

function popConfirmJs(aText,Jscript)
{
  var buttons="<form><input class='popper' type='button' onClick='"+Jscript+"' value='Yes'/>&nbsp;&nbsp;&nbsp;<input class='popper' type='button' onClick='hideMe();' value='No'/><\/form>";
  showMe("<center>"+aText+"<br/><br/>"+buttons,0);
}

// FAKE POPUP INFO

function hideMe2() {
  var theLayer=document.getElementById("theLayer2");
  if (isIE||isNN) theLayer.style.visibility="hidden";
  else if (isN4) document.theLayer.visibility="hide";
}

var infoX = 0;
var infoY = 0;

function showInfo(aText,x,y,page) {
  var theLayer=document.getElementById("theLayer2");
  document.getElementById('popText2').innerHTML="<center><font face='Verdana' color='#DDDDDD' style='font-size: 12px;'>"+aText;
  document.getElementById('popLocHold').name=page;
  if (isIE||isNN) theLayer.style.visibility="visible";
  else if (isN4) document.theLayer.visibility="show";
  infoX=x;
  infoY=y;
  if (page!=-1) {
    theLayer.style.top = (y-MapY+100) + 'px';
    theLayer.style.left = (x-MapX+100) + 'px';
  }
  else {
    theLayer.style.top = y + 'px';
    theLayer.style.left = x + 'px';
  }
}

function updateInfoPos() {
  var theLayer=document.getElementById("theLayer2");
  theLayer.style.top = (infoY-MapY+100) + 'px';
  theLayer.style.left = (infoX-MapX+100) + 'px';
}


// -------------------------------------- FAKE POPUP END ------------------------------------------

function setColors() 
{
<?php
$colorArray = array(
'1' => array('Black','Blue','Brown','DkBlue','DkRed','Green','GreenBlue','OliveGreen','Purple','Red','Yellow'),
'2' => array('Aqua','Black','Blue','Brown','Green','Purple','Red','Yellow'),
'3' => array('Black','Blue','Brown','DkPurple','Green','Purple','Red','Yellow'),
'4' => array('Blue','Brown','Green','LtBlue','Purple','Red','Yellow'),
'5' => array('Blue','Green','GreenGold','Purple','WhiteRed'),
'6' => array('BlueBrown','BlueGreen','BlueOrange','BlueRed','BlueYellow','GreenPurple'),
'7' => array('Aqua','Blue','Brown','Green','Purple','Red','Yellow'),
'8' => array('Black','Blue','Brown','Green','OliveGreen','Red'),
'9' => array('Aqua','Black','Blue','DkGreen','DkPurple','Green','LtBlue','LtGreen','OliveGreen','Purple','Red'),
'10' => array('Aqua','Black','Blue','Brown','DkPurple','Green','LtBlue','LtGreen','LtPurple','Orange','Red','Yellow'),
'11' => array('Blue','Green','Purple','Red'),
'12' => array('Black','Brown','DkBlue','Green','LtGreen','Purple','Red'),
'13' => array('BlueRed','BlueYellow','Green','GreenOrange','Purple'),
);
echo 'var c1 = new Array("', join($colorArray[1],'","'), '");'; 
echo 'var c2 = new Array("', join($colorArray[2],'","'), '");';
echo 'var c3 = new Array("', join($colorArray[3],'","'), '");';
echo 'var c4 = new Array("', join($colorArray[4],'","'), '");';
echo 'var c5 = new Array("', join($colorArray[5],'","'), '");';
echo 'var c6 = new Array("', join($colorArray[6],'","'), '");';
echo 'var c7 = new Array("', join($colorArray[7],'","'), '");';
echo 'var c8 = new Array("', join($colorArray[8],'","'), '");';
echo 'var c9 = new Array("', join($colorArray[9],'","'), '");';
echo 'var c10 = new Array("', join($colorArray[10],'","'), '");';
echo 'var c11 = new Array("', join($colorArray[11],'","'), '");';
echo 'var c12 = new Array("', join($colorArray[12],'","'), '");';
echo 'var c13 = new Array("', join($colorArray[13],'","'), '");';

?>
  var selElem = document.getElementById('style');
  var index = selElem.selectedIndex; 
  var newElem = document.getElementById('color');
  var tmp = '';
  var arr = '';
  newElem.options.length = 0;
  if (index == 1) arr = c1;
  else if (index == 2) arr = c2.slice();
  else if (index == 3) arr = c3.slice();
  else if (index == 4) arr = c4.slice();
  else if (index == 5) arr = c5.slice();
  else if (index == 6) arr = c6.slice();
  else if (index == 7) arr = c7.slice();
  else if (index == 8) arr = c8.slice();
  else if (index == 9) arr = c9.slice();
  else if (index == 10) arr = c10.slice();
  else if (index == 11) arr = c11.slice();
  else if (index == 12) arr = c12.slice();
  else if (index == 13) arr = c13.slice();  
  
  for (var i=0; i<arr.length; i++) 
  {
    tmp = arr[i];
    newElem.options[newElem.options.length] = new Option(tmp,tmp);
  }
  setFlag();
}

function setFlag()
{
  var style = document.getElementById('style').value;
  var color = document.getElementById('color').value;

  document.getElementById('display').style.backgroundImage = "url(images/Flags/Flag"+style+"-"+color+".gif)";
}

function setSigil()
{
  var sigil = document.getElementById('sigil').value;
  if (sigil != "0") document.getElementById('display').innerHTML = "<img src='images/Sigils/"+sigil+".gif' width=160 height=197/>";
  else document.getElementById('display').innerHTML = "&nbsp;";
}

function setClass()
{
  var newElem = document.getElementById('type');
  var nat = document.getElementById('nation').value;
  var crange = 4;
  if (nat == 2) crange =3;
  var ctypes = new Array('Armsman','Wanderer','Woodsman','Channeler');
  
  for (var i=1; i<=crange; i++) 
  {
    tmp = 0;
    newElem.options[newElem.options.length] = new Option(tmp,tmp);
  }
}


function submitKill()
{
  document.killForm.submit();
}

function submitTalismanForm(itm,name,act)
{
  document.taliForm.itemLoc.value=itm;
  document.taliForm.itemName.value=name;
  document.taliForm.action.value=act;
  document.taliForm.submit();
}

function wopen(url, name, w, h)
{
// Fudge factors for window decoration space.
 // In my tests these work well on all platforms & browsers.
w += 32;
h += 96;
 var win = window.open(url,
  name,
  'width=' + w + ', height=' + h + ', ' +
  'location=no, menubar=no, ' +
  'status=no, toolbar=no, scrollbars=no, resizable=no');
 win.resizeTo(w, h);
 win.focus();
}


-->

