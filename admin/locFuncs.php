<?php
//LOCATION FUNCTIONS
include_once('mapData.php');
include_once('displayFuncs.php');

$estate_lvls = array("Tent","Cabin","House","Lodge","Manor","Hamlet","Village","Town");
$estate_lvls_req = array(5,10,15,20,25,30,35,100);
$estate_ups = array(
  array("Guard House","zD","3"),
  array("Walls","zE","3"),
  array("Store House","zS","3"),
  array("Hunting Grounds","nJ","5"),
  array("Patrols","eT","10"),
  array("Roads","tV","1"),
  array("Tax Collector","wT","1"),
  array("Local Maps","zL","2"),
  array("Square","zQ","1"),
  array("Unique","","")
);
$estate_unq_wild= array("",
  array("Docks","oG","1"),
  array("Sawmill","fG","1"),
  array("Quarry","mG","1"),
  array("Trade Route","wG","1"),
  array("Ranch","hG","1"),
  array("Fields","pG","1")
);

$estate_up_lvls = array(0,0,1,3,5,99);
$estate_max_upgrade = array(2,3,3,4,4,5,5,5);

$estate_ji = array(0,5,15,25,50,100,150,200);

$build_names=array("Stable","Forge","Guard Posts","Barracks","Bank","Square");
$build_costs=array(25000,175000,400000,700000,1200000,2000000);
$build_goods=array(5,3,1,2,4,6);

$unique_buildings= array(
'Bandar Eban' => array('The Terhana Library','Arandi Square'),
'Falme' => array('The Seanchan Compound',"The Towers of Do Miere A&#39;vron"),
'Tanchico' => array("The Panarch&#39;s Palace","The Illuminator&#39;s Chapter House"),
'Amador' => array('The Fortress of Light','The Seranda Palace'),
'Maradon' => array('The Camp','The Cordamora Palace'),
"Emond&#39;s Field" => array("Thane&#39;s Mill","Luhhan&#39;s Forge"),
'Ebou Dar' => array('The Tarasin Palace','The Circut of Heaven'),
'Chachin' => array('The Aesdaishar Palace','The Bridge of Sunrise'),
'Caemlyn' => array('The Royal Palace','Acadamy of the Rose'),
'Lugard' => array('Commerce Grounds','The Inner Walls'),
'Illian' => array('The Square of Tammaz','The Bridge of Flowers'),
'Fal Dara' => array('Fal Dara Keep', 'Dry Moat'),
'Tar Valon' => array('The White Tower','The Warder Yard'),
'Cairhien' => array('The School of Cairhien','The Sun Palace'),
'Far Madding' => array('The Hall of the Counsels','Amhara Market'),
'Stedding Shangtai' => array('The Stump','The Great Trees'),
'Tear' => array('The Stone of Tear','The Maule'),
'Rhuidean' => array('The Great Plaza','Chaendaer'),
'Thakan&#39;dar' => array('The Forges','Pit of Doom'),
'Salidar' => array('The Little Tower','Pigeon Coop'),
'Cantorin' => array('Cantorin Harbor','Shipyards'),
'Mayene' => array('The Oilfish Shoals','Hospital'),
'Jehannah' => array('The Jheda Palace','Light Blessed Throne'),
'Shol Arbela' => array('Watchtowers','Bell Foundry'),
); 

$unique_build_bonuses = array(
// Amador
'The Fortress of Light'=>array(
'kQ5', // +5% Food Quality
'cD5'), // +5% dmg vs Channelers
'The Seranda Palace'=>array(
'dP-5', // -5% Sword Price
'sO5'), // +5% Sword dmg
// Bandar Eban
'The Terhana Library'=>array(
'bU-10', // -10% Business upgrade cost
'bU-5'), // -5% Business upgrade cost
'Arandi Square'=>array(
'hP-5', // -5% Shield price
'hC-3'), // -3% Shield cost
// Caemlyn
'The Royal Palace'=>array(
'rP-5', // -5% Body price
'aC-3'), // -3% Body cost
'Acadamy of the Rose'=>array(
'sS-10', // -10% Shop stock cost
'sS-5'), // -5% Shop stock cost
// Cairhien
'The School of Cairhien'=>array(
'fT-5', // -5% Farming Time
'fT-5'), // -5% Farming Time
'The Sun Palace'=>array(
'kP-5', // -5% Knife price
'kO5'), // +5% knife dmg
// Cantorin
'Cantorin Harbor'=>array(
'dV-5', // -5% drink price
'eE5'), // +5% def vs Exotic
'Shipyards'=>array(
'sT-5', // -5% Sailing Time
'sT-5'), // -5% Sailing Time
// Chachin
'The Aesdaishar Palace'=>array(
'oP-5', // -5% Bow price
'oC-3'), // -3% Bow cost
'The Bridge of Sunrise'=>array(
'mT-5', // -5% Mining Time
'mT-5'), // -5% Mining Time
// Ebou Dar
'The Tarasin Palace'=>array(
'kP-5', // -5% Knife price
'kC-3'), // -3% Knife cost
'The Circut of Heaven'=>array(
'uV-5', // -5% Horse & Feed cost
'uV-5'), // -5% Horse & Feed cost
// Emond's Field
'Thane&#39;s Mill'=>array(
'oP-5', // -5% Bow price
'oO5'), // +5% Bow dmg
'Luhhan&#39;s Forge'=>array(
'rV-10', // -10% Repair cost
'rV-5'), // -5% Repair cost
// Fal Dara
'Fal Dara Keep'=>array(
'iV-10', // -10% Inn cost
'iV-5'), // -5% Inn cost
'Dry Moat'=>array(
'kT-5', // -5% Cooking Time
'sE5'), // +5% def vs Shadow
// Falme
'The Seanchan Compound'=>array(
'sV-5', // -5% Storage cost
'sV-5'), // -5% Storage cost
'The Towers of Do Miere A&#39;vron'=>array(
'hV-5', // -5% Herb price
'eD5'), // +5% dmg vs Exotic
// Far Madding
'The Hall of the Counsels'=>array(
'uQ1', // All quests become Neutral
'cE5'), // +5% def vs Channelers
'Amhara Market'=>array(
'rS-200', // -200 Ji for new shop items
'rS-100'), // -100 Ji for new shop items
// Illian
'The Square of Tammaz'=>array(
'iQ10', // Bonus gold for item quests
'rD5'), // +5% dmg vs Ruffians
'The Bridge of Flowers'=>array(
'xP-5', // -5% Axe price
'xO5'), // +5% Axe dmg
// Jehannah
'The Jheda Palace'=>array(
'xP-5', // -5% Axe price
'xC-3'), // -3% Axe cost
'Light Blessed Throne'=>array(
'lQ1', // All quests become Light
'rE5'), // +5% def vs Ruffians
// Lugard
'Commerce Grounds'=>array(
'iS4', // 4 slots in clan shop
'iS2'), // 2 slots in clan shop
'The Inner Walls'=>array(
'hP-5', // -5% Shield Cost
'hO5'), //+5% Shield def
// Maradon
'The Camp'=>array(
'lP-5', // -5% Leg price
'lC-3'), //-3% Leg cost
'The Cordamora Palace'=>array(
'gT-5', // -5% Herb Grow Time
'mD5'), // +5% dmg vs Military
// Mayene
'Hospital'=>array(
'cQ5', // +5% Consumable Quality
'cQ5'), // +5% Consumable Quality
'The Oilfish Shoals'=>array(
'lP-5', // -5% Leg price
'lO5'), // +5% Leg def
// Rhuidean
'The Great Plaza'=>array(
'pP-5', // -5% Spear price
'pC-3'), // -3% Spear cost
'Chaendaer'=>array(
'bQ5', // +5% Drink Quality
'aD5'), // +5% dmg vs animals
// Salidar
'The Little Tower'=>array(
'gQ5', // +5% Herb Quality
'wO5'), // +5% Weave dmg
'Pigeon Coop'=>array(
'eQ2', // +2 Local Quests
'oR2'), // +2 Order hourly to ruled cities
// Shol Arbela
'Watchtowers'=>array(
'dW20', // +20% time between duels
'sD5'), // +5% dmg vs Shadow
'Bell Foundry'=>array(
'dP-5', // -5% Sword price
'sC-3'), // -3% Sword cost
// Stedding Shangtai
'The Stump'=>array(
'bP-5', // -5% Bludgeon Price
'bC-3'), // -3% Bludgeon cost
'The Great Trees'=>array(
'rM2', // +2 Stamina regen per hour
'aE5'), // +5% def vs Animal
// Tanchico
'The Panarch&#39;s Palace'=>array(
'bP-5', // -5% Bludgeon Price
'bO5'), // +5% Bludgeon dmg
'The Illuminator&#39;s Chapter House'=>array(
'cT-5', // -5% Consumable Time
'cT-5'), // -5% Consumable Time
// Tar Valon
'The White Tower'=>array(
'fV-5', // -5% Food price
'wC-3'), // -3% Weave cost
'The Warder Yard'=>array(
'oT-5', // -5% Assignment Time
'oT-5'), // -5% Assignment Time
// Tear
'The Stone of Tear'=>array(
'bT-5', // -5% Brewing Time
'mE5'), // +5% def vs Military
'The Maule'=>array(
'pP-5', // -5% Spear price
'pO5'), // +5% Spear dmg
// Thakan&#39;dar
'The Forges'=>array(
'rP-5', // -5% Body price
'aO5'), // +5% Body def
'Pit of Doom'=>array(
'sQ1', // All quests become Shadow
'cW2'), // +2 Chaos hourly to enemy cities
);

