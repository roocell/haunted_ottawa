<?php
require_once "db.php";

$data = getAllUsersSortedByLevel();

$title = "Leaderboard";

$body = "<div style=\"width: 300px;\" >";
$i = 0;
foreach ($data as $user)
{
  if ($user['googleid'] == "100068371984256379855") continue;
  $i++;

  $hintscore = 0;
  $varchar = $user['hints'];
  for ($h=0; $h<32; $h++)
  {
    $char = substr($varchar, $h, 1);
    if ($char != "0")
    {
      $upscore = pow(10, $h%3);
      //$upscore = 1;
      $hintscore+=$upscore;
    }
  }

  $body .= "<b>".$i.".</b> ".$user['username']." (Level ".$user['level']."), Hint Score: ".$hintscore;
  $body .= "<BR>";
}
$body .= "</div>";

include 'template.php'


?>
