<?php
// ITEM FUNCTIONS

// ITEM TYPES
$item_type = array (
  '0' => "seal",
  '1' => "sword",     // straight damage
  '2' => "axe",        // lower speed, higher damage
  '3' => "spear",     // dodge, damage%
  '4' => "bow",       // accuracy, firststrike
  '5' => "bludgeon", // defense%, stun
  '6' => "knife",       // speed, lower damage
  '7' => "shield",      // straight defense
  '8' => "weave",     // variable
  '9' => "body armor",
  '10' => "leg armor",
  '11' => "cloak",
  '12' => "ter'angreal", // held
  '13' => "ter'angreal", // worn
  '19' => "food",
  '20' => "herb",
  '21' => "drink",
  '99' => "legendary item",
);

$item_type_pl = array (
  '0' => "seals",
  '1' => "swords",     // straight damage
  '2' => "axes",        // lower speed, higher damage
  '3' => "spears",     // dodge, damage%
  '4' => "bows",       // accuracy, firststrike
  '5' => "bludgeons", // defense%, stun
  '6' => "knives",       // speed, lower damage
  '7' => "shields",      // straight defense
  '8' => "weaves",     // variable
  '9' => "body armor",
  '10' => "leg armor",
  '11' => "cloaks",
  '12' => "ter'angreal", // held
  '13' => "ter'angreal", // worn
  '19' => "food",
  '20' => "herbs",
  '21' => "drinks",
);

$ter_bonuses = array (
  '1' => array (' N2', ' O2'), // defense %, damage %
  '2' => array (' O2', ' S1'), // damage %, stun
  '3' => array (' V1', ' F1'), // dodge, first strike
  '4' => array (' F1', ' Y1'), // firststrike, accuracy
  '5' => array (' S1', ' N2'), // stun, defense %
  '6' => array (' Y1', ' V1'), // accuracy, dodge
  '7' => array (' G1', ' Y1'), // accuracy, health gain
  '8' => array (' A0', ' B0'), // variable
  '9' => array (' N2', ' V1'), // defense %, dodge
  '10' => array (' Y1', ' N2'), // accuracy, defense %
  '11' => array (' V1', ' G1'), // dodge, health gain %
  '12' => array ( '0' => array (' X1', ' G3'), 
                  '1' => array (' O5', ' W1'), 
                  '2' => array (' P3', ' A1 B2')), // held
  '13' => array ( '0' => array (' S2', ' Y3'), 
                  '1' => array (' T2', ' V3'), 
                  '2' => array (' L2', ' D1 E2'), 
                  '3' => array (' F2', ' N5')), // worn
);

// ITEM BASE
/*
X- Speed
S- Stun
Y- Accuracy
V- Dodge
P- Poison
T- Taint
G- Life Gain
H- Damage to self %
L- Luck
*/