$unique_build_goods = array(
// Amador
'The Fortress of Light'=>array(3,5),
'The Seranda Palace'=>array(1,4),
// Bandar Eban
'The Terhana Library'=>array(3,2),
'Arandi Square'=>array(1,6),
// Caemlyn
'The Royal Palace'=>array(2,6),
'Acadamy of the Rose'=>array(5,1),
// Cairhien
'The School of Cairhien'=>array(6,2),
'The Sun Palace'=>array(5,4),
// Cantorin
'Cantorin Harbor'=>array(6,1),
'Shipyards'=>array(2,4),
// Chachin
'The Aesdaishar Palace'=>array(3,4),
'The Bridge of Sunrise'=>array(2,1),
// Ebou Dar
'The Tarasin Palace'=>array(1,3),
'The Circut of Heaven'=>array(5,4),
// Emond's Field
'Thane&#39;s Mill'=>array(2,6),
'Luhhan&#39;s Forge'=>array(3,4),
// Fal Dara
'Fal Dara Keep'=>array(4,5),
'Dry Moat'=>array(6,3),
// Falme
'The Seanchan Compound'=>array(6,5),
'The Towers of Do Miere A&#39;vron'=>array(2,1),
// Far Madding
'The Hall of the Counsels'=>array(1,3),
'Amhara Market'=>array(4,5),
// Illian
'The Square of Tammaz'=>array(5,6),
'The Bridge of Flowers'=>array(1,2),
// Jehannah
'The Jheda Palace'=>array(3,2),
'Light Blessed Throne'=>array(4,1),
// Lugard
'Commerce Grounds'=>array(5,6),
'The Inner Walls'=>array(4,1),
// Maradon
'The Camp'=>array(1,5),
'The Cordamora Palace'=>array(4,2),
// Mayene
'Hospital'=>array(1,4),
'The Oilfish Shoals'=>array(6,5),
// Rhuidean
'The Great Plaza'=>array(4,6),
'Chaendaer'=>array(5,3),
// Salidar
'The Little Tower'=>array(6,3),
'Pigeon Coop'=>array(5,2),
// Shol Arbela
'Watchtowers'=>array(2,5),
'Bell Foundry'=>array(4,3),
// Stedding Shangtai
'The Stump'=>array(6,1),
'The Great Trees'=>array(2,5),
// Tanchico
'The Panarch&#39;s Palace'=>array(4,3),
'The Illuminator&#39;s Chapter House'=>array(1,2),
// Tar Valon
'The White Tower'=>array(6,4),
'The Warder Yard'=>array(2,3),
// Tear
'The Stone of Tear'=>array(3,6),
'The Maule'=>array(5,1),
// Thakan&#39;dar
'The Forges'=>array(3,5),
'Pit of Doom'=>array(4,2),
);

$shopmax= array (
"Amador" => 		array(0,1,10,4,9,7,6,2,5,11,3),
"Shol Arbela" => 	array(0,1,3,9,5,6,2,4,10,11,7),
"Illian" => 		array(0,2,9,5,4,1,10,7,3,11,6),
"Jehannah" => 		array(0,2,3,7,6,10,4,1,5,11,9),
"Rhuidean" => 		array(0,3,4,6,7,5,9,10,2,11,1),
"Tear" => 		array(0,3,7,10,1,9,2,6,4,11,5),
"Chachin" => 		array(0,4,1,7,10,6,3,2,9,11,5),
"Emond&#39;s Field" => 	array(0,4,5,2,3,7,6,9,1,11,10),
"Tanchico" =>		array(0,5,6,1,2,10,3,9,7,11,4),
"Stedding Shangtai" => 	array(0,5,7,4,9,3,1,10,6,11,2),
"Ebou Dar" => 		array(0,6,2,3,10,4,7,5,9,11,1),
"Cairhien" => 		array(0,6,4,9,1,2,5,3,7,11,10),
"Bandar Eban" => 	array(0,7,6,1,4,9,5,3,10,11,2),
"Lugard" => 		array(0,7,10,5,2,3,9,1,6,11,4),
"Mayene" => 		array(0,10,2,6,7,5,1,4,3,11,9),
"Maradon" => 		array(0,10,9,3,5,1,4,6,2,11,7),
"Thakan&#39;dar" => 	array(0,9,1,10,3,2,7,5,4,11,6),
"Caemlyn" => 		array(0,9,5,2,6,4,10,7,1,11,3),
"Cantorin" => 		array(1,2,7,1,4,10,6,5,9,3,11),
"Far Madding" => 	array(1,3,7,4,9,6,5,10,1,2,11),
"Falme" => 		array(1,4,10,6,3,1,9,2,5,7,11),
"Salidar" => 		array(1,5,10,2,1,7,3,9,6,4,11),
"Fal Dara" => 		array(1,1,9,3,2,5,10,7,4,6,11),
"Tar Valon" => 		array(1,6,9,5,7,4,2,3,10,1,11),
);

