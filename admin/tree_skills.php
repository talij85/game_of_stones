<?php

// SKILL TREES
$hello="yay";

$skilltree = array(
array(
array("Pine Mastery","Rawhide Mastery","Copper Mastery",""),
array("Oak Mastery","Buckskin Mastery","Iron Mastery",""),
array("Ash Mastery","Pelt Mastery","Silver Mastery","Master Weapons Smith"),
),
array(
array("Woodcutting","Hunting","Mining",""),
array("Improved Woodcutting","Improved Hunting","Improved Mining",""),
array("Advanced Woodcutting","Advanced Hunting","Advanced Mining","Master Supply Gatherer"),
),
array(
array("Pine Hauling","Rawhide Shipping","Copper Transport",""),
array("Oak Hauling","Buckskin Shipping","Iron Transport",""),
array("Ash Hauling","Pelt Shipping","Silver Transport","Master Merchant"),
)
);

// SKILL LIST 'skill name' => array("what it does.","previous skill required, 0=none","0=neutral 1=good 2=evil"),

$skill_list = array(
// Gatherer
'Mining' => array("Access to Copper, +1 Copper/skill point",0,0),
'Improved Mining' => array("Access to Iron, +1 Iron/skill point","Mining",0),
'Advanced Mining' => array("Access to Silver, +1 Silver/skill point","Improved Mining",0),
'Hunting' => array("Access to Rawhide, +1 Rawhide/skill point",0,0),
'Improved Hunting' => array("Access to Buckskin, +1 Buckskin/skill point","Hunting",0),
'Advanced Hunting' => array("Access to Pelt, +1 Pelt/skill point","Improved Hunting",0),
'Woodcutting' => array("Access to Pine, +1 Pine/skill point",0,0),
'Improved Woodcutting' => array("Access to Oak, +1 Oak/skill point","Woodcutting",0),
'Advanced Woodcutting' => array("Access to Ash, +1 Ash/skill point","Improved Woodcutting",0),
'Master Supply Gatherer' => array("Access to Gold, Yew and Leather, +1 res./skill point",2,0),
// Weapon Smith
'Pine Mastery' => array("Ability to make items that use Pine, can use 4/skill point",0,0),
'Oak Mastery' => array("Ability to make items that use Oak, can use 4/skill point","Pine Mastery",0),
'Ash Mastery' => array("Ability to make items that use Ash, can use 4/skill point","Oak Mastery",0),
'Rawhide Mastery' => array("Ability to make items that use Rawhide, can use 4/skill point",0,0),
'Buckskin Mastery' => array("Ability to make items that use Buckskin, can use 4/skill point","Rawhide Mastery",0),
'Pelt Mastery' => array("Ability to make items that use Pelt, can use 4/skill point","Buckskin Mastery",0),
'Copper Mastery' => array("Ability to make items that use Copper, can use 4/skill point",0,0),
'Iron Mastery' => array("Ability to make items that use Iron, can use 4/skill point","Copper Mastery",0),
'Silver Mastery' => array("Ability to make items that use Silver, can use 4/skill point","Iron Mastery",0),
'Master Weapons Smith' => array("Ability to make items that use Gold, Yew and Leather, can use 4/skill point",1,0),
// MERCHANT
'Pine Hauling' => array("Capability to carry Pine, can carry 6/skill point",0,0),
'Oak Hauling' => array("Capability to carry Oak, can carry 6/skill point","Pine Hauling",0),
'Ash Hauling' => array("Capability to carry Ash, can carry 6/skill point","Oak Hauling",0),
'Rawhide Shipping' => array("Capability to carry Rawhide, can carry 6/skill point",0,0),
'Buckskin Shipping' => array("Capability to carry Buckskin, can carry 6/skill point","Rawhide Shipping",0),
'Pelt Shipping' => array("Capability to carry Pelt, can carry 6/skill point","Buckskin Shipping",0),
'Copper Transport' => array("Capability to carry Copper, can carry 6/skill point",0,0),
'Iron Transport' => array("Capability to carry Iron, can carry 6/skill point","Copper Transport",0),
'Silver Transport' => array("Capability to carry Silver, can carry 6/skill point","Iron Transport",0),
'Master Merchant' => array("Capability to carry Gold, Yew and Leather, can carry 6/skill point",3,0),
);
?>
