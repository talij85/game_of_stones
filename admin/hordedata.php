<?php
//echo ($msgs[checktime]/6)."<".(time()/3600);
//if (floor($msgs[checktime]/6) < floor(time()/3600))
{
  mysql_query("LOCK TABLES Hordes WRITE, Users WRITE, Locations WRITE, Estates WRITE, Soc WRITE, messages WRITE, Items WRITE;");
  $makehorde=0;
  $horde_present=0;
  $hstime = intval (time()/3600);
  $result3 = mysql_query("SELECT id, ends, done, target, location FROM Hordes WHERE type='1' AND done<'2'");
  $num_hordes = mysql_num_rows($result3);
  if ($num_hordes)
  {
    while ($myHorde = mysql_fetch_array( $result3 ) )
    {
      // If past time for horde to end or all seals have broken, end the horde.
      if ($myHorde[ends]*3600<=time() || $numBroken >= 7)
      {
        if ($myHorde[done] == 1) $newdone=2;
        else $newdone=3;
        
        $result4= mysql_query("UPDATE Hordes SET done='$newdone' WHERE id = '$myHorde[id]'");
        $hstime=$myHorde[ends];
        
        // Horde Attack!
        if ($newdone==3)
        {
          $attacked = mysql_fetch_array(mysql_query("SELECT id, bank, upgrades, shoplvls, shopg, name, chaos, army FROM Locations WHERE name='$myHorde[target]'"));
          
          // Lose seal if it has one.
          $sealid = $attacked[id]+50000;
          $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
          if ($hasSeal)
          {
            $result5 = mysql_query("DELETE FROM Items WHERE type=0 && owner='$sealid'");
          }
          
          // Half the coin in city bank is lost.
          $attacked[bank]=  floor($attacked[bank]/2);
          
          // The targeted City loses it's highest upgrade (selected randomly if tied).
          $aups = unserialize($attacked[upgrades]);
          for ($i=0; $i < 2; $i++)
          {
            $highest = "";
            $c=0;
            $high=-1;
            for ($j=0; $j<6; $j++)
            {
              if ($aups[$j] >= $high)
              {
                if ($aups[$j] > $high) 
                {
                  $high = $aups[$j];
                  $highest = "";
                  $c=0;
                }
                $highest[$c]=$j;
                $c++;
              }
            }
            if ($aups[6])
            {
              $highest[$c]=6;
              $c++;  
            }
            if ($aups[7])
            {
              $highest[$c]=7;
              $c++;  
            }
            
            $adown= rand(0,count($highest)-1);
            if ($aups[$highest[$adown]] > 0) $aups[$highest[$adown]]--;     
          }
          $saups = serialize($aups);
          
          // Half the stock in city shops is lost, rounded up. This includes Shop as well as consumables.
          $ashop = unserialize($attacked[shoplvls]);
          foreach ($ashop as $n => $iclans)
          {
            foreach ($iclans as $c=>$cnum)
            {
              $ashop[$n][$c]= floor($ashop[$n][$c]/2);
            }
          }
          $sashop= serialize($ashop);
          
          $acons = unserialize($attacked[shopg]);
          for ($t=19; $t< 22; $t++)
          {
            for ($i =0; $i < 4; $i++)
            {
              for ($j=0; $j<12; $j++)
              {
                $acons[$t][$i][$j] = floor($acons[$t][$i][$j]/2);
              }
            }
          }
          $sacons = serialize($acons);
          
          // Apply damages to city defenses
          $armyDmg = $attacked[army]-$npc_info[1][1];
          $attacked[army] -= round($armyDmg/2);
          if ($attacked[army] < 1000) $attacked[army] = 1000;
          
          // updated database, including adding chaos
          mysql_query("UPDATE Locations SET bank='$attacked[bank]', upgrades='$saups', shoplvls='$sashop', shopg='$sacons', chaos=chaos+50, army='$attacked[army]' WHERE name='$myHorde[target]'");

          // All players in the targeted City get moved to the Wilderness area the horde attacked from. 
          $locusers = mysql_query("SELECT id, location FROM Users WHERE location='$attacked[name]'");
          while ($luser= mysql_fetch_array($locusers))
          {
            $luser[location]=$myHorde[location];
            mysql_query("UPDATE Users SET location='$luser[location]' WHERE id='$luser[id]'");
          }
          
          // Each Estate in the Wilderness loses 2 highest level upgrades (selected randomly if tied).
          $hloc = $myHorde[location];
          $eresult = mysql_query("SELECT id, upgrades FROM Estates WHERE location='$hloc'");
          while ($lestate= mysql_fetch_array($eresult))
          {
            $eups = unserialize($lestate[upgrades]);
            for ($i=0; $i < 2; $i++)
            {
              $highest = "";
              $c=0;
              $high=-1;
              for ($j=0; $j<10; $j++)
              {
                if ($eups[$j] >= $high)
                {
                  if ($eups[$j] > $high) 
                  {
                    $high = $eups[$j];
                    $highest = "";
                    $c=0;
                  }
                  $highest[$c]=$j;
                  $c++;
                }
              }
              $edown= rand(0,count($highest)-1);
              if ($eups[$highest[$edown]] > 0 ) $eups[$highest[$edown]]--;     
            }
            $seups = serialize($eups);  
            mysql_query("UPDATE Estates SET upgrades='$seups' WHERE id='$lestate[id]'");      
          }
                 
          // All clans lose 10% of their Ji in the city.
          $sresult = mysql_query("SELECT id, area_score, name FROM Soc WHERE 1");
          while ($lsoc = mysql_fetch_array($sresult))
          {
            $tas = unserialize($lsoc[area_score]);
            $tas[$attacked[id]] = $tas[$attacked[id]]*.90;
            $sas= serialize($tas);
            mysql_query("UPDATE Soc SET area_score='$sas' WHERE id='$lsoc[id]'");      
          }
        } // end Horde Attack!
        
        // CHECK FOR A BUBBLE
        $playerSeals = 0;
        $citySeals = 0;
        $result = mysql_query("SELECT * FROM Items WHERE type=0 ORDER BY last_moved");  
        while ($tmpSeal = mysql_fetch_array( $result ) )
        {
          if ($tmpSeal[owner] > 50000) $citySeals++;
          else $playerSeals++;
        }
        $bubbleOdds = 20+($playerSeals*4)+($citySeals*8);
        
        // If horde attacked or random number is greater than the odds, make a bubble!
        if ($newdone==3 || (rand(1,100) <= $bubbleOdds))
        {
          // Figure out the target
          $result = mysql_query("SELECT * FROM Locations WHERE 1 ORDER BY myOrder DESC");
          $l=0;
          while ($tmploc = mysql_fetch_array( $result ) )
          {
            $locs[$l++] = $tmploc;
          }
          $randNum = rand (1,300);
          $orderCount=0;
          $targetNum =17;
          $x=0;
          for ($x = 0; $x < 24; $x++)
          {
            $orderCount += $x + 1;
            if ($orderCount >= $randNum)
            {
              // found my target, so I can stop looking now
              $targetNum = $x;
              $x=24;
            }
          }
          $x = $targetNum;
          $myMsg = "<".$locs[$x][name]."_".time().">` A Bubble of Evil ";
          
          // Do negative effect
          $bubEffect = rand (1,5);
          switch ($bubEffect)
          {
            case 1:
              $defDown = floor($locs[$x][army] *0.1);
              $locs[$x][army] = $locs[$x][army] - $defDown;
              mysql_query("UPDATE Locations SET army='".$locs[$x][army]."' WHERE id='".$locs[$x][id]."'");
              $myMsg .= "weakened our defenses by ".$defDown."!|";
              break;
            case 2:
              $bankDown = floor($locs[$x][bank] *0.2);
              $locs[$x][bank] -= $bankDown;
              mysql_query("UPDATE Locations SET bank='".$locs[$x][bank]."' WHERE id='".$locs[$x][id]."'");
              $myMsg .= "caused ".displayGold($bankDown,1)." in damages!|";
              break;
            case 3:
              $upNum = rand(1,$locs[$x][num_ups]);
              $ups = unserialize($locs[$x][upgrades]);
              $countUps = 0;
              $targetUp = 0;
              for ($u=0; $u < 8; $u++)
              {
                $countUps += $ups[$u];
                if ($countUps >= $upNum)
                {
                  $targetUp = $u;
                  $u = 8;
                }
              }
              $ups[$targetUp]--;
              $newUps = serialize($ups);
              mysql_query("UPDATE Locations SET upgrades='".$newUps."', num_ups = num_ups-1 WHERE id='".$locs[$x][id]."'");
              // Copied from locFuncs.php
              $build_names=array("Stable","Forge","Arena","Barracks","Bank","Square");
              $myMsg .= "reduced the ".$build_names[$targetUp]." to level ".$ups[$targetUp]."!|";
              break;
            case 4:
              $shoplvls=unserialize($locs[$x]['shoplvls']);
              $totItems = 0;
              foreach ($shoplvls as $inum => $iclans)
              {
                foreach ($iclans as $cid=>$cnum)
                {
                  $destroyedItms = intval($shoplvls[$inum][$cid]/4);
                  $shoplvls[$inum][$cid] -= $destroyedItms;
                  $totItems += $destroyedItms;
                }
              }
              $shoplvls_str= serialize($shoplvls);
              mysql_query("UPDATE Locations SET shoplvls='$shoplvls_str' WHERE id='".$locs[$x][id]."'");
              $myMsg .= "destroyed ".$totItems." Equipment Items!|";
              break;
            case 5:
              $shopg = unserialize($locs[$x][shopg]);
              $totItems = 0;
              for ($t=19; $t<22; $t++)
              {
                for ($i =0; $i < 4; $i++)
                {
                  for ($j=0; $j<12; $j++)
                  {
                    $destroyedItms = intval($shopg[$t][$i][$j]/4);
                    $shopg[$t][$i][$j] -= $destroyedItms;
                    $totItems += $destroyedItms;
                  }
                }
              }
              $shopg_str= serialize($shopg);
              mysql_query("UPDATE Locations SET shopg='$shopg_str' WHERE id='".$locs[$x][id]."'");
              $myMsg .= "destroyed ".$totItems." Consumable Items!|";    
              break;
          }
          
          // Update City Rumors
          $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));
          $rumorMessages = unserialize($cityRumors[message]);
          $numRumors = count($rumorMessages);
          $rumorMessages[$numRumors] = $myMsg;
          $rumorMessages = pruneMsgs($rumorMessages, 50);
          $newRumors = serialize($rumorMessages);
          mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'");          
        }
      }
      if ($myHorde[done] == 0)
      {
        if ($myHorde[location] == $char['location'] || $myHorde[target] == $char['location'])
        {
          $horde_present = 1;
        }
      }
    }
  }

  // Get last horde to determine when to start next 
  $lastHorde = mysql_fetch_array(mysql_query("SELECT id, next, location, target FROM Hordes WHERE type='1' ORDER BY starts DESC"));
  // If at least one seal remains and either no previous horde or time to start a new horde, make a new one.
  if ($numBroken < 7 && ($lastHorde[next]*3600<=time() || !($lastHorde[id])))
  {
    if ($lastHorde[id]) $hstime = $lastHorde[next];
    else $hstime = floor(time()/3600);
    include_once("locFuncs.php");
    
    // ensure our horde isn't a bubble of evil
    $tnpc = "Bubble of Evil";
    while ($tnpc == "Bubble of Evil")
    {
      $tnpc = $npc_list[rand(0,$npc_count-1)];
    }
    $htarget_id=0;
    
    // FIND NEXT TARGET 
    //
    // Add up all the hordes targeting all towns except the most recent horde. 
    // Also, remember what the most hordes that have targeted a single town.
    $cHordes=0;
    $mostHordes=0;
    for ($x=0; $x<24; $x++)
    {
      // Don't have two in a row targeting same town.
      if ($lastHorde[target] != $townnames[$x])
      {
        $temp = mysql_num_rows(mysql_query("SELECT id FROM Hordes WHERE type='1' AND target='".$townnames[$x]."'"));
        // If the town has a seal, cancel out one horde that's targeted it. Make sure that doesn't make us negative!
        $sealid = $x+50001;
        $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'"));
        $temp -= $hasSeal;
        if ($temp < 0) $temp =0;
        
        if ($temp > $mostHordes) $mostHordes=$temp;
        $cHordes += $temp;
      }
    }
    
    // Multiply the highest number of hordes that have targeted a single town + 1 by 17 (all but one town).
    // Then subtracted the hordes we added before.  
    $cnum = ($mostHordes+1)*23 - $cHordes;
    $rtarget= rand(1,$cnum);
    $cHordes=0;
    for ($x=0; $x<24; $x++)
    {
      // Don't have two in a row targeting same town.
      if ($lastHorde[target] != $townnames[$x])
      {
        $temp = mysql_num_rows(mysql_query("SELECT id FROM Hordes WHERE type='1' AND target='".$townnames[$x]."'"));
        // If the town has a seal, add one extra
        $sealid = $x+50001;
        $hasSeal = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 && owner='$sealid'")); 
        $cHordes += $mostHordes-$temp+1+$hasSeal;
        if ($cHordes >= $rtarget) 
        {  
          $htarget_id=$x+1;
          $x=24;
        }
      }
    }
    $htown = mysql_fetch_array(mysql_query("SELECT id, name, chaos, army FROM Locations WHERE id='$htarget_id'"));
    $surrounding_area = $map_data[$htown[name]];
    
    $hwild = $surrounding_area[rand(0,3)];
    $maxloop=100;
    $cloop=0;
    while ($hwild == $lastHorde[location] && $cloop < $maxloop ) 
    {
      $cloop++;
      $hwild = $surrounding_area[rand(0,3)];
    }
    $hnpcs[0] = array($tnpc, $hhealth);
    $hnpcs[1] = array($city_defenses[$htown[name]], $htown[army]);
    $shnpcs = serialize($hnpcs);
    $hetime = $hstime+20;
    $hntime = $hstime+36+ rand(0,12);
    if ($hwild != "" && $htown[name])
    {
      $sql = "INSERT INTO Hordes (type, location, target,         starts,    ends,      next,      done, npcs) 
                          VALUES ('1',  '$hwild', '$htown[name]', '$hstime', '$hetime', '$hntime', 0,    '$shnpcs')";
      $resultt = mysql_query($sql, $db);
    }
    
    // Update City Rumors
    $cityRumors = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='50000'"));

    $rumorMessages = unserialize($cityRumors[message]);
    $numRumors = count($rumorMessages);
    
    $myMsg = "<".$htown[name]."_".time().">` A ".$horde_types[$hnpcs[0][0]]." of ".$hnpcs[0][0]."s is gathering in ".$hwild."!|";
    $rumorMessages[$numRumors] = $myMsg;
    
    $rumorMessages = pruneMsgs($rumorMessages, 50);
    $newRumors = serialize($rumorMessages);
    mysql_query("UPDATE messages SET message='$newRumors' WHERE id='50000'");
    
    // Update chaos in targeted city
    mysql_query("UPDATE Locations SET chaos=chaos+50 WHERE id='$htarget_id'");
  }
  mysql_query("UNLOCK TABLES");
}
?>