$shop_base = array (
"Amador" => array('100'=>array('-1'),'1000'=>array('-1'),'400'=>array('-1')),
"Shol Arbela" => array('100'=>array('-1'),'300'=>array('-1'),'900'=>array('-1')),
"Illian" => array('200'=>array('-1'),'900'=>array('-1'),'500'=>array('-1')),
"Jehannah" => array('200'=>array('-1'),'300'=>array('-1'),'700'=>array('-1')),
"Rhuidean" => array('300'=>array('-1'),'400'=>array('-1'),'600'=>array('-1')),
"Tear" => array('300'=>array('-1'),'700'=>array('-1'),'1000'=>array('-1')),
"Chachin" => array('400'=>array('-1'),'100'=>array('-1'),'700'=>array('-1')),
"Emond&#39;s Field" => array('400'=>array('-1'),'500'=>array('-1'),'200'=>array('-1')),
"Tanchico" => array('500'=>array('-1'),'600'=>array('-1'),'100'=>array('-1')),
"Stedding Shangtai" => array('500'=>array('-1'),'700'=>array('-1'),'400'=>array('-1')),
"Ebou Dar" => array('600'=>array('-1'),'200'=>array('-1'),'300'=>array('-1')),
"Cairhien" => array('600'=>array('-1'),'400'=>array('-1'),'900'=>array('-1')),
"Bandar Eban" => array('700'=>array('-1'),'600'=>array('-1'),'100'=>array('-1')),
"Lugard" => array('700'=>array('-1'),'1000'=>array('-1'),'500'=>array('-1')),
"Mayene" => array('1000'=>array('-1'),'200'=>array('-1'),'600'=>array('-1')),
"Maradon" => array('1000'=>array('-1'),'900'=>array('-1'),'300'=>array('-1')),
"Thakan&#39;dar" => array('900'=>array('-1'),'100'=>array('-1'),'1000'=>array('-1')),
"Caemlyn" => array('900'=>array('-1'),'500'=>array('-1'),'200'=>array('-1')),
"Cantorin" => array('200'=>array('-1'),'700'=>array('-1'),'100'=>array('-1')),
"Far Madding" => array('300'=>array('-1'),'700'=>array('-1'),'400'=>array('-1')),
"Falme" => array('400'=>array('-1'),'1000'=>array('-1'),'600'=>array('-1')),
"Salidar" => array('500'=>array('-1'),'1000'=>array('-1'),'200'=>array('-1')),
"Fal Dara" => array('100'=>array('-1'),'900'=>array('-1'),'300'=>array('-1')),
"Tar Valon" => array('600'=>array('-1'),'900'=>array('-1'),'500'=>array('-1')),
);


$loc_ship_goods = array(
'Mayene' =>      array(array(-1,-1,-1),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),),
'Tear' =>        array(array(0,0,0),array(-1,-1,-1),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),),
'Illian' =>      array(array(0,0,0),array(0,0,0),array(-1,-1,-1),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),),
'Ebou Dar' =>    array(array(0,0,0),array(0,0,0),array(0,0,0),array(-1,-1,-1),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),),
'Tanchico' =>    array(array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(-1,-1,-1),array(0,0,0),array(0,0,0),array(0,0,0),),
'Cantorin' =>    array(array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(-1,-1,-1),array(0,0,0),array(0,0,0),),
'Falme' =>       array(array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(-1,-1,-1),array(0,0,0),),
'Bandar Eban' => array(array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0),array(-1,-1,-1),), 
);

$shop_spec = array('','3','2','8','6','5','7','2','5','6','8','0','4','1','4','3','0','1','7');

$town_consumables = array (
"Tar Valon" => array(1,4,8,12,2,5,7,10,3,6,9,11), 
"Caemlyn" => array(1,4,9,11,2,5,8,12,3,6,7,10),
"Falme" => array(1,5,7,11,2,6,8,10,3,4,9,12),
"Lugard" => array(1,5,8,10,2,6,9,12,3,4,7,11), 
"Chachin" => array(1,5,9,12,2,6,7,11,3,4,8,10), 
"Illian" => array(1,6,7,12,2,4,9,10,3,5,8,11), 
"Stedding Shangtai" => array(1,6,8,11,2,4,7,12,3,5,9,10), 
"Cantorin" => array(1,6,9,10,2,4,8,11,3,5,7,12),
"Mayene" => array(2,5,7,10,3,6,9,11,1,4,8,12),
"Shol Arbela" => array(2,5,8,12,3,6,7,10,1,4,9,11),
"Rhuidean" => array(2,6,8,10,3,4,9,12,1,5,7,11),
"Far Madding" => array(2,6,9,12,3,4,7,11,1,5,8,10),
"Tanchico" => array(2,6,7,11,3,4,8,10,1,5,9,12),
"Bandar Eban" => array(2,4,9,10,3,5,8,11,1,6,7,12),
"Thakan&#39;dar" => array(2,4,7,12,3,5,9,10,1,6,8,11),
"Emond&#39;s Field" => array(2,4,8,11,3,5,7,12,1,6,9,10),
"Salidar" => array(3,6,9,11,1,4,8,12,2,5,7,10),
"Amador" => array(3,6,7,10,1,4,9,11,2,5,8,12),
"Maradon" => array(3,4,9,12,1,5,7,11,2,6,8,10),
"Jehannah" => array(3,4,7,11,1,5,8,10,2,6,9,12),
"Fal Dara" => array(3,4,8,10,1,5,9,12,2,6,7,11),
"Tear" => array(3,5,8,11,1,6,7,12,2,4,9,10),
"Ebou Dar" => array(3,5,9,10,1,6,8,11,2,4,7,12),
"Cairhien" => array(3,5,7,12,1,6,9,10,2,4,8,11),
);

$consumable_quality = array(
'19' => array('old','plain','seasoned','fresh'),
'20' => array('sickly','common','healthy','perfect'),
'21' => array('cheap','average','fine','quality'),
);

function getQualityWord($inum, $quality)
{
  global $consumable_quality;
  
  $rval = "";
  if ($quality < 0)   $quality = 0;
  else if ($quality > 3) $quality = 3;
  
  if ($inum <= 21 && $inum >= 19)
  {
    $rval = $consumable_quality[$inum][$quality];
  }
  else
  {
    $rval = "magic";
  }
  
  return $rval;
}

