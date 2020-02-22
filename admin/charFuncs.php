<?php
// CHAR FUNCTIONS

$char_class = array(
'0' => "Supreme Being",
'1' => "Armsman",
'2' => "Wanderer",
'3' => "Outdoorsman",
'4' => "Healer",
'5' => "Darkfriend",

'10' => "Commander",
'11' => "Redarm",
'12' => "Warder",
'13' => "Mercenary",
'14' => "Gleeman",
'15' => "Gambler",
'16' => "Hunter",
'17' => "Bandit",
'18' => "Thief Catcher",
'19' => "Fanatic",
'20' => "Dragonsworn",
'21' => "Assassin",

'31' => "Whitecloak",
'32' => "Wolfbrother",
'33' => "Noble",

'34' => "Aes Sedai",
'35' => "Asha'man",
'36' => "Bargainer",
'37' => "Blight Guard",
'38' => "Duelist",
'39' => "Militia",
'40' => "Smuggler",
'41' => "Wise One",
'42' => "Chief",
'43' => "Sailmistress",
'44' => "Cargomaster",
'45' => "Treesinger",
'46' => "Elder",
'47' => "Deathwatch Guard",
'48' => "Seeker",
'49' => "Companion",
'50' => "Legion",
'51' => "Queen's Guard",
'52' => "Defender",

'101' => "Sword Focus",
'102' => "Axe Focus",
'103' => "Spear Focus",
'104' => "Bow Focus",
'105' => "Bludgeon Focus",
'106' => "Knife Focus",
'107' => "Shield Focus",
'108' => "Weave Focus",
'109' => "Body Armor Focus",
'110' => "Leg Armor Focus",
);

$restricted_classes = array(0,1,2,3,4,5,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,45,46,47,48,49,50,51,52,108);
$chan_classes = array(34,35);
$no_chan_classes = array(31,32);
$male_classes = array(35,42,44);
$female_classes = array(34,41,43);


$nat_classes = array(
'0' => array(1,2,3,4,5,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,45,46,47,48,49,50,51,52),
'1' => array(37,41,42),
'2' => array(31,32,38,39),
'3' => array(31,32,38,40),
'4' => array(31,32,39,51),
'5' => array(31,32,37,39),
'6' => array(36,43,44),
'7' => array(31,32,33,36),
'8' => array(31,32,36,40),
'9' => array(31,32,39,50),
'10' => array(31,32,40,49),
'11' => array(31,32,36,37),
'12' => array(31,32,36,38),
'13' => array(45,46,47),
'14' => array(31,32,37,40),
'15' => array(32,47,48),
'16' => array(31,32,37,38),
'17' => array(31,32,38,52),
'18' => array(31,32,39,40),
);

$nationalities = array(
'0' => "All and None",
'1' => "Aiel",
'2' => "Altaran",
'3' => "Amadician",
'4' => "Andoran",
'5' => "Arafellin",
'6' => "Atha'an Miere",
'7' => "Cairhienin",
'8' => "Domani",
'9' => "Ghealdanin",
'10' => "Illianer",
'11' => "Kandori",
'12' => "Murandian",
'13' => "Ogier",
'14' => "Saldaean",
'15' => "Seanchan",
'16' => "Shienaran",
'17' => "Tairen",
'18' => "Taraboner",
);