$item_base = array (

// sword
'short sword' => array("A4 B6",1),
'sword' => array("A9 B11",1),
'trolloc blade' => array("A12 B13 T3",1),
'long sword' => array("A19 B21",1),
'tulwar' => array("A24 B26",1),
'bastard sword' => array("A29 B31",1),
'claymore' => array("A34 B36",1),
'broad sword' => array("A39 B41",1),
'battle sword' => array("A44 B46",1),
'flamberge' => array("A49 B51",1),
'winged sword' => array("A54 B56",1),
'dark blade' => array("A57 B59 H4 T6",1),
'heron mark blade' => array("A65 B65 L2",1),

// axes
'hatchet' => array("A4 B7 X-1 O3",2),
'hand axe' => array("A10 B12 X-1 O1",2),
'cleaver' => array("A15 B18 X-1",2),
'trolloc axe' => array("A20 B22 T1 X-1",2),
'half moon blade' => array("A25 B28 X-1 O2",2),
'combat axe' => array("A30 B34 X-1 O1",2),
'balanced axe' => array("A35 B38 X-1 O3",2),
'double bladed axe' => array("A41 B45 X-1",2),
'battle axe' => array("A47 B51 X-1 H1",2),
'bone splitter' => array("A51 B55 O1 X-1",2),
'executioner' => array("A57 B61 X-1",2),
'spiked axe' => array("A61 B66 T1 X-1",2),
'great axe' => array("A69 B74 X-1",2),

// spears
'sharpened stick' => array("A4 B5 V2",3),
'iron spear' => array("A8 B10 V3",3),
'short spear' => array("A13 B14 V2 F1",3),
'glaive' => array("A19 B20 V1",3),
'long spear' => array("A23 B25 V1 O1",3),
'pike' => array("A28 B29 V2",3),
'javelin' => array("A32 B34 V1 F1",3),
'lance' => array("A38 B40 V1",3),
'septum' => array("A42 B44 V2",3),
'trident' => array("A46 B49 F1 P1",3),
'black lance' => array("A51 B54 T2 P1",3),
'combat spear' => array("A57 B59 V1 O1",3),
'ashandarei' => array("A61 B64 V1 O1 F1",3),

// bows
'sling' => array("A3 B6 Y2",4),
'short bow' => array("A8 B10 Y3",4),
'light crossbow' => array("A12 B15 Y2 F1",4),
'long bow' => array("A18 B21 Y1",4),
'horn bow' => array("A23 B25 Y1 O1",4),
'battle bow' => array("A27 B30 Y2",4),
'heavy crossbow' => array("A31 B35 Y1 F1",4),
'hunters bow' => array("A37 B41 Y1",4),
'long battle bow' => array("A41 B45 Y2",4),
'warriors bow' => array("A45 B50 F1 P1",4),
'composite crossbow' => array("A49 B52 F2 Y1",4),
'two rivers long bow' => array("A56 B60 Y1 O1",4),
'silver bow' => array("A60 B65 Y1 O1 F1",4),

// bludgeon
'cudgel' => array("A4 B5 S1 N2",5),
'short staff' => array("A9 B10 S1",5),
'spiked club' => array("A13 B15 N2 S1",5),
'mallet' => array("A18 B20 S1 N1",5),
'quarter staff' => array("A23 B24 S1 N2",5),
'mace' => array("A27 B29 S2",5),
'flail' => array("A32 B34 S1 N2",5),
'morning star' => array("A38 B41 N1",5),
'maul' => array("A43 B44 S1",5),
'war hammer' => array("A49 B50 N1",5),
'battle staff' => array("A51 B52 S1 N3",5),
'spiked maul' => array("A56 B59 S1 N1",5),
'battle hammer' => array("A61 B64 S1 O2 N1",5),

// knives
'stone knife' => array("A3 B5 O2 X1",6),
'bone knife' => array("A8 B10 X1",6),
'steel bladed knife' => array("A12 B15 O1 X1",6),
'throwing knives' => array("A16 B19 X1 L1",6),
'belt knife' => array("A21 B24 O2 X1",6),
'long knife' => array("A26 B29 O1 X1",6),
'dagger' => array("A31 B34 X1",6),
'long throwing knives' => array("A35 B38 X1 O2",6),
'battle dagger' => array("A41 B43 X1",6),
'kriss' => array("A44 B46 T2 X1",6),
'stilletto' => array("A49 B51 O1 P1 X1",6),
'balanced throwing knives' => array("A54 B57 X1 O1",6),
'ruby dagger' => array("A60 B63 T2 X1 H1",6),

// shields
'wooden shield' => array("D4 E6",7),
'trolloc shield' => array("D9 E11",7),
'buckler' => array("D14 E16",7),
'kite shield' => array("D18 E22",7),
'tairen shield' => array("D23 E27",7),
'defenders shield' => array("D28 E32",7),
'amador shield' => array("D33 E37",7),
'spiked buckler' => array("D38 E42",7),
'tower shield' => array("D43 E47",7),
'great shield' => array("D48 E52",7),
'royal shield' => array("D53 E57",7),
'dark shield' => array("D55 E60 T3",7),
'dragon shield' => array("D59 E64 A1 B1 O1 N1 V1 Y1",7),

// weaves
'firespark' => array("fP0",8,0),
'cantrips' => array("eP0",8,0),
'water spike' => array("wP0",8,0),
'air razor' => array("aP0",8,0),
'ward' => array("sP0",8,0),
'arrows of fire' => array("fP0 aP0",8,0),
'boil' => array("fP0 wP0",8,0),
'fireball' => array("fP0 eP0",8,0),
'compel' => array("fP0 sP0",8,0),
'mudslide' => array("eP0 wP0",8,0),
'upheaval' => array("eP0 aP0",8,0),
'corrupt' => array("eP0 sP0",8,0),
'ice missiles' => array("wP0 aP0",8,0),
'drown' => array("wP0 sP0",8,0),
'crush' => array("aP0 sP0",8,0),
'lava flow' => array("fP0 eP0 wP0",8,0),
'blossoms of fire' => array("fP0 eP0 aP0",8,0),
'deathgate' => array("fP0 eP0 sP0",8,0),
'cyclone' => array("fP0 wP0 aP0",8,0),
'compulsion' => array("fP0 wP0 sP0",8,0),
'lightning strike' => array("fP0 aP0 sP0",8,0),
'hailstorm' => array("eP0 aP0 sP0",8,0),
'tsunami' => array("aP0 wP0 sP0",8,0),
'power shield' => array("eP0 wP0 sP0",8,0),
'rend' => array("wP0 aP0 eP0",8,0),
'fire storm' => array("fP0 eP0 sP0 aP0",8,0),
'earthquake' => array("fP0 eP0 wP0 aP0",8,0),
'decay' => array("fP0 eP0 wP0 sP0",8,0),
'lightning storm' => array("fP0 wP0 aP0 sP0",8,0),
'refresh' => array("eP0 wP0 aP0 sP0",8,0),

// Body Armor
'wool shirt' => array("D1 E3 V2",9),
'leather jerkin' => array("D5 E8 V3",9),
'padded vest' => array("D10 E13 V2 N1",9),
'cadinsor' => array("D14 E18 V3",9),
'leather armor' => array("D19 E23 V2 N1",9),
'chain mail' => array("D25 E28 N3",9),
'splint mail' => array("D29 E33 N4",9),
'cavalry plate' => array("D34 E37 N5",9),
'myrddraal armor' => array("D39 E42 T1 N3 V1 H2",9),
'plate mail' => array("D44 E48 N3",9),
'seanchan armor' => array("D48 E52 N3 P1",9),
'royal armor' => array("D53 E57 G2",9),
'dark shroud' => array("D59 E62 P2 T2 H2",9),

// Leg Armor
'wool pants' => array("D1 E3 Y2",10),
'leather pants' => array("D6 E7 Y3",10),
'padded pants' => array("D11 E12 Y2 N1",10),
'cadinsor pants' => array("D15 E17 Y3",10),
'leather greaves' => array("D20 E22 Y2 N1",10),
'chain greaves' => array("D24 E27 V1 Y3",10),
'splint greaves' => array("D29 E31 N2 Y1 S1",10),
'cavalry greaves' => array("D35 E36 N2 F1",10),
'myrddraal greaves' => array("D39 E41 T3",10),
'plate greaves' => array("D45 E48 N1",10),
'seanchan greaves' => array("D49 E51 S1 P1",10),
'royal greaves' => array("D54 E56 L3",10),
'dark greaves' => array("D58 E61 P3 T3 H3",10), 

// Cloak
'battle cloak' => array("D6 E8 O10",11),
'leather cloak' => array("D6 E8 N10",11),
'hunters cloak' => array("D4 E7 Y9",11),
'fancloth cloak' => array("D4 E7 V9",11),
'white cloak' => array("D5 E7 F6",11),
'silk cloak' => array("D5 E7 S6",11),
'ragged cloak' => array("D4 E7 P7",11),
'myrddraal cloak' => array("D4 E6 T4",11),
'wool cloak' => array("D4 E7 G9",11),
'gleemans cloak' => array("D4 E6 L4",11),
'riding cloak' => array("D4 E7 X3",11),
'shrouded cloak' => array("D4 E7 W3",11),

// ter'angreal
  'figurine' => array("A2 B4",12,0),
  'rod' => array("A2 B4",12,1),
  'plaque' => array("A2 B4",12,2),
  
  'bracelet' => array("A1 B2 D1 E2",13,0),
  'neckwear' => array("D2 E4",13,1),
  'headgear' => array("D2 E4",13,2),
  'ring' => array("D2 E4",13,3),

// food
  'old broth' => array("M2 N1 dC8",19,50),
  'old bread' => array("M4 L1 dC8",19,100),
  'old eggs' => array("M6 O1 dC8",19,150),
  'old hot cakes' => array("M8 G1 dC8",19,200),
  'old fruit' => array("M10 L1 dC8",19,250),
  'old cheese' => array("M12 N1 dC8",19,300),
  'old dried beef' => array("M14 G1 dC8",19,350),
  'old rabbit' => array("M16 O1 dC8",19,400),
  'old stew' => array("M18 L1 dC8",19,450),
  'old gilded fish' => array("M20 G1 dC8",19,500),
  'old spiced ham' => array("M22 N1 dC8",19,550),
  'old roast beef' => array("M24 O1 dC8",19,600),
  
  'plain broth' => array("M2 N2 dC7",19,100),
  'plain bread' => array("M4 L2 dC7",19,200),
  'plain eggs' => array("M6 O2 dC7",19,300),
  'plain hot cakes' => array("M8 G2 dC7",19,400),
  'plain fruit' => array("M10 L2 dC7",19,500),
  'plain cheese' => array("M12 N2 dC7",19,600),
  'plain dried beef' => array("M14 G2 dC7",19,700),
  'plain rabbit' => array("M16 O2 dC7",19,800),
  'plain stew' => array("M18 L2 dC7",19,900),
  'plain gilded fish' => array("M20 G2 dC7",19,1000),
  'plain spiced ham' => array("M22 N2 dC7",19,1100),
  'plain roast beef' => array("M24 O2 dC7",19,1200),
  
  'seasoned broth' => array("M2 N3 dC6",19,150),
  'seasoned bread' => array("M4 L3 dC6",19,300),
  'seasoned eggs' => array("M6 O3 dC6",19,450),
  'seasoned hot cakes' => array("M8 G3 dC6",19,600),
  'seasoned fruit' => array("M10 L3 dC6",19,750),
  'seasoned cheese' => array("M12 N3 dC6",19,900),
  'seasoned dried beef' => array("M14 G3 dC6",19,1050),
  'seasoned rabbit' => array("M16 O3 dC6",19,1200),
  'seasoned stew' => array("M18 L3 dC6",19,1350),
  'seasoned gilded fish' => array("M20 G3 dC6",19,1500),
  'seasoned spiced ham' => array("M22 N3 dC6",19,1650),
  'seasoned roast beef' => array("M24 O3 dC6",19,1800),
  
  'fresh broth' => array("M2 N4 dC5",19,200),
  'fresh bread' => array("M4 L4 dC5",19,400),
  'fresh eggs' => array("M6 O4 dC5",19,600),
  'fresh hot cakes' => array("M8 G4 dC5",19,800),
  'fresh fruit' => array("M10 L4 dC5",19,1000),
  'fresh cheese' => array("M12 N4 dC5",19,1200),
  'fresh dried beef' => array("M14 G4 dC5",19,1400),
  'fresh rabbit' => array("M16 O4 dC5",19,1600),
  'fresh stew' => array("M18 L4 dC5",19,1800),
  'fresh gilded fish' => array("M20 G4 dC5",19,2000),
  'fresh spiced ham' => array("M22 N4 dC5",19,2200),
  'fresh roast beef' => array("M24 O4 dC5",19,2400),

// herb
  'sickly worrynot root' => array("O2 dC2",20,100),
  'sickly boneknit' => array("N2 dC2",20,100),
  'sickly dogwort' => array("G2 dC2",20,200),
  'sickly sheepstongue root' => array("L2 dC2",20,200),
  'sickly silver leaf' => array("Y1 dC2",20,300),
  'sickly goosemint' => array("V1 dC2",20,300),
  'sickly grey fennel' => array("P1 dC2",20,400),
  'sickly crimsonthorn root' => array("T1 dC2",20,400),
  'sickly flatwort' => array("F1 dC2",20,500),
  'sickly greenwort' => array("S1 dC2",20,500),
  'sickly tarchrot leaf' => array("W1 O-5 dC2",20,600),
  'sickly andilay root' => array("X1 O-5 dC2",20,600),

  'common worrynot root' => array("O4 dC3",20,200),
  'common boneknit' => array("N4 dC3",20,200),
  'common dogwort' => array("G4 dC3",20,400),
  'common sheepstongue root' => array("L4 dC3",20,400),
  'common silver leaf' => array("Y2 dC3",20,600),
  'common goosemint' => array("V2 dC3",20,600),
  'common grey fennel' => array("P2 dC3",20,800),
  'common crimsonthorn root' => array("T2 dC3",20,800),
  'common flatwort' => array("F2 dC3",20,1000),
  'common greenwort' => array("S2 dC3",20,1000),
  'common tarchrot leaf' => array("W1 dC3",20,1200),
  'common andilay root' => array("X1 dC3",20,1200),
  
  'healthy worrynot root' => array("O6 dC4",20,300),
  'healthy boneknit' => array("N6 dC4",20,300),
  'healthy dogwort' => array("G6 dC4",20,600),
  'healthy sheepstongue root' => array("L6 dC4",20,600),
  'healthy silver leaf' => array("Y3 dC4",20,900),
  'healthy goosemint' => array("V3 dC4",20,900),
  'healthy grey fennel' => array("P3 dC4",20,1200),
  'healthy crimsonthorn root' => array("T3 dC4",20,1200),
  'healthy flatwort' => array("F3 dC4",20,1500),
  'healthy greenwort' => array("S3 dC4",20,1500),
  'healthy tarchrot leaf' => array("W2 O-5 dC4",20,1800),
  'healthy andilay root' => array("X2 O-5 dC4",20,1800),

  'perfect worrynot root' => array("O8 dC5",20,400),
  'perfect boneknit' => array("N8 dC5",20,400),
  'perfect dogwort' => array("G8 dC5",20,800),
  'perfect sheepstongue root' => array("L8 dC5",20,800),
  'perfect silver leaf' => array("Y4 dC5",20,1200),
  'perfect goosemint' => array("V4 dC5",20,1200),
  'perfect grey fennel' => array("P4 dC5",20,1600),
  'perfect crimsonthorn root' => array("T4 dC5",20,1600),
  'perfect flatwort' => array("F4 dC5",20,2000),
  'perfect greenwort' => array("S4 dC5",20,2000),
  'perfect tarchrot leaf' => array("W2 dC5",20,2400),
  'perfect andilay root' => array("X2 dC5",20,2400),
  
// drink
  'cheap oosquai' => array("O3 V-1 dC2",21,100),
  'cheap ale' => array("N3 Y-1 dC2",21,100),
  'cheap cider' => array("G3 N-2 dC2",21,200),
  'cheap apple brandy' => array("L3 O-2 dC2",21,200),
  'cheap wine' => array("Y2 V-1 dC2",21,300),
  'cheap willowbark tea' => array("V2 Y-1 dC2",21,300),
  'cheap gara milk' => array("P2 O-1 dC2",21,400),
  'cheap blood wine' => array("T2 N-1 dC2",21,400),
  'cheap chainleaf tea' => array("F2 V-3 dC2",21,500),
  'cheap brandy' => array("S2 Y-3 dC2",21,500),
  'cheap bluespine tea' => array("W1 N-4 dC2",21,600),
  'cheap kaf' => array("X1 O-4 dC2",21,600),
  
  'average oosquai' => array("O6 V-2 dC3",21,200),
  'average ale' => array("N6 Y-2 dC3",21,200),
  'average cider' => array("G6 N-4 dC3",21,400),
  'average apple brandy' => array("L6 O-4 dC3",21,400),
  'average wine' => array("Y4 V-2 dC3",21,600),
  'average willowbark tea' => array("V4 Y-2 dC3",21,600),
  'average gara milk' => array("P4 O-2 dC3",21,800),
  'average blood wine' => array("T4 N-2 dC3",21,800),
  'average chainleaf tea' => array("F4 V-6 dC3",21,1000),
  'average brandy' => array("S4 Y-6 dC3",21,1000),
  'average bluespine tea' => array("W2 N-8 dC3",21,1200),
  'average kaf' => array("X2 O-8 dC3",21,1200),
  
  'fine oosquai' => array("O9 V-3 dC4",21,300),
  'fine ale' => array("N9 Y-3 dC4",21,300),
  'fine cider' => array("G9 N-6 dC4",21,600),
  'fine apple brandy' => array("L9 O-6 dC4",21,600),
  'fine wine' => array("Y6 V-3 dC4",21,900),
  'fine willowbark tea' => array("V6 Y-3 dC4",21,900),
  'fine gara milk' => array("P6 O-3 dC4",21,1200),
  'fine blood wine' => array("T6 N-3 dC4",21,1200),
  'fine chainleaf tea' => array("F6 V-9 dC4",21,1500),
  'fine brandy' => array("S6 Y-9 dC4",21,1500),
  'fine bluespine tea' => array("W3 N-12 dC4",21,1800),
  'fine kaf' => array("X3 O-12 dC4",21,1800),
  
  'quality oosquai' => array("O12 V-4 dC5",21,400),
  'quality ale' => array("N12 Y-4 dC5",21,400),
  'quality cider' => array("G12 N-8 dC5",21,800),
  'quality apple brandy' => array("L12 O-8 dC5",21,800),
  'quality wine' => array("Y8 V-4 dC5",21,1200),
  'quality willowbark tea' => array("V8 Y-4 dC5",21,1200),
  'quality gara milk' => array("P8 O-4 dC5",21,1600),
  'quality blood wine' => array("T8 N-4 dC5",21,1600),
  'quality chainleaf tea' => array("F8 V-12 dC5",21,2000),
  'quality brandy' => array("S8 Y-12 dC5",21,2000),
  'quality bluespine tea' => array("W4 N-16 dC5",21,2400),
  'quality kaf' => array("X4 O-16 dC5",21,2400),
  
  'prison seal' => array("Q1",0),
  'broken seal' => array("Q1",0),
  'horn of valere' => array("J1",-1),
);
// TALISMAN MUST BE LAST ITEM FOR SHOP CODE TO WORK CORRECTLY

