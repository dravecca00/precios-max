<?php
error_reporting(E_ALL ^ E_NOTICE);
$dbHost = 'localhost';
$dbUsername = '';
$dbPassword = '';
$dbName = '';

   //connect with the database
$db = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);
$db->set_charset("utf8");
$e = $_GET['ean'];
if(empty($e)){ $e  = 4005900071279;}

/* 
$sql = "SELECT * FROM precio_productos p
where p.producto = $e 
LIMIT 100";
*/
    
$sql = "SELECT * FROM precio_productos p
    JOIN sucursales s on s.banderaId=p.banderaId
    where p.producto = $e 
    order by p.f_act asc
    LIMIT 3";

$salida = array();
$parajs = array();

$query = $db->query($sql);
while($row= $query->fetch_assoc()){
    $salida[$row['banderaDescripcion']][]=[$row['precioLista'], $row['sucursalNombre'], $row['localidad'], $row['f_act']];
    $parajs[]=
    [
    "precioLista" => $row['precioLista'],
    "f_act" => date("ymd",strtotime($row['f_act']))
    ];

}

echo $parajs;
//header('Content-Type: application/json; charset=utf-8');
//echo json_encode($parajs);
/*
echo("<pre>");
var_dump($salida);
*/



////////////////////////////////////////////////////////////
function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}


















?>