$town_shop_names = array (
  'Fal Dara' => array("Fal Dara Keep","Shatayan","The Mace and the Sword"),
  'Chachin' => array("The Evening Star","Field Medic","The Silver Penny"),
  'Tar Valon' => array("The Woman of Tanchico","Yellow Sister","The Tremalking Splice"),
  'Ebou Dar' => array("The Wandering Woman","Wise Woman","The Queen's Glory in Radiance"),
  'Falme' => array("The Three Plum Blossoms","Healer","The Sea Breeze"),
  'Lugard' => array("The Nine Horse Hitch","Wise Woman","The Good Night's Ride"),
  'Amador' => array("The Oak and Thorn","Hedge Doctor","The Golden Head"),
  'Emond&#39;s Field' => array("The Winespring Inn","Wisdom","The Archers"),
  'Stedding Shangtai' => array("The Trefoil Leaf","Elder","The Longing's Cure"),
  'Rhuidean' => array("The Shade and Water","Wise One","The Maiden's Kiss"),
  'Bandar Eban' => array("Trusting The Tarboner","Weather Wise","The Sword and Hand"),
  'Tear' => array("The Star","Mother","The Golden Cup"),
  'Maradon' => array("The Shield of the North","Shambayan","The Harvest Maiden"),
  'Cairhien' => array("The Great Tree","Wise One","The Bunch of Grapes"),
  'Caemlyn' => array("The Queen's Blesssing","Mother","The Crown of Roses"),
  'Tanchico' => array("The Three Plum Court","Sul'dam","The Garden of Silver Breezes"),
  'Illian' => array("The Silver Dolphin","Wise Woman","Easing the Badger"),
  'Far Madding' => array("The Counsel's Head","Herb Merchant","The Golden Wheel"),
  'Salidar' => array("The Hayloft", "Yellow Sister", "The Band's Watch"),
  'Shol Arbela' => array("A Debt Paid", "Field Medic", "The Rose's Thorn"),
  "Thakan&#39;dar" => array("The Town's Inn", "Samma N'Sei", "The Forger's Flame"),
  "Cantorin" => array("The Water Way", "Windfinder", "A Hard Bargain"),
  "Jehannah" => array("The Council's Chair", "Mother", "Over the Wall"),
  "Mayene" => array("The Oily Beard", "Yellow Sister", "The Crooked Cresent"),
);

$city_defenses= array(
  'Tanchico' => "Seanchan",
  'Amador' => "Whitecloak",
  'Chachin' => "Soldier",
  'Far Madding' => "Merchant Guard",
  'Rhuidean' => "Aiel",
  'Fal Dara' => "Warder",
  'Tear' => "Drunken Soldier",  
  "Jehannah" => "Dragonsworn",
  'Illian' => "Brigand",
  'Cairhien' => "Bandit",
  "Emond&#39;s Field" => "Wolfbrother",       
  'Lugard' => "Mercenary",
  'Bandar Eban' => "Wolf",
  'Caemlyn' => "Lion",
  'Stedding Shangtai' => "Deathwatch Guard",
  "Mayene" => "Guard Dog",
  'Falme' => "Lopar",
  "Cantorin" => "Raken",
  'Maradon' => "Asha&#39man",
  'Ebou Dar' => "Damane",
  'Tar Valon' => "Aes Sedai",
  'Shol Arbela' => "Mad Male Channeler",
  'Salidar' => "Wilder",
  "Thakan&#39;dar" => "Myrddraal",
);

$city_duel_bonus= array(
  'Tanchico' => "bN5",
  'Amador' => "nN5",
  'Chachin' => "kN5",
  'Far Madding' => "cN-5",
  'Rhuidean' => "aN5",
  'Fal Dara' => "sN5",
  'Tear' => "tN5",  
  "Jehannah" => "gN5",
  'Illian' => "iN5",
  'Cairhien' => "hN5",
  "Emond&#39;s Field" => "uN5",       
  'Lugard' => "mN5",
  'Bandar Eban' => "dN5",
  'Caemlyn' => "yN5",
  'Stedding Shangtai' => "oN5",
  "Mayene" => "rN5",
  'Falme' => "xN5",
  "Cantorin" => "fN5",
  'Maradon' => "eN5",
  'Ebou Dar' => "lN5",
  'Tar Valon' => "cN5",
  'Shol Arbela' => "qN5",
  'Salidar' => "wN5",
  "Thakan&#39;dar" => "zN5",
);

// OBE
$shopup = array (
  '0' => array(2000,9000,40000,270000,675000,1300000,2200000),
  '1' => array(3000,23000,111000,280000,780000,240000,3100000),
  '2' => array(2500,27000,131000,333000,1070000,2130000,4000000),
  '3' => array(1500,8000,32000,83000,215000,575000,731000),
  '4' => array(3000,23000,118000,322000,923000,2900000,3150000),
  '5' => array(2000,11000,65000,200000,455000,970000,1310000),
  '6' => array(1000,11000,70000,155000,325000,532000,650000),
  '7' => array(2000,22000,180000,600000,2260000,2560000),
);


function getShopUpLvls ($score,$spec)
{
  $base_shop_order = array (
   array(
    1,2,3,4,5,6,7,1,2,8,3,4,5,1,9,2,6,7,3,1,
    4,5,2,8,3,1,9,6,4,2,5,7,3,1,8,2,4,5,1,3,
    6,9,7,2,8,4,1,5,3,6,2,7,8,1,4,5,3,2,6,1,
    9,7,4,3,2,5,1,6,8,4,3,2,7,1,6,5,4,3,2,1, 
   ),
   array(
    1,2,3,4,5,6,7,2,3,1,8,5,6,4,10,3,1,2,9,6,
    4,5,7,1,3,2,10,3,2,1,8,4,6,5,9,2,1,3,6,5,
    4,10,1,2,3,7,5,4,6,8,2,3,1,9,6,4,5,3,1,2,
    10,1,3,2,7,5,6,4,8,3,2,1,9,4,5,6,7,2,1,3,
   ),
  );
  
  $scorelvls = (floor($score/100)+10);
  if ($scorelvls > 80) $scorelvls = 80;

  $maxlvl= array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
  for ($i=0; $i<$scorelvls; $i++)
  {
    $maxlvl[$spec[$base_shop_order[$spec[0]][$i]]]++;
  }

  return serialize($maxlvl);
}

$wild_types = array(
  'Aryth Ocean' => 1,
  'Sea of Storms' => 1,
  'Windbiter&#39;s Finger' => 1,
  'Bay of Remara' => 1,
  'Forest of Shadows' => 2,
  'Braem Wood' => 2, 
  'Haddon Mirk' => 2,
  'Paerish Swar' => 2,
  'Mountains of Mist' => 3,
  'Spine of the World' => 3, 
  'Kinslayer&#39;s Dagger' => 3,
  'Garen&#39;s Wall' => 3,
  'Aiel Waste' => 4,
  'Shadow Coast' => 4, 
  'Tarwin&#39;s Gap' => 4,
  'World&#39;s End' => 4,
  'Caralain Grass' => 5,
  'Black Hills' => 5,
  'Hills of Kintara' => 5,
  'Blasted Lands' => 5,
  'Plains of Maredo' => 6,
  'Plain of Lances' => 6, 
  'Almoth Plain' => 6,
  'Field of Merrilor' => 6
);

$wild_type_names = array(
  1 => "Ocean",
  2 => "Forest",
  3 => "Mountain",
  4 => "Wasteland",
  5 => "Hill",
  6 => "Plain",
);

$wt_bonuses = array(
  1 => "oE",
  2 => "fE",
  3 => "uE",
  4 => "wE",
  5 => "hE",
  6 => "pE",
);
  
$npc_type_names = array(
  1 => "Shadowspawn",
  2 => "Military",
  3 => "Ruffian",
  4 => "Channeler",
  5 => "Animal",
  6 => "Exotic Animal",
);

$nt_bonuses = array(
  1 => array("sD","sE"),
  2 => array("mD","mE"),
  3 => array("rD","rE"),
  4 => array("cD","cE"),
  5 => array("aD","aE"),
  6 => array("eD","eE"),
);  

