<!doctype html>
<html>

<head>
<meta charset="utf-8">
<title>Localizando GPS</title>
</head>

<body>
<h4>Grabar posicion actual de caja segura</h4>

<?php

//<!--?php
//$latitud = htmlspecialchars($_POST['lat']);
//$longitud = htmlspecialchars($_POST['long']);
//echo 'Coordenada GPS recibida: [', $latitud, ',', $longitud, ']';
//?-->

//$mensajeXMLrecv =
//"<!--?xml version='1.0' encoding='UTF-8'?-->
//<alerta>
//<x>-12.158170</x>
//<y>-76.994778</y>
//<msg>Reminder</msg>
//</alerta>";

//$mensajeXMLrecv = $_POST['alertaxml'];
//$xml=simplexml_load_string($mensajeXMLrecv) or die("Error: Cannot create object");
//print_r($xml);
//$latitud = htmlspecialchars($xml->x);
//$longitud = htmlspecialchars($xml->y);

$latitud0 = htmlspecialchars($_GET['lat0']);
$longitud0 = htmlspecialchars($_GET['long0']);
$latitud1 = htmlspecialchars($_GET['lat1']);
$longitud1 = htmlspecialchars($_GET['long1']);
$latitud2 = htmlspecialchars($_GET['lat2']);
$longitud2 = htmlspecialchars($_GET['long2']);
$latitud3 = htmlspecialchars($_GET['lat3']);
$longitud3 = htmlspecialchars($_GET['long3']);
$latitud4 = htmlspecialchars($_GET['lat4']);
$longitud4 = htmlspecialchars($_GET['long4']);
$latitud5 = htmlspecialchars($_GET['lat5']);
$longitud5 = htmlspecialchars($_GET['long5']);
$latitud6 = htmlspecialchars($_GET['lat6']);
$longitud6 = htmlspecialchars($_GET['long6']);
$latitud7 = htmlspecialchars($_GET['lat7']);
$longitud7 = htmlspecialchars($_GET['long7']);
$latitud8 = htmlspecialchars($_GET['lat8']);
$longitud8 = htmlspecialchars($_GET['long8']);
$latitud9 = htmlspecialchars($_GET['lat9']);
$longitud9 = htmlspecialchars($_GET['long9']);

$ultimasAlertas = htmlspecialchars($_GET['alertas']);


//echo 'Coordenada GPS recibida: [', $latitud, ',', $longitud, ']';
//Limitado a la region de Peru
echo 'Coordenadas GPS recibidas';
$stringData = '';
$stringLog = '';
$stringAlertLog = '';
$stringRawLog = '';

$h = "5";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -ñ $hm = $h * 60-6;
$hm = $h* 60;
$ms = ($hm * 60)-5;
$tiempo = gmdate("H:i:s", time()-($ms));

$stringRawLog =  "$tiempo, $latitud0, $longitud0\r\n"; 
$stringRawLog .= "$tiempo, $latitud2, $longitud2\r\n";
$stringRawLog .= "$tiempo, $latitud1, $longitud1\r\n"; 
$stringRawLog .= "$tiempo, $latitud3, $longitud3\r\n";
$stringRawLog .= "$tiempo, $latitud4, $longitud4\r\n"; 
$stringRawLog .= "$tiempo, $latitud5, $longitud5\r\n";
$stringRawLog .= "$tiempo, $latitud6, $longitud6\r\n"; 
$stringRawLog .= "$tiempo, $latitud7, $longitud7\r\n"; 
$stringRawLog .= "$tiempo, $latitud8, $longitud8\r\n";
$stringRawLog .= "$tiempo, $latitud9, $longitud9\r\n"; 
$stringRawLog .= "$tiempo, Alertas: $ultimasAlertas, $latitud7, $longitud7\r\n";

if($latitud0 != '' && $longitud0 != '' && $latitud0 < 0 && $latitud0 > -19 && $longitud0 < -68 && $latitud0 > -82) 
{
	$stringData =  "$latitud0, $longitud0\r\n"; 
	$stringLog =  "$tiempo, $latitud0, $longitud0\r\n"; 
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud1 != '' && $longitud1 != '' && $latitud1 < 0 && $latitud1 > -19 && $longitud1 < -68 && $latitud1 > -82)
{
	$stringData .= "$latitud1, $longitud1\r\n"; 
	$stringLog .= "$tiempo, $latitud1, $longitud1\r\n"; 
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud2 != '' && $longitud2 != '' && $latitud2 < 0 && $latitud2 > -19 && $longitud2 < -68 && $latitud2 > -82)
{
	$stringData .= "$latitud2, $longitud2\r\n"; 
	$stringLog .= "$tiempo, $latitud2, $longitud2\r\n";
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud3 != '' && $longitud3 != '' && $latitud3 < 0 && $latitud3 > -19 && $longitud3 < -68 && $latitud3 > -82)
{
	$stringData .= "$latitud3, $longitud3\r\n"; 
	$stringLog .= "$tiempo, $latitud3, $longitud3\r\n";
}