$item_ix = array(

// taint
'tarnished' =>"T2",
'tainted' => "T6",
'corrupt' => "T10",
'of the blight' => "T4",
'of shadar logoth' => "T8",
'of shayol ghul' => "T12",

// poison
'venomous' => "P4",
'poisonous' => "P8",
'toxic' => "P12",
'of vermin' => "P2",
'of serpents' => "P6",
'of the adder' => "P10",

// poison and taint
'stained' => "P2 T3",
'foul' => "P3 T5",
'deadly' => "P4 T7",
'of disease' => "P3 T2",
'of rot' => "P5 T3",
'of plague' => "P7 T4",

// Luck
'lucky' => "L2",
'favorable' => "L6",
'gamblers' => "L10",
'of chance' => "L4",
'of fortune' => "L8",
'of taveren' => "L12",

// life gain
'nurturing' => "G4",
'healing' => "G8",
'restoring' => "G12",
'of the leaf' => "G2",
'of the green man' => "G6",
'of avendesora' => "G10",

// speed
'small' => "X1 Y-2",
'fast' => "X2",
'rapid' => "X3 Y-2",
'of steeds' => "X1",
'of razors' => "X2 Y-2",
'of foxes' => "X3",

// wound
'cruel' => "W1 V-2",
'sadistic' => "W2",
'crippling' => "W3 V-2",
'of the ruthless' => "W1",
'of the ferocious' => "W2 V-2",
'of snakes' => "W3",

// stun
'large' => "S2",
'massive' => "S4",
'staggering' => "S6",
'of the pack' => "S1",
'of brawlers' => "S3",
'of incapacitation' => "S5",

// first strike
'ready' => "F2",
'quick' => "F4",
'swift' => "F6",
'of surprise' => "F1",
'of ambush' => "F3",
'of lightning' => "F5",

// accuracy
'trusty' => "Y2",
'faithful' => "Y6",
'legendary' => "Y10",
'of reliability' => "Y4",
'of precision' => "Y8",
'of marksmen' => "Y12",

// dodge
'deflecting' => "V4",
'stealthy' => "V8",
'warded' => "V12",
'of shadows' => "V2",
'of evasion' => "V6",
'of night' => "V10",

// attack
'fine' => "A3 B5",
'exceptional' => "A7 B9",
'exquisite' => "A11 B13",
'of thugs' => "A1 B3",
'of strongarms' => "A5 B7",
'of the mighty' => "A9 B11",

'warriors' => "O3",
'noble' => "O9",
'lords' => "O15",
'of commanders' => "O6",
'of chiefs' => "O12",
'of kings' => "O18",

// defense
'forged' => "D1 E3",
'master smithed' => "D5 E7",
'power wrought' => "D9 E11",
'of toughs' => "D3 E5",
'of protection' => "D7 E9",
'of the red hand' => "D11 E13",

'strong' => "N6",
'sturdy' => "N12",
'solid' => "N18",
'of the stedding' => "N3",
'of the stone' => "N9",
'of queens' => "N15",

// weapon specific
'blademasters' => "sC-5",
'headsmans' => "xC-5",
'dashain' => "pC-5",
'archers' => "oC-5",
'ogiers' => "bC-5",
'bloodknives' => "kC-5",
'blacksmiths' => "aC-5",
'horsemans' => "lC-5",

'of younglings' => "sO5",
'of the forest' => "xO5",
'of the waste' => "pO5",
'of birgitte' => "oO5",
'of the watch' => "bO5",
'of footpads' => "kO5",
'of the wall' => "aO5",
'of cavalry' => "lO5",

// general
'killers' => "A2 B4 P2",
'slayers' => "A4 B6 P4",
'forsaken' => "A6 B8 P6",
'of darkness' => "A2 B4 T2",
'of ashes' => "A4 B6 T4",
'of pestilence' => "A6 B8 T6",

'aggressive' => "O10 N-10",
'passive' => "O-10 N10",
'bloody' => "G10 H5",
'of veterans' => "O4 N4",
'of the borderlands' => "O8 N8",
'of the red eagle' => "O12 N12",

'heroic' => "N12 G4 F4",
'villianous' => "O12 P4 S4",
'mercenaries' => "L5 Y10 W2 N-10 V-3",
'of tinkers' => "G10 V5 X2 O-10 Y-3",
'of tar valon' => "S5 G6 Y3",
'of cutpurses' => "F5 L5 V3",

'jeweled' => "L18 N-6 O-6",
'scouts' => "X3 V3 N6",
'assassins' => "W3 Y3 O6",
'of madness' => "H10 O30",
'of the light' => "D5 E10 N10 G8",
'of the shadow' => "A5 B10 O10 T5",

// NPCs
"draghkars" => "S2 T2 W1",
"bears" => "O4 Y2 A2 B4",
"capars" => "V2 N4 D2 E4",
"garas" => "P2 S2 O4",
"lions" => "F2 O4 V2",
"guarding" => "N4 G2 D2 E4",
"bonded" => "Y2 V2 S2",
"dreadlords" => "T2 O4 W1",
"kinslayers" => "T4 O8 H2",
"bandits" => "V2 N4 P2",
"darkfriends" => "V2 O4 N4",
"dragonsworns" => "F2 O4 P2",
"sea folk" => "S2 O4 Y2",
"hawkwings" => "O8 N4",
"soldiers" => "O4 N4 Y2",
"guardians" => "Y2 O4 W1",
"servants" => "N4 V2 X1",
"fades" => "S2 V2 T2",
"blacklances" => "P4 X1",

"of wolfkin" => "Y4 O8 H2",
"of oosquai" => "O12 H2 A2 B4",
"of damane" => "F2 O4 Y2",
"of sredit" => "S4 N4",
"of torm" => "V4 N4",
"of lopar" => "O4 N4 G2",
"of grolm" => "G2 O8",
"of toraken" => "Y2 O4 X1",
"of corlm" => "Y4 O4",
"of raken" => "V4 X1 speed",
"of the black ajah" => "N4 T2 X1",
"of the children" => "N8 O4",
"of rhuidean" => "V2 Y2 F2",
"of the wolf" => "N4 Y2 F2",
"of the gholam" => "V4 W1",
"of graymen" => "V2 P2 T2",
"of jumara" => "G2 T2 A2 B4",
"of darkhounds" => "P2 T2 N4",
"of shadowspawn" => "O8 T2",
);