$npc_types = array(
// Shadow
  'Trolloc' => 1,
  'Myrddraal' => 1,
  'Darkhound' => 1,
  'Draghkar' => 1,
  'Grayman' => 1,
  'Gholam' => 1,
  'Jumara' => 1,
// Military
  'Soldier' => 2,
  'Merchant Guard' => 2,
  'Whitecloak' => 2,
  'Seanchan' => 2,
  'Aiel' => 2,
  'Warder' => 2,
  'Deathwatch Guard' => 2,
// Ruffian
  'Bandit' => 3,
  'Dragonsworn' => 3,
  'Darkfriend' => 3, 
  'Brigand' => 3,
  'Mercenary' => 3,
  'Drunken Soldier' => 3,
  'Wolfbrother' => 3, 
// Channeler
  'Wilder' => 4,
  'Mad Male Channeler' => 4,
  'Damane' => 4, 
  'Asha&#39;man' => 4,
  'Black Ajah' => 4,
  'Aes Sedai' => 4,
  'Dreadlord' => 4,
// Animal
  'Guard Dog' => 5,
  'Wolf' => 5,
  'Lion' => 5,
  'Capar' => 5, 
  'Bear' => 5,
  'Gara' => 5,
  'Blacklance' => 5,
// Exotic
  'Grolm' => 6,
  'Torm' => 6,
  'Corlm' => 6,
  'Raken' => 6,
  'Lopar' => 6,
  'To&#39;raken' => 6,
  'S&#39;redit' => 6,
);

$npc_plural = array(
// Shadow
  'Trolloc' => 'Trollocs',
  'Myrddraal' => 'Myrddraal',
  'Darkhound' => 'Darkhounds',
  'Draghkar' => 'Draghkar',
  'Grayman' => 'Greymen',
  'Gholam' => 'Gholam',
  'Jumara' => 'Jumara',
// Military
  'Soldier' => 'Soldiers',
  'Merchant Guard' => 'Merchant Guards',
  'Whitecloak' => 'Whitecloaks',
  'Seanchan' => 'Seanchan',
  'Aiel' => 'Aiel',
  'Warder' => 'Warders',
  'Deathwatch Guard' => 'Deathwatch Guards',
// Ruffian
  'Bandit' => 'Bandits',
  'Dragonsworn' => 'Dragonsworn',
  'Darkfriend' => 'Darkfriends', 
  'Brigand' => 'Brigands',
  'Mercenary' => 'Mercenaries',
  'Drunken Soldier' => 'Drunken Soldiers',
  'Wolfbrother' => 'Wolfbrothers', 
// Channeler
  'Wilder' => 'Wilders',
  'Mad Male Channeler' => 'Mad Male Channelers',
  'Damane' => 'Damane', 
  'Asha&#39;man' => 'Asha&#39;men',
  'Black Ajah' => 'Black Ajah',
  'Aes Sedai' => 'Aes Sedai',
  'Dreadlord' => 'Dreadlords',
// Animal
  'Guard Dog' => 'Guard Dogs',
  'Wolf' => 'Wolves',
  'Lion' => 'Lions',
  'Capar' => 'Capar', 
  'Bear' => 'Bear',
  'Gara' => 'Gara',
  'Blacklance' => 'Blacklances',
// Exotic
  'Grolm' => 'Grolm',
  'Torm' => 'Torm',
  'Corlm' => 'Crolm',
  'Raken' => 'Raken',
  'Lopar' => 'Lopar',
  'To&#39;raken' => 'To&#39;raken',
  'S&#39;redit' => 'S&#39;redit',
);

$npc_count=0;
$npc_list = "";
foreach ($npc_types as $tmpname => $data) {
  $npc_list[$npc_count++] = $tmpname;
}

$horde_types = array(
  'Trolloc' => 'fist',
  'Myrddraal' => 'group',
  'Darkhound' => 'pack',
  'Draghkar' => 'flight',
  'Greyman' => 'group',
  'Jumara' => 'pack',
  'Gholam' => 'group',
  
  'Soldier' => 'army',
  'Warder' => 'group',
  'Aiel' => 'army',
  'Whitecloak' => 'army',
  'Seanchan' => 'army',
  'Merchant Guard' => 'group',
  'Deathwatch Guard' => 'group',
  
  'Darkfriend' => 'group', 
  'Drunken Soldier' => 'army',
  'Bandit' => 'group',
  'Brigand' => 'band',
  'Dragonsworn' => 'army',
  'Mercenary' => 'band',
  'Wolfbrother' => 'pack', 
  
  'Wilder' => 'group',
  'Damane' => 'group', 
  'Aes Sedai' => 'group',
  'Black Ajah' => 'group',
  'Mad Male Channeler' => 'group',
  'Asha&#39;man' => 'group',
  'Dreadlord' => 'group',
  
  'Guard Dog' => 'pack',
  'Capar' => 'herd', 
  'Bear' => 'sloth',
  'Wolf' => 'pack',
  'Lion' => 'pride',
  'Gara' => 'lounge',
  'Blacklance' => 'nest',
  
  'Lopar' => 'herd',
  'Grolm' => 'herd',
  'Torm' => 'herd',
  'Corlm' => 'herd',
  'Raken' => 'flight',
  'To&#39;raken' => 'flight',
  'S&#39;redit' => 'herd',
);

$enemy_list = array(
"Aiel Waste"=> array("Darkfriend","Mad Male Channeler","Lion","Capar","Gara","Aiel"),
"Almoth Plain"=> array("Dragonsworn","Whitecloak","Torm","Seanchan","Lopar","S&#39;redit"),
"Aryth Ocean"=> array("Bandit","Torm","Grolm","Corlm","Damane","Raken"),
"Bay of Remara"=> array("Guard Dog","Wilder","Merchant Guard","Soldier","Brigand","Drunken Soldier"),
"Black Hills"=> array("Guard Dog","Trolloc","Dragonsworn","Wilder","Warder","Aes Sedai"),
"Blasted Lands"=> array("Darkhound","Darkfriend","Mad Male Channeler","Myrddraal","Draghkar","Jumara"),
"Braem Wood"=> array("Soldier","Guard Dog","Mad Male Channeler","Bandit","Mercenary","Asha&#39;man"),
"Caralain Grass"=> array("Bandit","Lion","Soldier","Wolf","Black Ajah","Mercenary"),
"Field of Merrilor"=> array("Trolloc","Dragonsworn","Myrddraal","Asha&#39;man","Grayman","Dreadlord"),
"Forest of Shadows"=> array("Wolf","Bandit","Corlm","Torm","Bear","Gholam"),
"Garen&#39;s Wall"=> array("Torm","Wilder","Wolf","Whitecloak","Darkhound","Deathwatch Guard"),
"Haddon Mirk"=> array("Soldier","Bandit","Wilder","Darkfriend","Drunken Soldier","Blacklance"),
"Hills of Kintara"=> array("Grolm","Soldier","Guard Dog","Merchant Guard","Darkfriend","Black Ajah"),
"Kinslayer&#39;s Dagger"=> array("Lion","Trolloc","Bandit","Grolm","Aiel","Capar"),
"Mountains of Mist"=> array("Merchant Guard","Wolf","Whitecloak","Trolloc","Lion","Wolfbrother"),
"Paerish Swar"=> array("Wilder","Grolm","Wolf","Dragonsworn","Corlm","Bear"),
"Plain of Lances"=> array("Myrddraal","Soldier","Trolloc","Darkhound","Aes Sedai","Draghkar"),
"Plains of Maredo"=> array("Damane","Darkhound","Dragonsworn","Guard Dog","Whitecloak","Seanchan"),
"Sea of Storms"=> array("Corlm","Merchant Guard","Damane","Raken","Asha&#39;man","Brigand"),
"Shadow Coast"=> array("Whitecloak","Grolm","Torm","Damane","To&#39;raken","Lopar"),
"Spine of the World"=> array("Wilder","Guard Dog","Darkfriend","Lion","Capar","Gara"),
"Tarwin&#39;s Gap"=> array("Trolloc","Myrddraal","Darkhound","Mad Male Channeler","Gholam","Warder"),
"Windbiter&#39;s Finger"=> array("Grolm","Damane","Merchant Guard","Brigand","Raken","To&#39;raken"),
"World&#39;s End"=> array("Mad Male Channeler","Corlm","Myrddraal","Draghkar","Seanchan","Grayman"),
);

