<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php" );
include_once("admin/locFuncs.php" );

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
$id=$char['id'];
$loc_name = str_replace('-ap-','&#39;',$char['location']);
$loc_query = $char['location'];
$soc_name = $char[society];

$shoplvls=unserialize($location['shoplvls']);
$society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));
$subleaders = unserialize($society['subleaders']);
$subs = $society['subs'];
$offices = unserialize($society['offices']);
$areascore = unserialize($society['area_score']);
$locscore = $areascore[$location[id]];

$ss=($town_bonuses[sS]+100)/100;
$is=$town_bonuses[iS];
$rs=$town_bonuses[rS];

$itmslots = floor($locscore/500)+10+$is;
$maxlvl= unserialize(getShopUpLvls($locscore-$rs,$shopmax[$loc_query]));

$message = $soc_name." Office in ".$loc_name;
$wikilink = "Clan+Office";

$office_reqs = array(0,0,1000,3000,10000,20000,40000,9999999);

$build = mysql_real_escape_string($_POST[build]);
$officer = mysql_real_escape_string($_POST[officer]);
$nindex = mysql_real_escape_string($_POST[index]);
$add = mysql_real_escape_string($_POST[add]);
$sell = mysql_real_escape_string($_POST[sell]);
$abandon = mysql_real_escape_string($_POST[abandon]);
$restock = mysql_real_escape_string($_POST[restock]);
$tab = mysql_real_escape_string($_GET['tab']);
$transGood = mysql_real_escape_string($_POST[transGood]);
$transFrom = mysql_real_escape_string($_POST[transFrom]);
$transAmount = mysql_real_escape_string($_POST[transAmount]);

// KICK OFF PAGE IF NOT CLAN LEADER
$b = 0; 

// leader
if (strtolower($name) == strtolower($society[leader]) && strtolower($lastname) == strtolower($society[leaderlast]) ) 
{
  $b=1;
}
// subleader
if ($subs > 0)
{
  foreach ($subleaders as $c_n => $c_s)
  {
    if (strtolower($name) == strtolower($c_s[0]) && strtolower($lastname) == strtolower($c_s[1]) )
    {
      $b=1;
    }
  }
}
// local officer
if ($offices[$location[id]][0] != '1')
{
  if (strtolower($name) == strtolower($offices[$location[id]][0]) && strtolower($lastname) == strtolower($offices[$location[id]][1]) ) 
  {
    $b=1;  
  }
}
if ($b==0)
{
 // header("Location: $server_name/world.php?time=$time&message=Stop trying to cheat");
  exit;
}

$slotc=0;
      
for ($type=1; $type<12; ++$type)
{
  for ($lvl=0; $lvl < $maxlvl[$type]; ++$lvl)
  {
    $index = $type*100+$lvl;
           
    if ($shoplvls[$index][$society[id]]>0)
    {
      $acount = $shoplvls[$index][$society[id]];
      while ($acount)
      {
        $slotc++;
        if ($acount <= 10)
        {
          $iavail = $acount;
          $acount = 0;
        }
        else
        {
          $iavail = 10;
          $acount -= 10;
        }
      }
    }
  }
}

// add inventory
if ($add != "")
{
  $atype = floor($nindex/100);
  $alvl = $nindex-100*$atype;
  if ($atype != 11) $alitm = $item_list[$atype][$alvl];
  else $alitm = $cloak_list[$location[id]][$alvl];
  $cost = intval((item_val(itp($item_base[$alitm][0]." ".$item_ix[""]." ".$item_ix[""],$atype))*10/4)*$ss);

  if ($society[bank] >= $cost)
  {
    if ($slotc<$itmslots )
    {
      $society[bank] -= $cost;
      mysql_query("UPDATE Soc SET bank='".$society[bank]."' WHERE name='$soc_name'");  
      updateSocRep($society, $location[id], $cost);  
    
      $shoplvls[$nindex][$society[id]] = $shoplvls[$nindex][$society[id]]+10;
      $shoplvls_str= serialize($shoplvls);
      mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE name='$loc_query'");
    
      $message = ucwords($alitm)." added to your shop!";
    }
    else
    {
      $message = "You are already using the maximum number of slots you can right now!";
    }
  }
  else
  {
    $message = "You don't have enough money!";
  }
}

