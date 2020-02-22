<?php
/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php");

$itmlist="";
$listsize=0;
$iresult=mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15 AND type>0");
while ($qitem = mysql_fetch_array($iresult))
{
  $itmlist[$listsize++] = $qitem;
}
  
if ($mode == 1) { include('header2.htm'); }
else {include('header.htm');}
?> 
<script type="text/javascript">
  var inv = new Array();
<?php
  for ($i=0; $i<$listsize; ++$i)
  {
    echo "  inv[".$itmlist[$i][id]."] = '".str_replace(" ","",$itmlist[$i][base])."';";
  }
?>  

  function updateItem(id) 
  {
    document.getElementById('itemImg').alt="items/"+inv[id]+".gif";
    document.getElementById('itemImg').src="items/"+inv[id]+".gif";
  }
  
</script>
  
<font class="littletext">
<center>
<?php 
$namesender = $_COOKIE['name'];
$lastnamesender = $_COOKIE['lastname'];
$nameto="";
$lastnameto="";
$nameto=mysql_real_escape_string($_GET[name]);
$lastnameto=mysql_real_escape_string($_GET[lastname]);
if ($nameto=="" || $lastnameto=="" ||
    (strtolower($nameto) == strtolower($namesender) && strtolowerto($lastname) == strtolower($lastnamesender)))
{
  echo "Quit cheating!";
}
else 
{
?>
<form action="messages.php?trade=1&name=<?php echo $nameto."&last=".$lastnameto;?>" method="post">
  <?php echo $div_img; ?>
  <table border="0" cellspacing="10" cellpadding="3">
    <tr>
      <td>
        <a align="left">
        <b>Trade:</b><br/><br/>
        <select name="item" id="itmList" size="1" class="form" onChange="updateItem(this.options[selectedIndex].value.split('|'));">
        <?php
          $have_item = 0;
          for ($x=0; $x<$listsize; $x++) {
            if (($itmlist[$x][istatus] == 0 || $itmlist[$x][istatus] == -2) && $itmlist[$x][society] == 0) 
            {
              echo "<option value='".$itmlist[$x][id]."'>".iname($itmlist[$x])."</option>\n";
              $have_item=1; 
              if (!$f_item) $f_item=strtolower(str_replace(" ","",$itmlist[$x][base]));
            }
          }
          if (!$have_item) echo "<option value=''>No items</option>";
        ?>
        </select>
      </td>
      <td width="110" height="110" class='foottext' align='center'>
      <?php
        if ($have_item) {
      ?>
        <img src="<?php echo 'items/'.$f_item.'.gif'; ?>" id="itemImg" alt=""/>
      <?php
        }
        else echo "You have no items to trade";
      ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p align="right">in return for
        <img src='images/gold.gif'/>
        <input type="text" name="gold" value="0" class="form" size="5" maxlength="5"/>
        <img src='images/silver.gif'/>
        <input type="text" name="silver" value="0" class="form" size="2" maxlength="2"/>
        <img src='images/copper.gif'/>
        <input type="text" name="copper" value="0" class="form" size="2" maxlength="2"/>
        <br/><br/>
        expires in 
        <select name="time" size="1" class="form">
          <option value="43200">Five days</option>
          <option selected value="259200">Three days</option>
          <option value="86400">One day</option>
          <option value="21600">Six hours</option>
          <option value="3600">One hour</option>
        </select>
        <br/><br/>
        <center>
          <input type="Submit" name="submit" value="Offer Trade" class="form"/>
        </center>
        </p>
      </td>
    </tr>
  </table>
  <?php echo $div_img; ?>
</form>



<?php
}
  include('footer.htm');
?>