// "nationality" => array("description", "favored terrain", "favored opponents", "unfavored opponents", "special", "restrictions"),
$nat_info = array(
"Aiel" => array("Aiel are skilled and fierce warriors from the Wastes, who follow the ways of Ji'e'toh: Honor and Obligation.", "Wastelands", "Military", "Channelers"," Can become a Blight Guard, Chief (Male) or Wise One (Female)","Cannot use swords. Cannot become a Noble, Wolfkin, or Whitecloak."),
"Altaran" => array("Altarans are polite and easygoing, but follow a strict code of conduct and etiquette that can bewilder those unfamiliar with it. Offend them and they will be quick to pull a knife on you, especially women.", "Hills", "Ruffians", "Exotic Animals", "Can become a Duelist or Militia", ""),
"Amadician" => array("While the Children of the Light don't officially rule, their influence on Amadician's is unmistakable. Women who are healers must be careful not to heal too well, lest they be charged with being Aes Sedai.", "Plains", "Channelers", "Military", "Can become a Duelist or Smuggler", ""),
"Andoran" => array("Andorans are hard-working people who are open, friendly, and willing to help out even strangers. But betray their friendliness or wrong them at your on risk!",'Forests',  "Military","Exotics",  "Can become a Militia or Queen's Guard",""),
"Arafellin" => array("Arafellins are a fiery people with strange ideas about honor and debts. It is not uncommon for an Arafellin to ask for punishment as a means of restoring their honor.", "Mountains", "Shadowspawn", "Military", "Can become a Blight Guard or Militia", ""),
"Atha'an Miere" => array("The Atha'an Miere, or Sea Folk, spend almost all their lives at sea, making their living mainly through trade and transport. They have a hierarchy that is very strict and complex, but everyone knows their place.", "Oceans", "Ruffians", "Military", "Can become a Bargainer, Cargomaster (Male) or Sailmistress (Female)","Cannot become a Noble, Wolfkin or Whitecloak"),
"Cairhienin" => array("Cairhienin have a tendancy to seek and create order, planning out their actions carefully. This also tends to lend to scheming and plotting to help keep themselves in control of situations.", "Mountains", "Exotic Animals", "Channelers", "Can become a Bargainer. Can become a Noble with any Net Worth", ""),
"Domani" => array("Domani enjoy pleasure for the sake of pleasure, and know how to use the desires of others to their own advantage, making them skilled bargainers. Beware of their fierce tempers!", "Hills", "Military", "Shadowspawn", "Can become a Bargainer or Smuggler", ""),
"Ghealdanin" => array("Ghealdanin are weary of strangers and given recent history, rightfully so. While they aren't considered a major military force, Ghealdanin's are willingly levied into service to supplement the Legion of the Wall in times of great need.", "Mountains", "Animals", "Ruffians", "Can become a Militia or Legion", ""),
"Illianer" => array("Illianers have a distaste for tyrants, but enjoy the luxuries and traditions typical among nobility. They take great pride in their history and their civilized society.", "Ocean", "Exotic Animals", "Animals", "Can become a Smuggler or Companion", ""),
"Kandori" => array("Out of all the Borderlands, Kandor is the one that has the most skill for trade. However, their people are just as well known for their military skill as their fellow Borderlanders.  ", "Plains", "Shadowspawn", "Animals", "Can become a Bargainer or Blight Guard", ""),
"Murandian" => array("Murandian's are considered quarrelsome and prickly, fighting duels over points of honor no one else can see. There's a saying in Lugard: 'Trust no one but yourself, and yourself not too much.'", "Forests", "Ruffians", "Shadowspawn", "Can become a Bargainer or Duelist", ""),
"Ogier" => array("Ogier are non-humans with a deep connection with nature. Their longer lifespans lead to a more measured temper compared to humans, but as the saying goes: 'Anger the Ogier and bring mountains down on your head'.", "Forests", "Animals", "Channelers", "Can become a Deathwatch Guard, Elder, or Treesinger", "Cannot Channel. Cannot become a Noble, Wolfkin, or Whitecloak"),
"Saldaean" => array("Women from Saldaea are known for their fiery temper and their unpredictability. They often expect their men to stand up to them and shout back when they get angry, feeling that if he does not, it is because he considers her weak.", "Wasteland", "Animals", "Exotic Animals", "Can become a Blight Guard or Smuggler", ""),
"Seanchan" => array("Seanchan have a rigid class structure with the belief that everyone has a place in which to serve and they belong in their place. They have a strong sense of honor, where a word of honor once given is considered absolute.", "Ocean", "Channelers", "Shadowspawn", "Can become a Deathwatch Guard or Seeker", "Cannot become a Whitecloak"),
"Shienaran" => array("Shienaran's value peace, beauty and life above all else. The looming threats of the Blight means they are constantly vigilant. Peace favor your Sword!", "Wasteland", "Shadowspawn", "Ruffians", "Can become a Blight Guard or Duelist", ""),
"Tairen" => array("Tairens have a strong conviction and self-esteem that guide them through their lives. This is particularly strong among the nobility, who often believe they have the right to take whatever they desire.", "Hills", "Channelers", "Animals", "Can become a Duelist or Defender", ""),
"Taraboner" => array("Taraboners have a proud heritage, which they claim dates back to the Age of Legends. They are well known for their custom of concealing their faces and their complex government leads them to enjoy political and social intrigue.", "Plains", "Exotic Animals", "Ruffians", "Can become a Militia or Smuggler", ""),
);

