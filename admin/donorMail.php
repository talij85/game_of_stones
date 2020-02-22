<?php

include("connect.php");

$result = mysql_query("SELECT * FROM donate WHERE 1",$db);

$stillDonor="";
$testDonor="";
$wasDonor = "";

$sD=0;
$tD=0;
$wD=0;

while ($donor = mysql_fetch_array($result))
{
  if ($donor[amount] >= 10)
  {
    $stillDonor[$sD++] = $donor[email];
  // mysql_query("UPDATE donate SET amount=amount-5 WHERE id='$donor[id]'");
  }
  else if ($donor[amount] >= 5)
  {
    $testDonor[$tD++] = $donor[email];
  //  mysql_query("UPDATE donate SET amount=amount-5 WHERE id='$donor[id]'");
  }
  else
  {
    $wasDonor[$wD++] = $donor[email];
  }
  mysql_query("UPDATE donate SET amount=amount+5 WHERE id='$donor[id]'");
}

echo "Still Donors: ";
for ($x=0; $x < $sD; $x++)
{
  echo $stillDonor[$x].", ";
}
echo "<br/><br/>";
echo "Test Donors: ";
for ($x=0; $x < $tD; $x++)
{
  echo $testDonor[$x].", ";
}
echo "<br/>";
?>