$npc_nation_names = array(
'Aiel' => array('Cosaida','Jarra','White Mountain','Jaern Rift','Bent Peak','Shelan','High Plain','Jhirad','Mosaada','Red Water','Stones River','Cold Peak','Smoke Water','Spine Ridge','Black Cliffs','Black Water','Salt Flat','Musara','Two Spires','Black Rock','Haido','Imran','Domai','Green Salts','Jonine','Jumai','Moshaine','Neder','Bitter Water','Bloody Water','Chumai','Four Holes','Four Stones','Iron Mountain','Jagged Spire','Jindo','Miadi','Nine Valleys','Jenda','Serai','Shorara'),
'Altara' => array('Tolande','Sutoma','Camrin','Carin','Cowlin','Shoran','Darvale','Anan','Furlan','Heilin','Jillien','Kostelle','Leich','Elonid','Crossin','Berengari','Nevin','Arnon','Asnobar','Sarat','Feir','Fearnim','Vadere','Vanem','Neres'),
'Amadicia' => array('Alfara','Arene','Shendar','Carnel','Jharen','Algoran','Torvald','Macura','Sim','Teran','Lugay','Faisar','Faral',' Saren'),
'Andor' => array('Adan','Allwine','Barshaw','Belman','Brune','Bunt','Conel','Farren','Fitch','Florry','Forney','Grinwell','Grubb',' Hake','Haren','Harnder','Holdwin','Kinch','Dorn','Mull','Nem','Shagrin','Halle','Silvin'),
'Arad Doman' => array('Shaeren','Fransi','Eriff','Basaheen','Neheran','Bakuvun','Tobanyi','Nishur','Caide','Sanghir','Lusara','Kurin','Chadmar','Rajar','Resara','Zeffar','Alkohima','Dabei','Marza','Soman'),
'Arafel' => array('Mosvani','Nalaam','Noreman','Terasian','Shianri','Terakuni','Raskovni','Kajima','Namelle','Vestovan','Alshinn','Venamar'),
'Cairhien' => array('Tol','Annallin','Sandair','Caldevwin','Chuliandred','Cinchonine','Corl','Daelvin','Daganred','Darengil','Delovinde','Dhulaine','Madwen','Maravin','Nethin','Nolaisen','Osiellin','Silvin','Tarsin','Tavolin','Tovere'),
'Far Madding' => array('Barsalla','Powys','Avharin','Gallger','Keene','Nalhera','Maslin','Nethvin','Cassin','Amhara','Shimel','Zeram'),
'Ghealdan' => array('Talvaen','Goared','Emry','Harod','Jorath','Treehill','Roon','Sokorin','Aleshin','Furlan','Leich'),
'Illian' => array('Raveneos','Azereos','Marcolin','Mesiaos','Padros','Boroleos','Netari','Rogad','Daronos','Karentanis','Sidoro','Dromand','Geraneos','Vevanios','Baradon','Tormon','Nathaenos','Maeldan','Cole'),
'Kandor' => array('Tolvina','Seroku','Asher','Sahera','Najima','Romavni','Bhoda','Tazanovni','Helvin','Demain','Mondevin','Shiman',' Shukosa','Nachenin','Marishna','Sahera','Kurenin','Satarov','Bihara','Tazanovni','Posavina','Arovni','Andomeran','Romera' ,'Dorelmin','Tomichi','Tuval','Marcasiev','Noallin'),
'Malkier' => array('Aldragoran','Gemallan','Arrel','Gorenellin','Kurenin','Venamar'),
'Mayene' => array('Paeron','Gallenne','Nurelle'),
'Murandy' => array('Conly','Mowly','Arman','Culen','Wynn','Dulain','Tharne','Deosin','Palan','A&#39;Balaman','Herimon','Melloy','Wynter','Neald','Shaene'),
'Ogier' => array('Alar','Corvil','Ella','Soong','Dalar','Camelle','Ala','Soferra','Halan','Elora','Amar','Coura','Dal','Morel','Jalanda','Aried','Coiam','Juin','Lacel','Laud','Laefar','Ledar','Shandin','Koimal','Moilin','Hamada','Juendan','Seren','Kolom','Radlin','Tomada','Trayal','Voniel'),
'Saldaea' => array('Bastine','Zeramene','Hachami','Kadere','Ramsin','Cheade','Ruthan','Bayanar','Ahzkan','Barada','Gahaur','Surtovni','Azeri','Nerbaijan','Kosaan','Alkaese'),
'Sea Folk' => array('Catelar','Dacan','Dacan','Rossaine','Shodein','Somarin','Takana'),
'Seanchan' => array('Yulan','Mor','Bakuun','Zeami','Faloun','Sarna','Loune','Arabah','Gurat','Magonine','Defane','Miraj','Jarath','Mondwin','Najirah','Morsa','Musenge','Emain','Sarek','Zarbey','Meldarath','Tehan','Tiras','Aladon','Turan'),
'Shienar' => array('Shinowa','Chulin','Mosalaine','Dagar','Nisura','Ronan','Timora','Nomesta'),
'Tarabon' => array('Alstaing','Forae','Larisen','Faisar','Larisett','Marinye','Lounalt','Haindehl','Rendra','Selindrin','Nemdahl','Dura','Lanasiet','Gelbarn','Delarme','Harella','Juarde','Hornval','Shefar','Torval','Nensen','Mosra'),
'Tar Valon' => array('Nermasiv','Mareed','Burlow','Dormaile','Chubain','Jovarin','Lerman','Millin','Fendry','Elward','Satina','Alkohima ','Wellin'),
'Tear' => array('Admira','Saranche','Guenna','Pendaloan','Nemosni','Narencelona','Shiego','Mecandes','Belcelona','Lopar','Arjuna','Ajala','Vandes','Alharra','Mallia','Handar','Haret','Mulan','Asegora','Cindal','Domanche','Tihera','Medrano','Toranes',' Tomares','Aldiaya'),
'Trolloc' => array("Ahf&#39;frait","Al&#39;ghol","Bhan&#39;sheen","Dha&#39;vol","Dhai&#39;mon","Dhjin&#39;nen","Ghar&#39;gheal","Ghob&#39;hlin","Gho&#39;hlem","Ghraem&#39;lan","Ko&#39;bal","Kno&#39;mon"),
'Two Rivers' => array('Ahan','Al&#39;Azar','Al&#39;Caar','Al&#39;Dai','Al&#39;Donel','Al&#39;Loras','Al&#39;Taron','Al&#39;Van','Avin','Aydaer','Ayellan','Barran','Calder','Candwin','Chandin','Cole','Congar','Coplin','Crawe','Dearn','Dautry','Dowtry','Eldin','Finngar','Gaelin','Gaelin','Garren','Hurn','Maerin','Marwin','Padwhin','Taron','Thane','Torfinn'),
);

