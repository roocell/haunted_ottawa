<?php

include "db.php";



// AJAX style calls from jQuery

header('Content-Type: application/json');
$aResult = array();
if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }
if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }
if( !isset($aResult['error']) ) {
   switch($_POST['functionname']) {
       case 'getUserAjax':
          if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 1) ) {
              $aResult['error'] = 'Error in arguments!';
          }
          else {
              $aResult['result'] = getUserAjax($_POST['arguments'][0]);
          }
          break;
        case 'insertUserAjax':
           if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
               $aResult['error'] = 'Error in arguments!';
           }
           else {
               $aResult['result'] = insertUserAjax($_POST['arguments'][0], $_POST['arguments'][1]);
           }
           break;
         case 'incrementHintByIndexAjax':
            if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
                $aResult['error'] = 'Error in arguments!';
            }
            else {
                $aResult['result'] = incrementHintByIndexAjax($_POST['arguments'][0], $_POST['arguments'][1], $_POST['arguments'][2]);
            }
            break;
          case 'setLevelAjax':
             if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
                 $aResult['error'] = 'Error in arguments!';
             }
             else {
                 $aResult['result'] = setLevelAjax($_POST['arguments'][0], $_POST['arguments'][1]);
             }
             break;
       default:
          $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
          break;
   }
}
echo json_encode($aResult);

function getUserAjax($googleid)
{
  global $database;
  global $g_googleid;
  $data = $database->select('users', [
    'googleid',
    'username',
    'udid',
    'level'
  ], [
      'googleid' => $googleid,
      LIMIT => 1
  ]);

  return $data;
}

function insertUserAjax($googleid, $username)
{
  global $database;

  $udid = uniqid($googleid, TRUE);

  $database->insert('users', [
    'username' => $username,
    'googleid' => $googleid,
    'udid' => $udid
  ]);
  return '{"operation":"success"}';

}

// level=1..10, hint=1..3
function incrementHintByIndexAjax($googleid, $level, $hint)
{
  global $database;

  // get varchar
  $data = $database->select('users', [
    'hints'
  ], [
      'googleid' => $googleid,
      LIMIT => 1
  ]);
  $varchar = $data[0]['hints'];

  // get char based on level/hint
  $char_index = (($level-1)*3+$hint);
  $char = substr($varchar, $char_index-1, 1);
  $char++;
  if ($char >= 9) $char = 9;

  // write back to varchar
  $temp = substr($varchar, 0, ($char_index)?$char_index-1:0);
  $temp .= $char;
  $temp .= substr($varchar, $char_index, 32-$char_index);

  $data = $database->update('users', [
    'hints' => $temp
  ], [
      'googleid' => $googleid
  ]);
  return $data;
}


function setLevelAjax($googleid, $level)
{
  global $database;
  $data = $database->update('users', [
    'level' => $level
  ], [
      'googleid' => $googleid
  ]);
  return '{"operation":"success"}';
}



?>