$item_list = array (
  '1' => array ('short sword','sword','trolloc blade','long sword','tulwar','bastard sword',
                'claymore','broad sword','battle sword','flamberge','winged sword',
                'dark blade','heron mark blade' ),
  '2' => array ('hatchet','hand axe','cleaver','trolloc axe','half moon blade','combat axe',
                'balanced axe','double bladed axe','battle axe','bone splitter','executioner',
                'spiked axe','great axe'),
  '3' => array ('sharpened stick','iron spear','short spear','glaive','long spear','pike',
                'javelin','lance','septum','trident','black lance','combat spear','ashandarei'),
  '4' => array ('sling','short bow','light crossbow','long bow','horn bow','battle bow',
                'heavy crossbow','hunters bow','long battle bow','warriors bow','composite crossbow',
                'two rivers long bow','silver bow'),
  '5' => array ('cudgel','short staff','spiked club','mallet','quarter staff','mace','flail',
                'morning star','maul','war hammer','battle staff','spiked maul','battle hammer'),
  '6' => array ('stone knife','bone knife','steel bladed knife','throwing knives','belt knife',
                'long knife','dagger','long throwing knives','battle dagger','kriss','stilletto',
                'balanced throwing knives','ruby dagger'),
  '7' => array ('wooden shield','trolloc shield','buckler','kite shield','tairen shield','defenders shield',
                'amador shield','spiked buckler','tower shield','great shield','royal shield',
                'dark shield','dragon shield'),
  '8' => array ('firespark','cantrips','water spike','air razor','ward',
                'arrows of fire','boil','fireball','compel','mudslide','upheaval','corrupt','ice missiles','drown','crush','lava flow',
                'blossoms of fire','deathgate','cyclone','compulsion','lightning strike','hailstorm','tsunami','power shield','rend',
                'fire storm','earthquake','decay','lightning storm','refresh'),
  '9' => array ('wool shirt','leather jerkin','padded vest','cadinsor','leather armor','chain mail','splint mail',
                'cavalry plate','myrddraal armor','plate mail','seanchan armor','royal armor','dark shroud'),
  '10' => array ('wool pants','leather pants','padded pants','cadinsor pants','leather greaves','chain greaves','splint greaves',
                 'cavalry greaves','myrddraal greaves','plate greaves','seanchan greaves','royal greaves','dark greaves'),
  '11' => array ('battle cloak','leather cloak','hunters cloak','fancloth cloak','white cloak','silk cloak',
                 'ragged cloak','myrddraal cloak','wool cloak','gleemans cloak','riding cloak','shrouded cloak'),
  '12' => array ('figurine', 'rod', 'plaque'),
  '13' => array ('bracelet', 'neckwear', 'headgear', 'ring'),
  '19' => array ('broth','bread','eggs','hot cakes','fruit','cheese','dried beef','rabbit',
                 'stew','gilded fish','spiced ham','roast beef'),
  '20' => array ('worrynot root','boneknit','dogwort','sheepstongue root','silver leaf','goosemint',
                 'grey fennel','crimsonthorn root','flatwort','greenwort','tarchrot leaf','andilay root'),
  '21' => array ('oosquai','ale','cider','apple brandy','wine','willowbark tea','gara milk','blood wine',
                 'chainleaf tea','brandy','bluespine tea','kaf'),
);