$npc_nation_titles = array(
'Aiel' => array('the','sept',array('Hold','Stand','Territory')),
'Sea Folk' => array('Clan', '', array('Ships','Trade','Territory')),
'Ogier' => array('Elder', '', array('Stedding', 'Grove')),
'Trolloc' => array('the','band',array('Camp','Territory')),
);

$loc_npc_nations = array(
'Aryth Ocean' => array('Seanchan',"Sea Folk",'Tarabon'),
'World&#39;s End' => array('Saldaea','Arad Doman'),
'Almoth Plain' => array('Arad Doman','Tarabon'),
'Shadow Coast' => array('Altara','Seanchan'),
'Plain of Lances' => array('Arafel','Kandor'),
'Mountains of Mist' => array('Two Rivers','Ghealdan'),
'Forest of Shadows' => array('Ghealdan','Murandy','Amadicia'),
'Caralain Grass' => array('Two Rivers','Saldaea'),
'Plains of Maredo' => array('Illian','Tear'),
'Sea of Storms' => array('Illian','Altara'),
'Tarwin&#39;s Gap' => array('Shienar','Kandor','Malkier'),
'Kinslayer&#39;s Dagger' => array('Cairhien','Tar Valon','Shienar'),
'Haddon Mirk' => array('Tear','Far Madding'),
'Spine of the World' => array('Ogier','Malkier'),
'Aiel Waste' => array('Aiel'),
'Black Hills' => array('Andor','Arafel','Tar Valon'),
'Braem Wood' => array('Andor','Cairhien'),
'Hills of Kintara' => array('Amadicia','Far Madding','Murandy'),
"Windbiter&#39;s Finger"=> array('Altara',"Sea Folk",),
"Bay of Remara"=> array('Tear',"Sea Folk",),
"Blasted Lands"=> array('Trolloc'),
"Field of Merrilor"=> array('Arafel','Shienar',),
"Garen&#39;s Wall"=> array('Ghealdan','Amadicia'),
"Paerish Swar"=> array('Arad Doman','Two Rivers'),
"Mayene"=> array('Tear',"Sea Folk","Mayene"),
"Salidar"=> array('Altara','Amadicia'),
"Jehannah"=> array('Ghealdan','Amadicia'),
"Shol Arbela"=> array('Arafel','Malkier'),
"Cantorin"=> array("Sea Folk","Seanchan"),
"Thakan&#39;dar"=> array('Trolloc'),
'Bandar Eban' => array('Arad Doman'),
'Falme' => array('Seanchan'),
'Tanchico' => array('Tarabon'),
'Amador' => array('Amadicia','Ghealdan'),
'Maradon' => array('Saldaea','Malkier'),
'Emond&#39;s Field' => array('Two Rivers'),
'Ebou Dar' => array('Altara'),
'Chachin' => array('Kandor'),
'Caemlyn' => array('Andor'),
'Lugard' => array('Murandy','Ghealdan'),
'Illian' => array('Illian'),
'Fal Dara' => array('Shienar','Arafel'),
'Tar Valon' => array('Tar Valon','Arafel'),
'Cairhien' => array('Cairhien'),
'Far Madding' => array('Far Madding','Murandy'),
'Stedding Shangtai' => array('Ogier'),
'Tear' => array('Tear'),
'Rhuidean' => array('Aiel'),
);

$location_list= array(
'Aiel Waste',
'Almoth Plain',
'Amador',
'Aryth Ocean',
'Bandar Eban',
'Bay of Remara',
'Black Hills',
'Blasted Lands',
'Braem Wood',
'Caemlyn',
'Cairhien',
'Cantorin',
'Caralain Grass',
'Chachin',
'Ebou Dar',
'Emond&#39;s Field',
'Fal Dara',
'Falme',
'Far Madding',
'Field of Merrilor',
'Forest of Shadows',
'Garen&#39;s Wall',
'Haddon Mirk',
'Hills of Kintara',
'Illian',
'Jehannah',
'Kinslayer&#39;s Dagger',
'Lugard',
'Maradon',
'Mayene',
'Mountains of Mist',
'Paerish Swar',
'Plain of Lances',
'Plains of Maredo',
'Rhuidean',
'Salidar',
'Sea of Storms',
'Shadow Coast',
'Shol Arbela',
'Spine of the World',
'Stedding Shangtai',
'Tanchico',
'Tar Valon',
'Tarwin&#39;s Gap',
'Tear',
'Thakan&#39;dar',
'Windbiter&#39;s Finger',
'World&#39;s End'
);

$npc_nation_data = array(
'Aiel' => array(array('Hold','Stand','Territory'),array('the'),array('Sept')),
'Altara' => array(array('Farm', 'Village','Ships' ),array('Master','Mistress'),array(''),),
'Amadicia' => array(array('Farm','Town','Merchants'),array('Master','Mistress'),array(''),),
'Andor' => array(array('Farm', 'Village', 'Workers'),array('Master','Mistress'),array(''),),
'Arad Doman' => array(array('Village','Town','Ships'),array('Master','Mistress'),array(''),),
'Arafel' => array(array('Fort', 'Troops', 'Merchants'),array('Master','Mistress'),array(''),),
'Cairhien' => array(array('Farm', 'Town', 'Workers' ),array('Master','Mistress'),array(''),),
'Far Madding' => array(array('Farm', 'Town', 'Merchants'),array('Master','Mistress'),array(''),),
'Ghealdan' => array(array('Farm', 'Town', 'Miners'),array('Master','Mistress'),array(''),),
'Illian' => array(array('Farm','Village', 'Ships'),array('Master','Mistress'),array(''),),
'Kandor' => array(array('Fort', 'Town', 'Miners'),array('Master','Mistress'),array(''),),
'Malkier' => array(array('Troops','Merchants','Miners'),array('Master','Mistress'),array(''),),
'Mayene' => array(array('Shoals', 'Ships', 'Merchants'),array('Master','Mistress'),array(''),),
'Murandy' => array(array('Farm', 'Village', 'Merchants'),array('Master','Mistress'),array(''),),
'Ogier' => array(array('Stedding','Grove', 'Workers'),array('Elder'),array(''),),
'Saldaea' => array(array('Farm', 'Fort', 'Merchants'),array('Master','Mistress'),array(''),),
'Sea Folk' => array(array('Ships','Isles','Territory'),array('Clan'),array(''),),
'Seanchan' => array(array('Farm','Troops','Ships'),array('Master','Mistress'),array(''),),
'Shienar' => array(array('Fort','Town','Troops'),array('Master','Mistress'),array(''),),
'Tarabon' => array(array('Farm','Village','Ships'),array('Master','Mistress'),array(''),),
'Tar Valon' => array(array('Fort','Town','Miners'),array('Master','Mistress'),array(''),),
'Tear' => array(array('Village','Fort','Merchants'),array('Master','Mistress'),array(''),),
'Trolloc' => array(array('Camp','Territory','Cookpots'),array('the'),array('band'),),
'Two Rivers' => array(array('Farm','Village','Workers'),array('Master','Mistress'),array(''),),
);

