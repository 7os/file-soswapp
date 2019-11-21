<?php
namespace TymFrontiers;
use \TymFrontiers\HTTP\Header;
require_once "app.init.php";
require_once APP_BASE_INC;

$gen = new Generic;
$params = $gen->requestParam(
  [
    "fname" =>["fname","text",3,256],
    "fid" =>["fid","int"],
    "dl" =>["dl","boolean"]
  ],
  "get",[]);
if( empty($params['fname']) && empty($params['fid']) ){
  Header::badRequest();
}
$query = "SELECT * FROM :db:.`:tbl:` ";
if (!empty($params['fname'])) $query .= " WHERE _name='{$database->escapeValue($params['fname'])}' ";
if (!empty($params['fid'])) $query .= " WHERE id={$params['fid']} ";
$query .= " LIMIT 1";
$file = File::findBySql($query);
if( !$file ) Header::notFound();
$file = $file[0];
// record download
$ext = \pathinfo($file->fullPath(),PATHINFO_EXTENSION);
// var_dump($file);
if( !\file_exists($file->fullPath()) ) Header::notFound(false);
\header("Content-Type: {$file->type()}");
\header("Content-Length: " . $file->size());
if( !empty($_GET['dl']) &&  (bool)$_GET['dl'] ){
  \header('Content-Disposition: attachment;filename="'.$file->nice_name.".".$ext.'"');
}
\readfile($file->fullPath());
exit;
