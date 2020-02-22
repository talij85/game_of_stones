 <html>
<head>
<title>Admin Recreate Society Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
// Connect
include("connect.php");
include("itemarray2.php");

$townnames = array ('Thakan-ap-dar','Falme','Tanchico','Ebou Dar','Illian','Tear','Stedding Shangtai','Cairhein',
                    'Tar Valon','Caemlyn','Far Madding','Amador','Emond-ap-s Field','Aiel Waste','Maradon',);
                    
for ($loc_id=0; $loc_id < 15; ++$loc_id)
{
    $tname = $townnames[$loc_id];
    
    //$shoplvls = serialize(array (1,1,1,1,1,1,1,1));
    echo $shop_base[$name][0];
    $forum = array ( array ("The", "Creator" , "Welcome to ".str_replace('-ap-','&#39;',$tname)."!",time()) );
    $forum = serialize($forum);
    //echo "id: ".$loc_id." name:".$tname;
    $ruler= "No One";
    mysql_query("INSERT INTO Locations (name,ruler,nextwins,bank,forum) VALUES ('$tname','$ruler','100','0','$forum')");
}
?>
</body>
</html>