$nat_start = array(
'0' => "Thakan&#39;dar",
'1' => "Rhuidean",
'2' => "Ebou Dar",
'3' => "Amador",
'4' => "Caemlyn",
'5' => "Shol Arbela",
'6' => "Cantorin",
'7' => "Cairhien",
'8' => "Bandar Eban",
'9' => "Jehannah",
'10' => "Illian",
'11' => "Chachin",
'12' => "Lugard",
'13' => "Stedding Shangtai",
'14' => "Maradon",
'15' => "Falme",
'16' => "Fal Dara",
'17' => "Tear",
'18' => "Tanchico",
);

$nat_champs = array(
  '0' => "Pattern",
  '1' => "Waste",
  '2' => "Leopards",
  '3' => "Thistle and Star",
  '4' => "White Lion",
  '5' => "Roses",
  '6' => "Waves",
  '7' => "Sun",
  '8' => "Sword and Hand",
  '9' => "Silver Stars",
  '10' => "Golden Bees",
  '11' => "Red Horse",
  '12' => "Red Bull",
  '13' => "Great Trees",
  '14' => "Silver Fish",
  '15' => "Nine Moons",
  '16' => "Black Hawk",
  '17' => "Crescent Moons",
  '18' => "Golden Tree",
);

$nation_bonus = array(
'1' => array('wE5 mD5 cD-5',5,10),
'2' => array('hE5 rD5 eD-5',5,12),
'3' => array('pE5 cD5 mD-5',5,1),
'4' => array('fE5 mD5 eD-5',7,2),
'5' => array('uE5 sD5 mD-5',8,10),
'6' => array('oE5 rD5 mD-5',9,11),
'7' => array('uE5 eD5 cD-5',6,2),
'8' => array('hE5 mD5 sD-5',6,11),
'9' => array('uE5 aD5 rD-5',7,3),
'10' => array('oE5 eD5 aD-5',4,1),
'11' => array('pE5 sD5 aD-5',6,12),
'12' => array('fE5 rD5 sD-5',8,3),
'13' => array('fE5 aD5 cD-5',4,11),
'14' => array('wE5 aD5 eD-5',8,1),
'15' => array('oE5 cD5 sD-5',7,12),
'16' => array('wE5 sD5 rD-5',4,2),
'17' => array('pE5 eD5 rD-5',9,10),
'18' => array('hE5 cD5 aD-5',9,3),
);

$worth_ranks = array(
  '0' => array(0,'Beggar','Beggar'),
  '1' => array(200000,'Commoner','Commoner'),
  '2' => array(500000,'Businessman','Businesswoman'),
  '3' => array(1000000,'Merchant','Merchant'),
  '4' => array(2500000,'Noble','Noble'),
  '5' => array(5000000,'Minor Lord','Minor Lady'),
  '6' => array(10000000,'Lord','Lady'),
  '7' => array(30000000,'High Lord','High Lady'),
);