if($latitud4 != '' && $longitud4 != '' && $latitud4 < 0 && $latitud4 > -19 && $longitud4 < -68 && $latitud4 > -82) 
{	
	$stringData .= "$latitud4, $longitud4\r\n"; 
	$stringLog .= "$tiempo, $latitud4, $longitud4\r\n"; 
}                                                                                                                                                                                           

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud5 != '' && $longitud5 != '' && $latitud5 < 0 && $latitud5 > -19 && $longitud5 < -68 && $latitud5 > -82) 
{
	$stringData .= "$latitud5, $longitud5\r\n"; 
	$stringLog .= "$tiempo, $latitud5, $longitud5\r\n";
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud6 != '' && $longitud6 != '' && $latitud6 < 0 && $latitud6 > -19 && $longitud6 < -68 && $latitud6 > -82) 
{
	$stringData .= "$latitud6, $longitud6\r\n"; 
	$stringLog .= "$tiempo, $latitud6, $longitud6\r\n"; 
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud7 != '' && $longitud7 != '' && $latitud7 < 0 && $latitud7 > -19 && $longitud7 < -68 && $latitud7 > -82)
{
	$stringData .= "$latitud7, $longitud7\r\n"; 
	$stringLog .= "$tiempo, $latitud7, $longitud7\r\n"; 
}	

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud8 != '' && $longitud8 != '' && $latitud8 < 0 && $latitud8 > -19 && $longitud8 < -68 && $latitud8 > -82) 
{
	$stringData .= "$latitud8, $longitud8\r\n"; 
	$stringLog .= "$tiempo, $latitud8, $longitud8\r\n";
}

$tiempo = gmdate("H:i:s", time()-($ms));
if($latitud9 != '' && $longitud9 != '' && $latitud9 < 0 && $latitud9 > -19 && $longitud9 < -68 && $latitud9 > -82) 
{
	$stringData .= "$latitud9, $longitud9\r\n";
	$stringLog .= "$tiempo, $latitud9, $longitud9\r\n"; 
}

if($ultimasAlertas != '0')
{
	$stringAlertLog .= "$tiempo, $ultimasAlertas, $latitud7, $longitud7\r\n";
}

$myFile = "C:\Users\Administrator\Downloads\coordenadas.txt";
$myLogfile = "C:\Users\Administrator\Downloads\logProyecto.txt";
$myAlertLogfile = "C:\Users\Administrator\Downloads\logAlertasProyecto.txt";
$myRawLogfile = "C:\Users\Administrator\Downloads\rawlogProyecto.txt";

if (!file_exists($myFile)) {
  print 'File not found';
}
else if(!$fh = fopen($myFile, 'a')) {
  print 'Can\'t open file';
}
else {
  print 'Success open file';
  if($stringData != '') fwrite($fh, $stringData);
}

//fopen($myFile, 'w') or die("can't open file");
//file_put_contents("/home/pm/Downloads/coordenadas.txt", $stringdata,FILE_APPEND);
fclose($fh);

if (!file_exists($myLogfile)) {
  print 'Log not found';
}
else if(!$fh2 = fopen($myLogfile, 'a')) {
  print 'Can\'t open log file';
}
else {
  print 'Success opening log file';
  if($stringLog != '') fwrite($fh2, $stringLog);
}
fclose($fh2);


if (!file_exists($myAlertLogfile)) {
  print 'Log not found';
}
else if(!$fh3 = fopen($myAlertLogfile, 'a')) {
  print 'Can\'t open log file';
}
else {
  print 'Success opening log file';
  if($stringAlertLog != '') fwrite($fh3, $stringAlertLog);
}
fclose($fh3);


if (!file_exists($myRawLogFile)) {
  print 'File not found';
}
else if(!$fh4 = fopen($myRawLogFile, 'a')) {
  print 'Can\'t open file';
}
else {
  print 'Success open file';
  fwrite($fh4, $stringRawLog);
}
fclose($fh4);

?> 


</body>
</html>
