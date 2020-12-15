<?php
error_reporting(E_ALL ^ E_NOTICE);

if(empty($_GET['provincia'])){
    $provincia = 'buenos-aires';
}else{
    $provincia = slugify($_GET['provincia']);
}
//echo "<pre>".$_GET['ean']."\r\n";
//echo $provincia."\r\n";
//$a = file_get_contents("https://preciosmaximos.argentina.gob.ar/api/products?pag=1&Provincia=".urlencode($provincia)."&regs=2010");

$arr = json_decode(file_get_contents($provincia.".json"), true);

$results = array_filter($arr['result'], function($producto) {
    return $producto['id_producto'] == $_GET['ean'];
  });

if(!empty($results)){
    $results = array_values($results)[0];
    $arr = $results['Precio sugerido'];
    $preciomax = "$ ".money_format('%i',$arr);
}else{
    $preciomax="no encontrado";
}

//var_dump($results);

echo "Precios Max: ".$preciomax."\r\n";
$simap = file_get_contents("https://simap.gba.gob.ar/consumidor_detalle.php?e=".$_GET['ean']);


if($pos =strpos($simap,"<span class=\"map-precio\"><small>$</small>")+strlen("<span class=\"map-precio\"><small>$</small>")){
   
    $a =floatval(substr($simap, $pos ,10));

    $temp = explode('.',$a);
    if(strlen($temp[1]==1)){$a=$a.'0';}
   
    $simap = "$ ".number_format($a, 2);
   //$simap = $output;
}else{
 $simap = "no encontrado";
}
//var_dump($a);

echo "Bs As Simap: ".$simap."\r\n";

$arr = json_decode(file_get_contents("precios-cuidados.json"), true);

$results = array_filter($arr['feed']['entry'], function($producto) {
    return $producto['gsx$ean']['$t'] == $_GET['ean'];
  });

if($r = array_values($results)[0]){
    echo "\rPrecios Cuidados: \r
    - Amba ".$r['gsx$amba']['$t']." \r
    - BsAs ".$r['gsx$pba']['$t']." \r
    - Centro Cuyo ".$r['gsx$centro-cuyo']['$t']." \r
    - NOA NEA ".$r['gsx$noa-nea']['$t']." \r
    - Patagonia ".$r['gsx$patagonia']['$t'];
}else{
    echo "\rPrecios Cuidados: no encontrado";
}


//var_dump($arr['feed']['entry']);


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