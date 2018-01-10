<!doctype html>
<html>

<head>
<meta charset="utf-8">
<title>Localizando GPS</title>
</head>

<body>
<h4>Posicion actual de caja segura y blindado</h4>

<?php

$agente = htmlspecialchars($_GET['agent']);
$alerta = htmlspecialchars($_GET['error']);
$latitud = htmlspecialchars($_GET['lat']);
$longitud = htmlspecialchars($_GET['long']);

$h = "5";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -ñ $hm = $h * 60-6;
$hm = $h* 60;
$ms = ($hm * 60)-5;
$tiempo = gmdate("His", time()-($ms));

echo "Alerta recibida a las: [", $tiempo, "]"."<br>";
if ($agente == 0) $agente="caja";
else $agente="carro";
echo "Emitida por el agente: [", $agente, "]"."<br>";
echo "Coordenada GPS recibida: [", $latitud, ",", $longitud, "]"."<br>";
echo "La alerta reportada es una alerta tipo: [", $alerta, "]"."<br>";

$myFile = "C:\Users\Administrator\Downloads\alertas.txt";

if (!file_exists($myFile)) {
  print 'File not found';
}
else if(!$fh = fopen($myFile, 'a')) {
  print 'Can\'t open file';
}
else {
  print 'Success open file';
  if($latitud != '' && $longitud != '' && $latitud < 0 && $latitud > -19 && $longitud < -68 && $longitud > -82)
  {
  	$stringData= "$agente,$tiempo,$alerta,$latitud,$longitud\r\n";
  	fwrite($fh, $stringData);
  }
  fclose($fh);
}
?> 

</body>
</html>