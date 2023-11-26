<?php
// expect
// $url, $thiskey
// $nexturl, $nextkey
// $title
// $hint1, $hint2, $hint3
// $hinttxt1, $hinttxt2, $hinttxt3
// $body
// $answer

$k=$_REQUEST['key'];
if ($k != $thiskey && $thiskey!="start")
{
  echo "stop you hacker";
  exit();
}


echo $title;

$action=$_REQUEST['action'];



switch($action)
{
  case "Hint 1":
    $hintimg = $hint1;
    $hinttxt = $hinttxt1;
    break;
  case "Hint 2":
    $hintimg = $hint2;
    $hinttxt = $hinttxt2;
    break;
  case "Hint 3":
    $hintimg = $hint3;
    $hinttxt = $hinttxt3;
    break;

  default:
    $hintimg=0;
    break;
}
$a = $_REQUEST['answer'];
if ($a)
{
    $hintimg = 0;
    $a = strtolower($a);
    $a = str_replace(" ", "", $a);
    if ($answer==$a)
    {
        $newURL=$nexturl."?key=".$nextkey;
        header('Location: '.$newURL);
    } else {
        echo "<font color=red> wrong answer </font>";
        echo "<BR>";
    }

}

echo "<p>".$body;
?>


<!-- hint buttons -->

<form action="<?php echo $url ?>">
  <input type=hidden name=key value=<?php echo $thiskey; ?> />
    <input type="submit" name=action value="Hint 1"  />
    <input type="submit" name=action value="Hint 2"  />
    <input type="submit" name=action value="Hint 3"  />

    <input type=text name=answer />
    <input type="submit" name=action value="Submit"  />

</form>
<br>
<?php

if ($hintimg)
{
  echo "<img height=100% src=".$hintimg."> </img>";
}
if ($hinttxt)
{
  echo "<BR>";
  echo $hinttxt;
}
?>