$cloak_list = array (
'1' =>  array ('white cloak',      'leather cloak',   'fancloth cloak',  'wool cloak'),
'2' =>  array ('riding cloak',     'shrouded cloak',  'silk cloak',      'leather cloak'),
'3' =>  array ('silk cloak',       'white cloak',     'gleemans cloak',  'leather cloak'),
'4' =>  array ('gleemans cloak',   'silk cloak',      'leather cloak',   'riding cloak'),
'5' =>  array ('leather cloak',    'ragged cloak',    'riding cloak',    'white cloak'),
'6' =>  array ('leather cloak',    'gleemans cloak',  'hunters cloak',   'shrouded cloak'),
'7' =>  array ('myrddraal cloak',  'white cloak',     'silk cloak',      'hunters cloak'),
'8' =>  array ('hunters cloak',    'ragged cloak',    'myrddraal cloak', 'shrouded cloak'),
'9' =>  array ('battle cloak',     'myrddraal cloak', 'wool cloak',      'riding cloak'),
'10' =>  array ('ragged cloak',    'fancloth cloak',  'myrddraal cloak', 'hunters cloak'),
'11' =>  array ('wool cloak',      'fancloth cloak',  'leather cloak',   'silk cloak'),
'12' =>  array ('riding cloak',    'battle cloak',    'fancloth cloak',  'gleemans cloak'),
'13' =>  array ('myrddraal cloak', 'shrouded cloak',  'ragged cloak',    'battle cloak'),
'14' =>  array ('battle cloak',    'hunters cloak',   'shrouded cloak',  'ragged cloak'),
'15' =>  array ('hunters cloak',   'leather cloak',   'wool cloak',      'battle cloak'),
'16' =>  array ('shrouded cloak',  'gleemans cloak',  'white cloak',     'fancloth cloak'),
'17' =>  array ('silk cloak',      'myrddraal cloak', 'battle cloak',    'fancloth cloak'),
'18' =>  array ('shrouded cloak',  'wool cloak',      'hunters cloak',   'silk cloak'),
'19' =>  array ('fancloth cloak',  'silk cloak',      'ragged cloak',    'myrddraal cloak'),
'20' =>  array ('gleemans cloak',  'riding cloak',    'battle cloak',    'ragged cloak'),
'21' =>  array ('wool cloak',      'hunters cloak',   'gleemans cloak',  'white cloak'),
'22' =>  array ('fancloth cloak',  'riding cloak',    'white cloak',     'wool cloak'),
'23' =>  array ('ragged cloak',    'wool cloak',      'riding cloak',    'gleemans cloak'),
'24' =>  array ('white cloak',     'battle cloak',    'shrouded cloak',  'myrddraal cloak'),
);

