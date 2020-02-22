<?php
// business arrays and functions

$wikilinks = array("Inn","Wise+Woman","Tavern","Outfitter");

$pouch_name=array(
  array('pockets',0,0),
  array('purse',2,0),
  array('belt pouch',4,0),
  array('scrip',6,0),
  array('small bag',8,0),
  array('case',10,0),
  array('saddle bag',12,0),
  array('chest',14,0),
);
$pouch_cost=array(
  0,
  5000,
  10000,
  40000,
  100000,
  200000,
  350000,
  500000,
);

  $pack_name=array(
    array('canvas sack',0,0),
    array('backpack',5,0),
    array('small cart',10,1),
    array('cart',15,2),
    array('farm cart',20,3),
    array('wagon',25,4),
    array('carriage',30,5),
    array('merchants wagon',35,6),
  );
  $pack_cost=array(
    0,
    5000,
    10000,
    40000,
    100000,
    200000,
    350000,
    500000,
  );
  
  $travel_mode=array(
    array('foot',0,0),
    array('pony',4,1),
    array('mule',8,3),
    array('mare',12,2),
    array('gelding',12,4),
    array('stallion',16,5),
    array('draft horse',12,6),
    array('war horse',20,4),
  );
 

# Old (P-1)
# Plump (M-1)
# Common
# Meek (M-1 P1)
# Fiery (M1 P-1)
# Tarien (M1 P1)
# Razor (M2)
  
  $travel_mode_cost=array(
    0,
    5000,
    10000,
    40000,
    100000,
    200000,
    400000,
    400000,
  );
  function HorseNamer()
  {
    $first=array(
      'Cloud',
      'Wind',
      'Spray',
      'Mist',
      'Willow',
      'Spirit',
      'Myth',
      'Rum',
      'Dark',
      'Long',
      'Quick',
      'Rye',
      'Sky',
    );
    $last=array(
      'walker',
      'prancer',
      'driver',
      'flasher',
      'breaker',
      'bender',
      'bucker',
      'smacker',
      'stepper',
      'dancer',
      'rider',
    );  
    if (rand(0,10)<7) $horsename=$first[rand(0,count($first)-1)].$last[rand(0,count($last)-1)];
    else $horsename=$first[rand(0,count($first)-1)];
    return $horsename;
  }
  
?>