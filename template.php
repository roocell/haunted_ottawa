<?php
require_once "db.php";

// expect
// $level
// $title
// $hint1, $hint2, $hint3
// $hinttxt1, $hinttxt2, $hinttxt3
// $body
// $answer
if (isset($_REQUEST['udid']))
{
  $g_udid=$_REQUEST['udid'];
  $g_user = getUserByUdid($g_udid);

  // validate this level is valid for the user
  if ($level > $g_user['level'])
  {
    echo "stop you hacker ";
    echo $level." ".$g_user['level'];
    exit();
  }

  $level_activated=1;
} else {
  // this can happen anytime
  // a redirect will happen after the user
  // logs in and then we'll have UDID
  // but before that we need to make sure the level is
  // not activated
  $level_activated=0;
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}



$submitted_answer = $_REQUEST['answer'];
if ($submitted_answer)
{
    $hintimg = 0;
    $submitted_answer = strtolower($submitted_answer);
    $submitted_answer = str_replace(" ", "", $submitted_answer);

    date_default_timezone_set('America/New_York');

    $datetime = date('Y-m-d H:i:s');
    file_put_contents("haunted_ottawa_".$level.".txt", $datetime." ip: ".get_client_ip()."user: ".$g_user['username']." answer: ".$submitted_answer."\n", FILE_APPEND);

    if (is_array($answer))
    {
      $rightanswer=FALSE;
      foreach ($answer as $a)
      {
        if ($a==$submitted_answer)
        {
          $rightanswer=TRUE;
        }
      }
    } else if ($answer==$submitted_answer) {
      $rightanswer= TRUE;
    }

    if ($rightanswer)
    {
        // before we redirect to the next level
        // we need to set the level in the db for the user
        setLevel($g_udid, $level+1);
        header('Location: '."level".($level+1).".php?udid=".$g_udid);
    } else {
      $error = "<font color=red> wrong answer </font>";
    }

}

?>
<!DOCTYPE html>
<html>
<title>Haunted Ottawa - <?php echo $title; ?> </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<style>
body,h1 {font-family: "Raleway", sans-serif}
body, html {height: 100%}
.bgimg {
  background-image: url('/smoke.jpeg');
  min-height: 100%;
  background-position: center;
  background-size: cover;
}

.outer-div
{
     display: table;
     position: absolute;
     top: 150px;
     height: 50%;
     width: auto;
}
.mid-div
{
     display: table-cell;
     vertical-align: middle;
}
.center-div
{
     margin: 0 auto;
     width: auto;
     height: auto;
}

</style>
<body>

<div class="bgimg w3-display-container w3-animate-opacity w3-text-white">
  <div class=" w3-padding-large w3-xlarge">
    <?php
     echo $title;

     // exclude login for leaderboards
     // exlude leaderboard button on leaderboard page
     if (strpos($_SERVER['REQUEST_URI'], "leaderboard.php") == false)
     {
       include "googlelogin.php";
       if ($error)
       {
        echo "<BR>".$error;
      }
      echo "<br><BR>";
      echo "<a class=\"w3-button w3-light-grey\" href=\"leaderboard.php\">Leaderboard</a>";
      $disable_cluediv_bydefault = 1;
    } else {
      $disable_cluediv_bydefault = 0;
    }

    ?>

  </div>

 <div id=cluediv style="display: <?php if ($disable_cluediv_bydefault) {echo "none"; } else { echo "block";} ?>;" class="outer-div" >
    <!-- <h1 class="w3-jumbo w3-animate-top">HAUNTED OTTAWA</h1>
    <hr class="w3-border-grey" style="margin:auto;width:40%">
    <p class="w3-large w3-center">Oct 5 2019</p> -->
 <div class="mid-div">
 <div  class="center-div" style="background-color:black; border: 3px solid white;">
   <div style="margin: 20px;">
<?php
    // display the level buttons up to the level the person is on