$tali_list = array (
'1' => array ('blademasters', 'headsmans', 'dashain', 'archers', 'of the watch', 'of footpads', 'of the wall', 'of cavalry'),
'2' => array ('ogiers', 'bloodknives', 'blacksmiths', 'horsemans', 'of younglings', 'of the forest', 'of the waste', 'of birgitte'),
'3' => array ('warriors', 'trusty', 'small', 'cruel', 'of the stedding', 'of surprise', 'of the pack', 'of shadows'),
'4' => array ('forged', 'lucky', 'ready', 'large', 'of the leaf', 'of thugs', 'of steeds', 'of the ruthless'),
'5' => array ('tarnished', 'strong', 'deflecting', 'nurturing', 'of vermin', 'of commanders', 'of reliability', 'of chance'),
'6' => array ('aggressive', 'passive', 'venomous', 'fine', 'of ambush', 'of brawlers', 'of toughs', 'of the blight'),
'7' => array ('quick', 'massive', 'stained', 'killers', 'of razors', 'of the ferocious', 'of disease', 'of darkness'),
'8' => array ('bloody', 'noble', 'master smithed', 'faithful', 'of veterans', 'of the stone', 'of strongarms', 'of evasion'),
'9' => array ('favorable', 'tainted', 'fast', 'sadistic', 'of lightning', 'of incapacitation', 'of serpents', 'of the green man'),
'10' => array ("kinslayers", "servants", "bonded", "fades", "of graymen", "of rhuidean", "of jumara", "of wolfkin"),
'11' => array ("bandits", "dragonsworns", "dreadlords", "guardians", "of damane", "of darkhounds", "of the wolf", "of sredit"),
'12' => array ("soldiers", "garas", "sea folk", "darkfriends", "of torm", "of corlm", "of lopar", "of grolm"),
'13' => array ("bears", "capars", "lions", "guarding", "of shadowspawn", "of toraken", "of the black ajah", "of oosquai"),
'14' => array ("draghkars", "blacklances", "hawkwings", 'sturdy', "of raken", "of the gholam", "of the children", 'of chiefs'),
'15' => array ('swift', 'staggering', 'stealthy', 'poisonous', 'of precision', 'of fortune', 'of shadar logoth', 'of madness'),
'16' => array ('slayers', 'foul', 'healing', 'exceptional', 'of protection', 'of ashes', 'of rot', 'of the borderlands'),
'17' => array ('rapid', 'crippling', 'legendary', 'lords', 'of queens', 'of night', 'of the adder', 'of avendesora'),
'18' => array ('deadly', 'power wrought', 'gamblers', 'corrupt', 'of the mighty', 'of foxes', 'of snakes', 'of plague'),
'19' => array ('jeweled', 'solid', 'exquisite', 'warded', 'of kings', 'of the red hand', 'of marksmen', 'of shayol ghul'),
'20' => array ('toxic', 'restoring', 'forsaken', 'mercenaries', 'of taveren', 'of pestilence', 'of tinkers', 'of the red eagle'),
'21' => array ('heroic', 'villianous', 'scouts', 'assassins', 'of tar valon', 'of cutpurses', 'of the shadow', 'of the light'),
);

