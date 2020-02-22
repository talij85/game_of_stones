<?php

$stationbase=2500;
$hirebase=2500;

// Jobs
$job_names = array(
'0'=>"Merchant",
'1'=>"Blacksmith",
'2'=>"Performer",
'3'=>"Cutpurse",
'4'=>"Medic",
'5'=>"Brewer",
'6'=>"Cook",
'7'=>"Farmer",
'8'=>"Miner",
'9'=>"Sailor",
'10'=>"Survivalist",
'11'=>"Peddler",
'12'=>"Tracker",
);

$job_biz_names = array(
'0'=>array("Stall","Display","Offer","Offer posted!"),
'4'=>array("Garden","Plot","Grow","Seeds planted!"),
'5'=>array("Brewery","Kettle","Brew","Mixing started!"),
'6'=>array("Kitchen","Oven","Cook","Food cooking!"),
'7'=>array("Farm","Field","Cultivate","Fields planted!"),
'8'=>array("Mine","Shaft","Mine","Mining underway!"),
'9'=>array("Shipping","Ship","Sail","Setting sail!"),
'99'=>array("Office","Associate","Assign","Assignment accepted!"),
);

$job_time_bonus = array(
'4'=>"cT",
'5'=>"cT",
'6'=>"cT",
'7'=>"fT",
'8'=>"mT",
'9'=>"sT",
'99'=>"oT",
);

$biz_max_produce = array(
'4'=>array(0,2,3,4,6,7,8,10,11,12),
'5'=>array(0,2,3,4,6,7,8,10,11,12),
'6'=>array(0,2,3,4,6,7,8,10,11,12),
'7'=>array(0,2,4,6,8,10,12,14,16,18),
'8'=>array(0,2,3,4,6,7,8,10,11,12),
'9'=>array(0,1,2,3,4,5,6,6,6,6),
'99'=>array(0,2,4,6,6,8,8,8,8,8),
);

$biz_max_produce = array(
'4'=>array(0,2,3,4,6,7,8,10,11,12),
'5'=>array(0,2,3,4,6,7,8,10,11,12),
'6'=>array(0,2,3,4,6,7,8,10,11,12),
'7'=>array(0,2,4,6,8,10,12,14,16,18),
'8'=>array(0,2,3,4,6,7,8,10,11,12),
'9'=>array(0,1,2,3,4,5,6,6,6,6),
'99'=>array(0,2,4,6,6,8,8,8,8,8),
);

$biz_hires = array(
'7'=>array("Farm Hand","Ogier"),
'8'=>array("Mine Worker","Earth Singer"),
);

$hire_stats = array(
"Farm Hand" => array("fT-2"),
"Ogier" => array("xV2"),
"Mine Worker" => array("mT-2"),
"Earth Singer" => array("mV2"),
);

$biz_costs = array(
'0'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'4'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'5'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'6'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'7'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'8'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'9'=>array(2500, 5000, 10000, 15000, 25000, 35000),
'99'=>array(10000, 20000, 40000, 60000, 90000, 120000),
);

$biz_tools= array(
'0'=>array("Discriminating Seller","Friendly Discount","Preferential Treatment"),
'4'=>array("Improved Fertalizer","Trained Dog","Balanced Watering"),
'5'=>array("Heater","Orchard","Improved Kettles"),
'6'=>array("Improved Cookware","Rare Spices","Recipe Book"),
'7'=>array("Harvester","Irrigation","Multiple Plow"),
'8'=>array("Mine Carts","Mineral Cutting Tools","Improved Lanterns"),
'9'=>array("Compass","Improved Cargo Holds","Superior Rigging"),
'99'=>array("Trusted Informants","Expansive Connections","Bribed Officials"),
);

$tool_stats = array(
"Discriminating Seller" => array("eM5 ",50000),
"Friendly Discount" => array("aM-5",50000),
"Preferential Treatment" => array("eM3 aM-3",100000),

"Improved Fertalizer" => array("gT-5 ",50000),
"Trained Dog" => array("gQ5 ",50000),
"Balanced Watering" => array("gT-3 gQ3 ",100000),

"Heater" => array("bT-5 ",50000),
"Orchard" => array("bQ5 ",50000),
"Improved Kettles" => array("bT-3 bQ3 ",100000),

"Improved Cookware" => array("kT-5",50000),
"Rare Spices" => array("kQ5",50000),
"Recipe Book" => array("kT-3 kQ3",100000),

"Harvester" => array("fT-5",50000),
"Irrigation" => array("xV5",50000),
"Multiple Plow" => array("fT-3 xV3",100000),

"Mine Carts" => array("mT-5 ",50000),
"Mineral Cutting Tools" => array("mV5 ",50000),
"Improved Lanterns" => array("mT-3 mV3 ",100000),

"Compass" => array("sT-5",50000),
"Improved Cargo Holds" => array("sV5",50000),
"Superior Rigging" => array("sT-3 sR50",100000),

"Trusted Informants" => array("oT-5",50000),
"Expansive Connections" => array("oQ5",50000),
"Bribed Officials" => array("oT-3 oQ3",100000),
);

