<!DOCTYPE html>
<html>
<head>
  <title>reply from responder in Ultimate Game</title>
</head>
<body>

<?php 

 include "../ulticontrol/ultimategameinfo.php"; 
  /*------initial information--------------------------*/
  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST["proposeroffer"])&&!empty($_POST["proposeroffer"])){

        /*-----------2. Create first agent--------------------*/
        
        $firstagent_state=CreateFirstagent($lccengineaddress,$institutionname,$gameprotocol_id,$playerid,$firstagent_role);
        $interactionid_proposerside=GetInteractionId($firstagent_state,$lccengineaddress,$institutionname); 

        if ($interactionid_proposerside!=""){
          /*---------2.2 check firstagent state---------------*/
          $interactionpath="http://{$lccengineaddress}/interaction/user/manager/{$institutionname}/{$interactionid_proposerside}";
          /*----------3. create second agent----------------*/
          CreateOtherAgent($lccengineaddress,$institutionname,$interactionid_proposerside,$botid,$secondagent_role);
          sleep(1);
          /*------------3.1 check if all agents are created ---------------*/
          $allagentsstates_json=getrequest($interactionpath);
          $allagentsstates=json_decode($allagentsstates_json,true);

          if (count($allagentsstates["agents"])==2){

            /*$firstagent_nextstep_1=AskAgentNextStep($lccengineaddress,$institutionname,$interactionid_proposerside,$$playerid);
            echo (gettype($firstagent_nextstep_1)).'<br>';
            echo ($firstagent_nextstep_1[0]);*/

            $firstagent_response_1="e(offernum({$_POST["proposeroffer"]}, {$botid}), _)";  
            AnswerAgentNextStep($lccengineaddress,$institutionname,$interactionid_proposerside,$playerid,$firstagent_response_1);
            sleep(1);

            //store data
            
            csv_storegamemsg("{$interactionid_proposerside}","{$gameprotocol_id}","{$playerid}","{$firstagent_role}","{$botid}","{$secondagent_role}","e(offernum({$_POST["proposeroffer"]}#{$botid}))","{$sourcefiledir}/gamemsgs.csv");
            mysql_insertmsgdata("{$interactionid_proposerside}","{$gameprotocol_id}","{$playerid}","{$firstagent_role}","{$botid}","{$secondagent_role}","e(offernum({$_POST["proposeroffer"]}#{$botid}))");

            /*$secondagent_nextstep_1=AskAgentNextStep($lccengineaddress,$institutionname,$interactionid_proposerside,$botid);
            echo (gettype($secondagent_nextstep_1)).'<br>';
            echo sizeof($secondagent_nextstep_1).'<br>';
            echo ($secondagent_nextstep_1[0]);*/

            $responderchoice_now=$responderchoice;
            $secondagent_response_1="e(acceptornot({$responderchoice_now}, {$_POST["proposeroffer"]}), _)";
            AnswerAgentNextStep($lccengineaddress,$institutionname,$interactionid_proposerside,$botid,$secondagent_response_1);
            sleep(1);
            
            //store data
            csv_storegamemsg("{$interactionid_proposerside}","{$gameprotocol_id}","{$botid}","{$secondagent_role}","{$playerid}","{$firstagent_role}","e(acceptornot({$responderchoice_now}#{$_POST["proposeroffer"]}))","{$sourcefiledir}/gamemsgs.csv");
            mysql_insertmsgdata("{$interactionid_proposerside}","{$gameprotocol_id}","{$botid}","{$secondagent_role}","{$playerid}","{$firstagent_role}","e(acceptornot({$responderchoice_now}#{$_POST["proposeroffer"]}))");
            
            //store player info
            $playerinfo_pid=$playerid;
            $playerinfo_prole=$firstagent_role;
            $playerinfo_interid=$interactionid_proposerside;
            $playerinfo_filedir="{$sourcefiledir}/playerinfo.csv";
            csv_storeplayerinfo("{$playerinfo_pid}","{$playerinfo_prole}#{$gameprotocol_id}","{$playerinfo_interid}","{$playerinfo_filedir}");
            mysql_insertplayerinfodata("{$playerinfo_interid}","{$playerinfo_pid}","{$playerinfo_prole}#{$gameprotocol_id}");

            //var_dump($secondagent_response_1); echo"<br><br>";
            echo "The responder has decided to {$responderchoice_now} your offer.<br><br>";
          }
          else{
            echo "Failed to create the second agent. *_*<br><br>";
          }
        }
        else{
          echo "Failed to create new interaction. *_* <br><br>";
        }
    }
  }
?>

If you want to play again, please click button below.<br><br>
<input type="button" value="Play Again" onclick="location.href='http://<?php echo $gameserveraddress?>/ultimategame/welcome.php'" >
<br><br>

<img src="bot.png">

</body>
</html>