$skill_names = array(
'1' => "Cat Crosses the Courtyard",
'2' => "Sword Finess",
'3' => "Heron Spreads its Wings",
'4' => "Sword Mastery",
'5' => "Parting the Silk",
'6' => "Mighty Chop",
'7' => "Axe Finess",
'8' => "Hacking through Brambles",
'9' => "Axe Mastery",
'10' => "Cleave",
'11' => "Reach",
'12' => "Spear Finess",
'13' => "Dance the Spears",
'14' => "Spear Mastery",
'15' => "Wash the Spears",
'16' => "Quick Draw",
'17' => "Bow Finess",
'18' => "Rapid Fire",
'19' => "Bow Mastery",
'20' => "Focused Shot",
'21' => "Deflection",
'22' => "Bludgeon Finess",
'23' => "Shattering Strike",
'24' => "Bludgeon Mastery",
'25' => "Stunning Blow",
'26' => "Splitting the Flows",
'27' => "Weave Finess",
'28' => "Channeling Focus",
'29' => "Weave Mastery",
'30' => "Strength in the Power",


'51' => "Poised to Strike",
'52' => "Offensive Prowess",
'53' => "Combat Readiness",
'54' => "Defensive Prowess",
'55' => "On Guard",
'56' => "An Extra Bit",
'57' => "Resourcefulness",
'58' => "What is Needed",
'59' => "Awareness",
'60' => "Watching your Back",
'61' => "Using Your Surroundings",
'62' => "Presicion",
'63' => "Tracking the Wind",
'64' => "Reflexes",
'65' => "Blending In",
'66' => "Fear and Respect",
'67' => "Long Life",
'68' => "Embracing the Source",
'69' => "Awe Insiring",
'70' => "Ward Weaving",

'101' => "Hammer and Anvil",
'102' => "Veteran",
'103' => "The Lesser Sadness",
'104' => "Time to Toss the Dice",
'105' => "Toughness",
'106' => "Maintaining Civility",
'107' => "Purging the Shadow",
'108' => "Defender of the Light",
'109' => "Justice",
'110' => "Heavier than a Mountain",
'111' => "Benefits of the Bond",
'112' => "Legendary Skill",
'113' => "Expendable",
'114' => "Soldier for Hire",
'115' => "Higher Pay",
'116' => "Urban Tracking",
'117' => "Stealth",
'118' => "Ambush",
'119' => "Unexpected Opponent",
'120' => "Nimbleness",
'121' => "Extra Flourish",
'122' => "Great Roll",
'123' => "Good Fortune",
'124' => "Lucky Shot",
'125' => "Ruthlessness",
'126' => "Thievery",
'127' => "Mug",
'128' => "Knowing their Weakness",
'129' => "Straight Shooter",
'130' => "Stalking Prey",
'131' => "The Beast Within",
'132' => "Heightened Senses",
'133' => "One with the Pack",
'134' => "Fanatical Devotion",
'135' => "Light-Blinded",
'136' => "Mob Mentality",
'137' => "Corruption",
'138' => "Mark of the Shadow",
'139' => "Leech",
'140' => "Incapacite",
'141' => "Bound by the Oaths",
'142' => "Legendary Reputation",
'143' => "Living Weapon",
'144' => "Forced Learning",
'145' => "Residual of Madness",
'146' => "The Gift of Passage",
'147' => "Art of the Deal",
'148' => "To Uphold the Bargain",
'149' => "Shade and Water",
'150' => "Dedicated",
'151' => "Of the Three-fold Land",
'152' => "Til Shade is Gone",
'153' => "Raid Tactics",
'154' => "Taking an Opening",
'155' => "Master of Trade",
'156' => "Disciplined",
'157' => "Defender of the Ship",
'158' => "Love of Knowledge",
'159' => "One with the Trees",
'160' => "Walking Legend",
'161' => "Pride of the Empire",
'162' => "Resilant Fighter",
'163' => "Fearsome",

'999' => "Go Team",
);