$loc_biz = array (
  'Chachin' => array('8'),
  'Falme' => array('9'),
  'Tanchico' => array('9'),
  'Ebou Dar' => array('9'),
  'Illian' => array('9'),
  'Tear' => array('9'),
  'Stedding Shangtai' => array('8'),
  'Cairhien' => array('7'),
  'Bandar Eban' => array('9'),
  'Tar Valon' => array('7'),
  'Caemlyn' => array('7'),
  'Far Madding' => array('8'),
  'Amador' => array('7'),
  'Emond&#39;s Field' => array('7'),
  'Rhuidean' => array('8'),
  'Maradon' => array('8'),
  'Fal Dara' => array('8'),
  'Lugard' => array('7'),
  "Thakan&#39;dar" => array('8'),
  "Cantorin" => array('9'),
  'Salidar' => array('7'),
  'Mayene' => array('9'),
  'Shol Arbela' => array('8'),
  'Jehannah' => array('7'),
);

$trade_goods= array(
// Farm
'Turnips' => array('8', '1250'),
'Potatoes' => array('16', '1950'),
'Hay' => array('32','3900'),
'Beans' => array('48','5900'),
'Wheat' => array('12','1550'),
'Zemai' => array('52','6400'),
'Barley' => array('20','2550'),
'Apples' => array('28','3450'),
'Tamat' => array('24','3000'),
'Chili' => array('36','4550'),
'Bananas' => array('44','5600'),
'Ice Peppers' => array('40','4950'),
'Bamboo' => array('56','7000'),
'Cherries' => array('60','7600'),
'Olives' => array('64','8200'),
'Strawberries' => array('4','525'),
'Algode' => array('68','8600'),
'Tabac' => array('72','9300'),

// Mine
'Stone' => array('12','1550'),
'Iron' => array('24','3000'),
'Lead' => array('36','4550'),
'Silver' => array('18','2250'),
'Copper' => array('30','3650'),
'Salt' => array('42','5250'),
'Crystal' => array('48','5900'),
'Gold' => array('54','6700'),
'Tin' => array('60','7600'),
'Sulfur' => array('6','800'),
'Gems' => array('66','8400'),
'Alum' => array('72','9300'),

// Ship
'Oil' => array('0','600'),
'Ivory' => array('0','600'),
'Silk' => array('0','600'),
'Grain' => array('1','600'),
'Olive Oil' => array('1','600'),
'Spices' => array('1','600'),
'Gold Bars' => array('2','600'),
'Fish' => array('2','600'),
'Honey' => array('2','600'),
'Alum Ore' => array('3','600'),
'Wool' => array('3','600'),
'Cloth' => array('3','600'),
'Dyes' => array('4','600'),
'Fireworks' => array('4','600'),
'Wine' => array('4','600'),
'Porcelain' => array('5','600'),
'Glass' => array('5','600'),
'Gemstones' => array('5','600'),
'Kaf' => array('6','600'),
'Lumber' => array('6','600'),
'Pelts' => array('6','600'),
'Furs' => array('7','600'),
'Books' => array('7','600'),
'Peppers' => array('7','600'),

// medic
'Worrynot Root' => array('10','100'),
'Boneknit' => array('10','100'),
'Dogwort' => array('20','200'),
'Sheepstongue Root' => array('20','200'),
'Silver Leaf' => array('30','300'),
'Goosemint' => array('30','300'),
'Grey Fennel' => array('40','400'),
'Crimsonthorn Root' => array('40','400'),
'Flatwort' => array('50','500'),
'Greenwort' => array('50','500'),
'Tarchrot Leaf' => array('60','600'),
'Andilay Root' => array('60','600'),

// brewer
'Oosquai' => array('10','100'),
'Ale' => array('10','100'),
'Cider' => array('20','200'),
'Apple Brandy' => array('20','200'),
'Wine' => array('30','300'),
'Willowbark Tea' => array('30','300'),
'Gara Milk' => array('40','400'),
'Blood Wine' => array('40','400'),
'Chainleaf Tea' => array('50','500'),
'Brandy' => array('50','500'),
'Bluespine Tea' => array('60','600'),
'Kaf' => array('60','600'),

// cook
'Broth' => array('5','50'),
'Bread' => array('10','100'),
'Eggs' => array('15','150'),
'Hot Cakes' => array('20','200'),
'Fruit' => array('25','250'),
'Cheese' => array('30','300'),
'Dried Beef' => array('35','350'),
'Rabbit' => array('40','400'),
'Stew' => array('45','450'),
'Gilded Fish' => array('50','500'),
'Spiced Ham' => array('55','550'),
'Roast Beef' => array('60','600'), 

// office
'Increase Order' => array('20','0.01'), 
'Increase Chaos' => array('20','0.01'), 
'Increase City Funds' => array('40','2'), 
'Decrease City Funds' => array('40','2'), 
'Weaken City Defenses' => array('60','1'),
'Strengthen City Defenses' => array('60','1'),
'Increase Ruler Ji' => array('80','0.01'), 
'Decrease Ruler Ji' => array('80','0.01'),
);

