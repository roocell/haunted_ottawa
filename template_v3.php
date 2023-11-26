<?php
require_once "db.php";

// expect
// $level
// $url, $thiskey
// $nexturl, $nextkey
// $title
// $hint1, $hint2, $hint3
// $hinttxt1, $hinttxt2, $hinttxt3
// $body
// $answer

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

$k=$_REQUEST['key'];
if ($k != $thiskey && $thiskey!="start")
{
  echo "stop you hacker";
  exit();
}


$action=$_REQUEST['action'];
$a = $_REQUEST['answer'];
if ($a)
{
    $hintimg = 0;
    $a = strtolower($a);
    $a = str_replace(" ", "", $a);

    date_default_timezone_set('America/New_York');

    $datetime = date('Y-m-d H:i:s');
    file_put_contents("haunted_ottawa_".$title.".txt", $datetime." ip: ".get_client_ip()." answer: ".$a."\n", FILE_APPEND);

    if ($answer==$a)
    {
        $newURL=$nexturl."?key=".$nextkey;
        header('Location: '.$newURL);
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
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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
       include "googlelogin.php";
      if ($error)
      {
        echo "<BR>".$error;
      }

    ?>
    <br><BR>
    <?php
    if (strpos($_SERVER['REQUEST_URI'], "leaderboard.php") == false)
    {
    ?>
    <a class="w3-button w3-light-grey" href=leaderboard.php>Leaderboard</a>
  <?php } ?>
  </div>

 <div id=cluediv style="display: none;" class="outer-div" >
    <!-- <h1 class="w3-jumbo w3-animate-top">HAUNTED OTTAWA</h1>
    <hr class="w3-border-grey" style="margin:auto;width:40%">
    <p class="w3-large w3-center">Oct 5 2019</p> -->
 <div class="mid-div">
 <div  class="center-div" style="background-color:black; border: 3px solid white;">
   <div style="margin: 20px;">

     <?php    echo $body;  ?>

       <BR>
         <!-- TODO: hint image names are visible in the source -->
         <?php
         function displayHintButton($h, $t, $bt)
         {
           if ($h || $t)
           {
             $buttonText = $bt;
             if ($h)
             {
               $hint = $h;
             } else {
               $hinttxt = $t;
             }
           }

           if ($hint)
           {
             echo "<a class=\"w3-button w3-light-grey\" onclick=\"onHintImg('".$hint."') \">".$buttonText."</a>\n";
           } else {
             echo "<a class=\"w3-button w3-light-grey\" onclick=\"onHintTxt('".$hinttxt."')\" >".$buttonText."</a>\n";
           }
            echo "&nbsp";
         }

        if ($hint1 || $hinttxt1) displayHintButton($hint1, $hinttxt1, "Hint 1");
        if ($hint2 || $hinttxt2) displayHintButton($hint2, $hinttxt2, "Hint 2");
        if ($hint3 || $hinttxt3) displayHintButton($hint3, $hinttxt3, "Hint 3");
        if ($answer)
        {
           ?>
           <p>
           <form >
             <input type=hidden name=key value=<?php echo $thiskey; ?> />
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
<!--
  <div class="w3-display-bottomleft w3-padding-large">
    Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">roocell</a>
  </div> -->




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

function countHintForUser()
{
    console.log("incrementing hint for user "+g_googleid);
    jQuery.ajax({
        type: "POST",
        url: 'dbajax.php',
        dataType: 'json',
        data: {functionname: 'incrementHint', arguments: [g_googleid]}
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
function onHintImg(img) {
  var captionText = document.getElementById("caption");
  captionText.innerHTML = "";
  document.getElementById("img01").src = img;
  document.getElementById("modal01").style.display = "block";
  countHintForUser();
}
function onHintTxt(text) {
  document.getElementById("img01").src = "";
  document.getElementById("modal01").style.display = "block";
  var captionText = document.getElementById("caption");
  captionText.innerHTML = text;
  countHintForUser();
}

// Change style of navbar on scroll
window.onscroll = function() {myFunction()};
function myFunction() {
    var navbar = document.getElementById("myNavbar");
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        navbar.className = "w3-bar" + " w3-card" + " w3-animate-top" + " w3-white";
    } else {
        navbar.className = navbar.className.replace(" w3-card w3-animate-top w3-white", "");
    }
}

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