function build_base_consume_list ($order)
{
  $base_list = array(
   '19' => array(array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0)),
   '20' => array(array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0)),
   '21' => array(array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0),
                 array(0,0,0,0,0,0,0,0,0,0,0,0)));
  for ($x = 0; $x < 4; $x++)
  {
    $base_list['19'][0][$order[$x]-1] = -1;
  }
  for ($x = 4; $x < 8; $x++)
  {
    $base_list['20'][0][$order[$x]-1] = -1;
  }
  for ($x = 8; $x < 12; $x++)
  {
    $base_list['21'][0][$order[$x]-1] = -1;
  }
  
  return $base_list;
}

$clan_building_bonuses= "";
$result = mysql_query("SELECT id, upgrades, name FROM Locations WHERE ruler='".$char[society]."' AND isDestroyed='0'");
while ($town = mysql_fetch_array( $result ) )
{
  $tmpUps = unserialize($town[upgrades]);
  $clan_building_bonuses .= getBuildClanBonuses($tmpUps, $unique_buildings[$town[name]], $unique_build_bonuses);
}

$location = mysql_fetch_array(mysql_query("SELECT * FROM Locations WHERE name='$char[location]'"));

// setup bonuses for the current location
include("locBonuses.php");

function getTownBonuses($upgrades, $ub, $ubb, $horde=0)
{
  $bonuses = "";
  for ($x=0; $x<6; $x++)
  {
    $bonuses .= getBuildBonuses($x,$upgrades[$x],$horde);
  }
  if (!$horde)
  {
    if ($upgrades[6])
    {
      $bonuses .= $ubb[$ub[0]][0]." ";
    }
    if ($upgrades[7])
    {
      $bonuses .= $ubb[$ub[1]][0]." ";
    }
  }    
  return $bonuses;
}

function getBuildBonuses($build, $lvl, $horde=0)
{
  $bonuses = '';
  
  $div = 1+$horde;
  if ($build==0)
    $bonuses = "oV-".($lvl*3/$div)." ";
  elseif ($build==1)
    $bonuses = "rV-".($lvl*2/$div)." ";
  elseif ($build==2)
    $bonuses = "dW".($lvl*10/$div)." ";
  elseif ($build==3)
    $bonuses = "iS".($lvl*2/$div)." ";
  elseif ($build==4)
    $bonuses = "bD".($lvl*2/$div)." ";
  elseif ($build==5)
    $bonuses = "lV-".($lvl*2/$div)." ";

  return $bonuses;
}

function getBuildClanBonuses($upgrades, $ub, $ubb)
{
  if ($upgrades[6])
  {
    $bonuses .= $ubb[$ub[0]][1]." ";
  }
  if ($upgrades[7])
  {
    $bonuses .= $ubb[$ub[1]][1]." ";
  }    
  return $bonuses;
}

function getNatBonuses($nb,$wf,$nf,$town_bonuses)
{
  $bonuses = "";
  $nat_bonus = cparse($nb);
  
  $bonuses .= "N".($nat_bonus[$wf]+$town_bonuses[$nf[1]]+0)." O".($nat_bonus[$nf[0]]+$town_bonuses[$nf[0]]+0);

  return $bonuses;
}

function updateSocScores()
{
  $result = mysql_query("SELECT id, ruler, estate_support, name FROM Locations WHERE 1");
  while ($loc = mysql_fetch_array( $result ) )
  {
    $clans = array();
    $locid = $loc['id'];
    $clanscores = '';
    $supporting='';
    $result2 = mysql_query("SELECT id, support, area_score FROM Soc WHERE 1 ORDER BY score DESC");
    while ($soc = mysql_fetch_array( $result2 ) )
    {
      $support = unserialize($soc['support']);
      $clan_id = $soc[id];
      if (!$clanscores[$clan_id]) $clanscores[$clan_id]=0;
      $supporting[$soc[id]]==0;
      if ($support[$locid] != 0 && $support[$locid] != $clan_id)
      {
        $supporting[$soc[id]]=$support[$locid];
        $clan_id = $support[$locid];
      }
      $area_score = unserialize($soc['area_score']);
      if ($supporting[$soc[id]] == 0)  $clanscores[$clan_id] += $area_score[$locid];
      else $clanscores[$clan_id] += $area_score[$locid]/2;
    }
    
    // add estate support and format
    $result3 = mysql_query("SELECT id FROM Hordes WHERE done='0' AND target='$loc[name]'");
    $numhorde = mysql_num_rows($result3);
    $ediv=1;
    if ($numhorde) $ediv=2;
    $es= unserialize($loc[estate_support]);
    $finalscores='';
    if ($clanscores)
    {
      foreach ($clanscores as $csid => $cscore)
      {
        if ($es[$csid]) 
        {
          $cscore = $cscore + ($es[$csid]/$ediv);
        }
        $cscore=number_format($cscore, 4, '.', '');
        $finalscores[$csid]=$cscore;
        if (!$scid) unset($clanscores[$scid]);
      }
      $clanscores=$finalscores;
      asort($clanscores);
      $clanscores = array_reverse($clanscores, TRUE);
      $scs=serialize($clanscores);
      $ss=serialize($supporting);
      mysql_query("UPDATE Locations SET clan_scores='$scs', clan_support='$ss' WHERE id='$loc[id]'"); 
    }
  }
}

function updateEstateSupport($estate_ji)
{
  $result= mysql_query("SELECT * FROM Estates WHERE 1");
  $esup = array();
  while ($estate = mysql_fetch_array( $result ) )
  {
    if ($estate[supporting] != "")
    {
      $supporting = unserialize($estate[supporting]);
      if (count($supporting))
      {
        foreach ($supporting as $loc => $clan) 
        {
          if (!$esup[$loc][$clan]) 
          {$esup[$loc][$clan]=0;}
         $esup[$loc][$clan]+= $estate_ji[$estate[level]];
        }
      }
    }
  }
  
  foreach ($esup as $loc => $data)
  {
    $sdata = serialize($data);
    mysql_query("UPDATE Locations SET estate_support='$sdata' WHERE name='$loc'"); 
  }
}

function updateSocRep($society, $locid, $gold)
{
  $area_rep = unserialize($society[area_rep]);
  $area_rep[$locid] += $gold/10000;
  if ($area_rep[$locid] > 500) $area_rep[$locid] = 500;
  $area_reps = serialize($area_rep);
  mysql_query("UPDATE Soc SET area_rep='".$area_reps."' WHERE id='".$society[id]."'");
}

?>