// sell inventory
if ($sell != "")
{
  $stype = floor($nindex/100);
  $slvl = $nindex-100*$stype;
  $ival = item_val(itp($item_base[$item_list[$stype][$slvl]][0]." ".$item_ix[""]." ".$item_ix[""],$stype))*$sell/8;

  if ($shoplvls[$nindex][$society[id]]>=$sell)
  {
    $society[bank] += $ival;
    $area_rep = unserialize($society[area_rep]);
    $area_rep[$location[id]] -= $ival/5000;
    $area_reps = serialize($area_rep);
    mysql_query("UPDATE Soc SET bank='".$society[bank]."', area_rep='".$area_reps."' WHERE name='$soc_name'");
    
    $shoplvls[$nindex][$society[id]] = $shoplvls[$nindex][$society[id]]-$sell;
    $shoplvls_str= serialize($shoplvls);
    mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE name='$loc_query'");
    
    $message = $sell." ".ucwords($item_list[$stype][$slvl])." sold for ".displayGold($ival)."!";
  }
  else
  {
    $message = "Something odd happened during your transaction! Please try again!";
  }
}

// restock inventory
if ($restock != "")
{
  $rtype = floor($nindex/100);
  $rlvl = $nindex-100*$rtype;
  $cost = intval((item_val(itp($item_base[$item_list[$rtype][$rlvl]][0]." ".$item_ix[""]." ".$item_ix[""],$rtype))*($restock)*11/40)*$ss);

  if ($society[bank] >= $cost)
  {
    if (($shoplvls[$nindex][$society[id]]+$restock)%10 == 0)
    {
      $society[bank] -= $cost;
      mysql_query("UPDATE Soc SET bank='".$society[bank]."' WHERE name='$soc_name'");
      updateSocRep($society, $location[id], $cost);
    
      $shoplvls[$nindex][$society[id]] = $shoplvls[$nindex][$society[id]]+$restock;
      $shoplvls_str= serialize($shoplvls);
      mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE name='$loc_query'");
    
      $message = ucwords($item_list[$rtype][$rlvl])." restocked to your shop!";
    }
    else
    {
      $message = "Something odd happened during your transaction! Please try again!";
    }
  }
  else
  {
    $message = "You don't have enough money!";
  }
}
// Build Office
if ($build)
{
  if (!($offices[$location[id]]))
  { 
    if ($office_reqs[count($offices)] <= $society[score])
    {
      $cost = 10*$office_reqs[count($offices)];
      if ($cost <= $society[bank])
      {
        $society['bank'] -= $cost;
        $offices[$location[id]][0] = '1';
        $area_rep = unserialize($society[area_rep]);
        $area_rep[$location[id]] += 100;
        $area_reps = serialize($area_rep);
        $offices_ser = serialize($offices);
        mysql_query("UPDATE Soc SET offices='$offices_ser', bank='".$society[bank]."', area_rep='".$area_reps."' WHERE name='$soc_name'");
        $message = "Office successfully built!";
      }
      else
      { $message = "You don't have enough money!"; }
    }
    else
    { $message = "Your clan hasn't earned enough Ji for another office yet!";}
  }
  else 
  { $message= "One office in this town should be enough"; }
}

// set local officer
if ($officer)
{
  if ($officer != '-1')
  {
    $result = mysql_query("SELECT * FROM Users WHERE id='$officer'");
    $char_sub = mysql_fetch_array($result);
    $offices[$location[id]]= array($char_sub[name],$char_sub[lastname]);
  }
  else $offices[$location[id]]= array('1');
  
  $offices_ser = serialize($offices);
  $result = mysql_query("UPDATE Soc SET offices='$offices_ser' WHERE name='$soc_name'");
  $message="Character added as Officer";   
}


