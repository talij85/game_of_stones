<script type="text/javascript" src="myjava.js"></script>
<?php
include_once("rumors_code.php");
if ($mode == 1) 
{
?>
<link REL="StyleSheet" TYPE="text/css" HREF="styleb.css">
<?php
}
else
{
?>
<link REL="StyleSheet" TYPE="text/css" HREF="stylea.css">
<?php
}
?>
<script language="javascript">
// initialize chat
window.onload=intitializeChat;
</script>
<html>
<head>
</head>
<?php
if ($mode == 1) 
{
?>
<body style="background-image:url('images/excel.gif'); font-family: Verdana, Helvetica, Arial, sans-serif; font-size: 11px; color: #000000; margin: 0px; padding: 0px"> 
<center>
<table cellpadding=0 cellspacing=0>
  <tr><td colspan=3 width=701 align=left></td></tr>
  <tr>
    <td width=50 align=right></td>
    <td>
      <div id='chatdiv' align='left'></div>
      <input type='hidden' id='mid' value='0'/>
    </td>
    <td width=50></td>
  </tr>
  <tr><td colspan=3 width=701 align=right></td></tr>
</table>
</body>
<?php 
}
else
{
?>
<body style="background-image:url('images/GoSBackdrop.gif');background-repeat: repeat-y repeat-x; font-family: Verdana, Helvetica, Arial, sans-serif; font-size: 11px; color: #FFFFFF; margin: 0px; padding: 0px">
<center>
<table cellpadding=0 cellspacing=0>
  <tr><td colspan=3 width=701 align=left><img src='images/TAR/TARtop.gif'></td></tr>
  <tr>
    <td width=50 align=right><img src='images/TAR/TARleft.gif'></td>
    <td>
      <div id='chatdiv' align='left'></div>
      <input type='hidden' id='mid' value='50000'/>
    </td>
    <td width=50><img src='images/TAR/TARright.gif'></td>
  </tr>
  <tr><td colspan=3 width=701 align=right><img src='images/TAR/TARbottom.gif'></td></tr>
</table>
</body>
<?php
}
?>

</html>