$job_goods = array(
'4' => array('','Worrynot Root','Boneknit','Dogwort','Sheepstongue Root', 'Silver Leaf','Goosemint',
             'Grey Fennel', 'Crimsonthorn Root', 'Flatwort', 'Greenwort', 'Tarchrot Leaf', 'Andilay Root'),
'5' => array('','Oosquai','Ale','Cider','Apple Brandy','Wine','Willowbark Tea',
             'Gara Milk','Blood Wine', 'Chainleaf Tea', 'Brandy','Bluespine Tea','Kaf'),
'6' => array('','Broth','Bread','Eggs','Hot Cakes', 'Fruit', 'Cheese',
              'Dried Beef','Rabbit','Stew','Gilded Fish','Spiced Ham','Roast Beef'),
'7' => array('','Turnips', 'Potatoes', 'Hay', 'Beans', 'Wheat', 'Zemai', 
             'Barley', 'Apples', 'Tamat', 'Chili', 'Bananas', 'Ice Peppers', 
             'Bamboo', 'Cherries', 'Olives', 'Strawberries', 'Algode', 'Tabac'),
'8' => array('','Stone', 'Iron', 'Lead', 'Silver', 'Copper', 'Salt',
             'Crystal', 'Gold', 'Tin', 'Sulfur', 'Gems', 'Alum'),
'9' => array(array('Oil','Ivory','Silk'),
             array('Grain', 'Olives', 'Spices'),
             array('Fish', 'Honey', 'Gold Bars'),
             array('Alum Ore', 'Wool', 'Cloth'),
             array('Wine','Dyes','Fireworks'),
             array('Gemstones', 'Glass', 'Porcelain'),
             array('Lumber','Pelts', 'Kaf'),
             array('Furs','Books','Peppers'),
             ),  
'99' => array('','Increase Order', 'Increase Chaos', 'Increase City Funds', 'Decrease City Funds', 
              'Strengthen City Defenses', 'Weaken City Defenses', 'Increase Ruler Ji', 'Decrease Ruler Ji'),          
);

$ship_ports = array('Mayene','Tear','Illian','Ebou Dar','Tanchico','Cantorin','Falme','Bandar Eban');

$ship_travel= array(
'Mayene' => array(0,100,250,350,600,800,900,1000),
'Tear' => array(100,0,150,250,500,700,800,900),
'Illian' => array(250,150,0,100,350,550,650,750),
'Ebou Dar' => array(350,250,100,0,250,450,550,650),
'Tanchico' => array(600,500,350,250,0,200,300,400),
'Cantorin' => array(800,700,550,450,200,0,100,200),
'Falme' => array(900,800,650,550,300,100,0,100),
'Bandar Eban' => array(1000,900,750,650,400,200,100,0), 
);

$ship_order = array('Work Boat','River Boat','Soarer','Darter','Raker','Seanchan');

$ship_type = array(
'Work Boat' => array(5000,10,10,300),
'River Boat' => array(10000,11,12,400),
'Soarer' => array(20000,13,11,500),
'Darter' => array(30000,15,13,600),
'Raker' => array(40000,14,14,800),
'Seanchan' => array(50000,13,15,1000),
);

function getOfficeLevel($ji)
{
  $rval=0;
  $officeJiReqs= array(0,50,150,300,500,750,1050,1400,1800,2250);
  for ($i=1; $i<10; $i++)
  {
    if ($ji >= $officeJiReqs[$i]) $rval=$i;
  }
  return $rval;
}
?>