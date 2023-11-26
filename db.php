<?php
// use this as an include in PHP

include "Medoo.php";


use Medoo\Medoo;


// Initialize
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'goldflushcanada',
    'server' => 'localhost',
    'username' => '',
    'password' => ''
]);

function getAllUsersSortedByLevel()
{
  global $database;
  $data = $database->select('users', [
    'googleid',
    'username',
    'level',
    'hints'
  ], [
    "ORDER" => ["level" => "DESC"]
  ]);

  return $data;
}

function getUserByUdid($udid)
{
  global $database;
  $data = $database->select('users', [
    'username',
    'level',
  ], [
    'udid' => $udid
  ]);

  return $data[0];
}

function setLevel($udid, $level)
{
  global $database;
  $data = $database->update('users', [
    'level' => $level
  ], [
      'udid' => $udid
  ]);
  return $data;
}

?>
