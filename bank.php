<?php
$bd = $town_bonuses[bD];
$max_deposit=($bd+80)/100;
      $tot_dep = floor(($char['gold']+$char['bankgold'])*$max_deposit)-$char['bankgold'];
      $gold_dep = floor($tot_dep/10000);
      $silver_dep = floor(($tot_dep - ($gold_dep*10000))/100);
      $copper_dep = floor(($tot_dep - ($gold_dep*10000)-$silver_dep*100)); 
      if ($tot_dep<0 || $clear) {$gold_dep = 0; $silver_dep = 0; $copper_dep = 0;}
?>
  <SCRIPT LANGUAGE="JavaScript">

    function doBanking (act)
    {
       document.bankForm.action.value = act;
       document.bankForm.submit();
    }
    
    function clearBank()
    {
      document.bankForm.gold.value= 0;
      document.bankForm.silver.value= 0;
      document.bankForm.copper.value= 0;   
    }

  </SCRIPT>  
  <div class="row" style='margin-top:5px; margin-bottom: 5px;'>
    <form name='bankForm' action="world.php" method="post">
      <div class="col-sm-4" align='center'>
        <div id='bankGold'>
          <img src='images/till.gif'/>&nbsp;<b>Bank:</b>
          <?php
            echo displayGold($char['bankgold']);
          ?>
        </div>
      </div>
      <div class="col-sm-4" align='center'>
        <input type='hidden' name='cleared' value='0'/>
        <input type='hidden' name='action' value='0'/>
        <img src='images/gold.gif' width='15' style='vertical-align:middle' alt='g:'/>
        <input type="text" name="gold" value="<?php echo $gold_dep; ?>" class="gos-form" size="5" maxlength="5"/>
        <img src='images/silver.gif' width='15' style='vertical-align:middle' alt='s:'/>
        <input type="text" name="silver" value="<?php echo $silver_dep; ?>" class="gos-form" size="2" maxlength="2"/>
        <img src='images/copper.gif' width='15' style='vertical-align:middle' alt='c:'/>
        <input type="text" name="copper" value="<?php echo $copper_dep; ?>" class="gos-form" size="2" maxlength="2"/>
        <input type="button" name="clear" class="btn btn-xs btn-danger" onClick="clearBank();" value="C"/>
      </div>
      <div class="col-sm-4" align='center'>
        <input type="button" name="deposit" class="btn btn-xs btn-primary" onClick="doBanking('1');" value="Deposit"/>
        &nbsp;
        <input type="button" name="withdraw" class="btn btn-xs btn-warning" onClick="doBanking('2');" value="Withdraw"/>
        <?php 
          if ($char['society'] != "")
          {
        ?>
        &nbsp;
        <button name="donate" class="btn btn-xs btn-info" onClick="doBanking('3');">Donate</button>
        <?php
          }
        ?>
      </div>
    </form>
  </div>