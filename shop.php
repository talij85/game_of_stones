<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/itemFuncs.php");
include_once("admin/locFuncs.php");

// Check if city has been destroyed. If so, don't display anything.
if (!$location[isDestroyed])
{
$message = mysql_real_escape_string($_GET[message]);
$tab = mysql_real_escape_string($_GET['tab']);

$loc = $char['location'];

$type_discounts[1]=$town_bonuses[dP] + $town_bonuses[tV];
$type_discounts[2]=$town_bonuses[xP] + $town_bonuses[tV];
$type_discounts[3]=$town_bonuses[pP] + $town_bonuses[tV];
$type_discounts[4]=$town_bonuses[oP] + $town_bonuses[tV];
$type_discounts[5]=$town_bonuses[bP] + $town_bonuses[tV];
$type_discounts[6]=$town_bonuses[kP] + $town_bonuses[tV];
$type_discounts[7]=$town_bonuses[hP] + $town_bonuses[tV];
$type_discounts[8]=$town_bonuses[vP] + $town_bonuses[tV];
$type_discounts[9]=$town_bonuses[rP] + $town_bonuses[tV];
$type_discounts[10]=$town_bonuses[lP] + $town_bonuses[tV];
$type_discounts[11]=$town_bonuses[cP] + $town_bonuses[tV];
$type_discounts[12]=$town_bonuses[tP] + $town_bonuses[tV];
$type_discounts[13]=$town_bonuses[tP] + $town_bonuses[tV];

$wikilink = "Shops";

if ($message == '')
 $message = "<b>Shop</b> of ".str_replace('-ap-','&#39;',$loc);
$buy = mysql_real_escape_string($_GET[buy]);

$shoplvls=unserialize($location['shoplvls']);
$listsize=0;

foreach ($shoplvls as $n => $iclans)
{
  $type = floor($n/100);
  $index = $n-$type*100;
  $inum = 0;
  foreach ($iclans as $c=>$cnum)
  {
    $inum+=$cnum;
  }

  if ($type != 11) $litm=$item_list[$type][$index];
  else $litm=$cloak_list[$location[id]][$index];

  if ($inum != 0)
  {
    $displayType[$type][$index]=1;
    $itmlist[$n][0] = $litm;
    $itmlist[$n][1] = "";
    $itmlist[$n][2] = "";
    $itmlist[$n][3] = $type;
    $itmlist[$n][4] = $inum; 
    $itmlist[$n][5] = 100;   
    $listsize=count($itmlist);
  }
}

// BUY ITEM
$time = time();
{
  if (iname_list($buy-1,$itmlist) == $_GET[name] && $buy>0)
  {
    $buy--;
    if ($item_base[$itmlist[$buy][0]][1]>13) $worth = $item_base[$itmlist[$buy][0]][2];
    else $worth = item_val(itp($item_base[$itmlist[$buy][0]][0]." ".$item_ix[$itmlist[$buy][1]]." ".$item_ix[$itmlist[$buy][2]],$item_base[$itmlist[$buy][0]][1]))*($type_discounts[$item_base[$itmlist[$buy][0]][1]]+100)/100;
    if ($char[gold] >= $worth)
    {
      $itmsize = mysql_num_rows(mysql_query("SELECT * FROM Items WHERE owner='$id' AND type<15"));
      if ($itmsize < $inv_max)
      {
        $char[gold]-=$worth; 
        $base= $itmlist[$buy][0];
        $itype=$item_base[$base][1];
        $istats= itp($item_base[$itmlist[$buy][0]][0]." ".$item_ix[$itmlist[$buy][1]]." ".$item_ix[$itmlist[$buy][2]],$item_base[$itmlist[$buy][0]][1]);
        $ipts= lvl_req($istats,100);
        $itime=time();
        $result = mysql_query("INSERT INTO Items (owner,type,    cond, istatus,points, society,last_moved,base,   prefix,suffix,stats) 
                                          VALUES ('$id','$itype','100','0',    '$ipts','',     '$itime',  '$base','',    '',    '$istats')");
        $ustats = mysql_fetch_array(mysql_query("SELECT * FROM Users_stats WHERE id='$id'"));
        $ustats['item_earn'] -= $worth;
        $ustats['spent_shop'] += $worth;
        $result = mysql_query("UPDATE Users_stats SET item_earn='".$ustats[item_earn]."', spent_shop='".$ustats['spent_shop']."' WHERE id='".$id."'");
        mysql_query("UPDATE Users SET gold='$char[gold]', lastbuy='$time' WHERE id=$id");
        $worth2 = intval(0.5*$worth);
        
        $seller = 0;
        $clan_cut = floor(($worth2*.8));
        $ruler_cut = $worth2-$clan_cut;
        
        $clancount=0;
        foreach ($shoplvls[$buy] as $n => $a)
        {
          if ($a != 0) $clans[$clancount++]=$n;
        }
        $sellclan = $clans[rand(0,count($clans)-1)];

        if ($sellclan != 0)
        {
          $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE id='".$sellclan."' "));
          $society[bank] += $clan_cut;
          mysql_query("UPDATE Soc SET bank='".$society[bank]."' WHERE id='".$sellclan."'");
        }
        else
        {
          $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='".$location['ruler']."' "));
          $society[bank] += $clan_cut;
          mysql_query("UPDATE Soc SET bank='".$society[bank]."' WHERE name='".$location['ruler']."'");
        }

        $location[bank] += $ruler_cut;
        mysql_query("UPDATE Locations SET bank='".$location[bank]."' WHERE name='$loc'");
        
        if ($shoplvls[$buy][$sellclan]>0)
        {
          $shoplvls[$buy][$sellclan]--;
          $shoplvls_str= serialize($shoplvls);
          mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE id='".$location[id]."'");
          $itmlist[$buy][4]--;
        }
        
        $message = mysql_real_escape_string($_GET[name])." purchased for ".displayGold($worth);
      }
      else 
      {
        $message = "You have no more room to carry that.";
      }      

      $time = time();
      $bought_something = 1;
    }
    else
      $message = "You do not have enough gold";
  }
  else if ($buy >0)
    $message = "The shopkeeper gives you a confused look.";
}



// DRAW PAGE
if ($mode != 1) 
{
  $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
}
include('header.htm');
?>

<script type="text/javascript">
function marketBuy(bid,bs)
{
  document.getElementById('bizid').value=bid;
  document.getElementById('bizslot').value=bs;
  document.marketForm.submit();
}
</script>

  <div class="row solid-back">
    <div class="col-sm-12">
      <div id="content">
        <ul id="shoptabs" class="nav nav-tabs" data-tabs="tabs">
        <?php
          $types = 0;
          for ($t=1; $t<=13; ++$t)
          {
            if ($displayType[$t])
            {
              $isActive="";
              if ($tab && $tab==$t) $isActive="class='active'";
              else if (!$tab && $types==0) $isActive="class='active'";
              echo "<li ".$isActive."><a href='#".str_replace(" ","_",$item_type[$t])."_tab' data-toggle='tab'>".ucwords($item_type[$t])."</a></li>";
              $types++;
            }
          }
        ?>
          <li <?php if ($tab == 99) echo "class='active'";?>><a href="#market_tab" data-toggle="tab">Market</a></li>
        </ul>

        <div class="tab-content">
        <?php
          $types = 0;
          for ($t=1; $t<=13; ++$t)
          {
            if ($displayType[$t])
            {
              $isActive="";
              if ($tab && $tab==$t) $isActive="active";
              else if (!$tab && $types==0) $isActive="active";            
              echo "<div class='tab-pane ".$isActive."' id='".str_replace(" ","_",$item_type[$t])."_tab'>";
              $types++;

              for ($i = 0; $i < 13; ++$i)
              {
                if ($displayType[$t][$i])
                {
                  echo "<div class='col-sm-5ths'>";
                  $x = 100*$t+$i;
                  $points = lvl_req($item_base[$itmlist[$x][0]][0]." ".$item_ix[$itmlist[$x][1]]." ".$item_ix[$itmlist[$x][2]], 100);
                  if ($item_base[$itmlist[$x][0]][1] >13) $worth = $item_base[$itmlist[$x][0]][2];
                  else $worth = (item_val(itp($item_base[$itmlist[$x][0]][0]." ".$item_ix[$itmlist[$x][1]]." ".$item_ix[$itmlist[$x][2]],$item_base[$itmlist[$x][0]][1]))*($type_discounts[$t]+100)/100);
                  if ($worth > $char[gold]) 
                    $color = "danger"; 
                  else 
                    $color = "success";
                  $shopinfo[$t][$i] = "<div class='panel panel-".$color."'><div class='panel-heading'><h3 class='panel-title'>".iname_list($x,$itmlist)."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(" ","",$itmlist[$x][0]).".gif'/>";
                  $shopstats[$t][$i] = itm_info(cparse($item_base[$itmlist[$x][0]][0]." ".$item_ix[$itmlist[$x][1]]." ".$item_ix[$itmlist[$x][2]],$item_base[$itmlist[$x][0]][1]));
                  $shopinfo[$t][$i] .= "<p>".displayGold($worth)."<br/>";
                  $shopinfo[$t][$i] .= "Equip points: ".$points."<br/>";
                  $shopinfo[$t][$i] .= "Available:";
                  if ($itmlist[$x][4] > 0) $shopinfo[$t][$i] .= $itmlist[$x][4]."</p>";
                  else $shopinfo[$t][$i] .= "Unlimited</p>";
                  $shopinfo[$t][$i] .= "<button type='button' class='btn btn-info btn-sm btn-wrap' data-html='true' data-toggle='popover' data-placement='top' data-content='".$shopstats[$t][$i]."'>Info</button>";                  
                  if ($worth <= $char[gold]) 
                  {
                    $shopinfo[$t][$i] .= "<a data-href=\"shop.php?buy=".($x+1)."&tab=".$t."&name=".iname_list($x,$itmlist)."\" data-toggle='confirmation' data-placement='top' title='Buy this item?' class='btn btn-sm btn-wrap btn-success'>Buy</a>";                
                  }
                  $shopinfo[$t][$i] .= "</div></div>";
                  echo $shopinfo[$t][$i];
                  echo "</div>";
                }
              }
            ?>
          </div>
    <?php
        }
      }
    ?>
          <div class="tab-pane <?php if ($tab == 99) echo 'active';?>" id="market_tab">
          <?php 
            $foundnum=0;
            $farr='';
            $result = mysql_query("SELECT * FROM Profs WHERE location='$char[location]' AND type=0");  
            while ($bq = mysql_fetch_array( $result ) )
            {
              $tbis = unserialize($bq[status]);
              for ($i=0; $i <9; $i++)
              {
                if ($tbis[$i][0]==1) // is offer
                {
                  $icost = $tbis[$i][1][1]; 
                  $tname = strtolower(iname($tbis[$i][1][0]));  
                  $statstr = iparse($tbis[$i][1][0],$item_base, $item_ix,$ter_bonuses);
                  $teq = lvl_req($statstr,100);
                  $os = mysql_fetch_array(mysql_query("SELECT society,jobs FROM Users WHERE id='".$bq[owner]."'"));
                  $costmult = 1;
                  if ($char[society] != "" && $os[society] != "")
                  {
                    $oups=unserialize($bq[upgrades]);
                    $enemyMult= (100+$oups[1][0]*5 + $oups[1][2]*3)/100;

                    if ($stance[str_replace(" ","_",$os['society'])] == 1)
                    {
                      $costmult = $allyMult;
                    }
                    elseif ($stance[str_replace(" ","_",$os['society'])] == 2)
                    {
                      $costmult = $enemyMult;
                    }
                  }
                  
                  $farr[$foundnum]=$tbis[$i][1];
                  $farr[$foundnum][3]=$tname;
                  $farr[$foundnum][4]=$tstats;
                  $farr[$foundnum][5]=$bq[location];
                  $farr[$foundnum][6]=$teq;
                  $farr[$foundnum][7]=0;
                  $farr[$foundnum][8]=array($bq[id],$i);
                    
                  $farr[$foundnum][1] = intval($farr[$foundnum][1] * $costmult);
                  $foundnum++;        
                }
              }
            }
            // SORT RESULTS
            $sarr='';
            for ($f=0; $f < $foundnum; $f++)
            {
              $tmin = -1;
              for ($g=0; $g < $foundnum; $g++)
              {
                if ($farr[$g][7]) // already sorted; skip
                { }
                elseif ($tmin == -1) // first unsorted
                {
                  $tmin=$g;
                }
                else
                {
                  if ($farr[$g][1] < $farr[$tmin][1])
                  {
                    $tmin=$g;
                  }
                }
              }
              $sarr[$f]=$tmin;
              $farr[$tmin][7]=1;
            } 
            
            // DRAW RESULTS
            $count = 0;
            for ($x=0; $x < $foundnum; $x++)
            {
              $worth = $farr[$sarr[$x]][1];
              echo "<div class='col-sm-5ths'>";
              $worth = $farr[$sarr[$x]][1];
              $points = lvl_req($item_base[$farr[$sarr[$x]][0][base]][0]." ".$item_ix[$farr[$sarr[$x]][0][prefix]]." ".$item_ix[$farr[$sarr[$x]][0][suffix]], 100);
              if ($worth > $char[gold]) 
                $color = "danger"; 
              else 
                $color = "success";
              $shopinfo = "<div class='panel panel-".$color."'><div class='panel-heading'><h3 class='panel-title'>".ucwords($farr[$sarr[$x]][3])."</h3></div><div class='panel-body abox' align='center'><img class='img-responsive hidden-xs img-optional-nodisplay' border='0' bordercolor='black' src='items/".str_replace(" ","",$farr[$sarr[$x]][0][base]).".gif'/>";
              $shopstats = itm_info(cparse($item_base[$farr[$sarr[$x]][0][base]][0]." ".$item_ix[$farr[$sarr[$x]][0][prefix]]." ".$item_ix[$farr[$sarr[$x]][0][suffix]],$item_base[$farr[$sarr[$x]][0][base]][1]));              
              $shopinfo .= "<p>".displayGold($worth)."<br/>";
              $shopinfo .= "Equip points: ".$points."<br/>";
              $shopinfo .= "Status: ".$farr[$sarr[$x]][0][cond]."%<br/>";
              $shopinfo .= "<button type='button' class='btn btn-info btn-sm btn-wrap' data-html='true' data-toggle='popover' data-placement='top' data-content='".$shopstats."'>Info</button>";                  
              if ($worth <= $char[gold]) 
              {
                $shopinfo .= "<a data-href=\"javascript:marketBuy(".$farr[$sarr[$x]][8][0].",".$farr[$sarr[$x]][8][1].")\" data-toggle='confirmation' data-placement='top' title='Buy this item?' class='btn btn-sm btn-wrap btn-success'>Buy</a>";                
              }
              $shopinfo .= "</div></div>";
              echo $shopinfo;
              echo "</div>";                      
            }       
          ?>
            <form name='marketForm' method="post" action="market.php">
              <input type='hidden' name='bizid' id='bizid' val='-1'><input type='hidden' name='bizslot' id='bizslot' val='-1'>
            </form>   
          </div>
        </div> <!-- close tab content -->
      </div> <!-- close tabbable -->
    </div> <!-- close outer col -->
  </div> <!-- close row -->
<?php
} // !isDestroyed
else
{
  $message = "The remains of the Shop in ".$location[name];
  if ($mode != 1) 
  {
    $bg = "background-image:url('images/townback/".str_replace(' ','_',strtolower($char['location'])).".jpg'); ";
  }
  include('header.htm');  
}
include('footer.htm');
?>