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

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

/*
*****************************************************************
AJAX-Based Chat System
Author: Alejandro Gervasio
Version: 1.0
*****************************************************************
*/

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
   var user=getCookie('name')+" "+getCookie('lastname');
   document.getElementById('chatlabel').innerHTML="Logged in as: "+user;
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
      var parts = messages[(messages.length-1)-i].split('`');
      nparts = parts[0].substring(1,parts[0].length-1).split('_');
      a.setAttribute('href','bio.php?name='+nparts[0]+'&last='+nparts[1]);
      if (id==0)
      {
        a.setAttribute('target',"_parent");
      }
      a.className='btn btn-xs '+nparts[2];
      if (!nparts[3]){ nparts[3]='1000000000'}
      var postTime = millTime-nparts[3];
      var sentTime = "Seconds ago";
      var minpast= Math.floor(postTime/60);
      if (minpast >=2880) sentTime = Math.floor(minpast/1440)+" days ago"; 
      else if (minpast >=1440) sentTime= "A day ago";
      else if (minpast >= 120) sentTime = Math.floor(minpast/60)+" hours ago";
      else if (minpast >=60) sentTime= "A hour ago";
      else if (minpast > 1) sentTime = minpast+" minutes ago";
      else if (minpast > 0) sentTime = "A minute ago";
 

      var b=document.createElement('b');
      b.appendChild(document.createTextNode(nparts[0]+" "+nparts[1]+" ("+sentTime+")"));
      a.appendChild(b);
      p.appendChild(a);
      p.appendChild(document.createTextNode(parts[1]));
      mdiv.appendChild(p);
   }
}
// send user message
function sendMessage(){
   var user='<?php echo $user?>';
   if (user.length > 50)
   {
     alert("You are not logged in!");
   }
   else
   {
   var currTime = new Date();
   var millTime = Math.floor(currTime.getTime()/1000);  
   var id=document.getElementById('mid').value;
   var message=document.getElementById('chatmessage').value;
   message=encodeURIComponent(message);
   document.getElementById('chatmessage').value='';
   if(message.length>0)
   {
    // open socket connection
    senderXMLHttpObj.open('POST','sendchatdata.php?id='+id+'&time='+millTime,true);
    // set form http header
    senderXMLHttpObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    senderXMLHttpObj.send('user='+user+'&message='+message);
    senderXMLHttpObj.onreadystatechange=senderStatusChecker;
   }
   }
}
// create messages board
function createMessageBoard(){
   var mdiv=document.createElement('div');
   mdiv.setAttribute('id','messages');
   document.getElementById('chatdiv').appendChild(mdiv);
}
// create message input box
function createMessageBox(id){
   // create message box container
   var mdiv=document.createElement('div');
   mdiv.setAttribute('id','messagebox');
   
   // create message form
   var mform=document.createElement('form');
   
   // create message box
   var mbox=document.createElement('input');
   mbox.setAttribute('type','text');
   mbox.setAttribute('name','chatmessage');
   mbox.setAttribute('id', 'chatmessage');
   mbox.className='form-control gos-form input-sm';
   mbox.setAttribute('size','100%');
   mbox.setAttribute('maxlength','200');
  	
   // create 'send' button
   var mbutton=document.createElement('input');
   mbutton.setAttribute('type','button');
   mbutton.setAttribute('value','Send');
   mbutton.className='btn btn-xs btn-success';
   mbutton.onclick=sendMessage;

   // create sender label
   var mlabel=document.createElement('div');
   mlabel.setAttribute('id','chatlabel');
   mlabel.className='div-inline';
   
   // append elements
   mform.appendChild(mbox);
   mform.appendChild(mbutton);
   mform.appendChild(mlabel);
   mdiv.appendChild(mform);
   document.getElementById('chatdiv').appendChild(mdiv);
   mbox.focus();
   mbox.onfocus=function(){this.value='';}
}
// initialize chat 
function intitializeChat(){
   if(document.getElementById&&document.getElementsByTagName&&document.createElement){
      createMessageBoard();
      createMessageBox();
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