// COMPUTE WEAVE STATS
function weaveStats ($sname, $skills)
{
  $c_skills = cparse($skills,0);
  
  if (!$c_skills['fP']) $c_skills['fP']=0;
  if (!$c_skills['wP']) $c_skills['wP']=0;
  if (!$c_skills['sP']) $c_skills['sP']=0;
  if (!$c_skills['aP']) $c_skills['aP']=0;
  if (!$c_skills['eP']) $c_skills['eP']=0;
  
  $fp = $c_skills['fP'];
  $wp = $c_skills['wP'];
  $sp = $c_skills['sP'];
  $ap = $c_skills['aP'];
  $ep = $c_skills['eP'];

  switch($sname)
  {
    case 'firespark':
    $stats = calcWeaveParts("O",$fp);
    break;
    case 'cantrips':
    $stats = calcWeaveParts("N",$ep);
    break;
    case 'water spike':
    $stats = calcWeaveParts("V",$wp);
    break;
    case 'air razor':
    $stats = calcWeaveParts("Y",$ap);
    break;
    case 'ward':
    $stats = calcWeaveParts("G",$sp,1);
    break;

    case 'arrows of fire':
    $stats = calcWeaveParts("F",$fp)." ".calcWeaveParts("Y",$ap);
    break;
    case 'boil':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("V",$wp);
    break;
    case 'fireball':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("N",$ep);
    break;
    case 'compel':
    $stats = calcWeaveParts("S",$fp,1)." ".calcWeaveParts("P",$sp,1);
    break;
    case 'mudslide':
    $stats = calcWeaveParts("V",$wp,1)." ".calcWeaveParts("N",$ep,1);
    break;
    case 'upheaval':
    $stats = calcWeaveParts("T",$ap)." ".calcWeaveParts("F",$ep);
    break;
    case 'corrupt':
    $stats = calcWeaveParts("L",$sp)." ".calcWeaveParts("T",$ep);
    break;
    case 'ice missiles':
    $stats = calcWeaveParts("X",$wp)." ".calcWeaveParts("Y",$ap);
    break;
    case 'drown':
    $stats = calcWeaveParts("P",$wp)." ".calcWeaveParts("G",$sp);
    break;
    case 'crush':
    $stats = calcWeaveParts("G",$sp,1)." ".calcWeaveParts("W",$ap,1);
    break;
    
    case 'lava flow':
    $stats = calcWeaveParts("S",$fp)." ".calcWeaveParts("V",$wp)." ".calcWeaveParts("L",$ep);
    break;
    case 'blossoms of fire':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("W",$ap)." ".calcWeaveParts("N",$ep);
    break;
    case 'deathgate':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("G",$sp)." ".calcWeaveParts("F",$ep);
    break;
    case 'cyclone':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("V",$wp)." ".calcWeaveParts("Y",$ap);
    break;
    case 'compulsion':
    $stats = calcWeaveParts("S",$fp,1)." ".calcWeaveParts("T",$wp,1)." ".calcWeaveParts("P",$sp,1);
    break;
    case 'lightning strike':
    $stats = calcWeaveParts("F",$fp)." ".calcWeaveParts("L",$sp)." ".calcWeaveParts("X",$ap);
    break;
    case 'hailstorm':
    $stats = calcWeaveParts("X",$sp,1)." ".calcWeaveParts("T",$ap,1)." ".calcWeaveParts("N",$ep,1);
    break;
    case 'tsunami':
    $stats = calcWeaveParts("S",$wp)." ".calcWeaveParts("G",$sp)." ".calcWeaveParts("Y",$ap);
    break;
    case 'power shield':
    $stats = calcWeaveParts("V",$wp,1)." ".calcWeaveParts("G",$sp,1)." ".calcWeaveParts("N",$ep,1);
    break;
    case 'rend':
    $stats = calcWeaveParts("W",$wp)." ".calcWeaveParts("Y",$ap)." ".calcWeaveParts("F",$ep);
    break;
 
    case 'fire storm':
    $stats = calcWeaveParts("O",$fp)." ".calcWeaveParts("Y",$sp)." ".calcWeaveParts("X",$ap)." ".calcWeaveParts("F",$ep);
    break;
    case 'earthquake':
    $stats = calcWeaveParts("F",$fp,1)." ".calcWeaveParts("S",$wp,1)." ".calcWeaveParts("L",$ap)." ".calcWeaveParts("N",$ep);
    break;
    case 'decay':
    $stats = calcWeaveParts("P",$fp)." ".calcWeaveParts("W",$wp)." ".calcWeaveParts("T",$sp)." ".calcWeaveParts("S",$ep);
    break;
    case 'lightning storm':
    $stats = calcWeaveParts("W",$fp)." ".calcWeaveParts("L",$wp)." ".calcWeaveParts("V",$sp)." ".calcWeaveParts("Y",$ap);
    break;
    case 'refresh':
    $stats = calcWeaveParts("V",$wp,1)." ".calcWeaveParts("G",$sp,1)." ".calcWeaveParts("L",$ap,1)." ".calcWeaveParts("X",$ep,1);
    break;
    default:
    $stats = "A1 B1";
    break;
  }

  return $stats;
}

// COMPUTE WEAVE STATS
function getWeaves ($item_list,$item_base,$skills)
{
  $c_skills = cparse($skills,0);
  $weaves[0] = '';
  $wcount = 0;
  
  for ($i=0; $i< count($item_list['8']); $i++)
  {
    $sname= $item_list['8'][$i];
    $powerlist = str_replace("0","",$item_base[$sname][0]);
    $powers = explode(' ',$powerlist);
    $numpow = count($powers);
    
    $pcount = 0;
    for ($p = 0; $p < $numpow; $p++)
    {
      $pcount += $c_skills[$powers[$p]];
    }
    
    if ($pcount >= $numpow*$numpow)
    {
      $weaves[$wcount][0] = $sname;
      $weaves[$wcount][1] = weaveStats($sname,$skills);
      $weaves[$wcount][2] = weaveStats($sname,"fP1");
      $weaves[$wcount][3] = weaveStats($sname,"wP1");
      $weaves[$wcount][4] = weaveStats($sname,"sP1");
      $weaves[$wcount][5] = weaveStats($sname,"aP1");
      $weaves[$wcount][6] = weaveStats($sname,"eP1");
      $wcount++;
    }
  }    

  return $weaves;
}

