<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "Quests"</u><br><br>
<?php
// Connect
include("connect.php");

$numquests = 3; // NOTE: change this number whenever new quest are added
for ($i = 0; $i < $numquests; ++$i)
{
  switch ($i)
  {
    case 0:
      $qs[name]="Item Quest Test";
      $qs[type]=0;
      $qs[offerer] = "The_Creator";
      $qs[num_avail] = -1;
      $loc_array[0] = "Tear";
      $qs[location] = serialize($loc_array);
      $qs[reqs] = 0;
      $goals_array[0] = 5;
      $goals_array[1] = array("Rags","","*");
      $goals_array[2] = "Illian";
      $qs[goals] = serialize($goals_array);  
      $spec_array[0] = 0;
      $qs[special] = serialize($spec_array);
      $reward_array[0] = "G";
      $reward_array[1] = "1000";      
      $qs[reward] = serialize($reward_array);
      $qs[started] = time()/3600;
      $qs[expire] = $qs[started]+48;      
      break;
    case 1:
      $qs[name]="NPC Quest Test";
      $qs[type]=1;
      $qs[offerer] = "";
      $qs[num_avail] = 5;
      $loc_array[0] = "Emond-ap-s_Field";
      $loc_array[1] = "Amador";
      $qs[location] = serialize($loc_array);
      $qs[reqs] = "L3 S1";
      $goals_array[0] = 3;
      $goals_array[1] = "Bear";
      $goals_array[2] = "Mountains_of_Mist";
      $qs[goals] = serialize($goals_array);  
      $spec_array[0] = 0;
      $qs[special] = serialize($spec_array);
      $reward_array[0] = "I";
      $reward_array[1] = "Dagger";      
      $qs[reward] = serialize($reward_array);
      $qs[started] = time()/3600;
      $qs[expire] = $qs[started]+24;
      break;
    case 2:
      $qs[name]="Player Quest Test";
      $qs[type]=2;
      $qs[offerer] = "The_Creator";
      $qs[num_avail] = -1;
      $loc_array[0] = "Caemlyn";
      $qs[location] = serialize($loc_array);
      $qs[reqs] = "C1";
      $goals_array[0] = 10;
      $goals_array[1] = "Quest_Target";
      $goals_array[2] = "";
      $qs[goals] = serialize($goals_array);  
      $spec_array[0] = 0;
      $qs[special] = serialize($spec_array);
      $reward_array[0] = "G";
      $reward_array[1] = "1111";      
      $qs[reward] = serialize($reward_array); 
      $qs[started] = time()/3600;
      $qs[expire] = -1;
      break;           
  }

  echo $i." ";
  $sql = "INSERT INTO Quests (name,        type,         offerer,        num_avail,       num_done, started,        expire,       location,        reqs,        goals,        special,        reward) 
                     VALUES ('$qs[name]', '$qs[type]', '$qs[offerer]', '$qs[num_avail]', 0,         '$qs[started]', '$qs[expire]', '$qs[location]', '$qs[reqs]', '$qs[goals]', '$qs[special]', '$qs[reward]')";
  $result = mysql_query($sql, $db);
}

// Close Database
mysql_close($db);
?>

<br><br>
~Table reset and recreator for the shop~
</body>
</html>