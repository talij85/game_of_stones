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


// bio.php
  function submitFormBio(ddd)
  {
    document.duelform.ddd.value = ddd;
    document.duelform.submit();
  }

  function resize(which) {
    var elem = document.getElementById(which);
    if (elem == undefined || elem == null) return false;
    if (elem.width > elem.height*3/4) {
      if (elem.width > 500) elem.width = 500;
    } else {
      if (elem.height > 325) elem.height = 325;
    }
  } 
  
  function submitREForm(act) // also used by myquests.php
  {
    document.rEquipForm.action.value= act;
    document.rEquipForm.submit();
  }
  
// clanoffice.php  
  function addFormOffice(index, count)
  {
    document.shopform.index.value= index;
    document.shopform.add.value= count;
    document.shopform.submit();
  }

  function sellFormOffice(index, count)
  {
    document.shopform.index.value= index;
    document.shopform.sell.value= count;
    document.shopform.submit();
  }
  
  function restockFormOffice(index, count)
  {
    document.shopform.index.value= index;
    document.shopform.restock.value= count;
    document.shopform.submit();
  }  
  
// clansettings.php
  function submitFormClanSettings(index)
  {
    document.skillform.skill.value= index;
    document.skillform.submit();
  }
  
// item.php
  checked = false;
  function checkedAll () // used in others as well
  {
    if (checked == false){checked = true}else{checked = false}
    for (var i = 0; i < document.getElementById('itemForm').elements.length; i++) 
    {
      document.getElementById('itemForm').elements[i].checked = checked;
    }
  }

  function submitFormItem(act) // also used by myquests.php
  {
    document.itemForm.action.value= act;
    document.itemForm.submit();
  }

  function submitConsumeForm(act)
  {
    document.consumeForm.consume.value= act;
    document.consumeForm.submit();
  }
  
  function submitWeaveForm(isEq)
  {
    var neww = document.weaveForm.newweave.value;
    if (neww <0) alert("You must select a new weave first!");
    document.weaveForm.submit();
  }  
  
  function submitTaliForm(itm,name)
  {
    document.taliForm.taliLoc.value=itm;
    document.taliForm.taliName.value=name;
    document.taliForm.submit();
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

// look.php
  function submitFormLook(id)
  {
    document.duelform.enemyid.value = id;
    document.duelform.submit();
  }
  
// messages.php
  function toggle(elid) 
  {
    var ele = document.getElementById(elid);
    if(ele.style.display == "block") {
      ele.style.display = "none";
    }
    else {
      ele.style.display = "block";
    }
  }
  
// myquests.php
  var labels =new Array(3);
  labels[0]="Target Item";  
  labels[1]="Target Name";
  labels[2]="Target Clan";
  
  function submitQuestForm(sub,type)
  {
    document.questForm.sub.value= sub;
    document.questForm.subtype.value= type;    
    document.questForm.submit();
  }

  function expireQuestForm(ex)
  {
    document.myQuestForm.exq.value= ex;
    document.myQuestForm.submit();
  }
   
  function setTargetNumber()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex; 
    var newElem = document.getElementById('nqtarnum');  
    var num = 5;
    if (index != 0) num = 20
    newElem.options.length = 0;
    for (var i=1; i<=num; i++) 
    {
      newElem.options[newElem.options.length] = new Option(i,i);
    } 
  }
  
  function setOffers()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex; 
    var index2 = document.makequestform.nqreward[1].checked;     
    var newElem = document.getElementById('nqoffers');  
    var num = 5;
    if (index == 1) num = 10;
    else if (index == 2) num = 20;    
    if (index2) num = 1;  
    newElem.options.length = 0;  
    for (var i=1; i<=num; i++) 
    {
      newElem.options[newElem.options.length] = new Option(i,i);
    } 
  }
  
  function setType()
  {
    var selElem = document.getElementById('nqtype');
    var index = selElem.selectedIndex;   
    
    if (index == 0)
    {
      document.getElementById('nqtarname').innerHTML=labels[0];   
      document.getElementById('nqtaritem').style.display = "block";
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "none";            
    }
    else if (index == 1)
    {
      document.getElementById('nqtarname').innerHTML=labels[1];
      document.getElementById('nqtaritem').style.display = "none";
      document.getElementById('nqtarduel').style.display = "block";
      document.getElementById('nqtarclan').style.display = "none";      
    }
    else if (index == 2)
    {
      document.getElementById('nqtarname').innerHTML=labels[2];
      document.getElementById('nqtaritem').style.display = "none";
      document.getElementById('nqtarduel').style.display = "none";
      document.getElementById('nqtarclan').style.display = "block";      
    }
    setOffers();
    setTargetNumber();
  }
  
  function setRewards()
  {
    var index = document.makequestform.nqreward[0].checked;  
    if (index)
    {
      document.getElementById('coinreward').style.display = "block";    
      document.getElementById('itemreward').style.display = "none";
    }
    else
    {
      document.getElementById('coinreward').style.display = "none";    
      document.getElementById('itemreward').style.display = "block";      
    }     
    setOffers(); 
  }
  
// quests.php
  function acceptQuestForm(accept,type)
  {
    document.questForm.accepted.value= accept;
    document.questForm.atype.value= type;    
    document.questForm.submit();
  } 

//townhall.php
  function buyBiz(biz)
  {
    document.bizForm.bought.value= biz;
    document.bizForm.submit();
  } 
    
