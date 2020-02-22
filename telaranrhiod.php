<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
    <script src="gos_cmn.js"></script>
<?php
include_once("chat_code.php");
?>   
<?php
  if ($mode == 1)
  {
?>
  <link rel="stylesheet" href="http://talij.com/gosBootstrap/css/goslitetheme.min.css">
  <link rel="stylesheet" href="gos_style.css">
  <link rel="stylesheet" href="gos_lite.css">
  <style>
body {
  margin: 0;
  color: #000000;
  padding-top: 0px; 
} 
  </style>  
<?php 
  } 
  else 
  {
?>
  <link rel="stylesheet" href="http://talij.com/gosBootstrap/css/gostheme.min.css">
  <link rel="stylesheet" href="gos_style.css">
  <link rel="stylesheet" href="gos_default.css">
  <style>
body {
  margin: 0;
  background-image:url('images/GoSBackdrop.gif'); 
  color: #FFFFFF;
  padding-top: 0px; 
} 
  </style>  
<?php 
  }
?>  
   
  <script language="javascript">
    // initialize chat
    window.onload=intitializeChat;
  </script>
</head>

<body>
  <div class='row'>
    <div class='col-sm-12' align='center'>
      <table class='table-clear' cellpadding=0 cellspacing=0 style="max-width:700px;">
        <tr><td colspan=3 align=left><img src='images/TAR/TARtop.gif' class='hidden-xs img-optional-nodisplay'/></td></tr>
          <tr>
            <td align=right><img src='images/TAR/TARleft.gif' class='hidden-xs img-optional-nodisplay'/></td>
            <td>
              <div id='chatdiv' align='left'></div>
              <input type='hidden' id='mid' value='0'/>
            </td>
            <td><img src='images/TAR/TARright.gif' class='hidden-xs img-optional-nodisplay'/></td>
          </tr>
          <tr><td colspan=3 align=right><img src='images/TAR/TARbottom.gif' class='hidden-xs img-optional-nodisplay'/></td></tr>
          <tr><td colspan=3  align=center>
            <a class='btn btn-lg btn-primary btn-wrap' href="https://www.facebook.com/groups/104773266246790/">Join us on Facebook!</a>
            <a class='btn btn-lg btn-info btn-wrap' href="http://discord.gg/wthbz3U/">Join us on Discord!</a>
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>

</html>