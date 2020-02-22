<?php
// include class file
require_once ("mysql_class.php");
include_once ("admin/connect.php");

$id=mysql_real_escape_string($_GET['id']);
$msgs = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='".$id."'"));
$msg = unserialize($msgs[message]);
// send messages to the client
for ($i = 0; $i < count($msg); $i++)
{
  $output = str_replace("-ap-","'",$msg[$i]);
  $output = str_replace("&amp;","&",$output);  
  $output = str_replace("&quot;","\"",$output);
  $output = str_replace("&gt;",">",$output);
  $output = str_replace("&lt;","<",$output); 
    $output = str_replace("&#39;","'",$output);   

  echo $output;
}
?>