function displayLevelButton ($lvl, $url)
{
    echo "<a class=\"w3-button w3-light-grey\" onclick='window.location.href=\"".$url."\"'>".$lvl."</a>\n";
    echo "&nbsp";
}
  // ideally it would be nice to get the level out of theh database
  // but we don't have googleid in PHP here. (that's all in JS)
  // so instead ....
  // if the user is succesfully displaying this page - they've made it this far.
  // so we can display all the buttons up to this page
  for ($l = 1; $l <= $g_user['level']; $l++)
  {
    displayLevelButton($l, "level".$l.".php?udid=".$g_udid);
  }

?>


     <?php    echo $body;  ?>

       <BR>
         <!-- TODO: hint image names are visible in the source -->
         <?php
         function displayHintButton($h, $t, $bt, $num)
         {
           global $level;
           if ($h || $t)
           {
             $buttonText = $bt;
             $hint = $h;
             $hinttxt = addslashes($t);
           }
           echo "<a class=\"w3-button w3-light-grey\" onclick=\"onHintImg('".$hint."', '".$hinttxt."', ".$level.", ".$num.") \">".$buttonText."</a>\n";
          echo "&nbsp\n";
         }
        if ($hint1 || $hinttxt1) displayHintButton($hint1, $hinttxt1, "Hint 1", 1);
        if ($hint2 || $hinttxt2) displayHintButton($hint2, $hinttxt2, "Hint 2", 2);
        if ($hint3 || $hinttxt3) displayHintButton($hint3, $hinttxt3, "Hint 3", 3);
        if ($answer)
        {
           ?>
           <p>
           <form >
             <input type=hidden name=udid value=<?php echo $g_udid; ?> />
               <input type=text maxlength=32 name=answer />
               <input type="submit" name=action value="Submit"  />

           </form>
           <?php
        }
          ?>


    </div>

  </div>
</div>

  </div>


</div>




<!-- Modal for full size images on click-->
<div id="modal01" class="w3-modal w3-black" style="margin-left: auto; margin-right: auto; width: 100%; " onclick="this.style.display='none'">
  <span class="w3-button w3-large w3-black w3-display-topright" title="Close Modal Image"><i class="fa fa-remove"></i></span>
  <div class="w3-modal-content w3-animate-zoom w3-center w3-transparent w3-padding-64">
    <img id="img01" class="w3-image" style="width: 100%; max-width: 400px;">
    <p id="caption" class="w3-opacity w3-large"></p>
  </div>
</div>

</div>


<script>

// hints are stored in a 32 character varchar in the DB
// each character is a counter (0-9) for each hint in the game
// a set of 3 chars for each level
// left most char is level1 hint 1
// ie - 012 means on level1 hint1 clicked none, hint2 clicked once, hint3 clicked twice
function countHintForUser(level, num)
{
    console.log("incrementing hint for user "+g_googleid+" "+level+" "+num);
    jQuery.ajax({
        type: "POST",
        url: 'dbajax.php',
        dataType: 'json',
        data: {functionname: 'incrementHintByIndexAjax', arguments: [g_googleid, level, num]}
        })
        .done(function (data) {
                      console.log("worked");
                      console.log(data);
                })
        .fail(function (data) {
                console.log("fail");
                console.log(data);
              });

}


// Modal Image Gallery
function onHintImg(img, txt, level, num) {
  var captionText = document.getElementById("caption");
  captionText.innerHTML = txt;
  document.getElementById("img01").src = img;
  document.getElementById("modal01").style.display = "block";
  countHintForUser(level, num);
}

// Change style of navbar on scroll
// window.onscroll = function() {myScrollFunction()};
// function myScrollFunction() {
//     var navbar = document.getElementById("myNavbar");
//     if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
//         navbar.className = "w3-bar" + " w3-card" + " w3-animate-top" + " w3-white";
//     } else {
//         //navbar.className = navbar.className.replace(" w3-card w3-animate-top w3-white", "");
//     }
// }

// Used to toggle the menu on small screens when clicking on the menu button
function toggleFunction() {
    var x = document.getElementById("navDemo");
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}
</script>
</body>
</html>