// Abandon the office
if ($abandon != "")
{
    $sell_total=0;
    for ($type=1; $type<12; ++$type)
    {
        for ($lvl=0; $lvl < $maxlvl[$type]; ++$lvl)
        {
          $index = $type*100+$lvl;              
          if ($shoplvls[$index][$society[id]]>0)
          {
            $sell_total = item_val(itp($item_base[$item_list[$type][$lvl]][0]." ".$item_ix[""]." ".$item_ix[""],$type))*$shoplvls[$index][$society[id]]/8;
            $shoplvls[$index][$society[id]] = 0;
          }
        }
    }
    $society[bank] += $sell_total;
    $area_rep = unserialize($society[area_rep]);
    $area_rep[$location[id]] -= ($sell_total/5000) + 500;
    $area_reps = serialize($area_rep);
    unset($offices[$location[id]]);
    $offices_ser = serialize($offices);
    mysql_query("UPDATE Soc SET offices='$offices_ser', bank='".$society[bank]."', area_rep='".$area_reps."' WHERE name='$soc_name'");

    $shoplvls_str= serialize($shoplvls);
    mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE name='$loc_query'");
    
    $message = "Office abandoned.";
}

$countups=0;
for ($type=1; $type<12; ++$type)
{
  for ($lvl=0; $lvl < $maxlvl[$type]; ++$lvl)
  {
    $index = $type*100+$lvl;
    if ($shoplvls[$index][$society[id]] > 0)
    {
      $countups += floor($shoplvls[$index][$society[id]]/10)+1;
    }
  }
}
if ($countups >= 25) $office_img = 5;
elseif ($countups >= 20) $office_img = 4;
elseif ($countups >= 15) $office_img = 3;
elseif ($countups >= 10) $office_img = 2;
else $office_img = 1;

// Transfer Goods
if ($transFrom)
{
  $gname = $estate_unq_prod[$transGood];
  // From Clan
  if ($transFrom == 99)
  {
    $cgoods = unserialize($society[goods]);
    if ($cgoods[$transGood] >= $transAmount)
    {
      $cgoods[$transGood] -= $transAmount;
      $location[$gname] += $transAmount;
      $scgoods = serialize($cgoods);
      $society[goods] = $scgoods;
      $result = mysql_query("UPDATE Soc SET goods='$scgoods' WHERE name='$soc_name'");
      $result2 = mysql_query("UPDATE Locations SET ".$gname."='".$location[$gname]."' WHERE name='$loc_query'");
      $message = "Transferred ".$transAmount." ".ucwords($gname)." from the clan to ".$location[name];
    }
    else { $message = "The clan does not have that much ".ucwords($gname)."!"; }
  }
  else // From City
  {
    $city = mysql_fetch_array(mysql_query(
              "SELECT id, name, grain, livestock, lumber, stone, fish, luxury, ruler FROM Locations WHERE id='$transFrom'"));
    if ($city[id])
    {
      if ($city[$gname] > $transAmount)
      {
        $maxTransfer = floor(($city[$gname] + $location[$gname])/2) - $location[$gname];
        if ($maxTransfer >= $transAmount)
        {
          $city[$gname] -= $transAmount;
          $location[$gname] += $transAmount;
          $result2 = mysql_query("UPDATE Locations SET ".$gname."='".$city[$gname]."' WHERE name='$city[name]'");
          $result2 = mysql_query("UPDATE Locations SET ".$gname."='".$location[$gname]."' WHERE name='$loc_query'");
          $message = "Transferred ".$transAmount." ".ucwords($gname)." from the ".$city[name]." to ".$location[name];
        }
        else { $message = "A transfer cannot cause the receiving city to end with more than the transferring city (Max ".ucwords($gname)." to transfer: ".$maxTransfer.")"; }
      }
      else { $message = $city[name]." does not have that much ".ucwords($gname)."!"; }
    }
    else { $message = "Something strange happened selecting where to transfer from..."; }
  }
}

if (!$tab) $tab = 1;
include('header.htm');

