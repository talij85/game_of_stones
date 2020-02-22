// getXMLHttpRequest object
function getXMLHttpRequestObject(){
   var xmlobj;

    // check for existing requests
    if(xmlobj!=null&&xmlobj.readyState!=0&&xmlobj.readyState!=4){
        xmlobj.abort();
    }
    if (window.XMLHttpRequest) {
        // instantiate object for Mozilla, Nestcape, etc.
        xmlobj=new XMLHttpRequest();
    }
    else if ( window.ActiveXObject ) 
    {
      try 
      {
        xmlobj = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch( e ) 
      {
        xmlobj = new ActiveXObject("Msxml2.XMLHTTP");
      }
    }
    
    if (xmlobj==null) {
        return false;
    }
    return xmlobj;
}

  function submitTravelForm(toLoc)
  {
    document.travelForm.toLoc.value = toLoc;
    document.travelForm.submit();
  }

  function submitEscortTravelForm(eId)
  {
    document.travelForm.escortId.value = eId;
    document.travelForm.submit();
  }

  function submitWaysTravelForm(wId)
  {
    document.travelForm.waysId.value = wId;
    document.travelForm.submit();
  }
    
  function resortForm(sortBy) // also used by myquests.php & vault.php
  {
    document.sortForm.sort.value = sortBy;
    document.sortForm.submit();
  }

  function resortForm2(sortBy)
  {
    document.sortForm2.sort2.value = sortBy;
    document.sortForm2.submit();
  }
  
  checked = false;
  function checkedAll () // used in others as well
  {
    if (checked == false){checked = true}else{checked = false}
    for (var i = 0; i < document.getElementById('itemForm').elements.length; i++) 
    {
      document.getElementById('itemForm').elements[i].checked = checked;
    }
  }
  
  function checkToggle(field,check)
  {
    var i = 0;
    if (check)
    {
      for (i = 0; i < field.length; i++)
	field[i].checked = true ;
    }
    else  
    {
      for (i = 0; i < field.length; i++)
	field[i].checked = false ;
    }
  }   
  
$(document).on('click', '.panel-heading span.clickable', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-minus').addClass('glyphicon-plus');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});
$(document).on('click', '.panel div.clickable', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-minus').addClass('glyphicon-plus');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});
$(document).ready(function () {
    $('.panel-heading span.clickable').click();
    $('.panel div.clickable').click();
});  

function popConfirmJs(aText,Jscript)
{
  var buttons="<form><input class='popper' type='button' onClick='"+Jscript+"' value='Yes'/>&nbsp;&nbsp;&nbsp;<input class='popper' type='button' onClick='hideMe();' value='No'/><\/form>";
  showMe("<center>"+aText+"<br/><br/>"+buttons,0);
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
  document.getElementById('popText').innerHTML="<center><font face='Verdana' color='#CCCCC' style='font-size: 12px;'>"+aText;

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