function level_at($exp,$exp_up,$exp_up_s,$lvl,$pts,$health,$stamaxa,$spts,$jpts,$nskill,$nprof) {
  $ospts = $spts;
  $ojpts = $jpts;
  while ($exp>=$exp_up) 
  {

    $lvl++;
    $pts += 15;
    $health += 10;
    $exp_up_s += 10;
    $exp_up+=$exp_up_s;
    $stamaxa+=1;
    $spts+= floor($lvl/15)+1;
    if ($lvl%5==0) $jpts+=1;
    $nskill = $spts - $ospts;
    $nprof = $jpts - $ojpts;
  }
  
  return array($lvl,$exp_up,$exp_up_s,$pts,$health,$stamaxa,$spts,$jpts,$nskill,$nprof);
}

function getAlts ($ips)
{
  $alts = '';
  for ($i = 0; $i < count($ips); $i++)
  {
    $result = mysql_query("SELECT * FROM IP_logs WHERE addy='$ips[$i]'");
    $ip_log = mysql_fetch_array($result);
    $users= unserialize($ip_log[users]);
    for ($j=0; $j < count($users); $j++)
    {
      $alts[$users[$j]] = 1;
    }
  }

  return $alts;
}

function getStrength ($usedPts, $maxPts, $stamina, $stamaxa)
{
  $strength = (($usedPts/$maxPts)*3 + ($stamina/$stamaxa))*25;
  
  return $strength;
}

function getStrengthLevel ($strength)
{
  $slvl = "Broken";
  
  if ($strength > 85) $slvl = "Heavy";
  else if ($strength > 70) $slvl = "Strong";
  else if ($strength > 55) $slvl = "Fair";
  else if ($strength > 40) $slvl = "Light";
  else if ($strength > 25) $slvl = "Damaged";
  
  return $slvl;
}

function isChanneler ($types)
{
  $isChan = 0;
  
  for ($t=0; $t < count($types); $t++)
  {
    if ($types[$t] == 108) $isChan=1;
  }
  
  return $isChan;
}

