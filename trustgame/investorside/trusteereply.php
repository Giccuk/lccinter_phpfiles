<!DOCTYPE html>
<html>
<head>
  <title>reply from trustee in Trust Game</title>
</head>
<body>

<?php 

 include "../trucontrol/trustgameinfo.php";
  /*------initial information--------------------------*/
  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST["investoroffer"])&&!empty($_POST["investoroffer"])){

        /*-----------2. Create first agent--------------------*/
        $firstagent_state=CreateFirstagent($lccengineaddress,$institutionname,$gameprotocol_id,$botid,$firstagent_role);
        $interactionid_investorside=GetInteractionId($firstagent_state,$lccengineaddress,$institutionname); 

        if ($interactionid_investorside!=""){
          /*---------2.2 check firstagent state---------------*/
          $interactionpath="http://{$lccengineaddress}/interaction/user/manager/{$institutionname}/{$interactionid_investorside}";
          /*----------3. create second agent----------------*/
          CreateOtherAgent($lccengineaddress,$institutionname,$interactionid_investorside,$playerid,$secondagent_role);
          sleep(1);
          /*------------3.1 check if all agents are created ---------------*/
          $allagentsstates_json=getrequest($interactionpath);
          $allagentsstates=json_decode($allagentsstates_json,true);

          if (count($allagentsstates["agents"])==2){

            //$firstagent_nextstep_1=AskAgentNextStep($lccengineaddress,$institutionname,$interactionid,$botid);

            $firstagent_response_1="e(invest({$_POST["investoroffer"]}, {$playerid}), _)";  
            AnswerAgentNextStep($lccengineaddress,$institutionname,$interactionid_investorside,$botid,$firstagent_response_1);
            sleep(1);

            csv_storegamemsg("{$interactionid_investorside}","{$gameprotocol_id}","{$botid}","{$firstagent_role}","{$playerid}","{$secondagent_role}","e(invest({$_POST["investoroffer"]}#{$playerid}))","{$sourcefiledir}/gamemsgs.csv");
            mysql_insertmsgdata("{$interactionid_investorside}","{$gameprotocol_id}","{$botid}","{$firstagent_role}","{$playerid}","{$secondagent_role}","e(invest({$_POST["investoroffer"]}#{$playerid}))");

            //$secondagent_nextstep_1=AskAgentNextStep($lccengineaddress,$institutionname,$interactionid_investorside,$playerid);

            $trusteerepay=gettrusteerepay($_POST["investoroffer"],$game_rate);
            $secondagent_response_1="e(repay({$trusteerepay}, {$botid}), _)";
            AnswerAgentNextStep($lccengineaddress,$institutionname,$interactionid_investorside,$playerid,$secondagent_response_1);
            sleep(1);
            
            csv_storegamemsg("{$interactionid_investorside}","{$gameprotocol_id}","{$playerid}","{$secondagent_role}","{$botid}","{$firstagent_role}","e(repay({$trusteerepay}#{$botid}))","{$sourcefiledir}/gamemsgs.csv");
            mysql_insertmsgdata("{$interactionid_investorside}","{$gameprotocol_id}","{$playerid}","{$secondagent_role}","{$botid}","{$firstagent_role}","e(repay({$trusteerepay}#{$botid}))");

            //store player info
            $playerinfo_pid=$playerid;
            $playerinfo_prole=$firstagent_role;
            $playerinfo_interid=$interactionid_investorside;
            $playerinfo_filedir="{$sourcefiledir}/playerinfo.csv";
            csv_storeplayerinfo("{$playerinfo_pid}","{$playerinfo_prole}","{$playerinfo_interid}","{$playerinfo_filedir}");
            mysql_insertplayerinfodata("{$playerinfo_interid}","{$playerinfo_pid}","{$playerinfo_prole}");

            echo "The trustee has decided to repay £{$trusteerepay} to you.<br><br>";
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
<input type="button" value="Play Again" onclick="location.href='http://<?php echo $gameserveraddress?>/trustgame/welcome.php'" >
<br><br>

<img src="smile2.png">

</body>
</html>
