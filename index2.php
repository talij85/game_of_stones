<?php
$skipVerify = 1;
include('header.htm');
?>
  <div class="row"align='left'>
    <div class="col-sm-3">
      <form role="form" action="verify.php" method="post">
        <div class="form-group form-group-sm">
          <label for="userid">First Name:</label>
          <input type="text" class="form-control gos-form" id="userid" name="userid" maxlength="15">
        </div>
        <div class="form-group form-group-sm">
          <label for="lastname">House Name:</label>
          <input type="text" class="form-control gos-form" id="lastname" name="lastname" maxlength="15">
        </div>        
        <div class="form-group form-group-sm">
          <label for="password">Password:</label>
          <input type="password" class="form-control gos-form" id="pswd" name="pswd" maxlength="20">
        </div>
        <div class="form-group form-group-sm">
          <label for="mode">Mode:</label>
          <select class="form-control gos-form" id="mode" name="mode">
            <option value='0'>Normal</option>
            <option value='1'>Lite</option>
          </select>
        </div>
        <button type="submit" class="btn btn-block btn-success">Enter World</button>
      </form>     
      <br/><br/>
      <center>Don't have a character? </center>
      <a href='create.php' class='btn btn-block btn-warning'>Create a Character</a>
    </div>
    <div class="col-sm-9" align='left'>
      <div id="content">
        <ul id="biotabs" class="nav nav-tabs" data-tabs="tabs">
          <li class="active"><a href="#welcome_tab" data-toggle="tab">Welcome</a></li>
          <li><a href="#about_tab" data-toggle="tab">About GoS</a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="welcome_tab">
            <div align="center">
              <h3>V18.7 Launch Date: 02/02/2020, 9PM CST</h3>
              <img src='images/v9loginbanner.jpg' class="img-responsive img-rounded hidden-sm hidden-x
              s">
            </div>
            <p>Welcome to <i>A Game of Stones: V18.8!</i> This is the 19th full version of GoS I've released in the last 10+ years, 
            but there is still more to come! No significant changes made since last age.</p>
            <p>Please report all bugs or behavioral issues to me on the forums and if you have any questions check out the wiki, or just ask. Thanks and please enjoy the game!</p>
            <p>Please visit the forums for the latest progress and news!</p>
          </div>

          <div class="tab-pane" id="about_tab">
            <div class="panel-group" id="accordion">
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">1. What is "A Game of Stones"?</a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in">
                  <div class="panel-body">
                    <p>A Game of Stones is a browser-based Massively Multiplayer Online Role Playing Game (MMORPG) set in the universe of Robert Jordan's
                    Wheel of Time series. You control your character as he/she duels other players, battles NPCs, and completes quests. You can form 'Clans'
                    with other players and compete to take over towns from other clans. As you complete tasks, you gain better skills and better equipment,
                    struggling to be known as a Hero of the Horn.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">2. What with the name? I don't see in Stones in this Game?</a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>The name comes from the strategy board game 'Stones' within the WoT universe. I know there are no actual stones in the game, but hey,
                    I didn't name it.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">3. Wow! The setting/NPCs/weapons/classes etc sure are creative? Did you come up with these?</a>
                  </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>No. The setting, a large majority of the NPCs and items, classes, and a lot of other elements are derived from the work of Robert Jordan.
                    While I'm not going to say Jordan created the idea of using 'Swords' or fighting 'Bears', I've tried to only include elements found in the 
                    Wheel of Time series or elements that are likely to appear in such a setting. Any emulation of these ideas are done purely as the work of 
                    fan.</p>
                  </div>
                </div>
              </div>                
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">4. I haven't read any of the Wheel of Time books. Can I still play? Will I be overly confused?</a>
                  </h4>
                </div>
                <div id="collapseFour" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Of course you can play! The Wheel of Time source material is used primarily for naming conventions, but most gameplay elements are 
                    comparable to other RPGs, if only called another name.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">5. "In the Wheel of Time world, <insert game or gameplay element> 
                    doesn't make sense! It should be more like..."</a>
                  </h4>
                </div>
                <div id="collapseFive" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>I try hard to keep as true to the books as possible, but at some point I have to draw a line to keep the game balanced. I also try to
                      include as much WoT elements to the game as possible, sometimes at the cost of continuity. Yeah, a channeler would likely be able to 
                      manhandle any non-channeler in a straight duel, but if channelers were that powerful the game would be unbalanced to the point of negatively
                      effecting the game. Balancing WoT continuity and gameplay is something I try my best at though.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">6. I don't understand how <insert any gameplay element> works?</a>
                  </h4>
                </div>
                <div id="collapseSix" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>If you ever have a question, I'd suggest first checking the <a href='http://talij.com/goswiki/index.php'>GoS Wiki page</a>. 
                    It has a lot of basic information that may solve your 
                    problem. If that doesn't help, start a thread on the forum asking. Other players (myself include) check in there regularly and someone 
                    there should be able to answer your question. Or simply post your question in Tel'aran'rhoid, as many of the veteran players are quick to help others.
                    If all else fails, shoot me a PM and I'll get back to you as soon as I can.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">7. Your documentation sucks!</a>
                  </h4>
                </div>
                <div id="collapseSeven" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Yeah. I know. I'm working on trying to keep it more up to date, but when it comes down to documenting how stuff works or making changes 
                    to the game to make it better, coding usually wins for me. A <a href='http://talij.com/goswiki/index.php'>GoS Wiki</a> has been created, 
                    so if you wish to share your knowledge of 
                    the game and how it works, it'd be much appreciated.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight">8. Ok. You've sold me. Now how much is this gonna cost me?</a>
                  </h4>
                </div>
                <div id="collapseEight" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Nothing! The game if FREE to everyone. I will mention that those who donate (see the 'Donate' button to your left...) 
                    to the game do get a few perks, but nothing that give their
                    characters any major advantages over non-donators. Mainly little options to make the game easier to manage.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseNine">9. What made you decide to make this game? </a>
                  </h4>
                </div>
                <div id="collapseNine" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Actually, I didn't. It was started by Craig Harrison, who worked on the game well before I did. I just happened to play the game and 
                    really enjoyed it. When Craig decided to call it quits back in May of 2008, he was nice enough to let me have the source code to the game and 
                    carry on the torch. A few others got the source code as well, but I'm not aware of any other sites actively using it right now.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTen">10. Love the graphics! You rock!</a>
                  </h4>
                </div>
                <div id="collapseTen" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Thanks, but I didn't do them (you likely wouldn't be loving them if I did...). The original game graphics were created by Aaron 
                    McCollough, though most of the current graphics are the work of Edward Givens. So pass your graphical feelings his way.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseEleven">11. I love this game! What can I do to help?</a>
                  </h4>
                </div>
                <div id="collapseEleven" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Wow! So nice of you to ask!</p>
                    <p>The biggest thing you can do is spread the word about the game. The more players we have, the more fun the game can be. I'm also very open 
                    to suggestions for game improvements. That's where most of the changes come from. I figure you guys are the ones playing the game, so it 
                    might as well be as close to what you all what anyways (within reason). If you REALLY want to help, there's always updating the Wiki to 
                    include more detail. The better the descriptions of what to do are, the easier the game will be for people to pick up. Plus I do accept 
                    donations to help with the costs of running the site.</p>    
                    <p>If you want to do even more, come to me and we can talk. As nice as it'd be to have a second (or tenth) pair of hands working on coding game
                    improvements, I'm pretty content right now doing the work myself. Smaller teams are much easier to manage. Maybe someday I'll build a team 
                    to do the work, but I'd probably be a little hard pressed right now. This is a hobby for me.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve">12. I love the idea of this game! What do I need to do to make one myself?</a>
                  </h4>
                </div>
                <div id="collapseTwelve" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>First, learning HTML, PHP, and Javascript would be a good idea. Other than that, its just a lot of hard work and creativity.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThirteen">13. I think I found a bug in your game!</a>
                  </h4>
                </div>
                <div id="collapseThirteen" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>Then please let me know on the <a href="http://gosos.proboards.com/">forums</a>. I can't fix them if I don't know about them.</p>
                  </div>
                </div>
              </div>
              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourteen">14. Thanks for making this game!</a>
                  </h4>
                </div>
                <div id="collapseFourteen" class="panel-collapse collapse">
                  <div class="panel-body">
                    <p>You're very welcome! Go Team!</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
include('footer.htm');
?>