// branchname => stat, dependancy, text, levels, rewards, reward type, wiki
$achievements = array(
'skill_points' => array('skill_pts_used', '', array("Use "," Skill Points"), array(2), array('1'),'sp','Skills'),
'skill_level' => array('highest_skill', array('skill_points',1), array("Raise a Skill to level ",""), array(5,10,15), array('5', '10', '15'),'sp','Skills'),
'active_skills' => array('num_active_skills', '', array("Use "," Active Skills"), array(1,4), array('1','1'),'sp','Skills'),
'specialized_classes' => array('num_classes', '', array("Add a specialized class"), array(1), array('2'),'sp','Skills'),

'prof_points' => array('prof_pts_used', '', array("Use "," Profession Point"), array(1), array('1'),'pp','Professions'),
'prof_levels' => array('highest_prof', array('prof_points',1), array("Raise a Profession to level ",""), array(5,9), array('2','3'),'pp','Professions'),
'prof_all_one' => array('num_profs', array('prof_points',1), array("Raise "," Profession to level 1 or higher"), array(7,13), array('1','3'),'pp','Professions'),

'find_quests' => array('find_quests_done', '', array("Complete "," Find Quests"), array(1,10,50,100), array('5','10','25','50'),'ji','Quests'),
'item_quests' => array('item_quests_done', '', array("Complete "," Item Quests"), array(1,10,50,100), array('5','10','25','50'),'ji','Quests'),
'npc_quests' => array('npc_quests_done', '', array("Complete "," NPC Quests"), array(1,10,50,100), array('5','10','25','50'),'ji','Quests'),
'horde_quests' => array('horde_quests_done', '', array("Complete "," Horde Quests"), array(1,10,50,100), array('5','10','25','50'),'ji','Quests'),
'escort_quests' => array('escort_quests_done', '', array("Complete "," Escort Quests"), array(1,10,50,100), array('5','10','25','50'),'ji','Quests'),
'quests' => array('quests_done', '', array("Complete "," Quests"), array(10,50,100,500), array('10','20','50','100'),'ji','Quests'),

'player_quests' => array('play_quests_done', '', array("Complete "," Player Quests"), array(1,10,50,100), array('5','10','15','25'),'ji','Player_Quests'),
'create_quests' => array('quests_created', '', array("Create "," Quest"), array(1), array('5'),'ji','Player_Quests'),
'my_quests_done' => array('my_quests_done', array('create_quests',1), array("Have "," of your Quests Completed"), array(1,5,20,50), array('5','10','15','25'),'ji','Player_Quests'),

'own_biz' => array('num_biz', '', array("Own "," Business"), array(1), array('2500'),'cp','Business'),
'biz_types' => array('num_biz_types', array('own_biz',1), array("Own "," different types Businesses "), array(3,7), array('10000','50000'),'cp','Business'),
'biz_same' => array('most_biz', array('own_biz',1), array("Own "," Businesses of the same type"), array(3,6), array('25000','100000'),'cp','Business'),
'biz_level' => array('highest_biz', array('own_biz',1), array("Own a Level "," Business"), array(3,6,9), array('25000','50000','100000'),'cp','Business'),
'biz_worth' => array('tot_business', array('own_biz',1), array("Reach "," in Total Business Value"), array(250000,1000000,2500000,5000000,10000000), array('10000','20000','50000','100000','200000'),'cp','Business'),

'npc_wins' => array('npc_wins', '', array("Defeat "," NPCs"), array(1,50,500,1000,3000), array('2000','5000','10000','50000','150000'),'cp','Battle'),
'npc_same' => array('most_npc_wins', array('npc_wins',2), array("Defeat "," NPCs of the same type"), array(50,100,500), array('5000','10000','50000'),'cp','Battle'),
'horde_wins' => array('horde_wins', '', array("Win "," battles vs Hordes"), array(1,50,500,1000,3000), array('2000','5000','10000','50000','150000'),'cp','Battle'),
'army_wins' => array('army_wins', '', array("Win "," battles vs Armies"), array(1,50,500,1000,3000), array('2000','5000','10000','50000','150000'),'cp','Battle'),
'horde_finish' => array('horde_finish', array('horde_wins',1), array("Deal final blow to a Horde",""), array(1), array('50'),'ji','Battle'),
'army_finish' => array('army_finish', array('army_wins',1), array("Deal final blow to an Army",""), array(1), array('50'),'ji','Battle'),
'duel_wins' => array('duel_wins', '', array("Win "," Duels"), array(1,50,250,500,1000), array('2000','5000','10000','50000','150000'),'cp','Battle'),
'defend_wins' => array('def_wins', '', array("Win "," Duels while Defending"), array(1,50,100,500), array('5','10','20','50'),'ji','Battle'),

'find_items' => array('items_found', array('npc_wins',1), array("Find "," Items"), array(10,50,100,500,1000), array('1000','5000','10000','50000','150000'),'cp','Item'),
'drop_items' => array('items_dropped', '', array("Drop "," Equipment Items"), array(1,20,50), array('5','10','25'),'ji','Item'),
'sell_items' => array('items_sold', '', array("Sell "," Equipment Item"), array(1), array('500'),'cp','Item'),
'combine_ter' => array('items_combined', array('find_items',1), array("Combine "," Items with Terangreal"), array(1,10,50,100,500), array('5','10','25','50','100'),'ji','Item'),
'use_consume' => array('items_consumed', '', array("Use "," Consumable Items"), array(1,10,50,100,500), array('5','10','25','50','100'),'ji','Item'),
'market_items' => array('items_marketed', array('own_biz',1), array("Sell "," Items on the Market"), array(1,10,25,50), array('5','10','25','50'),'ji','Item'),

'market_buy' => array('items_from_market', '', array("Buy "," Item from the Market"), array(1,10,25,50), array('5','10','20','40'),'ji','Purchase'),
'spend_items' => array('spent_shop', '', array("Spend "," on Equipment from Shop",1), array(10000,100000,500000,1000000,5000000), array('1000','5000','25000','50000','250000'),'cp','Purchase'),
'repair_items' => array('items_repaired', '', array("Repair "," Items"), array(1), array('5'),'ji','Purchase'),
'spend_repair' => array('spent_repair', array('repair_items',1), array("Spend "," on Repairs",1), array(5000,10000,100000,500000), array('1000','2000','20000','50000'),'cp','Purchase'),
'visit_inn' => array('inn_use', '', array("Rent "," Room from an Inn"), array(1), array('500'),'cp','Purchase'),
'upgrade_gear' => array('outfit_use', '', array("Purchase "," Upgrades from the Outfitter"), array(1), array('2500'),'cp','Purchase'),

'bank_withdrawl' => array('withdrawls', '', array("Make "," Withdrawal from bank"), array(1), array('100'),'cp','Coin'),
'bank_deposit' => array('deposits', '', array("Make "," Deposit into bank"), array(1), array('100'),'cp','Coin'),
'bank_size' => array('bankcoin', array('bank_deposit',1), array("Have "," in your bank",1), array(100000,1000000,5000000), array('5000','50000','200000'),'cp','Coin'),
'dice_wins' => array('dice_wins', '', array("Win "," games of Dice"), array(1,10,100,500), array('100','2500','10000','100000'),'cp','Coin'),
'net_worth' => array('net_worth', '', array("Reach "," in Net Worth"), array(1000000,2500000,5000000,10000000,30000000), array('10000','20000','50000','100000','200000'),'cp','Coin'),

'earn_ji' => array('ji', '', array("Earn "," Ji"), array(100,250,500,1000,5000), array('5000', '10000', '25000', '50000', '100000'),'cp','Ji'),
'num_tourneys' => array('num_tourney', '', array("Enter "," Tournaments"), array(1,10,25,50), array('5','10','25','50'),'ji','Ji'),
'win_tourneys' => array('win_tourney', array('num_tourneys',1), array("Win "," Tournaments"), array(1,5,10), array('10','25','50'),'ji','Ji'),

'join_clan' => array('clans_joined', '', array("Join a Clan"), array(1), array('25'),'ji','Clan'),
'enemy_wins' => array('enemy_wins', array('join_clan',1), array("Defeat "," Clan Enemies"), array(1,25,50,100,500), array('5','10','20','50','100'),'ji','Clan'),
'ally_wins' => array('ally_wins', array('join_clan',1), array("Defeat "," Clan Allies"), array(1,25,50,100,500), array('-5','-10','-20','-50','-100'),'ji','Clan'),
'donate_items' => array('items_donated', array('join_clan',1), array("Donate "," Items to Clan Vault"), array(1,10,25), array('5','10','15'),'ji','Clan'),
'donate_coin' => array('coin_donated', array('join_clan',1), array("Donate "," to a Clan",1), array(100,10000,100000,500000,1000000), array('5','10','20','40','100'),'ji','Clan'),
'clan_chat' => array('clan_posts', array('join_clan',1), array("Make "," post in a Clan Chat"), array(1), array('5'),'ji','General'),

'own_estate' => array('num_estates', '', array("Own "," Estate"), array(1), array('2500'),'cp','Estate'),
'estate_level' => array('highest_estate', array('own_estate',1), array("Own a Level "," Estate"), array(2,4,6,8), array('2500','10000','50000','100000'),'cp','Estate'),
'estate_worth' => array('tot_estate', array('own_estate',1), array("Reach "," in Total Estate Value"), array(250000,1000000,2500000,5000000,10000000), array('10000','20000','50000','100000','200000'),'cp','Estate'),

'align_light' => array('align_high', '', array("Earn +"," Alignment or higher"), array(300, 1000), array('25','100'),'ji','Alignment'),
'align_shadow' => array('align_low', '', array("Earn -"," Alignment or lower"), array(300, 1000), array('25','100'),'ji','Alignment'),

'travel_ways' => array('ways_use', '', array("Travel the Ways "," time"), array(1), array('5'),'ji','General'),
'use_tar' => array('tar_posts', '', array("Make "," post in Tel&#39;aran&#39;rhoid"), array(1), array('5'),'ji','General'),

);
//46, 154
//'biz_maxed' => array('maxed_biz', array('biz_level',3), array("Own a Business with all possible upgrades purchased"), array(1), array('250000'),'cp','Business'),  