function calcWeaveParts($stat, $p, $def=0)
{
  $stats='';
  $ad=array("A","B");
  if ($def) $ad=array("D","E");
  switch($stat)
  {
    case 'O':
    $stats = $ad[0].(2*$p)." ".$ad[1].(3*$p)." O".(2*$p);
    break;
    case 'N':
    $stats = $ad[0].(2*$p)." ".$ad[1].(3*$p)." N".(2*$p);
    break;
    case 'Y':
    $stats = $ad[0].(2*$p)." ".$ad[1].(3*$p)." Y".($p);
    break;
    case 'V':
    $stats = $ad[0].(2*$p)." ".$ad[1].(3*$p)." V".($p);
    break;
    case 'G':
    $stats = $ad[0].(2*$p)." ".$ad[1].(3*$p)." G".($p);
    break;
    case 'L':
    $stats = $ad[0].(1*$p)." ".$ad[1].(3*$p)." L".($p);
    break;
    case 'P':
    $stats = $ad[0].(1*$p)." ".$ad[1].(3*$p)." P".($p);
    break;
    case 'T':
    $stats = $ad[0].(1*$p)." ".$ad[1].(3*$p)." T".($p);
    break;
    case 'F':
    $stats = $ad[0].(1*$p)." ".$ad[1].(3*$p)." F".($p);
    break;
    case 'S':
    $stats = $ad[0].(1*$p)." ".$ad[1].(3*$p)." S".($p);
    break;
    case 'X':
    $stats = $ad[0].(1*$p)." ".$ad[1].(2*$p)." X".(round(0.5*$p));
    break;
    case 'W':
    $stats = $ad[0].(1*$p)." ".$ad[1].(2*$p)." W".(round(0.5*$p));
    break;
  }
  return $stats;
}


// ITEM NAME GENERATOR FUNCTION
function iname($item)
{
  $name = ucwords($item[prefix]);
  if ($name) $name .= " ";
  $name .= ucwords($item[base]);
  if ($item[suffix]) $name .= " ".str_replace("Of","of",ucwords($item[suffix]));
  return $name;
}

// ITEM NAME GENERATOR FUNCTION
function iname_list($item,$itmlist)
{
  $name = ucwords($itmlist[$item][1]);
  if ($name) $name .= " ";
  $name .= ucwords($itmlist[$item][0]);
  if ($itmlist[$item][2]) $name .= " ".str_replace("Of","of",ucwords($itmlist[$item][2]));
  return $name;
}

function getConditionMod($type, $cond)
{
  $string = "";
  if ($type >= 7)
    $string .= " N-".floor((100-$cond)/3);
  else 
    $string .= " O-".floor((100-$cond)/3);
  
  return $string;
}

function getTerMod($ter_bonuses,$type,$prefix,$suffix,$base="")
{
  $string = '';
  if ($type < 12)
  {
    if ($prefix != '')
      $string .= $ter_bonuses[$type][0];
    if ($suffix != '')
      $string .= $ter_bonuses[$type][1];
  }
  elseif ($type < 14)
  {
    if ($prefix != '')
      $string .= $ter_bonuses[$type][$base][0];
    if ($suffix != '')
      $string .= $ter_bonuses[$type][$base][1];    
  }
  
  return $string;
}

function iparse($itm, $item_base, $item_ix, $ter_bonuses)
{
  $string = $item_base[$itm[base]][0]." ".$item_ix[$itm[prefix]]." ".$item_ix[$itm[suffix]];
  $string .= getConditionMod($itm[type], $itm[cond]);
  $string .= getTerMod($ter_bonuses,$itm[type],$itm[prefix],$itm[suffix],$item_base[$itm[base]][2]);

  return $string;
}

// ITEM STRING REDUCER FUNCTION
function itp($string,$type,$cond=100)
{
  $string .= getConditionMod($type, $cond);
  
  // Handle item parsing
  return $string;
}

// COMBINE LIKE BATTLE COMMANDS
function clbc($string)
{
  $array = cparse($string,0);
  $return_string = "";
  foreach ($array as $x => $y)
  {
    $return_string .= $x.$y." ";
  }
  return $return_string;
}

// ITEM VALUE PRODUCER
function item_val($string)
{
  $worth = round(pow(lvl_req($string,100),2)/3);

  if ($worth < 0) $worth = 0;
  return $worth;
}

function lvl_req($string, $mod) 
{
  $char_stat = cparse($string,0);
  $num = (($char_stat['A']+$char_stat['B']+$char_stat['D']+$char_stat['E']+$char_stat['X']+$char_stat['W'])*3 + ($char_stat['P'])*2 + 
            ($char_stat['T']+$char_stat['L']+$char_stat['C'])*5 + ($char_stat['O']+$char_stat['N'])/3 +
            ($char_stat['F']+$char_stat['S']+$char_stat['V']+$char_stat['Y']+$char_stat['G']-$char_stat['H']));
  $per = ((($char_stat['X']+$char_stat['W'])*6 + ($char_stat['F']+$char_stat['S'])*3 + ($char_stat['P']+$char_stat['O']+$char_stat['N']-$char_stat['H'])+
            ($char_stat['V']+$char_stat['Y']+$char_stat['G'])*2)/100+1);
  $lvl = $num*$per;
  $lvl = round(($lvl * $mod)/100);
  return $lvl < 1 ? 1 : $lvl;
}

function getIstats($myitm, $skills)
{
  $estats="";
  if ($myitm != "")
  {
    if ($myitm[type] == 8)
      $estats = weaveStats($myitm[base],$skills);
    else
      $estats = $myitm[stats]." ".getConditionMod($myitm[type],$myitm[cond]);
  }
  
  return $estats;
}

function getstats($myitm1,$myitm2,$myitm3,$myitm4,$myitm5,$skills='')
{
  $estats = "";
  $estats[0] = "";
  $estats[1] = "";
  $estats[2] = "";
  $estats[3] = "";
  $estats[4] = "";
  $estats[5] = "";
  
  $estats[1] = getIstats($myitm1,$skills);
  $estats[0].= " ".$estats[1];
  $estats[2] = getIstats($myitm2,$skills);
  $estats[0].= " ".$estats[2];
  $estats[3] = getIstats($myitm3,$skills);
  $estats[0].= " ".$estats[3];
  $estats[4] = getIstats($myitm4,$skills);
  $estats[0].= " ".$estats[4];
  $estats[5] = getIstats($myitm5,$skills);
  $estats[0].= " ".$estats[5];

  
  return $estats;
}

//function isEquipped ($itmlist,$item_base,$type)
//{
//  $rval = 0;
//  $y=0;
//  while ($y < count($itmlist) && $rval==0)
//  {
//    if ($itmlist[$y][3] > 0 && $item_base[$itmlist[$y][0]][1]==$type)
//    {
//      $rval = 1;
//    }
//    $y++;
//  }
//  
//  return $rval;
//}