?> 
  <div class="row solid-back">
    <div class='col-sm-12'>

    <?php
      if ($offices[$location[id]])
      {
    ?>    
      <div class='row'>
        <div class='col-sm-4 col-sm-push-4'>
          <p class='h5'> Clan Bank: <?php echo displayGold($society[bank]); ?></p>
          <?php
            if ($offices[$location[id]][0] != '1')
            {
          ?>
          <p class='h5'>Local Officer: <?php echo $offices[$location[id]][0]." ".$offices[$location[id]][1];?></p>
          <?php
            }
          ?>
          <form action="clanoffice.php" method="post">
            <div class="form-group form-group-sm">
              <label for="officer">Change Local Officer:</label>
              <select id="officer" name="officer" size="1" class="form-control gos-form">  
                <option value="-1">None</option>
              <?php
                // GET ALL SOCIETY MEMBERS
                $result = mysql_query("SELECT id, name, lastname FROM Users WHERE society='$soc_name' ORDER BY lastname, name");
                $numpeople = mysql_num_rows($result);    
        
                $b = 0;
                $x = 0;
                while ($x < $numpeople)
                { 
                  $charnew = mysql_fetch_array($result);
                  if ( strtolower($charnew[name]) != strtolower($char[name]) || strtolower($charnew[lastname]) != strtolower($char[lastname]) )
                  {
                    $b=1;
                    foreach ($subleaders as $c_n => $c_s)
                    {
                      if (strtolower($charnew[name]) == strtolower($c_s[0]) && strtolower($charnew[lastname]) == strtolower($c_s[1]) )
                      {
                        $b=0;
                      }
                    }
                  }
                  if ($b ==1)
                  {
              ?>
                <option value="<?php echo $charnew[id]; ?>"><?php echo $charnew[name]." ".$charnew[lastname]; ?></option>
              <?php
                  }
                  $x++;
                  $b = 0;
                }
              ?>
              </select>
              <input type="Submit" name="submit" value="Set Officer" class="btn btn-sm btn-primary">
            </div> 
          </form>
          <form action="clanoffice.php" method="post">
            <div class="form-group form-group-sm">
              <label for="abandoning">Abandon this Office:</label>
              <select id="abandon" name="abandon" size="1" class="form-control gos-form">  
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
              <input type="Submit" name="submit" value="Abandon" class="btn btn-sm btn-danger">
            </div> 
          </form>          
        </div>
      </div>
      <div class='row'>
        <div class='col-sm-12'>
          <div id="content">
            <ul id="offtabs" class="nav nav-tabs" data-tabs="tabs">
              <li <?php if ($tab == 1) echo "class='active'";?>><a href="#shop_tab" data-toggle="tab">Clan Shop</a></li>
              <li <?php if ($tab == 2) echo "class='active'";?>><a href="#add_tab" data-toggle="tab">Add Items</a></li>
              <li <?php if ($tab == 3) echo "class='active'";?>><a href="#goods_tab" data-toggle="tab">Trade Goods</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane <?php if ($tab == 1) echo 'active';?>" id="shop_tab">
                <form name="shopform" action="clanoffice.php" method="post">
                  <input type="hidden" name="index" id="index" value="" />
                  <input type="hidden" name="sell" id="sell" value="" />
                  <input type="hidden" name="restock" id="restock" value="" />
                  <div class='row'>
                    <div class='col-sm-5 col-md-4 col-lg-3 hidden-xs'>
                      <img src="images/office<?php echo $office_img;?>.gif" width='200'/>
                    </div>
                    <div class='col-sm-7 col-md-8 col-lg-9'>
                      <table class='table table-responsive table-condensed table-stripped table-hover table-clear small'>
                        <tr>
                          <th>Slot</th>
                          <th>Item</th>
                          <th>Avail</th>
                          <th>Sell Price</th>
                          <th>Restock Cost</th>
                          <th>Actions</th>
                        </tr>
                        <?php
                          $slotc=0;      
                          for ($type=1; $type<12; ++$type)
                          {
                            for ($lvl=0; $lvl < $maxlvl[$type]; ++$lvl)
                            {
                              $index = $type*100+$lvl;              
                              if ($shoplvls[$index][$society[id]]>0)
                              {
                                $acount = $shoplvls[$index][$society[id]];
                                while ($acount)
                                {
                                  $slotc++;
                                  if ($acount <= 10)
                                  {
                                    $iavail = $acount;
                                    $acount = 0;
                                  }
                                  else
                                  {
                                    $iavail = 10;
                                    $acount -= 10;
                                  }
                                  if ($type != 11) $litm=$item_list[$type][$lvl];
                                  else $litm=$cloak_list[$location[id]][$lvl];
                                  echo "<tr><td>".$slotc."</td>";
                                  echo "<td>".ucwords($litm)."</td>";
                                  echo "<td>".$iavail."</td>";
                                  $ival = item_val(itp($item_base[$litm][0]." ".$item_ix['']." ".$item_ix[''],$type));
                                  echo "<td>".displayGold($ival*$iavail/8)."</td>";
                                  echo "<td>".displayGold(intval(($ival*(10-$iavail)*11/40)*$ss))."</td>";
                                  echo "<td><input type='button' name='sell".$index."' value='Sell' onclick='javascript: sellFormOffice(".$index.",".$iavail.");' class='btn btn-xs btn-danger'> ";
                                  echo "<input type='button' name='restock".$index."' value='Restock' onclick='javascript: restockFormOffice(".$index.",".(10-$iavail).");' class='btn btn-xs btn-primary'></td></tr>";
                                }
                              }
                            }
                          }
                          echo "<tr><td colspan=6>".$slotc." of ".$itmslots." slots used</td></tr>";
                        ?>
                      </table>
                    </div>
                  </div>
                </form>
              </div>                
              <div class="tab-pane <?php if ($tab == 2) echo 'active';?>" id="add_tab">  
                <form name="addform" action="clanoffice.php" method="post">
                  <input type="hidden" name="index" id="index" value="" />
                  <input type="hidden" name="add" id="add" value="" />                      
                  <div class='row'>
                  <?php
                    for ($type=1; $type<12; ++$type)
                    {
                      if ($maxlvl[$type] > 0)
                      {
                  ?>
                    <div class='col-sm-6 col-md-4 col-lg-3'>    
                      <table class='table table-responsive table-condensed table-stripped table-hover table-clear small'>
                        <tr>
                          <th><?php echo ucwords($item_type[$type]);?></th>
                          <th>Total</th>
                          <th>Cost</th>
                          <th>Action</th>
                        </tr>
                        <?php 
                          for ($lvl=0; $lvl < $maxlvl[$type]; ++$lvl)
                          {
                            $index = $type*100+$lvl;
                           
                            $inum = 0;
                            if ($shoplvls[$index])
                            {
                              foreach ($shoplvls[$index] as $c=>$cnum)
                              {
                                $inum+=$cnum;
                              }
                            }
                          
                            if ($type != 11) $litm=$item_list[$type][$lvl];
                            else $litm=$cloak_list[$location[id]][$lvl];
                          
                            if ($inum >=0) 
                            {
                              echo "<tr>";
                              echo "<td align='center'>".ucwords($litm)."</td>";
                              echo "<td align='center'>".$inum."</td>";
                              echo "<td align='center'>".displayGold(item_val(itp($item_base[$litm][0]." ".$item_ix['']." ".$item_ix[''],$type))*10/4*$ss)."</td>";
                              if ($slotc < $itmslots)
                                echo "<td align='center'><input type='button' name='submit".$index."' value='Buy 10' onclick='javascript: addFormOffice(".$index.");' class='btn btn-xs btn-success'></td>";
                              else
                                echo "<td>&nbsp;</td>";
                              echo "</tr>";
                            }
                          }
                        ?>
                      </table>
                    </div>
                    <div class="clearfix visible-xs"></div>
                  <?php
                      } 
                    }
                  ?>
                  </div>
                </form>
              </div>
              <div class="tab-pane <?php if ($tab == 3) echo 'active';?>" id="goods_tab">
                <?php
                  echo "<table class='table table-responsive table-condensed table-hover table-clear small'>";
                  echo "<tr><th>City/Clan</th><th>Fish</th><th>Lumber</th><th>Stones</th>";
                  echo "<th>Luxuries</th><th>Livestock</th><th>Grains</th></tr>";
                  $ruled = "";
                  $rnum = 0;
                  $cgoods = unserialize($society[goods]);
                  echo "<tr><td align='center'><a href='#' class='btn btn-xs btn-block btn-wrap btn-info'>".$society[name]."</a></td>";
                  for ($i=1; $i<7; $i++)
                  {
                    echo "<td align='center'>".$cgoods[$i]."</td>";
                  }
                  echo "</tr>";
                  for ($l=1; $l<=24; $l++)
                  {
                    if ($offices[$l])
                    {
                      $city = mysql_fetch_array(mysql_query(
                                "SELECT id, name, grain, livestock, lumber, stone, fish, luxury, ruler FROM Locations WHERE id='$l'"));
                      if ($city[ruler]==$society[name])
                      {
                        $ruled[$rnum++] = $city;
                        $bstyle = "btn-success";
                      }
                      else
                      {
                        $bstyle = "btn-warning";
                      }
                      echo "<tr><td align='center'><a href='#' class='btn btn-xs btn-block btn-wrap ".$bstyle."'>".$city[name]."</a></td>"; 
                      echo "<td align='center'>".$city[fish]."</td><td align='center'>".$city[lumber]."</td>";
                      echo "<td align='center'>".$city[stone]."</td><td align='center'>".$city[luxury]."</td>";
                      echo "<td align='center'>".$city[livestock]."</td><td align='center'>".$city[grain]."</td></tr>"; 
                    }
                  }
                  echo "</table>";
                ?>
                <div class='row'>
                  <div class='col-xs-12'>
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                        <h3 class="panel-title">Transfer Goods</h3>
                      </div>
                      <div class="panel-body abox">
                        <div class='row'>
                          <form class='form-inline' name="transForm" action="clanoffice.php" method="post">  
                            <div class="form-group form-group-sm col-sm-3">
                              <label for="transGood">Good Type:</label>
                              <select id="transGood" name='transGood' class="form-control gos-form">
                              <?php
                                for ($i=1; $i<7; $i++)
                                {
                              ?>
                                <option value="<?php echo $i;?>"><?php echo ucwords($estate_unq_prod[$i]);?></option>
                              <?php
                                }
                              ?>
                              </select>
                            </div>
                            <div class="form-group form-group-sm col-sm-3">
                              <label for="transFrom">Transfer From:</label>
                              <select id="transFrom" name='transFrom' class="form-control gos-form">
                                <option value="99"><?php echo $society[name];?></option>
                              <?php
                                for ($i=0; $i<$rnum; $i++)
                                {
                                  if ($ruled[$i][id] != $location[id])
                                  {
                              ?>
                                <option value="<?php echo $ruled[$i][id];?>"><?php echo $ruled[$i][name];?></option>
                              <?php
                                  }
                                }
                              ?>
                              </select>
                            </div>
                            <div class="form-group form-group-sm col-sm-3">
                              <label for="transAmount">Amount:</label>
                              <input type="text" id="transAmount" name='transAmount' value='0' class="form-control gos-form">
                            </div>
                            <div class="col-sm-3">
                              <input type="submit" class="btn btn-success btn-sm btn-block" value="Transfer"/>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>        
        </div>
      </div>
    <?php
      }
      else
      {
    ?>
      <div class='row'>
        <div class='col-sm-4 col-sm-push-4'>
          <p class='h5'> Clan Bank: <?php echo displayGold($society[bank]); ?></p>
          <form name="shopform" action="clanoffice.php" method="post">
            <div class="form-group form-group-sm">
              <label>Bulid Local Office?</label><br/>
              <label>Cost:</label>       
              <?php
                echo displayGold(10*$office_reqs[count($offices)]);
              ?>
              <br/>
              <label>Global Ji Needed:</label>
              <?php
                echo count($offices)." ".$office_reqs[count($offices)];
              ?>
              <br/>
              <input type="hidden" name="build" value="1" id="build" />
              <input type="Submit" name="bulidsub" value="Bulid" class="btn btn-sm btn-success">
            </div>
          </form>
    <?php    
      }
    ?>
    </div>
  </div>
<script type='text/javascript'>
  function addFormOffice(index, count)
  {
    document.addform.index.value= index;
    document.addform.add.value= count;
    document.addform.submit();
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
</script>
<?php
} // !isDestroyed
else
{
  $message = "No Clan Offices remain in ".$location[name];
  if ($mode == 1) { include('header2.htm'); }
  else 
  {
    $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
    include('header.htm');
  }
}
include('footer.htm');
?>