function removeFromClan($rchar)
{
  $soc_name = $rchar['society'];
  $society = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name' "));

  // CHECK IF LEADER CHANGES
  if (strtolower($rchar[name]) == strtolower($society[leader]) && strtolower($rchar[lastname]) == strtolower($society[leaderlast]) )
  {
    //$message = $user['name'];
    if ($society['subs']>0)
    {
      $subs = $society['subs'];
      $new_id = 9999999;
      $subleaders = unserialize($society['subleaders']);
      foreach ($subleaders as $c_n => $c_s)
      {
        if ($c_n < $new_id)
        {
          $new_id = $c_n;
        }
      }
      foreach ($subleaders as $c_n => $c_s)
      {
        if ($c_n == $new_id)
        {
          $result = mysql_query("UPDATE Soc SET leader='$c_s[0]', leaderlast='$c_s[1]' WHERE name='$soc_name'");
          --$subs;
          $subleaders[$new_id][0]=0;
          $subleaders=delete_blank($subleaders);

          if ($subs > 0)
          {
            $query = "UPDATE Soc SET subleaders='".serialize($subleaders)."', subs='$subs' WHERE name='$soc_name'";
          }
          else
          {
            $query = "UPDATE Soc SET subleaders='', subs='$subs' WHERE name='$soc_name'";
          }
          $result = mysql_query($query);
        }
      }
    }
    else 
    {
      $user = mysql_fetch_array(mysql_query("SELECT * FROM Users WHERE society='".$rchar['society']."' ORDER BY exp DESC LIMIT 1"));
      mysql_query("UPDATE Soc SET leader='".$user['name']."', leaderlast='".$user['lastname']."' WHERE name='".$rchar['society']."'");
    }
  }
  // SET DATABASE
  $querya = "UPDATE Users SET society='' WHERE id='$rchar[id]'";
  $result = mysql_query($querya);

  // return all vault items
  $vid = 10000+$society[id];
  $iresult=mysql_query("SELECT id, owner FROM Items WHERE owner='$rchar[id]' AND society='".$society[id]."'");
  while ($qitem = mysql_fetch_array($iresult))
  {
    $result = mysql_query("UPDATE Items SET owner='".$vid."', last_moved='".time()."', istatus='0' WHERE id='".$qitem[id]."'");
  }

  // UPDATE NUMBER OF MEMBERS
  $result = mysql_query("UPDATE Soc SET members=members-1 WHERE name='$soc_name' ");

  $numchar = mysql_num_rows(mysql_query("SELECT name, lastname FROM Users WHERE society='$soc_name' ORDER BY exp DESC"));
  if ($numchar == 0)
  {
    $stance = unserialize($society[stance]);
    foreach ($stance as $c_n => $c_s)
    {
      if ($c_s != 0)
      {
        $soc_name2 = str_replace("_"," ",$c_n);
        $society2 = mysql_fetch_array(mysql_query("SELECT * FROM Soc WHERE name='$soc_name2' "));
        $stance2 = unserialize($society2[stance]);
        $stance2[str_replace(" ","_",$soc_name)] = 0;
        $changed_stance2 = serialize($stance2);
        mysql_query("UPDATE Soc SET stance='$changed_stance2' WHERE name='$society2[name]' ");
      }
    }
  
    // IF THERE IS NO ONE LEFT IN THE CLAN THEN DELETE IT
    $query = "DELETE FROM Soc WHERE name='$soc_name'";
    $result5 = mysql_query($query);
  }
}
?>