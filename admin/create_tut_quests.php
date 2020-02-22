<?php
include_once('connect.php');
include_once('questFuncs.php');

  $nqcat = 1;
  $nqtype = 30;  
  $nqofferer = "Green Man";
  $nqstart = time()/3600;
  $nqlocation = "Anywhere";
  $nqrewarded[0]= "T";
  $nqrewarded[1]= 0;
  $nqrewards = serialize($nqrewarded);

  $nqname="Prepare for Battle";  
  $nqgoals['skill_points']=1;
  $nqgoals['active_skills']=1;
  $nqgoals['npc_wins']=1;
  $nqgoal = serialize($nqgoals);
  $nqreqs = 0;
  $nqspec = "Battle_Track#Get_Ready_For_Battle";

  $sql = "INSERT INTO Quests (id,name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (1, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);

  $nqgoals="";
  $nqreq="";
  $nqname="Recover from Battle";  
  $nqgoals['repair_items']=1;
  $nqgoals['use_consume']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 1;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Battle_Track#Recover_From_Battle";

  $sql = "INSERT INTO Quests (id,name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (2, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Intro to NPC Quests";
  $nqgoals[0] = 5;
  $nqgoals[1] = 0;
  $nqgoals[2] = "Field of Merrilor";
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 2;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "LI";
  $reward[1] = 12;
  $reward[2] = 30;
  $sreward = serialize($reward);  
  $stype = 1;
  $special[2]="Forest";
  $special[9]="Battle_Track#Intro_To_NPC_Quests";
  $sspecial = serialize($special);
  $sloc = "Field of Merrilor";

  $sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (3, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Inventory and Stamina Management";  
  $nqgoals['combine_ter']=1;
  $nqgoals['sell_items']=1;
  $nqgoals['drop_items']=1;
  $nqgoals['visit_inn']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 3;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Battle_Track#Inventory_and_Stamina_Management";

  $sql = "INSERT INTO Quests (id,name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (4, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
    
  $nqgoals="";
  $nqreq="";
  $nqname="Intro to Horde Quests";
  $nqgoals[0] = 10;
  $nqgoals[1] = 0;
  $nqgoals[2] = "Aryth Ocean";
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 4;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "LI";
  $reward[1] = 13;
  $reward[2] = 50;
  $sreward = serialize($reward);
  $special[9]="Battle_Track#Intro_to_Horde_Quests";
  $sspecial = serialize($special); 
  $stype = 3;
  $sloc = "Aryth Ocean";

  $sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (5, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);

  $nqgoals="";
  $nqreq="";
  $nqname="Intro to Escort Quests";
  $sloc = "Mayene";
  $nqgoals[0] = 6;
  $route = mysql_fetch_array(mysql_query("SELECT * FROM Routes WHERE start='".$sloc."' AND length >'".$goals[0]."' ORDER BY rand()"));
  $nqgoals[1] = $route[id];
  $path = unserialize($route[path]);
  $nqgoals[2] = $path[$nqgoals[0]];  
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 5;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "LG";
  $reward[1] = 20*$goals[0];
  $sreward = serialize($reward);
  $special[9]="Battle_Track#Intro_to_Escort_Quests";
  $sspecial = serialize($special);
  $stype = 4;

  $sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (6, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);

  $nqgoals="";
  $nqreq="";
  $nqname="Give Tasks to Others";  
  $nqgoals['create_quests']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 6;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Battle_Track#Give_Tasks_to_Others";

  $sql = "INSERT INTO Quests (id,name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (7, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Intro to Find Quests";
  $nqgoals[0] = 1;
  $nqgoals[1] = 0;
  $nqgoals[2] = "Braem Wood";
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 7;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "LI";
  $reward[1] = 1;
  $reward[2] = 50;
  $sreward = serialize($reward);
  $special[9]="Battle_Track#Intro_to_Find_Quests";
  $sspecial = serialize($special);
  $stype = 2;
  $sloc = "Braem Wood";

  $sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (8, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Intro to Item Quests";
  $nqgoals[0] = 1;
  $itype = array("T",1);
  $prefix = "*";
  $suffix = "*";

  $nqgoals[1]= array($itype,$prefix,$suffix);  
  $nqgoals[2] = "Illian";
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 8;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "GP";
  $reward[1] = 110;
  $sreward = serialize($reward);
  $special[9]="Battle_Track#Intro_to_Item_Quests";
  $sspecial = serialize($special);
  $stype = 0;
  $sloc = "Illian";

  $sql = "INSERT INTO Quests (id,name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (9, '$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Start your own Business";  
  $nqgoals['prof_points']=1;
  $nqgoals['bank_withdrawl']=1;
  $nqgoals['own_biz']=1;
  $nqgoals['bank_deposit']=1;
  $nqgoal = serialize($nqgoals);
  $nqreqs = 0;
  $nqspec = "Business_Track#Start_Your_Own_Business";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (21, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Grow your own Business";  
  $nqgoals['biz_level']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 21;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Business_Track#Grow_Your_Own_Business";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (22, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Expand your own Business";  
  $nqgoals['biz_same']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 22;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Business_Track#Expand_your_own_Business";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (23, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
    
  $nqgoals="";
  $nqreq="";
  $nqname="Diversify your Businesses";  
  $nqgoals['biz_types']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 23;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Business_Track#Diversify_your_Businesses";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (24, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Choose a Side";  
  $nqgoals['join_clan']=1;
  $nqgoals['clan_chat']=1;
  $nqgoal = serialize($nqgoals);
  $nqreqs = 0;
  $nqspec = "Clan_Track#Choose_a_Side";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (31, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);
  
  $nqgoals="";
  $nqreq="";
  $nqname="Contribute to the Cause";  
  $nqgoals['donate_items']=1;
  $nqgoals['donate_coin']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 31;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Clan_Track#Contribute_to_the_Cause";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (32, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);  
  
  $nqgoals="";
  $nqreq="";
  $nqname="Brave the Ways";  
  $nqgoals['travel_ways']=1;
  $nqgoal = serialize($nqgoals);
  $nqreqs = 0;
  $nqspec = "Bonus_Track#Brave_the_Ways";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (41, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db);  

  $nqgoals="";
  $nqreq="";
  $nqname="Time to Toss the Dice";
  $nqgoals['dice_wins']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 41;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Bonus_Track#Time_to_Toss_the_Dice";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (42, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db); 

  $nqgoals="";
  $nqreq="";
  $nqname="Prepare for your Journey";
  $nqgoals['upgrade_gear']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 42;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Bonus_Track#Prepare_for_your_Journey";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (43, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db); 

  $nqgoals="";
  $nqreq="";
  $nqname="Sweet Dreams";
  $nqgoals['use_tar']=1;
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 43;
  $nqreqs = serialize($nqreq); 
  $nqspec = "Bonus_Track#Sweet_Dreams";

  $sql = "INSERT INTO Quests (id, name,      type,      cat,      offerer,      num_avail, num_done, started,    expire, align, location,      reqs,      goals,     special, reward,      done) 
                      VALUES (44, '$nqname', '$nqtype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$nqlocation', '$nqreqs', '$nqgoal', '$nqspec','$nqrewards','0')";
  $resultt = mysql_query($sql, $db); 
  
  $nqgoals="";
  $nqreq="";
  $nqname="Final Test";
  $nqgoals[0] = 1;
  $nqgoals[1] = "Green_Man";
  $nqgoal = serialize($nqgoals);
  $nqreq['Q'] = 9;
  $nqreqs = serialize($nqreq); 
  $reward[0] = "G";
  $reward[1] = 1000;
  $sreward = serialize($reward);
  $special[9]="Battle_Track#Final_Test";
  $sspecial = serialize($special);
  $stype = 10;
  $sloc = "Eye of the World";

  $sql = "INSERT INTO Quests (id, name,      type,     cat,      offerer,      num_avail, num_done, started,    expire, align, location, reqs,      goals,     special,    reward,    done) 
                      VALUES (100,'$nqname', '$stype', '$nqcat', '$nqofferer', '-1',      0,        '$nqstart', '-1',   0,     '$sloc',  '$nqreqs', '$nqgoal', '$sspecial','$sreward','0')";
  $resultt = mysql_query($sql, $db);