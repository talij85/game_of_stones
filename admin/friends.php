<center>
<table border="0" cellpadding="0" cellspacing="0"><tr><td valign="top">
<table border="0" cellpadding="2" cellspacing="0">
	<tr><td valign="top" height="15"><center><font class="littletext"><u>Friends</u></td></tr>
	<?php	
	$draw_any = 0;
	foreach ($friends as $id => $info) {
		if ($info[2] == 0) {echo "<tr><td><center><font class=foottext><a href=\"bio.php?name=".$info[0]."&last=".$info[1]."&time=$curtime\">".$info[0]."&nbsp;".$info[1]."</a></td></tr>"; $draw_any = 1;}
	}
	if ($draw_any == 0) echo "<tr><td><center><font class=foottext>-no friends-</td></tr>";
	?>
</table>
</td><td width="35"></td><td valign="top">
<table border="0" cellpadding="2" cellspacing="0">
	<tr><td valign="top" height="15"><center><font class="littletext"><u>Enemies</u></td></tr>
	<?php	
	$draw_any = 0;
	foreach ($friends as $id => $info) {
		if ($info[2] == 1) {echo "<tr><td><center><font class=foottext><a href=\"bio.php?name=".$info[0]."&last=".$info[1]."&time=$curtime\">".$info[0]."&nbsp;".$info[1]."</a></td></tr>"; $draw_any = 1;}
	}
	if ($draw_any == 0) echo "<tr><td><center><font class=foottext>-no enemies-</td></tr>";
	?>
</table>
</tr></td></table>
