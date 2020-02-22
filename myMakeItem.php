<html>
<head>
<title>GoS Test</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php 
include_once("admin/connect.php");
include_once("admin/userdata.php");
if (strtolower($name) != "the" && strtolower($lastname) != "creator")
{
  echo $name." ".$lastname;
  echo "Only the Creator has such powers!";
}
else
{

include("admin/itemFuncs.php");
$prefix=mysql_real_escape_string($_POST['nqitem_pre']);   
$finditem=mysql_real_escape_string($_POST['nqitem_base']);   
$suffix=mysql_real_escape_string($_POST['nqitem_suf']);
$doGive=mysql_real_escape_string($_POST['giveItem']);
$giveTo=mysql_real_escape_string($_POST['ito']); 

if ($doGive)
{
  if ($giveTo)
  {
      $ftype = $item_base[$finditem][1];
      $istats= itp($item_base[$finditem][0]." ".$item_ix[$prefix]." ".$item_ix[$suffix],$ftype);
      $istats .= getTerMod($ter_bonuses,$ftype,$prefix,$suffix,$item_base[$finditem][2]);
      $ipts= lvl_req($istats,100);
      $itime=time();
      $result = mysql_query("INSERT INTO Items (owner,    type,    cond, istatus,points, society,last_moved,base,       prefix,   suffix,   stats) 
                                        VALUES ('$giveTo','$ftype','100','0',    '$ipts','',     '$itime',  '$finditem','$prefix','$suffix','$istats')");
      $message = "Item given to player with id ".$giveTo;
  }
  else {$message = "Put in an id, buddy..."; }
}

include('header2.htm');
?>
    <form name='makeitemform' method="post" action="myMakeItem.php">
      <table cellpadding=0 cellspacing=10 width='650'>
        <tr>
          <td class='littletext' align=right><div id='nqtarname' name='nqtarname'></div></td>
          <td>
              <select name='nqitem_pre' size='1' class="form">
                <option value=''>None</option>
                <?php 
                  foreach ($item_ix as $pname => $pdata)
                  {
                    $ptemp = explode(" ", $pname);
                    if (count($ptemp) == 1)
                      echo "<option value='".$pname."'>".ucwords($pname)."</option>";
                  }
                ?>
              </select>  
              <select name='nqitem_base' size='1' class="form">
                <?php 
                  foreach ($item_base as $iname => $data)
                  {
                    if ($data[1] != 8 && $data[1] <14)
                      echo "<option value='".$iname."'>".ucwords($iname)."</option>";
                  }
                  echo "<option value='Terangreal'>".Terangreal."</option>";
                ?>
              </select>
              <select name='nqitem_suf' size='1' class="form">
                <option value=''>None</option>
                <?php 
                  foreach ($item_ix as $sname => $sdata)
                  {
                    $stemp = explode(" ", $sname);
                    if (count($stemp) > 1)
                      echo "<option value='".$sname."'>".ucwords($sname)."</option>";
                  }
                ?>
              </select>
          </td>
        </tr>
        <tr>
          <td><input type='hidden' name='giveItem' value='1'/>&nbsp;</td>
          <td>To: <input type='text' name='ito' value='' class="form"></td>
          <td>
            <input type="Submit" name="submit" value="Give Item" class="form">
          </td>
        </tr>
      </table>
    </form>
<?php
}
// Close Database
mysql_close($db);
?>

</body>
</html>