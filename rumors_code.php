<?php
// get user name
//session_start();

include_once ("admin/connect.php");
include_once ("admin/userdata.php");

$user = $char[name]." ".$char[lastname];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<meta http-equiv="Content-Type" content="text/xml; charset=iso-8859-1" />
<script language="javascript">
/*
*****************************************************************
AJAX-Based Chat System
Author: Alejandro Gervasio
Version: 1.0
*****************************************************************
*/

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

// check status of sender object
function senderStatusChecker(){
    // check if request is completed
    if(senderXMLHttpObj.readyState==4){
        if(senderXMLHttpObj.status==200){
         // if status == 200 display chat data
         displayChatData(senderXMLHttpObj);         
        }
        else{
            //alert('Failed to get response :'+ senderXMLHttpObj.statusText);
        }
    }
}
// check status of receiver object
function receiverStatusChecker(){
    // if request is completed
    if(receiverXMLHttpObj.readyState==4){
        if(receiverXMLHttpObj.status==200){
         // if status == 200 display chat data
         displayChatData(receiverXMLHttpObj);
        }
        else{
            alert('Failed to get response :'+ receiverXMLHttpObj.statusText);
        }
    }
}
// get messages from database each 5 seconds
function getChatData(){
   var id=document.getElementById('mid').value;
   receiverXMLHttpObj.open('GET','getchatdata.php?id='+id,true);
   receiverXMLHttpObj.send('work');
   receiverXMLHttpObj.onreadystatechange=receiverStatusChecker;
   setTimeout('getChatData()',60*1000);
}
// display messages
function displayChatData(reqObj)
{
   // remove previous messages
   var mdiv=document.getElementById('messages');
   if(!mdiv){return};
   mdiv.innerHTML='';
   var messages=reqObj.responseText.split('|');
   var id=document.getElementById('mid').value;
   var currTime = new Date();
   var millTime = Math.floor(currTime.getTime()/1000);   
   // display messages
   for(var i=1;i<messages.length;i++){
      var p=document.createElement('p');
      p.className='chatp small';
      var a=document.createElement('a');
      a.setAttribute('href','#');
      a.className='btn btn-xs btn-warning';      
      var parts = messages[(messages.length-1)-i].split('`');
      nparts = parts[0].substring(1,parts[0].length-1).split('_');
      if (!nparts[1]){ nparts[1]='1000000000'}
      var postTime = millTime-nparts[1];
      var sentTime = "Seconds ago";
      var minpast= Math.floor(postTime/60);
      if (minpast >=2880) sentTime = Math.floor(minpast/1440)+" days ago"; 
      else if (minpast >=1440) sentTime= "A day ago";
      else if (minpast >= 120) sentTime = Math.floor(minpast/60)+" hours ago";
      else if (minpast >=60) sentTime= "A hour ago";
      else if (minpast > 1) sentTime = minpast+" minutes ago";
      else if (minpast > 0) sentTime = "A minute ago";

      var b=document.createElement('b');
      
      b.appendChild(document.createTextNode(nparts[0]+" <"+sentTime+">:"));
      a.appendChild(b);
      p.appendChild(a);
      p.appendChild(document.createTextNode(parts[1]));
      mdiv.appendChild(p);
   }
}

// create messages board
function createMessageBoard(){
   var mdiv=document.createElement('div');
   mdiv.setAttribute('id','messages');
   document.getElementById('chatdiv').appendChild(mdiv);
}

// initialize chat 
function intitializeChat(){
   if(document.getElementById&&document.getElementsByTagName&&document.createElement){
      createMessageBoard();
      getChatData();
   }
   document.onkeypress = stopRKey;
}

// instantiate sender XMLHttpRequest object
var senderXMLHttpObj=getXMLHttpRequestObject();
// instantiate receiver XMLHttpRequest object
var receiverXMLHttpObj=getXMLHttpRequestObject();

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  
  { 
    sendMessage();
    return false;
  }
} 

 
</script>