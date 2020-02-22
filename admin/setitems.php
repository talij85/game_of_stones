<?php
include_once("connect.php");
include_once("displayFuncs.php");
include_once("itemFuncs.php");

// array ( name , number owned , item type , bonus , equipped position [if equipped]) :: OLD AND NO LONGER TRUE. SAME WITH NEXT COMMENT.
// in case of angreal or such: item type is 7, bonus is percent by which one power is multiplied (ex. 250%)

$base= "stone knife";
$itype=$item_base[$base][1];
$istats= $item_base[$base][0];
$ipts= lvl_req($istats,100);
$result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                  VALUES ('$id','$itype','100','1',    '$ipts','',     '$base','',    '',    '$istats')");
$base= "wool pants";
$itype=$item_base[$base][1];
$istats= $item_base[$base][0];
$ipts= lvl_req($istats,100);
$result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                  VALUES ('$id','$itype','100','2',    '$ipts','',     '$base','',    '',    '$istats')");
$base= "wool shirt";
$itype=$item_base[$base][1];
$istats= $item_base[$base][0];
$ipts= lvl_req($istats,100);
$result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                  VALUES ('$id','$itype','100','3',    '$ipts','',     '$base','',    '',    '$istats')");
if ($item == "1")
{
  $base= "short sword";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "2")
{
  $base= "hatchet";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "3")
{
  $base= "sharpened stick";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "4")
{
  $base= "sling";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "5")
{
  $base= "cudgel";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "6" || $item == "7")
{
  $base= "wooden shield";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','',     '$base','',    '',    '$istats')");
}
else if ($item == "8" && $sex==0)
{
  $base= "firespark";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','10000','$base','',    '',    '$istats')");
}
else if ($item == "8" && $sex==1)
{
  $base= "water spike";
  $itype=$item_base[$base][1];
  $istats= $item_base[$base][0];
  $ipts= lvl_req($istats,100);
  $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,base,   prefix,suffix,stats) 
                                    VALUES ('$id','$itype','100','5',    '$ipts','10000','$base','',    '',    '$istats')");
}

$base= "old broth";
$itype=$item_base[$base][1];
$istats= $item_base[$base][0];
$result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points,society,base,   prefix,suffix,stats) 
                                  VALUES ('$id','$itype','100','0',    '0',   '',     '$base','',    '',    '$istats')");
?>