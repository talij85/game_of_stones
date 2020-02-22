<?php
require_once("ayah.php");
$integration = new AYAH();

if (array_key_exists("Submit", $_POST)) {
$score = $integration->scoreResult();
if ($score) {
echo "Successful";
echo $integration->recordConversion();
} else {
echo "Not...";
}
}?>
<form method="post">
<input type="hidden" name="Submit" value="Yes">
<?php  echo $integration->getPublisherHTML(); ?>
<input type="Submit" value="Submit">
</form>