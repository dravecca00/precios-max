<?php

$ean = $_GET['ean'];
$dbHost = 'localhost';
$dbUsername = 'damian';
$dbPassword = 'Damian200';
$dbName = 'adelco';

   //connect with the database
$db = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);
$db->set_charset("utf8");

$salida = array();
$parajs = array();
$x1 = array();
$y1 = array();

$e = $_GET['ean'];
if(empty($e)){ $e  = 4005900071279;}

$sql = "SELECT * FROM articulos
    where id = $e 
    LIMIT 1";

$query = $db->query($sql);
$row= $query->fetch_assoc();
$nombre_producto = $row['nombre'];
$presentacion_producto = $row['presentacion'];

    
$sql = "SELECT * FROM precio_productos p
    right JOIN sucursales s on s.banderaId=p.banderaId
    where p.producto = $e 
    order by p.f_act desc
    LIMIT 6000";

$sql = "SELECT * FROM precio_productos p
    where p.producto = $e 
    order by p.f_act asc
    LIMIT 6000";




$query = $db->query($sql);
while($row= $query->fetch_assoc()){
    //$salida[$row['banderaDescripcion']][]=[$row['precioLista'], $row['sucursalNombre'], $row['localidad'], $row['f_act']];
    $parajs[]=
    [
    "precioLista" => $row['precioLista'],
    "f_act" => date("ymd",strtotime($row['f_act']))
    ];
    $y1[$row['banderaId']][] = intval($row['precioLista']);
    //$y1[$row['banderaId']][] = date("ymd",strtotime($row['f_act']));
    $x1[$row['banderaId']][] = strval(date('Y-m-d',strtotime($row['f_act'])));

}

function encontrarBandera($b, $db){
    $sql = "SELECT * FROM sucursales
    where banderaId = $b 
    order by f_act desc
    LIMIT 1";
  
  $query = $db->query($sql);
  $row= $query->fetch_assoc();
  return $row;
}

//var_dump(encontrarBandera(3));
/*
foreach($data as $dat){
  $x1[] = $dat[0];
  $y1[] = $dat[1];
}

*/
    /*    x1 = [1,2,3];
    y1 =[1,2,3];

function ladata(ean){
      $.getJSON("historico.php?ean=" + ean, function(json) {
      console.log(json);
      return json;
    });
}

var elarr = ladata("7790060054961");

console.log(elarr);

elarr.forEach(function(entry){
       x1.push(parseInt(entry[0]));
       y1.push(parseInt(entry[1]));
      });
*/

?>

<!doctype html>
<html>

<head>
	<title><?php echo $nombre_producto;?></title>
	<script src="plotly-latest.min.js"></script>
</head>

<body>
	<div id="tester" >

	</div>


	<script>
<?php 
$tr = array();
foreach(array_keys($x1) as $item){
  $nam = encontrarBandera($item,$db);
  $it = "trace".$item;
  $tr[]=$it;
   ?>
	var <?php echo $it;?> = {
  x: ["<?php echo implode('","',$x1[$item]); ?>"],
  y: [<?php echo implode(',',$y1[$item]); ?>],
  mode: 'markers',
  type: 'scatter',
  name: '<?php echo $nam['banderaDescripcion'];?>',
  //text: ['A-1', 'A-2', 'A-3', 'A-4', 'A-5'],
  marker: { size: 12 }
};

<?php } ?>

var data = [<?php echo(implode(',',$tr));?>];

var layout = {
  xaxis: {
    autorange: true,
    type: 'date',
    title: 'fecha'
  },
  yaxis: {
    title: 'Precio',
    autorange: true
  },
  title:'<?php echo $nombre_producto;?>'
};

Plotly.newPlot('tester', data, layout);

</script>
</body>

</html>