// trade.php
  function updateItem(name) {
    document.getElementById('itemImg').alt="items/"+(name[0]).replace(" ","").toLowerCase()+".gif";
    document.getElementById('itemImg').src="items/"+(name[0]).replace(" ","").toLowerCase()+".gif";
  }
  
// vault.php
  function submitFormVault(act)
  {
    document.vaultForm.action.value= act;
    document.vaultForm.submit();
  }
  
// world.php
  // instantiate sender XMLHttpRequest object
  var moveXMLHttpObj=getXMLHttpRequestObject();    
    
  var disX = 0;
  var disY = 0;
  var totDist = 0;
  var startX = 0;
  var startY = 0;
  var amtMoved = 100;
  var amtPerSec = 5;
  var UpdateMap = 0;
  var pageon = "";
  var dontGo = 0; 
    
        
  // PRELOAD IMAGES
  var image_url = new Array();
  image_url[0] = 'images/u_scroll_s.gif';
  image_url[1] = 'images/d_scroll_s.gif';
  image_url[2] = 'images/l_scroll_s.gif';
  image_url[3] = 'images/r_scroll_s.gif';


  var TimeLeft = 0;
  var travelTime = 1;
  var travelled = 0;
  var travelPage = '';
  
  function myCounter()
  {
    TimeLeft--;
    if (TimeLeft<0)
    {
      TimeLeft=0;
      if (!travelled)
      {
        travelled=1;
        location.reload(true);
      }
    }
    var t_per = TimeLeft/travelTime;

    if (TimeLeft>0) document.getElementById('travelInfo').innerHTML="<font class='littletext'>"+Math.round(100-t_per*100)+' percent completed';
    else document.getElementById('travelInfo').innerHTML="<font class='medtext'>Arriving at Destination . . ."; 
    
    setTimeout('myCounter()',1000);
  }
  
  function SetMapPos(x,y,slide) {
    x-=5;
    y-=5;
    if (x<0) x=0;
    if (y<0) y=0;
    if (x>240) x=240;
    if (y>165) y=165;
    var theCell = document.getElementById("MapMark");
    if (!slide) {
      theCell.width=x;
      theCell.height=y;
    }
    else {
      startX=theCell.width;
      startY=theCell.height;
      amtMoved=0;
      disX=startX-x;
      disY=startY-y;
      amtPerSec=slide;
      totDist = (5*Math.sqrt(disX*disX+disY*disY));
    }
  }
  function moveStatusChecker()
  {
    // check if request is completed
    if(moveXMLHttpObj.readyState==4){
        if(moveXMLHttpObj.status==200)
        { 
          // alert(moveXMLHttpObj.responseText);      
          var response = moveXMLHttpObj.responseText.split('|');
          
          TimeLeft = response[0];
          travelTime = response[1];
          travelPage=response[2];
          document.getElementById('InfoPage').innerHTML="";
          document.getElementById('TownMap').innerHTML="<font color='#C6CCD8'><center><br><br><br>Preparing to travel somewhere. . .";      
          document.getElementById('TownMap').innerHTML=travelPage; 
          myCounter();
        }
        else{
            alert('Failed to get response :'+ moveXMLHttpObj.statusText);
        }
    }
  }
     
  function setTraveling(loc,mode) {
    var locto='';
    var locnow=Here;
    if (loc) {
      hideMe();
      if (loc==1) locto=Loc[1];
      if (loc==2) locto=Loc[2];
      if (loc==3) locto=Loc[3];
      if (loc==4) locto=Loc[4];
      if (loc==5) locto=Loc[5];
      if (loc==6) locto=Loc[6];
      if (loc==7) locto=Loc[7];
      
      // alert (locto+":"+loc);
      showTravel(locto, loc);     
    }
  }
  
  function showTravel(locto,loc)
  {
      // open socket connection
      moveXMLHttpObj.open('POST','map/traveling.php?',true);
      // set form http header
      moveXMLHttpObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
      moveXMLHttpObj.send('goto='+locto+'&dir='+loc);
      moveXMLHttpObj.onreadystatechange=moveStatusChecker;  
  }

  function mapPopup() 
  {
    popupWindow = window.open(
      'images/map.swf','popUpWindow','height=700,width=890,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')
  }

  function showArea(dir)
  {
    var speed = 35;
    if (dir==-1) {speed=0; dir=0;}
    SetMapPos(Loc[dir][0],Loc[dir][1],speed);
  }
      
  function clickArrow(arrow)
  {
    if (arrowSelected && arrowSelected!=arrow.id) document.getElementById(arrowSelected).src="map/imgs/e.gif";
    arrowSelected=arrow.id;
    if (Loc[arrow.name]) {
      popConfirm('Take the road to '+Loc[arrow.name].replace('-ap-',"&#39;")+'?','javascript:setTraveling('+arrow.name+',0)');
    }
  }
      
  function leaveArrow(arrow)
  {
    if (arrow.name % 2 == 1) 
    {
        arrow.src="map/places/imgs/e.gif";
    }
    else 
    {
        arrow.src="map/places/imgs/w.gif";
    }
  }
      
  function overArrow(arrow)
  {
    if (Loc[arrow.name])
    {
      if (arrow.name % 2 == 1) 
      {
        arrow.src="map/places/imgs/e_s.gif";
      }
      else 
      {
        arrow.src="map/places/imgs/w_s.gif";
      }
    }
  }  