<!doctype html>
<html>

<head>
<meta charset="utf-8" http-equiv="refresh" content="10">

<title>Localizando GPS</title>
</head>

<body>
<h4>Posicion actual de caja segura y blindado</h4>

<?php

$nc = 400;

$lonarray = array($nc);
$latarray = array($nc);

$i=0;

//$latitud = htmlspecialchars($_GET['lat']);
//$longitud = htmlspecialchars($_GET['long']);

//echo 'Coordenada GPS recibida: [', $latitud, ',', $longitud, ']';
$myFile = "C:\Users\Administrator\Downloads\coordenadas.txt";
if (!file_exists($myFile)) {
	print 'File not found';echo "<br>";
}
else if(!$fh = fopen($myFile, 'r')) {
	print 'Can\'t open file';echo "<br>";
}
else {
    print '!!';
}

//Obtenemos ultimo registro GPS

$cursor = -1;

fseek($fh, $cursor, SEEK_END); //obtenemos fin del archivo
$char = fgetc($fh);

while($i<$nc){
	//Se obvia caracteres de nueva linea
	while ($char === "\n" || $char === "\r") {
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}

	//Se obtiene la ultima longitud
	while ($char !== " ") {
		/**
		 * Prepend the new char
		 */
		$longitud = $char . $longitud;
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}

	//Se obvia caracteres intermedios entre latitud y longitud
	while ($char === " " || $char === ",") {
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}

	//Se obtiene la ultima latitud
	while ($char !== false && $char !== "\n" && $char !== "\r" && $char !== " ") {
		/**
		 * Prepend the new char
		 */
		$latitud = $char . $latitud;
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}

	//$stringData= "$latitud, $longitud\r\n";

	//echo "Coordenada GPS nro. $i leida:";
	$lonarray[$i] = $longitud;
	$latarray[$i] = $latitud;	
	//echo '[', $latarray[$i], '|', $longarray[$i], ']'."<br>";
	$longitud = '';
	$latitud = '';
	$i = $i+1;
}

fclose($fh);

  //echo "MONITOR DE COORDENADAS:"."<br>";
  echo "Ultima Coordenada GPS recibida:";
  echo '[', $latarray[0], '|', $lonarray[0], ']';
  //."<br>";
  //$stringData= "$latitud, $longitud\r\n";
?> 

<?php
//$myFile2 = "C:\Users\Administrator\Downloads\alertas.txt";
$myFile2 = "C:\Users\Administrator\Downloads\logAlertasProyecto.txt";
if (!file_exists($myFile2)) {
  print 'File not found';echo "<br>";
  }
  else if(!$fh2 = fopen($myFile2, 'r')) {
    print 'Can\'t open file';echo "<br>";
    }
    else {
      print '!!';
      }

//Obtenemos ultimo registro de Eventualidades
$cursor = -1;

fseek($fh2, $cursor, SEEK_END); //obtenemos fin del archivo
$char2 = fgetc($fh2);

//Se obvia caracteres de nueva linea
while ($char2 === "\n" || $char2 === "\r") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene la ultima longitud
while ($char2 !== " " && $char2 !== ",") {
    /**
     * Prepend the new char
     */
    $longitud2 = $char2 . $longitud2;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obvia caracteres intermedios entre latitud y longitud
while ($char2 === " " || $char2 === ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene la ultima latitud
while ($char2 !== " " && $char2 !== ",") {
    /**
     * Prepend the new char
     */
    $latitud2 = $char2 . $latitud2;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obvia caracteres intermedios entre latitud y alertas
while ($char2 === " " || $char2 === ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene las alertas de los ultimos 10 segundos
while ($char2 !== " " && $char2 !== ",") {
    /**
     * Prepend the new char
     */
    $alertas10 = $char2 . $alertas10;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obvia caracteres intermedios entre alertas y tiempo
while ($char2 === " " || $char2 === ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene el ultimo tiempo
while ($char2 !== false && $char2 !== "\n" && $char2 !== "\r" && $char2 !== " ") {
    /**
     * Prepend the new char
     */
    $tiempo = $char2 . $tiempo;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}
fclose($fh2);
?>
<div id="map" style="width:900px;height:400px"></div>

<script>
function myMap() { 
  var latGPS00= parseFloat('<?php echo $latarray[0];?>');
  var lonGPS00= parseFloat('<?php echo $lonarray[0];?>');
  var latGPS01= parseFloat('<?php echo $latarray[1];?>');
  var lonGPS01= parseFloat('<?php echo $lonarray[1];?>');
  var latGPS02= parseFloat('<?php echo $latarray[2];?>');
  var lonGPS02= parseFloat('<?php echo $lonarray[2];?>');
  var latGPS03= parseFloat('<?php echo $latarray[3];?>');
  var lonGPS03= parseFloat('<?php echo $lonarray[3];?>');
  var latGPS04= parseFloat('<?php echo $latarray[4];?>');
  var lonGPS04= parseFloat('<?php echo $lonarray[4];?>');
  var latGPS05= parseFloat('<?php echo $latarray[5];?>');
  var lonGPS05= parseFloat('<?php echo $lonarray[5];?>');
  var latGPS06= parseFloat('<?php echo $latarray[6];?>');
  var lonGPS06= parseFloat('<?php echo $lonarray[6];?>');
  var latGPS07= parseFloat('<?php echo $latarray[7];?>');
  var lonGPS07= parseFloat('<?php echo $lonarray[7];?>');
  var latGPS08= parseFloat('<?php echo $latarray[8];?>');
  var lonGPS08= parseFloat('<?php echo $lonarray[8];?>');
  var latGPS09= parseFloat('<?php echo $latarray[9];?>');
  var lonGPS09= parseFloat('<?php echo $lonarray[9];?>');
  var latGPS10= parseFloat('<?php echo $latarray[10];?>');
  var lonGPS10= parseFloat('<?php echo $lonarray[10];?>');
  var latGPS11= parseFloat('<?php echo $latarray[11];?>');
  var lonGPS11= parseFloat('<?php echo $lonarray[11];?>');
  var latGPS12= parseFloat('<?php echo $latarray[12];?>');
  var lonGPS12= parseFloat('<?php echo $lonarray[12];?>');
  var latGPS13= parseFloat('<?php echo $latarray[13];?>');
  var lonGPS13= parseFloat('<?php echo $lonarray[13];?>');
  var latGPS14= parseFloat('<?php echo $latarray[14];?>');
  var lonGPS14= parseFloat('<?php echo $lonarray[14];?>');
  var latGPS15= parseFloat('<?php echo $latarray[15];?>');
  var lonGPS15= parseFloat('<?php echo $lonarray[15];?>');
  var latGPS16= parseFloat('<?php echo $latarray[16];?>');
  var lonGPS16= parseFloat('<?php echo $lonarray[16];?>');
  var latGPS17= parseFloat('<?php echo $latarray[17];?>');
  var lonGPS17= parseFloat('<?php echo $lonarray[17];?>');
  var latGPS18= parseFloat('<?php echo $latarray[18];?>');
  var lonGPS18= parseFloat('<?php echo $lonarray[18];?>');
  var latGPS19= parseFloat('<?php echo $latarray[19];?>');
  var lonGPS19= parseFloat('<?php echo $lonarray[19];?>');
  var latGPS20= parseFloat('<?php echo $latarray[20];?>');
  var lonGPS20= parseFloat('<?php echo $lonarray[20];?>');
  var latGPS21= parseFloat('<?php echo $latarray[21];?>');
  var lonGPS21= parseFloat('<?php echo $lonarray[21];?>');
  var latGPS22= parseFloat('<?php echo $latarray[22];?>');
  var lonGPS22= parseFloat('<?php echo $lonarray[22];?>');
  var latGPS23= parseFloat('<?php echo $latarray[23];?>');
  var lonGPS23= parseFloat('<?php echo $lonarray[23];?>');
  var latGPS24= parseFloat('<?php echo $latarray[24];?>');
  var lonGPS24= parseFloat('<?php echo $lonarray[24];?>');
  var latGPS25= parseFloat('<?php echo $latarray[25];?>');
  var lonGPS25= parseFloat('<?php echo $lonarray[25];?>');
  var latGPS26= parseFloat('<?php echo $latarray[26];?>');
  var lonGPS26= parseFloat('<?php echo $lonarray[26];?>');
  var latGPS27= parseFloat('<?php echo $latarray[27];?>');
  var lonGPS27= parseFloat('<?php echo $lonarray[27];?>');
  var latGPS28= parseFloat('<?php echo $latarray[28];?>');
  var lonGPS28= parseFloat('<?php echo $lonarray[28];?>');
  var latGPS29= parseFloat('<?php echo $latarray[29];?>');
  var lonGPS29= parseFloat('<?php echo $lonarray[29];?>');
  var latGPS30= parseFloat('<?php echo $latarray[30];?>');
  var lonGPS30= parseFloat('<?php echo $lonarray[30];?>');
  var latGPS31= parseFloat('<?php echo $latarray[31];?>');
  var lonGPS31= parseFloat('<?php echo $lonarray[31];?>');
  var latGPS32= parseFloat('<?php echo $latarray[32];?>');
  var lonGPS32= parseFloat('<?php echo $lonarray[32];?>');
  var latGPS33= parseFloat('<?php echo $latarray[33];?>');
  var lonGPS33= parseFloat('<?php echo $lonarray[33];?>');
  var latGPS34= parseFloat('<?php echo $latarray[34];?>');
  var lonGPS34= parseFloat('<?php echo $lonarray[34];?>');
  var latGPS35= parseFloat('<?php echo $latarray[35];?>');
  var lonGPS35= parseFloat('<?php echo $lonarray[35];?>');
  var latGPS36= parseFloat('<?php echo $latarray[36];?>');
  var lonGPS36= parseFloat('<?php echo $lonarray[36];?>');
  var latGPS37= parseFloat('<?php echo $latarray[37];?>');
  var lonGPS37= parseFloat('<?php echo $lonarray[37];?>');
  var latGPS38= parseFloat('<?php echo $latarray[38];?>');
  var lonGPS38= parseFloat('<?php echo $lonarray[38];?>');
  var latGPS39= parseFloat('<?php echo $latarray[39];?>');
  var lonGPS39= parseFloat('<?php echo $lonarray[39];?>');
  var latGPS40= parseFloat('<?php echo $latarray[40];?>');
  var lonGPS40= parseFloat('<?php echo $lonarray[40];?>');
  var latGPS41= parseFloat('<?php echo $latarray[41];?>');
  var lonGPS41= parseFloat('<?php echo $lonarray[41];?>');
  var latGPS42= parseFloat('<?php echo $latarray[42];?>');
  var lonGPS42= parseFloat('<?php echo $lonarray[42];?>');
  var latGPS43= parseFloat('<?php echo $latarray[43];?>');
  var lonGPS43= parseFloat('<?php echo $lonarray[43];?>');
  var latGPS44= parseFloat('<?php echo $latarray[44];?>');
  var lonGPS44= parseFloat('<?php echo $lonarray[44];?>');
  var latGPS45= parseFloat('<?php echo $latarray[45];?>');
  var lonGPS45= parseFloat('<?php echo $lonarray[45];?>');
  var latGPS46= parseFloat('<?php echo $latarray[46];?>');
  var lonGPS46= parseFloat('<?php echo $lonarray[46];?>');
  var latGPS47= parseFloat('<?php echo $latarray[47];?>');
  var lonGPS47= parseFloat('<?php echo $lonarray[47];?>');
  var latGPS48= parseFloat('<?php echo $latarray[48];?>');
  var lonGPS48= parseFloat('<?php echo $lonarray[48];?>');
  var latGPS49= parseFloat('<?php echo $latarray[49];?>');
  var lonGPS49= parseFloat('<?php echo $lonarray[49];?>');
  var latGPS50= parseFloat('<?php echo $latarray[50];?>');
  var lonGPS50= parseFloat('<?php echo $lonarray[50];?>');
  var latGPS51= parseFloat('<?php echo $latarray[51];?>');
  var lonGPS51= parseFloat('<?php echo $lonarray[51];?>');
  var latGPS52= parseFloat('<?php echo $latarray[52];?>');
  var lonGPS52= parseFloat('<?php echo $lonarray[52];?>');
  var latGPS53= parseFloat('<?php echo $latarray[53];?>');
  var lonGPS53= parseFloat('<?php echo $lonarray[53];?>');
  var latGPS54= parseFloat('<?php echo $latarray[54];?>');
  var lonGPS54= parseFloat('<?php echo $lonarray[54];?>');
  var latGPS55= parseFloat('<?php echo $latarray[55];?>');
  var lonGPS55= parseFloat('<?php echo $lonarray[55];?>');
  var latGPS56= parseFloat('<?php echo $latarray[56];?>');
  var lonGPS56= parseFloat('<?php echo $lonarray[56];?>');
  var latGPS57= parseFloat('<?php echo $latarray[57];?>');
  var lonGPS57= parseFloat('<?php echo $lonarray[57];?>');
  var latGPS58= parseFloat('<?php echo $latarray[58];?>');
  var lonGPS58= parseFloat('<?php echo $lonarray[58];?>');
  var latGPS59= parseFloat('<?php echo $latarray[59];?>');
  var lonGPS59= parseFloat('<?php echo $lonarray[59];?>');
  var latGPS60= parseFloat('<?php echo $latarray[60];?>');
  var lonGPS60= parseFloat('<?php echo $lonarray[60];?>');
  var latGPS61= parseFloat('<?php echo $latarray[61];?>');
  var lonGPS61= parseFloat('<?php echo $lonarray[61];?>');
  var latGPS62= parseFloat('<?php echo $latarray[62];?>');
  var lonGPS62= parseFloat('<?php echo $lonarray[62];?>');
  var latGPS63= parseFloat('<?php echo $latarray[63];?>');
  var lonGPS63= parseFloat('<?php echo $lonarray[63];?>');
  var latGPS64= parseFloat('<?php echo $latarray[64];?>');
  var lonGPS64= parseFloat('<?php echo $lonarray[64];?>');
  var latGPS65= parseFloat('<?php echo $latarray[65];?>');
  var lonGPS65= parseFloat('<?php echo $lonarray[65];?>');
  var latGPS66= parseFloat('<?php echo $latarray[66];?>');
  var lonGPS66= parseFloat('<?php echo $lonarray[66];?>');
  var latGPS67= parseFloat('<?php echo $latarray[67];?>');
  var lonGPS67= parseFloat('<?php echo $lonarray[67];?>');
  var latGPS68= parseFloat('<?php echo $latarray[68];?>');
  var lonGPS68= parseFloat('<?php echo $lonarray[68];?>');
  var latGPS69= parseFloat('<?php echo $latarray[69];?>');
  var lonGPS69= parseFloat('<?php echo $lonarray[69];?>');
  var latGPS70= parseFloat('<?php echo $latarray[70];?>');
  var lonGPS70= parseFloat('<?php echo $lonarray[70];?>');
  var latGPS71= parseFloat('<?php echo $latarray[71];?>');
  var lonGPS71= parseFloat('<?php echo $lonarray[71];?>');
  var latGPS72= parseFloat('<?php echo $latarray[72];?>');
  var lonGPS72= parseFloat('<?php echo $lonarray[72];?>');
  var latGPS73= parseFloat('<?php echo $latarray[73];?>');
  var lonGPS73= parseFloat('<?php echo $lonarray[73];?>');
  var latGPS74= parseFloat('<?php echo $latarray[74];?>');
  var lonGPS74= parseFloat('<?php echo $lonarray[74];?>');
  var latGPS75= parseFloat('<?php echo $latarray[75];?>');
  var lonGPS75= parseFloat('<?php echo $lonarray[75];?>');
  var latGPS76= parseFloat('<?php echo $latarray[76];?>');
  var lonGPS76= parseFloat('<?php echo $lonarray[76];?>');
  var latGPS77= parseFloat('<?php echo $latarray[77];?>');
  var lonGPS77= parseFloat('<?php echo $lonarray[77];?>');
  var latGPS78= parseFloat('<?php echo $latarray[78];?>');
  var lonGPS78= parseFloat('<?php echo $lonarray[78];?>');
  var latGPS79= parseFloat('<?php echo $latarray[79];?>');
  var lonGPS79= parseFloat('<?php echo $lonarray[79];?>');
  var latGPS80= parseFloat('<?php echo $latarray[80];?>');
  var lonGPS80= parseFloat('<?php echo $lonarray[80];?>');
  var latGPS81= parseFloat('<?php echo $latarray[81];?>');
  var lonGPS81= parseFloat('<?php echo $lonarray[81];?>');
  var latGPS82= parseFloat('<?php echo $latarray[82];?>');
  var lonGPS82= parseFloat('<?php echo $lonarray[82];?>');
  var latGPS83= parseFloat('<?php echo $latarray[83];?>');
  var lonGPS83= parseFloat('<?php echo $lonarray[83];?>');
  var latGPS84= parseFloat('<?php echo $latarray[84];?>');
  var lonGPS84= parseFloat('<?php echo $lonarray[84];?>');
  var latGPS85= parseFloat('<?php echo $latarray[85];?>');
  var lonGPS85= parseFloat('<?php echo $lonarray[85];?>');
  var latGPS86= parseFloat('<?php echo $latarray[86];?>');
  var lonGPS86= parseFloat('<?php echo $lonarray[86];?>');
  var latGPS87= parseFloat('<?php echo $latarray[87];?>');
  var lonGPS87= parseFloat('<?php echo $lonarray[87];?>');
  var latGPS88= parseFloat('<?php echo $latarray[88];?>');
  var lonGPS88= parseFloat('<?php echo $lonarray[88];?>');
  var latGPS89= parseFloat('<?php echo $latarray[89];?>');
  var lonGPS89= parseFloat('<?php echo $lonarray[89];?>');
  var latGPS90= parseFloat('<?php echo $latarray[90];?>');
  var lonGPS90= parseFloat('<?php echo $lonarray[90];?>');
  var latGPS91= parseFloat('<?php echo $latarray[91];?>');
  var lonGPS91= parseFloat('<?php echo $lonarray[91];?>');
  var latGPS92= parseFloat('<?php echo $latarray[92];?>');
  var lonGPS92= parseFloat('<?php echo $lonarray[92];?>');
  var latGPS93= parseFloat('<?php echo $latarray[93];?>');
  var lonGPS93= parseFloat('<?php echo $lonarray[93];?>');
  var latGPS94= parseFloat('<?php echo $latarray[94];?>');
  var lonGPS94= parseFloat('<?php echo $lonarray[94];?>');
  var latGPS95= parseFloat('<?php echo $latarray[95];?>');
  var lonGPS95= parseFloat('<?php echo $lonarray[95];?>');
  var latGPS96= parseFloat('<?php echo $latarray[96];?>');
  var lonGPS96= parseFloat('<?php echo $lonarray[96];?>');
  var latGPS97= parseFloat('<?php echo $latarray[97];?>');
  var lonGPS97= parseFloat('<?php echo $lonarray[97];?>');
  var latGPS98= parseFloat('<?php echo $latarray[98];?>');
  var lonGPS98= parseFloat('<?php echo $lonarray[98];?>');
  var latGPS99= parseFloat('<?php echo $latarray[99];?>');
  var lonGPS99= parseFloat('<?php echo $lonarray[99];?>');
  var latGPS100= parseFloat('<?php echo $latarray[100];?>');
  var lonGPS100= parseFloat('<?php echo $lonarray[100];?>');
  var latGPS101= parseFloat('<?php echo $latarray[101];?>');
  var lonGPS101= parseFloat('<?php echo $lonarray[101];?>');
  var latGPS102= parseFloat('<?php echo $latarray[102];?>');
  var lonGPS102= parseFloat('<?php echo $lonarray[102];?>');
  var latGPS103= parseFloat('<?php echo $latarray[103];?>');
  var lonGPS103= parseFloat('<?php echo $lonarray[103];?>');
  var latGPS104= parseFloat('<?php echo $latarray[104];?>');
  var lonGPS104= parseFloat('<?php echo $lonarray[104];?>');
  var latGPS105= parseFloat('<?php echo $latarray[105];?>');
  var lonGPS105= parseFloat('<?php echo $lonarray[105];?>');
  var latGPS106= parseFloat('<?php echo $latarray[106];?>');
  var lonGPS106= parseFloat('<?php echo $lonarray[106];?>');
  var latGPS107= parseFloat('<?php echo $latarray[107];?>');
  var lonGPS107= parseFloat('<?php echo $lonarray[107];?>');
  var latGPS108= parseFloat('<?php echo $latarray[108];?>');
  var lonGPS108= parseFloat('<?php echo $lonarray[108];?>');
  var latGPS109= parseFloat('<?php echo $latarray[109];?>');
  var lonGPS109= parseFloat('<?php echo $lonarray[109];?>');
  var latGPS110= parseFloat('<?php echo $latarray[110];?>');
  var lonGPS110= parseFloat('<?php echo $lonarray[110];?>');
  var latGPS111= parseFloat('<?php echo $latarray[111];?>');
  var lonGPS111= parseFloat('<?php echo $lonarray[111];?>');
  var latGPS112= parseFloat('<?php echo $latarray[112];?>');
  var lonGPS112= parseFloat('<?php echo $lonarray[112];?>');
  var latGPS113= parseFloat('<?php echo $latarray[113];?>');
  var lonGPS113= parseFloat('<?php echo $lonarray[113];?>');
  var latGPS114= parseFloat('<?php echo $latarray[114];?>');
  var lonGPS114= parseFloat('<?php echo $lonarray[114];?>');
  var latGPS115= parseFloat('<?php echo $latarray[115];?>');
  var lonGPS115= parseFloat('<?php echo $lonarray[115];?>');
  var latGPS116= parseFloat('<?php echo $latarray[116];?>');
  var lonGPS116= parseFloat('<?php echo $lonarray[116];?>');
  var latGPS117= parseFloat('<?php echo $latarray[117];?>');
  var lonGPS117= parseFloat('<?php echo $lonarray[117];?>');
  var latGPS118= parseFloat('<?php echo $latarray[118];?>');
  var lonGPS118= parseFloat('<?php echo $lonarray[118];?>');
  var latGPS119= parseFloat('<?php echo $latarray[119];?>');
  var lonGPS119= parseFloat('<?php echo $lonarray[119];?>');
  var latGPS120= parseFloat('<?php echo $latarray[120];?>');
  var lonGPS120= parseFloat('<?php echo $lonarray[120];?>');
  var latGPS121= parseFloat('<?php echo $latarray[121];?>');
  var lonGPS121= parseFloat('<?php echo $lonarray[121];?>');
  var latGPS122= parseFloat('<?php echo $latarray[122];?>');
  var lonGPS122= parseFloat('<?php echo $lonarray[122];?>');
  var latGPS123= parseFloat('<?php echo $latarray[123];?>');
  var lonGPS123= parseFloat('<?php echo $lonarray[123];?>');
  var latGPS124= parseFloat('<?php echo $latarray[124];?>');
  var lonGPS124= parseFloat('<?php echo $lonarray[124];?>');
  var latGPS125= parseFloat('<?php echo $latarray[125];?>');
  var lonGPS125= parseFloat('<?php echo $lonarray[125];?>');
  var latGPS126= parseFloat('<?php echo $latarray[126];?>');
  var lonGPS126= parseFloat('<?php echo $lonarray[126];?>');
  var latGPS127= parseFloat('<?php echo $latarray[127];?>');
  var lonGPS127= parseFloat('<?php echo $lonarray[127];?>');
  var latGPS128= parseFloat('<?php echo $latarray[128];?>');
  var lonGPS128= parseFloat('<?php echo $lonarray[128];?>');
  var latGPS129= parseFloat('<?php echo $latarray[129];?>');
  var lonGPS129= parseFloat('<?php echo $lonarray[129];?>');
  var latGPS130= parseFloat('<?php echo $latarray[130];?>');
  var lonGPS130= parseFloat('<?php echo $lonarray[130];?>');
  var latGPS131= parseFloat('<?php echo $latarray[131];?>');
  var lonGPS131= parseFloat('<?php echo $lonarray[131];?>');
  var latGPS132= parseFloat('<?php echo $latarray[132];?>');
  var lonGPS132= parseFloat('<?php echo $lonarray[132];?>');
  var latGPS133= parseFloat('<?php echo $latarray[133];?>');
  var lonGPS133= parseFloat('<?php echo $lonarray[133];?>');
  var latGPS134= parseFloat('<?php echo $latarray[134];?>');
  var lonGPS134= parseFloat('<?php echo $lonarray[134];?>');
  var latGPS135= parseFloat('<?php echo $latarray[135];?>');
  var lonGPS135= parseFloat('<?php echo $lonarray[135];?>');
  var latGPS136= parseFloat('<?php echo $latarray[136];?>');
  var lonGPS136= parseFloat('<?php echo $lonarray[136];?>');
  var latGPS137= parseFloat('<?php echo $latarray[137];?>');
  var lonGPS137= parseFloat('<?php echo $lonarray[137];?>');
  var latGPS138= parseFloat('<?php echo $latarray[138];?>');
  var lonGPS138= parseFloat('<?php echo $lonarray[138];?>');
  var latGPS139= parseFloat('<?php echo $latarray[139];?>');
  var lonGPS139= parseFloat('<?php echo $lonarray[139];?>');
  var latGPS140= parseFloat('<?php echo $latarray[140];?>');
  var lonGPS140= parseFloat('<?php echo $lonarray[140];?>');
  var latGPS141= parseFloat('<?php echo $latarray[141];?>');
  var lonGPS141= parseFloat('<?php echo $lonarray[141];?>');
  var latGPS142= parseFloat('<?php echo $latarray[142];?>');
  var lonGPS142= parseFloat('<?php echo $lonarray[142];?>');
  var latGPS143= parseFloat('<?php echo $latarray[143];?>');
  var lonGPS143= parseFloat('<?php echo $lonarray[143];?>');
  var latGPS144= parseFloat('<?php echo $latarray[144];?>');
  var lonGPS144= parseFloat('<?php echo $lonarray[144];?>');
  var latGPS145= parseFloat('<?php echo $latarray[145];?>');
  var lonGPS145= parseFloat('<?php echo $lonarray[145];?>');
  var latGPS146= parseFloat('<?php echo $latarray[146];?>');
  var lonGPS146= parseFloat('<?php echo $lonarray[146];?>');
  var latGPS147= parseFloat('<?php echo $latarray[147];?>');
  var lonGPS147= parseFloat('<?php echo $lonarray[147];?>');
  var latGPS148= parseFloat('<?php echo $latarray[148];?>');
  var lonGPS148= parseFloat('<?php echo $lonarray[148];?>');
  var latGPS149= parseFloat('<?php echo $latarray[149];?>');
  var lonGPS149= parseFloat('<?php echo $lonarray[149];?>');
  var latGPS150= parseFloat('<?php echo $latarray[150];?>');
  var lonGPS150= parseFloat('<?php echo $lonarray[150];?>');
  var latGPS151= parseFloat('<?php echo $latarray[151];?>');
  var lonGPS151= parseFloat('<?php echo $lonarray[151];?>');
  var latGPS152= parseFloat('<?php echo $latarray[152];?>');
  var lonGPS152= parseFloat('<?php echo $lonarray[152];?>');
  var latGPS153= parseFloat('<?php echo $latarray[153];?>');
  var lonGPS153= parseFloat('<?php echo $lonarray[153];?>');
  var latGPS154= parseFloat('<?php echo $latarray[154];?>');
  var lonGPS154= parseFloat('<?php echo $lonarray[154];?>');
  var latGPS155= parseFloat('<?php echo $latarray[155];?>');
  var lonGPS155= parseFloat('<?php echo $lonarray[155];?>');
  var latGPS156= parseFloat('<?php echo $latarray[156];?>');
  var lonGPS156= parseFloat('<?php echo $lonarray[156];?>');
  var latGPS157= parseFloat('<?php echo $latarray[157];?>');
  var lonGPS157= parseFloat('<?php echo $lonarray[157];?>');
  var latGPS158= parseFloat('<?php echo $latarray[158];?>');
  var lonGPS158= parseFloat('<?php echo $lonarray[158];?>');
  var latGPS159= parseFloat('<?php echo $latarray[159];?>');
  var lonGPS159= parseFloat('<?php echo $lonarray[159];?>');
  var latGPS160= parseFloat('<?php echo $latarray[160];?>');
  var lonGPS160= parseFloat('<?php echo $lonarray[160];?>');
  var latGPS161= parseFloat('<?php echo $latarray[161];?>');
  var lonGPS161= parseFloat('<?php echo $lonarray[161];?>');
  var latGPS162= parseFloat('<?php echo $latarray[162];?>');
  var lonGPS162= parseFloat('<?php echo $lonarray[162];?>');
  var latGPS163= parseFloat('<?php echo $latarray[163];?>');
  var lonGPS163= parseFloat('<?php echo $lonarray[163];?>');
  var latGPS164= parseFloat('<?php echo $latarray[164];?>');
  var lonGPS164= parseFloat('<?php echo $lonarray[164];?>');
  var latGPS165= parseFloat('<?php echo $latarray[165];?>');
  var lonGPS165= parseFloat('<?php echo $lonarray[165];?>');
  var latGPS166= parseFloat('<?php echo $latarray[166];?>');
  var lonGPS166= parseFloat('<?php echo $lonarray[166];?>');
  var latGPS167= parseFloat('<?php echo $latarray[167];?>');
  var lonGPS167= parseFloat('<?php echo $lonarray[167];?>');
  var latGPS168= parseFloat('<?php echo $latarray[168];?>');
  var lonGPS168= parseFloat('<?php echo $lonarray[168];?>');
  var latGPS169= parseFloat('<?php echo $latarray[169];?>');
  var lonGPS169= parseFloat('<?php echo $lonarray[169];?>');
  var latGPS170= parseFloat('<?php echo $latarray[170];?>');
  var lonGPS170= parseFloat('<?php echo $lonarray[170];?>');
  var latGPS171= parseFloat('<?php echo $latarray[171];?>');
  var lonGPS171= parseFloat('<?php echo $lonarray[171];?>');
  var latGPS172= parseFloat('<?php echo $latarray[172];?>');
  var lonGPS172= parseFloat('<?php echo $lonarray[172];?>');
  var latGPS173= parseFloat('<?php echo $latarray[173];?>');
  var lonGPS173= parseFloat('<?php echo $lonarray[173];?>');
  var latGPS174= parseFloat('<?php echo $latarray[174];?>');
  var lonGPS174= parseFloat('<?php echo $lonarray[174];?>');
  var latGPS175= parseFloat('<?php echo $latarray[175];?>');
  var lonGPS175= parseFloat('<?php echo $lonarray[175];?>');
  var latGPS176= parseFloat('<?php echo $latarray[176];?>');
  var lonGPS176= parseFloat('<?php echo $lonarray[176];?>');
  var latGPS177= parseFloat('<?php echo $latarray[177];?>');
  var lonGPS177= parseFloat('<?php echo $lonarray[177];?>');
  var latGPS178= parseFloat('<?php echo $latarray[178];?>');
  var lonGPS178= parseFloat('<?php echo $lonarray[178];?>');
  var latGPS179= parseFloat('<?php echo $latarray[179];?>');
  var lonGPS179= parseFloat('<?php echo $lonarray[179];?>');
  var latGPS180= parseFloat('<?php echo $latarray[180];?>');
  var lonGPS180= parseFloat('<?php echo $lonarray[180];?>');
  var latGPS181= parseFloat('<?php echo $latarray[181];?>');
  var lonGPS181= parseFloat('<?php echo $lonarray[181];?>');
  var latGPS182= parseFloat('<?php echo $latarray[182];?>');
  var lonGPS182= parseFloat('<?php echo $lonarray[182];?>');
  var latGPS183= parseFloat('<?php echo $latarray[183];?>');
  var lonGPS183= parseFloat('<?php echo $lonarray[183];?>');
  var latGPS184= parseFloat('<?php echo $latarray[184];?>');
  var lonGPS184= parseFloat('<?php echo $lonarray[184];?>');
  var latGPS185= parseFloat('<?php echo $latarray[185];?>');
  var lonGPS185= parseFloat('<?php echo $lonarray[185];?>');
  var latGPS186= parseFloat('<?php echo $latarray[186];?>');
  var lonGPS186= parseFloat('<?php echo $lonarray[186];?>');
  var latGPS187= parseFloat('<?php echo $latarray[187];?>');
  var lonGPS187= parseFloat('<?php echo $lonarray[187];?>');
  var latGPS188= parseFloat('<?php echo $latarray[188];?>');
  var lonGPS188= parseFloat('<?php echo $lonarray[188];?>');
  var lonGPS189= parseFloat('<?php echo $lonarray[189];?>');
  var latGPS189= parseFloat('<?php echo $latarray[189];?>');
  var latGPS190= parseFloat('<?php echo $latarray[190];?>');
  var lonGPS190= parseFloat('<?php echo $lonarray[190];?>');
  var latGPS191= parseFloat('<?php echo $latarray[191];?>');
  var lonGPS191= parseFloat('<?php echo $lonarray[191];?>');
  var latGPS192= parseFloat('<?php echo $latarray[192];?>');
  var lonGPS192= parseFloat('<?php echo $lonarray[192];?>');
  var latGPS193= parseFloat('<?php echo $latarray[193];?>');
  var lonGPS193= parseFloat('<?php echo $lonarray[193];?>');
  var latGPS194= parseFloat('<?php echo $latarray[194];?>');
  var lonGPS194= parseFloat('<?php echo $lonarray[194];?>');
  var latGPS195= parseFloat('<?php echo $latarray[195];?>');
  var lonGPS195= parseFloat('<?php echo $lonarray[195];?>');
  var latGPS196= parseFloat('<?php echo $latarray[196];?>');
  var lonGPS196= parseFloat('<?php echo $lonarray[196];?>');
  var latGPS197= parseFloat('<?php echo $latarray[197];?>');
  var lonGPS197= parseFloat('<?php echo $lonarray[197];?>');
  var latGPS198= parseFloat('<?php echo $latarray[198];?>');
  var lonGPS198= parseFloat('<?php echo $lonarray[198];?>');
  var lonGPS199= parseFloat('<?php echo $lonarray[199];?>');
  var latGPS199= parseFloat('<?php echo $latarray[199];?>');

  var latGPS200= parseFloat('<?php echo $latarray[200];?>');
  var lonGPS200= parseFloat('<?php echo $lonarray[200];?>');
  var latGPS201= parseFloat('<?php echo $latarray[201];?>');
  var lonGPS201= parseFloat('<?php echo $lonarray[201];?>');
  var latGPS202= parseFloat('<?php echo $latarray[202];?>');
  var lonGPS202= parseFloat('<?php echo $lonarray[202];?>');
  var latGPS203= parseFloat('<?php echo $latarray[203];?>');
  var lonGPS203= parseFloat('<?php echo $lonarray[203];?>');
  var latGPS204= parseFloat('<?php echo $latarray[204];?>');
  var lonGPS204= parseFloat('<?php echo $lonarray[204];?>');
  var latGPS205= parseFloat('<?php echo $latarray[205];?>');
  var lonGPS205= parseFloat('<?php echo $lonarray[205];?>');
  var latGPS206= parseFloat('<?php echo $latarray[206];?>');
  var lonGPS206= parseFloat('<?php echo $lonarray[206];?>');
  var latGPS207= parseFloat('<?php echo $latarray[207];?>');
  var lonGPS207= parseFloat('<?php echo $lonarray[207];?>');
  var latGPS208= parseFloat('<?php echo $latarray[208];?>');
  var lonGPS208= parseFloat('<?php echo $lonarray[208];?>');
  var latGPS209= parseFloat('<?php echo $latarray[209];?>');
  var lonGPS209= parseFloat('<?php echo $lonarray[209];?>');
  var latGPS210= parseFloat('<?php echo $latarray[210];?>');
  var lonGPS210= parseFloat('<?php echo $lonarray[210];?>');
  var latGPS211= parseFloat('<?php echo $latarray[211];?>');
  var lonGPS211= parseFloat('<?php echo $lonarray[211];?>');
  var latGPS212= parseFloat('<?php echo $latarray[212];?>');
  var lonGPS212= parseFloat('<?php echo $lonarray[212];?>');
  var latGPS213= parseFloat('<?php echo $latarray[213];?>');
  var lonGPS213= parseFloat('<?php echo $lonarray[213];?>');
  var latGPS214= parseFloat('<?php echo $latarray[214];?>');
  var lonGPS214= parseFloat('<?php echo $lonarray[214];?>');
  var latGPS215= parseFloat('<?php echo $latarray[215];?>');
  var lonGPS215= parseFloat('<?php echo $lonarray[215];?>');
  var latGPS216= parseFloat('<?php echo $latarray[216];?>');
  var lonGPS216= parseFloat('<?php echo $lonarray[216];?>');
  var latGPS217= parseFloat('<?php echo $latarray[217];?>');
  var lonGPS217= parseFloat('<?php echo $lonarray[217];?>');
  var latGPS218= parseFloat('<?php echo $latarray[218];?>');
  var lonGPS218= parseFloat('<?php echo $lonarray[218];?>');
  var latGPS219= parseFloat('<?php echo $latarray[219];?>');
  var lonGPS219= parseFloat('<?php echo $lonarray[219];?>');
  var latGPS220= parseFloat('<?php echo $latarray[220];?>');
  var lonGPS220= parseFloat('<?php echo $lonarray[220];?>');
  var latGPS221= parseFloat('<?php echo $latarray[221];?>');
  var lonGPS221= parseFloat('<?php echo $lonarray[221];?>');
  var latGPS222= parseFloat('<?php echo $latarray[222];?>');
  var lonGPS222= parseFloat('<?php echo $lonarray[222];?>');
  var latGPS223= parseFloat('<?php echo $latarray[223];?>');
  var lonGPS223= parseFloat('<?php echo $lonarray[223];?>');
  var latGPS224= parseFloat('<?php echo $latarray[224];?>');
  var lonGPS224= parseFloat('<?php echo $lonarray[224];?>');
  var latGPS225= parseFloat('<?php echo $latarray[225];?>');
  var lonGPS225= parseFloat('<?php echo $lonarray[225];?>');
  var latGPS226= parseFloat('<?php echo $latarray[226];?>');
  var lonGPS226= parseFloat('<?php echo $lonarray[226];?>');
  var latGPS227= parseFloat('<?php echo $latarray[227];?>');
  var lonGPS227= parseFloat('<?php echo $lonarray[227];?>');
  var latGPS228= parseFloat('<?php echo $latarray[228];?>');
  var lonGPS228= parseFloat('<?php echo $lonarray[228];?>');
  var latGPS229= parseFloat('<?php echo $latarray[229];?>');
  var lonGPS229= parseFloat('<?php echo $lonarray[229];?>');
  var latGPS230= parseFloat('<?php echo $latarray[230];?>');
  var lonGPS230= parseFloat('<?php echo $lonarray[230];?>');
  var latGPS231= parseFloat('<?php echo $latarray[231];?>');
  var lonGPS231= parseFloat('<?php echo $lonarray[231];?>');
  var latGPS232= parseFloat('<?php echo $latarray[232];?>');
  var lonGPS232= parseFloat('<?php echo $lonarray[232];?>');
  var latGPS233= parseFloat('<?php echo $latarray[233];?>');
  var lonGPS233= parseFloat('<?php echo $lonarray[233];?>');
  var latGPS234= parseFloat('<?php echo $latarray[234];?>');
  var lonGPS234= parseFloat('<?php echo $lonarray[234];?>');
  var latGPS235= parseFloat('<?php echo $latarray[235];?>');
  var lonGPS235= parseFloat('<?php echo $lonarray[235];?>');
  var latGPS236= parseFloat('<?php echo $latarray[236];?>');
  var lonGPS236= parseFloat('<?php echo $lonarray[236];?>');
  var latGPS237= parseFloat('<?php echo $latarray[237];?>');
  var lonGPS237= parseFloat('<?php echo $lonarray[237];?>');
  var latGPS238= parseFloat('<?php echo $latarray[238];?>');
  var lonGPS238= parseFloat('<?php echo $lonarray[238];?>');
  var latGPS239= parseFloat('<?php echo $latarray[239];?>');
  var lonGPS239= parseFloat('<?php echo $lonarray[239];?>');
  var latGPS240= parseFloat('<?php echo $latarray[240];?>');
  var lonGPS240= parseFloat('<?php echo $lonarray[240];?>');
  var latGPS241= parseFloat('<?php echo $latarray[241];?>');
  var lonGPS241= parseFloat('<?php echo $lonarray[241];?>');
  var latGPS242= parseFloat('<?php echo $latarray[242];?>');
  var lonGPS242= parseFloat('<?php echo $lonarray[242];?>');
  var latGPS243= parseFloat('<?php echo $latarray[243];?>');
  var lonGPS243= parseFloat('<?php echo $lonarray[243];?>');
  var latGPS244= parseFloat('<?php echo $latarray[244];?>');
  var lonGPS244= parseFloat('<?php echo $lonarray[244];?>');
  var latGPS245= parseFloat('<?php echo $latarray[245];?>');
  var lonGPS245= parseFloat('<?php echo $lonarray[245];?>');
  var latGPS246= parseFloat('<?php echo $latarray[246];?>');
  var lonGPS246= parseFloat('<?php echo $lonarray[246];?>');
  var latGPS247= parseFloat('<?php echo $latarray[247];?>');
  var lonGPS247= parseFloat('<?php echo $lonarray[247];?>');
  var latGPS248= parseFloat('<?php echo $latarray[248];?>');
  var lonGPS248= parseFloat('<?php echo $lonarray[248];?>');
  var latGPS249= parseFloat('<?php echo $latarray[249];?>');
  var lonGPS249= parseFloat('<?php echo $lonarray[249];?>');
  var latGPS250= parseFloat('<?php echo $latarray[250];?>');
  var lonGPS250= parseFloat('<?php echo $lonarray[250];?>');
  var latGPS251= parseFloat('<?php echo $latarray[251];?>');
  var lonGPS251= parseFloat('<?php echo $lonarray[251];?>');
  var latGPS252= parseFloat('<?php echo $latarray[252];?>');
  var lonGPS252= parseFloat('<?php echo $lonarray[252];?>');
  var latGPS253= parseFloat('<?php echo $latarray[253];?>');
  var lonGPS253= parseFloat('<?php echo $lonarray[253];?>');
  var latGPS254= parseFloat('<?php echo $latarray[254];?>');
  var lonGPS254= parseFloat('<?php echo $lonarray[254];?>');
  var latGPS255= parseFloat('<?php echo $latarray[255];?>');
  var lonGPS255= parseFloat('<?php echo $lonarray[255];?>');
  var latGPS256= parseFloat('<?php echo $latarray[256];?>');
  var lonGPS256= parseFloat('<?php echo $lonarray[256];?>');
  var latGPS257= parseFloat('<?php echo $latarray[257];?>');
  var lonGPS257= parseFloat('<?php echo $lonarray[257];?>');
  var latGPS258= parseFloat('<?php echo $latarray[258];?>');
  var lonGPS258= parseFloat('<?php echo $lonarray[258];?>');
  var latGPS259= parseFloat('<?php echo $latarray[259];?>');
  var lonGPS259= parseFloat('<?php echo $lonarray[259];?>');
  var latGPS260= parseFloat('<?php echo $latarray[260];?>');
  var lonGPS260= parseFloat('<?php echo $lonarray[260];?>');
  var latGPS261= parseFloat('<?php echo $latarray[261];?>');
  var lonGPS261= parseFloat('<?php echo $lonarray[261];?>');
  var latGPS262= parseFloat('<?php echo $latarray[262];?>');
  var lonGPS262= parseFloat('<?php echo $lonarray[262];?>');
  var latGPS263= parseFloat('<?php echo $latarray[263];?>');
  var lonGPS263= parseFloat('<?php echo $lonarray[263];?>');
  var latGPS264= parseFloat('<?php echo $latarray[264];?>');
  var lonGPS264= parseFloat('<?php echo $lonarray[264];?>');
  var latGPS265= parseFloat('<?php echo $latarray[265];?>');
  var lonGPS265= parseFloat('<?php echo $lonarray[265];?>');
  var latGPS266= parseFloat('<?php echo $latarray[266];?>');
  var lonGPS266= parseFloat('<?php echo $lonarray[266];?>');
  var latGPS267= parseFloat('<?php echo $latarray[267];?>');
  var lonGPS267= parseFloat('<?php echo $lonarray[267];?>');
  var latGPS268= parseFloat('<?php echo $latarray[268];?>');
  var lonGPS268= parseFloat('<?php echo $lonarray[268];?>');
  var latGPS269= parseFloat('<?php echo $latarray[269];?>');
  var lonGPS269= parseFloat('<?php echo $lonarray[269];?>');
  var latGPS270= parseFloat('<?php echo $latarray[270];?>');
  var lonGPS270= parseFloat('<?php echo $lonarray[270];?>');
  var latGPS271= parseFloat('<?php echo $latarray[271];?>');
  var lonGPS271= parseFloat('<?php echo $lonarray[271];?>');
  var latGPS272= parseFloat('<?php echo $latarray[272];?>');
  var lonGPS272= parseFloat('<?php echo $lonarray[272];?>');
  var latGPS273= parseFloat('<?php echo $latarray[273];?>');
  var lonGPS273= parseFloat('<?php echo $lonarray[273];?>');
  var latGPS274= parseFloat('<?php echo $latarray[274];?>');
  var lonGPS274= parseFloat('<?php echo $lonarray[274];?>');
  var latGPS275= parseFloat('<?php echo $latarray[275];?>');
  var lonGPS275= parseFloat('<?php echo $lonarray[275];?>');
  var latGPS276= parseFloat('<?php echo $latarray[276];?>');
  var lonGPS276= parseFloat('<?php echo $lonarray[276];?>');
  var latGPS277= parseFloat('<?php echo $latarray[277];?>');
  var lonGPS277= parseFloat('<?php echo $lonarray[277];?>');
  var latGPS278= parseFloat('<?php echo $latarray[278];?>');
  var lonGPS278= parseFloat('<?php echo $lonarray[278];?>');
  var latGPS279= parseFloat('<?php echo $latarray[279];?>');
  var lonGPS279= parseFloat('<?php echo $lonarray[279];?>');
  var latGPS280= parseFloat('<?php echo $latarray[280];?>');
  var lonGPS280= parseFloat('<?php echo $lonarray[280];?>');
  var latGPS281= parseFloat('<?php echo $latarray[281];?>');
  var lonGPS281= parseFloat('<?php echo $lonarray[281];?>');
  var latGPS282= parseFloat('<?php echo $latarray[282];?>');
  var lonGPS282= parseFloat('<?php echo $lonarray[282];?>');
  var latGPS283= parseFloat('<?php echo $latarray[283];?>');
  var lonGPS283= parseFloat('<?php echo $lonarray[283];?>');
  var latGPS284= parseFloat('<?php echo $latarray[284];?>');
  var lonGPS284= parseFloat('<?php echo $lonarray[284];?>');
  var latGPS285= parseFloat('<?php echo $latarray[285];?>');
  var lonGPS285= parseFloat('<?php echo $lonarray[285];?>');
  var latGPS286= parseFloat('<?php echo $latarray[286];?>');
  var lonGPS286= parseFloat('<?php echo $lonarray[286];?>');
  var latGPS287= parseFloat('<?php echo $latarray[287];?>');
  var lonGPS287= parseFloat('<?php echo $lonarray[287];?>');
  var latGPS288= parseFloat('<?php echo $latarray[288];?>');
  var lonGPS288= parseFloat('<?php echo $lonarray[288];?>');
  var latGPS289= parseFloat('<?php echo $latarray[289];?>');
  var lonGPS289= parseFloat('<?php echo $lonarray[289];?>');
  var latGPS290= parseFloat('<?php echo $latarray[290];?>');
  var lonGPS290= parseFloat('<?php echo $lonarray[290];?>');
  var latGPS291= parseFloat('<?php echo $latarray[291];?>');
  var lonGPS291= parseFloat('<?php echo $lonarray[291];?>');
  var latGPS292= parseFloat('<?php echo $latarray[292];?>');
  var lonGPS292= parseFloat('<?php echo $lonarray[292];?>');
  var latGPS293= parseFloat('<?php echo $latarray[293];?>');
  var lonGPS293= parseFloat('<?php echo $lonarray[293];?>');
  var latGPS294= parseFloat('<?php echo $latarray[294];?>');
  var lonGPS294= parseFloat('<?php echo $lonarray[294];?>');
  var latGPS295= parseFloat('<?php echo $latarray[295];?>');
  var lonGPS295= parseFloat('<?php echo $lonarray[295];?>');
  var latGPS296= parseFloat('<?php echo $latarray[296];?>');
  var lonGPS296= parseFloat('<?php echo $lonarray[296];?>');
  var latGPS297= parseFloat('<?php echo $latarray[297];?>');
  var lonGPS297= parseFloat('<?php echo $lonarray[297];?>');
  var latGPS298= parseFloat('<?php echo $latarray[298];?>');
  var lonGPS298= parseFloat('<?php echo $lonarray[298];?>');
  var latGPS299= parseFloat('<?php echo $latarray[299];?>');
  var lonGPS299= parseFloat('<?php echo $lonarray[299];?>');
  var latGPS300= parseFloat('<?php echo $latarray[300];?>');
  var lonGPS300= parseFloat('<?php echo $lonarray[300];?>');
  var latGPS301= parseFloat('<?php echo $latarray[301];?>');
  var lonGPS301= parseFloat('<?php echo $lonarray[301];?>');
  var latGPS302= parseFloat('<?php echo $latarray[302];?>');
  var lonGPS302= parseFloat('<?php echo $lonarray[302];?>');
  var latGPS303= parseFloat('<?php echo $latarray[303];?>');
  var lonGPS303= parseFloat('<?php echo $lonarray[303];?>');
  var latGPS304= parseFloat('<?php echo $latarray[304];?>');
  var lonGPS304= parseFloat('<?php echo $lonarray[304];?>');
  var latGPS305= parseFloat('<?php echo $latarray[305];?>');
  var lonGPS305= parseFloat('<?php echo $lonarray[305];?>');
  var latGPS306= parseFloat('<?php echo $latarray[306];?>');
  var lonGPS306= parseFloat('<?php echo $lonarray[306];?>');
  var latGPS307= parseFloat('<?php echo $latarray[307];?>');
  var lonGPS307= parseFloat('<?php echo $lonarray[307];?>');
  var latGPS308= parseFloat('<?php echo $latarray[308];?>');
  var lonGPS308= parseFloat('<?php echo $lonarray[308];?>');
  var latGPS309= parseFloat('<?php echo $latarray[309];?>');
  var lonGPS309= parseFloat('<?php echo $lonarray[309];?>');
  var latGPS310= parseFloat('<?php echo $latarray[310];?>');
  var lonGPS310= parseFloat('<?php echo $lonarray[310];?>');
  var latGPS311= parseFloat('<?php echo $latarray[311];?>');
  var lonGPS311= parseFloat('<?php echo $lonarray[311];?>');
  var latGPS312= parseFloat('<?php echo $latarray[312];?>');
  var lonGPS312= parseFloat('<?php echo $lonarray[312];?>');
  var latGPS313= parseFloat('<?php echo $latarray[313];?>');
  var lonGPS313= parseFloat('<?php echo $lonarray[313];?>');
  var latGPS314= parseFloat('<?php echo $latarray[314];?>');
  var lonGPS314= parseFloat('<?php echo $lonarray[314];?>');
  var latGPS315= parseFloat('<?php echo $latarray[315];?>');
  var lonGPS315= parseFloat('<?php echo $lonarray[315];?>');
  var latGPS316= parseFloat('<?php echo $latarray[316];?>');
  var lonGPS316= parseFloat('<?php echo $lonarray[316];?>');
  var latGPS317= parseFloat('<?php echo $latarray[317];?>');
  var lonGPS317= parseFloat('<?php echo $lonarray[317];?>');
  var latGPS318= parseFloat('<?php echo $latarray[318];?>');
  var lonGPS318= parseFloat('<?php echo $lonarray[318];?>');
  var latGPS319= parseFloat('<?php echo $latarray[319];?>');
  var lonGPS319= parseFloat('<?php echo $lonarray[319];?>');
  var latGPS320= parseFloat('<?php echo $latarray[320];?>');
  var lonGPS320= parseFloat('<?php echo $lonarray[320];?>');
  var latGPS321= parseFloat('<?php echo $latarray[321];?>');
  var lonGPS321= parseFloat('<?php echo $lonarray[321];?>');
  var latGPS322= parseFloat('<?php echo $latarray[322];?>');
  var lonGPS322= parseFloat('<?php echo $lonarray[322];?>');
  var latGPS323= parseFloat('<?php echo $latarray[323];?>');
  var lonGPS323= parseFloat('<?php echo $lonarray[323];?>');
  var latGPS324= parseFloat('<?php echo $latarray[324];?>');
  var lonGPS324= parseFloat('<?php echo $lonarray[324];?>');
  var latGPS325= parseFloat('<?php echo $latarray[325];?>');
  var lonGPS325= parseFloat('<?php echo $lonarray[325];?>');
  var latGPS326= parseFloat('<?php echo $latarray[326];?>');
  var lonGPS326= parseFloat('<?php echo $lonarray[326];?>');
  var latGPS327= parseFloat('<?php echo $latarray[327];?>');
  var lonGPS327= parseFloat('<?php echo $lonarray[327];?>');
  var latGPS328= parseFloat('<?php echo $latarray[328];?>');
  var lonGPS328= parseFloat('<?php echo $lonarray[328];?>');
  var latGPS329= parseFloat('<?php echo $latarray[329];?>');
  var lonGPS329= parseFloat('<?php echo $lonarray[329];?>');
  var latGPS330= parseFloat('<?php echo $latarray[330];?>');
  var lonGPS330= parseFloat('<?php echo $lonarray[330];?>');
  var latGPS331= parseFloat('<?php echo $latarray[331];?>');
  var lonGPS331= parseFloat('<?php echo $lonarray[331];?>');
  var latGPS332= parseFloat('<?php echo $latarray[332];?>');
  var lonGPS332= parseFloat('<?php echo $lonarray[332];?>');
  var latGPS333= parseFloat('<?php echo $latarray[333];?>');
  var lonGPS333= parseFloat('<?php echo $lonarray[333];?>');
  var latGPS334= parseFloat('<?php echo $latarray[334];?>');
  var lonGPS334= parseFloat('<?php echo $lonarray[334];?>');
  var latGPS335= parseFloat('<?php echo $latarray[335];?>');
  var lonGPS335= parseFloat('<?php echo $lonarray[335];?>');
  var latGPS336= parseFloat('<?php echo $latarray[336];?>');
  var lonGPS336= parseFloat('<?php echo $lonarray[336];?>');
  var latGPS337= parseFloat('<?php echo $latarray[337];?>');
  var lonGPS337= parseFloat('<?php echo $lonarray[337];?>');
  var latGPS338= parseFloat('<?php echo $latarray[338];?>');
  var lonGPS338= parseFloat('<?php echo $lonarray[338];?>');
  var latGPS339= parseFloat('<?php echo $latarray[339];?>');
  var lonGPS339= parseFloat('<?php echo $lonarray[339];?>');
  var latGPS340= parseFloat('<?php echo $latarray[340];?>');
  var lonGPS340= parseFloat('<?php echo $lonarray[340];?>');
  var latGPS341= parseFloat('<?php echo $latarray[341];?>');
  var lonGPS341= parseFloat('<?php echo $lonarray[341];?>');
  var latGPS342= parseFloat('<?php echo $latarray[342];?>');
  var lonGPS342= parseFloat('<?php echo $lonarray[342];?>');
  var latGPS343= parseFloat('<?php echo $latarray[343];?>');
  var lonGPS343= parseFloat('<?php echo $lonarray[343];?>');
  var latGPS344= parseFloat('<?php echo $latarray[344];?>');
  var lonGPS344= parseFloat('<?php echo $lonarray[344];?>');
  var latGPS345= parseFloat('<?php echo $latarray[345];?>');
  var lonGPS345= parseFloat('<?php echo $lonarray[345];?>');
  var latGPS346= parseFloat('<?php echo $latarray[346];?>');
  var lonGPS346= parseFloat('<?php echo $lonarray[346];?>');
  var latGPS347= parseFloat('<?php echo $latarray[347];?>');
  var lonGPS347= parseFloat('<?php echo $lonarray[347];?>');
  var latGPS348= parseFloat('<?php echo $latarray[348];?>');
  var lonGPS348= parseFloat('<?php echo $lonarray[348];?>');
  var latGPS349= parseFloat('<?php echo $latarray[349];?>');
  var lonGPS349= parseFloat('<?php echo $lonarray[349];?>');
  var latGPS350= parseFloat('<?php echo $latarray[350];?>');
  var lonGPS350= parseFloat('<?php echo $lonarray[350];?>');
  var latGPS351= parseFloat('<?php echo $latarray[351];?>');
  var lonGPS351= parseFloat('<?php echo $lonarray[351];?>');
  var latGPS352= parseFloat('<?php echo $latarray[352];?>');
  var lonGPS352= parseFloat('<?php echo $lonarray[352];?>');
  var latGPS353= parseFloat('<?php echo $latarray[353];?>');
  var lonGPS353= parseFloat('<?php echo $lonarray[353];?>');
  var latGPS354= parseFloat('<?php echo $latarray[354];?>');
  var lonGPS354= parseFloat('<?php echo $lonarray[354];?>');
  var latGPS355= parseFloat('<?php echo $latarray[355];?>');
  var lonGPS355= parseFloat('<?php echo $lonarray[355];?>');
  var latGPS356= parseFloat('<?php echo $latarray[356];?>');
  var lonGPS356= parseFloat('<?php echo $lonarray[356];?>');
  var latGPS357= parseFloat('<?php echo $latarray[357];?>');
  var lonGPS357= parseFloat('<?php echo $lonarray[357];?>');
  var latGPS358= parseFloat('<?php echo $latarray[358];?>');
  var lonGPS358= parseFloat('<?php echo $lonarray[358];?>');
  var latGPS359= parseFloat('<?php echo $latarray[359];?>');
  var lonGPS359= parseFloat('<?php echo $lonarray[359];?>');
  var latGPS360= parseFloat('<?php echo $latarray[360];?>');
  var lonGPS360= parseFloat('<?php echo $lonarray[360];?>');
  var latGPS361= parseFloat('<?php echo $latarray[361];?>');
  var lonGPS361= parseFloat('<?php echo $lonarray[361];?>');
  var latGPS362= parseFloat('<?php echo $latarray[362];?>');
  var lonGPS362= parseFloat('<?php echo $lonarray[362];?>');
  var latGPS363= parseFloat('<?php echo $latarray[363];?>');
  var lonGPS363= parseFloat('<?php echo $lonarray[363];?>');
  var latGPS364= parseFloat('<?php echo $latarray[364];?>');
  var lonGPS364= parseFloat('<?php echo $lonarray[364];?>');
  var latGPS365= parseFloat('<?php echo $latarray[365];?>');
  var lonGPS365= parseFloat('<?php echo $lonarray[365];?>');
  var latGPS366= parseFloat('<?php echo $latarray[366];?>');
  var lonGPS366= parseFloat('<?php echo $lonarray[366];?>');
  var latGPS367= parseFloat('<?php echo $latarray[367];?>');
  var lonGPS367= parseFloat('<?php echo $lonarray[367];?>');
  var latGPS368= parseFloat('<?php echo $latarray[368];?>');
  var lonGPS368= parseFloat('<?php echo $lonarray[368];?>');
  var latGPS369= parseFloat('<?php echo $latarray[369];?>');
  var lonGPS369= parseFloat('<?php echo $lonarray[369];?>');
  var latGPS370= parseFloat('<?php echo $latarray[370];?>');
  var lonGPS370= parseFloat('<?php echo $lonarray[370];?>');
  var latGPS371= parseFloat('<?php echo $latarray[371];?>');
  var lonGPS371= parseFloat('<?php echo $lonarray[371];?>');
  var latGPS372= parseFloat('<?php echo $latarray[372];?>');
  var lonGPS372= parseFloat('<?php echo $lonarray[372];?>');
  var latGPS373= parseFloat('<?php echo $latarray[373];?>');
  var lonGPS373= parseFloat('<?php echo $lonarray[373];?>');
  var latGPS374= parseFloat('<?php echo $latarray[374];?>');
  var lonGPS374= parseFloat('<?php echo $lonarray[374];?>');
  var latGPS375= parseFloat('<?php echo $latarray[375];?>');
  var lonGPS375= parseFloat('<?php echo $lonarray[375];?>');
  var latGPS376= parseFloat('<?php echo $latarray[376];?>');
  var lonGPS376= parseFloat('<?php echo $lonarray[376];?>');
  var latGPS377= parseFloat('<?php echo $latarray[377];?>');
  var lonGPS377= parseFloat('<?php echo $lonarray[377];?>');
  var latGPS378= parseFloat('<?php echo $latarray[378];?>');
  var lonGPS378= parseFloat('<?php echo $lonarray[378];?>');
  var latGPS379= parseFloat('<?php echo $latarray[379];?>');
  var lonGPS379= parseFloat('<?php echo $lonarray[379];?>');
  var latGPS380= parseFloat('<?php echo $latarray[380];?>');
  var lonGPS380= parseFloat('<?php echo $lonarray[380];?>');
  var latGPS381= parseFloat('<?php echo $latarray[381];?>');
  var lonGPS381= parseFloat('<?php echo $lonarray[381];?>');
  var latGPS382= parseFloat('<?php echo $latarray[382];?>');
  var lonGPS382= parseFloat('<?php echo $lonarray[382];?>');
  var latGPS383= parseFloat('<?php echo $latarray[383];?>');
  var lonGPS383= parseFloat('<?php echo $lonarray[383];?>');
  var latGPS384= parseFloat('<?php echo $latarray[384];?>');
  var lonGPS384= parseFloat('<?php echo $lonarray[384];?>');
  var latGPS385= parseFloat('<?php echo $latarray[385];?>');
  var lonGPS385= parseFloat('<?php echo $lonarray[385];?>');
  var latGPS386= parseFloat('<?php echo $latarray[386];?>');
  var lonGPS386= parseFloat('<?php echo $lonarray[386];?>');
  var latGPS387= parseFloat('<?php echo $latarray[387];?>');
  var lonGPS387= parseFloat('<?php echo $lonarray[387];?>');
  var latGPS388= parseFloat('<?php echo $latarray[388];?>');
  var lonGPS388= parseFloat('<?php echo $lonarray[388];?>');
  var lonGPS389= parseFloat('<?php echo $lonarray[389];?>');
  var latGPS389= parseFloat('<?php echo $latarray[389];?>');
  var latGPS390= parseFloat('<?php echo $latarray[390];?>');
  var lonGPS390= parseFloat('<?php echo $lonarray[390];?>');
  var latGPS391= parseFloat('<?php echo $latarray[391];?>');
  var lonGPS391= parseFloat('<?php echo $lonarray[391];?>');
  var latGPS392= parseFloat('<?php echo $latarray[392];?>');
  var lonGPS392= parseFloat('<?php echo $lonarray[392];?>');
  var latGPS393= parseFloat('<?php echo $latarray[393];?>');
  var lonGPS393= parseFloat('<?php echo $lonarray[393];?>');
  var latGPS394= parseFloat('<?php echo $latarray[394];?>');
  var lonGPS394= parseFloat('<?php echo $lonarray[394];?>');
  var latGPS395= parseFloat('<?php echo $latarray[395];?>');
  var lonGPS395= parseFloat('<?php echo $lonarray[395];?>');
  var latGPS396= parseFloat('<?php echo $latarray[396];?>');
  var lonGPS396= parseFloat('<?php echo $lonarray[396];?>');
  var latGPS397= parseFloat('<?php echo $latarray[397];?>');
  var lonGPS397= parseFloat('<?php echo $lonarray[397];?>');
  var latGPS398= parseFloat('<?php echo $latarray[398];?>');
  var lonGPS398= parseFloat('<?php echo $lonarray[398];?>');
  var lonGPS399= parseFloat('<?php echo $lonarray[399];?>');
  var latGPS399= parseFloat('<?php echo $latarray[399];?>');
  
    
  var GPSpositions =[
	  {lat: latGPS00, lng: lonGPS00},
	  {lat: latGPS01, lng: lonGPS01},
	  {lat: latGPS02, lng: lonGPS02},
	  {lat: latGPS03, lng: lonGPS03},
	  {lat: latGPS04, lng: lonGPS04},
	  {lat: latGPS05, lng: lonGPS05},
	  {lat: latGPS06, lng: lonGPS06},
	  {lat: latGPS07, lng: lonGPS07},
	  {lat: latGPS08, lng: lonGPS08},
	  {lat: latGPS09, lng: lonGPS09},
	  {lat: latGPS10, lng: lonGPS10},
	  {lat: latGPS11, lng: lonGPS11},
	  {lat: latGPS12, lng: lonGPS12},
	  {lat: latGPS13, lng: lonGPS13},
	  {lat: latGPS14, lng: lonGPS14},
	  {lat: latGPS15, lng: lonGPS15},
	  {lat: latGPS16, lng: lonGPS16},
	  {lat: latGPS17, lng: lonGPS17},
	  {lat: latGPS18, lng: lonGPS18},
	  {lat: latGPS19, lng: lonGPS19},
	  {lat: latGPS20, lng: lonGPS20},
	  {lat: latGPS21, lng: lonGPS21},
	  {lat: latGPS22, lng: lonGPS22},
	  {lat: latGPS23, lng: lonGPS23},
	  {lat: latGPS24, lng: lonGPS24},
	  {lat: latGPS25, lng: lonGPS25},
	  {lat: latGPS26, lng: lonGPS26},
	  {lat: latGPS27, lng: lonGPS27},
	  {lat: latGPS28, lng: lonGPS28},
	  {lat: latGPS29, lng: lonGPS29},
	  {lat: latGPS30, lng: lonGPS30},
	  {lat: latGPS31, lng: lonGPS31},
	  {lat: latGPS32, lng: lonGPS32},
	  {lat: latGPS33, lng: lonGPS33},
	  {lat: latGPS34, lng: lonGPS34},
	  {lat: latGPS35, lng: lonGPS35},
	  {lat: latGPS36, lng: lonGPS36},
	  {lat: latGPS37, lng: lonGPS37},
	  {lat: latGPS38, lng: lonGPS38},
	  {lat: latGPS39, lng: lonGPS39},
	  {lat: latGPS40, lng: lonGPS40},
	  {lat: latGPS41, lng: lonGPS41},
	  {lat: latGPS42, lng: lonGPS42},
	  {lat: latGPS43, lng: lonGPS43},
	  {lat: latGPS44, lng: lonGPS44},
	  {lat: latGPS45, lng: lonGPS45},
	  {lat: latGPS46, lng: lonGPS46},
	  {lat: latGPS47, lng: lonGPS47},
	  {lat: latGPS48, lng: lonGPS48},
	  {lat: latGPS49, lng: lonGPS49},
	  {lat: latGPS50, lng: lonGPS50},
	  {lat: latGPS51, lng: lonGPS51},
	  {lat: latGPS52, lng: lonGPS52},
	  {lat: latGPS53, lng: lonGPS53},
	  {lat: latGPS54, lng: lonGPS54},
	  {lat: latGPS55, lng: lonGPS55},
	  {lat: latGPS56, lng: lonGPS56},
	  {lat: latGPS57, lng: lonGPS57},
	  {lat: latGPS58, lng: lonGPS58},
	  {lat: latGPS59, lng: lonGPS59},
	  {lat: latGPS60, lng: lonGPS60},
	  {lat: latGPS61, lng: lonGPS61},
	  {lat: latGPS62, lng: lonGPS62},
	  {lat: latGPS63, lng: lonGPS63},
	  {lat: latGPS64, lng: lonGPS64},
	  {lat: latGPS65, lng: lonGPS65},
	  {lat: latGPS66, lng: lonGPS66},
	  {lat: latGPS67, lng: lonGPS67},
	  {lat: latGPS68, lng: lonGPS68},
	  {lat: latGPS69, lng: lonGPS69},
	  {lat: latGPS70, lng: lonGPS70},
	  {lat: latGPS71, lng: lonGPS71},
	  {lat: latGPS72, lng: lonGPS72},
	  {lat: latGPS73, lng: lonGPS73},
	  {lat: latGPS74, lng: lonGPS74},
	  {lat: latGPS75, lng: lonGPS75},
	  {lat: latGPS76, lng: lonGPS76},
	  {lat: latGPS77, lng: lonGPS77},
	  {lat: latGPS78, lng: lonGPS78},
	  {lat: latGPS79, lng: lonGPS79},
	  {lat: latGPS80, lng: lonGPS80},
	  {lat: latGPS81, lng: lonGPS81},
	  {lat: latGPS82, lng: lonGPS82},
	  {lat: latGPS83, lng: lonGPS83},
	  {lat: latGPS84, lng: lonGPS84},
	  {lat: latGPS85, lng: lonGPS85},
	  {lat: latGPS86, lng: lonGPS86},
	  {lat: latGPS87, lng: lonGPS87},
	  {lat: latGPS88, lng: lonGPS88},
	  {lat: latGPS89, lng: lonGPS89},
	  {lat: latGPS90, lng: lonGPS90},
	  {lat: latGPS91, lng: lonGPS91},
	  {lat: latGPS92, lng: lonGPS92},
	  {lat: latGPS93, lng: lonGPS93},
	  {lat: latGPS94, lng: lonGPS94},
	  {lat: latGPS95, lng: lonGPS95},
	  {lat: latGPS96, lng: lonGPS96},
	  {lat: latGPS97, lng: lonGPS97},
	  {lat: latGPS98, lng: lonGPS98},
	  {lat: latGPS99, lng: lonGPS99},
	  {lat: latGPS100, lng: lonGPS100},
	  {lat: latGPS101, lng: lonGPS101},
	  {lat: latGPS102, lng: lonGPS102},
	  {lat: latGPS103, lng: lonGPS103},
	  {lat: latGPS104, lng: lonGPS104},
	  {lat: latGPS105, lng: lonGPS105},
	  {lat: latGPS106, lng: lonGPS106},
	  {lat: latGPS107, lng: lonGPS107},
	  {lat: latGPS108, lng: lonGPS108},
	  {lat: latGPS109, lng: lonGPS109},
	  {lat: latGPS110, lng: lonGPS110},
	  {lat: latGPS111, lng: lonGPS111},
	  {lat: latGPS112, lng: lonGPS112},
	  {lat: latGPS113, lng: lonGPS113},
	  {lat: latGPS114, lng: lonGPS114},
	  {lat: latGPS115, lng: lonGPS115},
	  {lat: latGPS116, lng: lonGPS116},
	  {lat: latGPS117, lng: lonGPS117},
	  {lat: latGPS118, lng: lonGPS118},
	  {lat: latGPS119, lng: lonGPS119},
	  {lat: latGPS120, lng: lonGPS120},
	  {lat: latGPS121, lng: lonGPS121},
	  {lat: latGPS122, lng: lonGPS122},
	  {lat: latGPS123, lng: lonGPS123},
	  {lat: latGPS124, lng: lonGPS124},
	  {lat: latGPS125, lng: lonGPS125},
	  {lat: latGPS126, lng: lonGPS126},
	  {lat: latGPS127, lng: lonGPS127},
	  {lat: latGPS128, lng: lonGPS128},
	  {lat: latGPS129, lng: lonGPS129},
	  {lat: latGPS130, lng: lonGPS130},
	  {lat: latGPS131, lng: lonGPS131},
	  {lat: latGPS132, lng: lonGPS132},
	  {lat: latGPS133, lng: lonGPS133},
	  {lat: latGPS134, lng: lonGPS134},
	  {lat: latGPS135, lng: lonGPS135},
	  {lat: latGPS136, lng: lonGPS136},
	  {lat: latGPS137, lng: lonGPS137},
	  {lat: latGPS138, lng: lonGPS138},
	  {lat: latGPS139, lng: lonGPS139},
	  {lat: latGPS140, lng: lonGPS140},
	  {lat: latGPS141, lng: lonGPS141},
	  {lat: latGPS142, lng: lonGPS142},
	  {lat: latGPS143, lng: lonGPS143},
	  {lat: latGPS144, lng: lonGPS144},
	  {lat: latGPS145, lng: lonGPS145},
	  {lat: latGPS146, lng: lonGPS146},
	  {lat: latGPS147, lng: lonGPS147},
	  {lat: latGPS148, lng: lonGPS148},
	  {lat: latGPS149, lng: lonGPS149},
	  {lat: latGPS150, lng: lonGPS150},
	  {lat: latGPS151, lng: lonGPS151},
	  {lat: latGPS152, lng: lonGPS152},
	  {lat: latGPS153, lng: lonGPS153},
	  {lat: latGPS154, lng: lonGPS154},
	  {lat: latGPS155, lng: lonGPS155},
	  {lat: latGPS156, lng: lonGPS156},
	  {lat: latGPS157, lng: lonGPS157},
	  {lat: latGPS158, lng: lonGPS158},
	  {lat: latGPS159, lng: lonGPS159},
	  {lat: latGPS160, lng: lonGPS160},
	  {lat: latGPS161, lng: lonGPS161},
	  {lat: latGPS162, lng: lonGPS162},
	  {lat: latGPS163, lng: lonGPS163},
	  {lat: latGPS164, lng: lonGPS164},
	  {lat: latGPS165, lng: lonGPS165},
	  {lat: latGPS166, lng: lonGPS166},
	  {lat: latGPS167, lng: lonGPS167},
	  {lat: latGPS168, lng: lonGPS168},
	  {lat: latGPS169, lng: lonGPS169},
	  {lat: latGPS170, lng: lonGPS170},
	  {lat: latGPS171, lng: lonGPS171},
	  {lat: latGPS172, lng: lonGPS172},
	  {lat: latGPS173, lng: lonGPS173},
	  {lat: latGPS174, lng: lonGPS174},
	  {lat: latGPS175, lng: lonGPS175},
	  {lat: latGPS176, lng: lonGPS176},
	  {lat: latGPS177, lng: lonGPS177},
	  {lat: latGPS178, lng: lonGPS178},
	  {lat: latGPS179, lng: lonGPS179},
	  {lat: latGPS180, lng: lonGPS180},
	  {lat: latGPS181, lng: lonGPS181},
	  {lat: latGPS182, lng: lonGPS182},
	  {lat: latGPS183, lng: lonGPS183},
	  {lat: latGPS184, lng: lonGPS184},
	  {lat: latGPS185, lng: lonGPS185},
	  {lat: latGPS186, lng: lonGPS186},
	  {lat: latGPS187, lng: lonGPS187},
	  {lat: latGPS188, lng: lonGPS188},
	  {lat: latGPS189, lng: lonGPS189},
	  {lat: latGPS190, lng: lonGPS190},
	  {lat: latGPS191, lng: lonGPS191},
	  {lat: latGPS192, lng: lonGPS192},
	  {lat: latGPS193, lng: lonGPS193},
	  {lat: latGPS194, lng: lonGPS194},
	  {lat: latGPS195, lng: lonGPS195},
	  {lat: latGPS196, lng: lonGPS196},
	  {lat: latGPS197, lng: lonGPS197},
	  {lat: latGPS198, lng: lonGPS198},
	  {lat: latGPS199, lng: lonGPS199},	  
	  {lat: latGPS200, lng: lonGPS200},
	  {lat: latGPS201, lng: lonGPS201},
	  {lat: latGPS202, lng: lonGPS202},
	  {lat: latGPS203, lng: lonGPS203},
	  {lat: latGPS204, lng: lonGPS204},
	  {lat: latGPS205, lng: lonGPS205},
	  {lat: latGPS206, lng: lonGPS206},
	  {lat: latGPS207, lng: lonGPS207},
	  {lat: latGPS208, lng: lonGPS208},
	  {lat: latGPS209, lng: lonGPS209},
	  {lat: latGPS210, lng: lonGPS210},
	  {lat: latGPS211, lng: lonGPS211},
	  {lat: latGPS212, lng: lonGPS212},
	  {lat: latGPS213, lng: lonGPS213},
	  {lat: latGPS214, lng: lonGPS214},
	  {lat: latGPS215, lng: lonGPS215},
	  {lat: latGPS216, lng: lonGPS216},
	  {lat: latGPS217, lng: lonGPS217},
	  {lat: latGPS218, lng: lonGPS218},
	  {lat: latGPS219, lng: lonGPS219},
	  {lat: latGPS220, lng: lonGPS220},
	  {lat: latGPS221, lng: lonGPS221},
	  {lat: latGPS222, lng: lonGPS222},
	  {lat: latGPS223, lng: lonGPS223},
	  {lat: latGPS224, lng: lonGPS224},
	  {lat: latGPS225, lng: lonGPS225},
	  {lat: latGPS226, lng: lonGPS226},
	  {lat: latGPS227, lng: lonGPS227},
	  {lat: latGPS228, lng: lonGPS228},
	  {lat: latGPS229, lng: lonGPS229},
	  {lat: latGPS230, lng: lonGPS230},
	  {lat: latGPS231, lng: lonGPS231},
	  {lat: latGPS232, lng: lonGPS232},
	  {lat: latGPS233, lng: lonGPS233},
	  {lat: latGPS234, lng: lonGPS234},
	  {lat: latGPS235, lng: lonGPS235},
	  {lat: latGPS236, lng: lonGPS236},
	  {lat: latGPS237, lng: lonGPS237},
	  {lat: latGPS238, lng: lonGPS238},
	  {lat: latGPS239, lng: lonGPS239},
	  {lat: latGPS240, lng: lonGPS240},
	  {lat: latGPS241, lng: lonGPS241},
	  {lat: latGPS242, lng: lonGPS242},
	  {lat: latGPS243, lng: lonGPS243},
	  {lat: latGPS244, lng: lonGPS244},
	  {lat: latGPS245, lng: lonGPS245},
	  {lat: latGPS246, lng: lonGPS246},
	  {lat: latGPS247, lng: lonGPS247},
	  {lat: latGPS248, lng: lonGPS248},
	  {lat: latGPS249, lng: lonGPS249},
	  {lat: latGPS250, lng: lonGPS250},
	  {lat: latGPS251, lng: lonGPS251},
	  {lat: latGPS252, lng: lonGPS252},
	  {lat: latGPS253, lng: lonGPS253},
	  {lat: latGPS254, lng: lonGPS254},
	  {lat: latGPS255, lng: lonGPS255},
	  {lat: latGPS256, lng: lonGPS256},
	  {lat: latGPS257, lng: lonGPS257},
	  {lat: latGPS258, lng: lonGPS258},
	  {lat: latGPS259, lng: lonGPS259},
	  {lat: latGPS260, lng: lonGPS260},
	  {lat: latGPS261, lng: lonGPS261},
	  {lat: latGPS262, lng: lonGPS262},
	  {lat: latGPS263, lng: lonGPS263},
	  {lat: latGPS264, lng: lonGPS264},
	  {lat: latGPS265, lng: lonGPS265},
	  {lat: latGPS266, lng: lonGPS266},
	  {lat: latGPS267, lng: lonGPS267},
	  {lat: latGPS268, lng: lonGPS268},
	  {lat: latGPS269, lng: lonGPS269},
	  {lat: latGPS270, lng: lonGPS270},
	  {lat: latGPS271, lng: lonGPS271},
	  {lat: latGPS272, lng: lonGPS272},
	  {lat: latGPS273, lng: lonGPS273},
	  {lat: latGPS274, lng: lonGPS274},
	  {lat: latGPS275, lng: lonGPS275},
	  {lat: latGPS276, lng: lonGPS276},
	  {lat: latGPS277, lng: lonGPS277},
	  {lat: latGPS278, lng: lonGPS278},
	  {lat: latGPS279, lng: lonGPS279},
	  {lat: latGPS280, lng: lonGPS280},
	  {lat: latGPS281, lng: lonGPS281},
	  {lat: latGPS282, lng: lonGPS282},
	  {lat: latGPS283, lng: lonGPS283},
	  {lat: latGPS284, lng: lonGPS284},
	  {lat: latGPS285, lng: lonGPS285},
	  {lat: latGPS286, lng: lonGPS286},
	  {lat: latGPS287, lng: lonGPS287},
	  {lat: latGPS288, lng: lonGPS288},
	  {lat: latGPS289, lng: lonGPS289},
	  {lat: latGPS290, lng: lonGPS290},
	  {lat: latGPS291, lng: lonGPS291},
	  {lat: latGPS292, lng: lonGPS292},
	  {lat: latGPS293, lng: lonGPS293},
	  {lat: latGPS294, lng: lonGPS294},
	  {lat: latGPS295, lng: lonGPS295},
	  {lat: latGPS296, lng: lonGPS296},
	  {lat: latGPS297, lng: lonGPS297},
	  {lat: latGPS298, lng: lonGPS298},
	  {lat: latGPS299, lng: lonGPS299},
	  {lat: latGPS300, lng: lonGPS300},
	  {lat: latGPS301, lng: lonGPS301},
	  {lat: latGPS302, lng: lonGPS302},
	  {lat: latGPS303, lng: lonGPS303},
	  {lat: latGPS304, lng: lonGPS304},
	  {lat: latGPS305, lng: lonGPS305},
	  {lat: latGPS306, lng: lonGPS306},
	  {lat: latGPS307, lng: lonGPS307},
	  {lat: latGPS308, lng: lonGPS308},
	  {lat: latGPS309, lng: lonGPS309},
	  {lat: latGPS310, lng: lonGPS310},
	  {lat: latGPS311, lng: lonGPS311},
	  {lat: latGPS312, lng: lonGPS312},
	  {lat: latGPS313, lng: lonGPS313},
	  {lat: latGPS314, lng: lonGPS314},
	  {lat: latGPS315, lng: lonGPS315},
	  {lat: latGPS316, lng: lonGPS316},
	  {lat: latGPS317, lng: lonGPS317},
	  {lat: latGPS318, lng: lonGPS318},
	  {lat: latGPS319, lng: lonGPS319},
	  {lat: latGPS320, lng: lonGPS320},
	  {lat: latGPS321, lng: lonGPS321},
	  {lat: latGPS322, lng: lonGPS322},
	  {lat: latGPS323, lng: lonGPS323},
	  {lat: latGPS324, lng: lonGPS324},
	  {lat: latGPS325, lng: lonGPS325},
	  {lat: latGPS326, lng: lonGPS326},
	  {lat: latGPS327, lng: lonGPS327},
	  {lat: latGPS328, lng: lonGPS328},
	  {lat: latGPS329, lng: lonGPS329},
	  {lat: latGPS330, lng: lonGPS330},
	  {lat: latGPS331, lng: lonGPS331},
	  {lat: latGPS332, lng: lonGPS332},
	  {lat: latGPS333, lng: lonGPS333},
	  {lat: latGPS334, lng: lonGPS334},
	  {lat: latGPS335, lng: lonGPS335},
	  {lat: latGPS336, lng: lonGPS336},
	  {lat: latGPS337, lng: lonGPS337},
	  {lat: latGPS338, lng: lonGPS338},
	  {lat: latGPS339, lng: lonGPS339},
	  {lat: latGPS340, lng: lonGPS340},
	  {lat: latGPS341, lng: lonGPS341},
	  {lat: latGPS342, lng: lonGPS342},
	  {lat: latGPS343, lng: lonGPS343},
	  {lat: latGPS344, lng: lonGPS344},
	  {lat: latGPS345, lng: lonGPS345},
	  {lat: latGPS346, lng: lonGPS346},
	  {lat: latGPS347, lng: lonGPS347},
	  {lat: latGPS348, lng: lonGPS348},
	  {lat: latGPS349, lng: lonGPS349},
	  {lat: latGPS350, lng: lonGPS350},
	  {lat: latGPS351, lng: lonGPS351},
	  {lat: latGPS352, lng: lonGPS352},
	  {lat: latGPS353, lng: lonGPS353},
	  {lat: latGPS354, lng: lonGPS354},
	  {lat: latGPS355, lng: lonGPS355},
	  {lat: latGPS356, lng: lonGPS356},
	  {lat: latGPS357, lng: lonGPS357},
	  {lat: latGPS358, lng: lonGPS358},
	  {lat: latGPS359, lng: lonGPS359},
	  {lat: latGPS360, lng: lonGPS360},
	  {lat: latGPS361, lng: lonGPS361},
	  {lat: latGPS362, lng: lonGPS362},
	  {lat: latGPS363, lng: lonGPS363},
	  {lat: latGPS364, lng: lonGPS364},
	  {lat: latGPS365, lng: lonGPS365},
	  {lat: latGPS366, lng: lonGPS366},
	  {lat: latGPS367, lng: lonGPS367},
	  {lat: latGPS368, lng: lonGPS368},
	  {lat: latGPS369, lng: lonGPS369},
	  {lat: latGPS370, lng: lonGPS370},
	  {lat: latGPS371, lng: lonGPS371},
	  {lat: latGPS372, lng: lonGPS372},
	  {lat: latGPS373, lng: lonGPS373},
	  {lat: latGPS374, lng: lonGPS374},
	  {lat: latGPS375, lng: lonGPS375},
	  {lat: latGPS376, lng: lonGPS376},
	  {lat: latGPS377, lng: lonGPS377},
	  {lat: latGPS378, lng: lonGPS378},
	  {lat: latGPS379, lng: lonGPS379},
	  {lat: latGPS380, lng: lonGPS380},
	  {lat: latGPS381, lng: lonGPS381},
	  {lat: latGPS382, lng: lonGPS382},
	  {lat: latGPS383, lng: lonGPS383},
	  {lat: latGPS384, lng: lonGPS384},
	  {lat: latGPS385, lng: lonGPS385},
	  {lat: latGPS386, lng: lonGPS386},
	  {lat: latGPS387, lng: lonGPS387},
	  {lat: latGPS388, lng: lonGPS388},
	  {lat: latGPS389, lng: lonGPS389},
	  {lat: latGPS390, lng: lonGPS390},
	  {lat: latGPS391, lng: lonGPS391},
	  {lat: latGPS392, lng: lonGPS392},
	  {lat: latGPS393, lng: lonGPS393},
	  {lat: latGPS394, lng: lonGPS394},
	  {lat: latGPS395, lng: lonGPS395},
	  {lat: latGPS396, lng: lonGPS396},
	  {lat: latGPS397, lng: lonGPS397},
	  {lat: latGPS398, lng: lonGPS398},
	  {lat: latGPS399, lng: lonGPS399}	  
	];
  
  var pos_tarjeta1 = new google.maps.LatLng(latGPS00, lonGPS00);
  var pos_tarjeta2 = new google.maps.LatLng(latGPS01, lonGPS01);
  var latitudGPS3= parseFloat('<?php echo $latitud2;?>');
  var longitudGPS3= parseFloat('<?php echo $longitud2;?>');
  var pos_alerta = new google.maps.LatLng(latitudGPS3, longitudGPS3);
  
  var mapCanvas = document.getElementById("map");
  var mapOptions = 
  {
     center: pos_tarjeta2,
     zoom: 19,
	 //mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(mapCanvas, mapOptions);
  var marker1 = new google.maps.Marker
  ({
     position: pos_tarjeta1,
     map: map,
	 label: "box"
  });
  var marker2 = new google.maps.Marker
  ({
     position: pos_tarjeta2,
     map: map,
	 label: "car"
  });
  var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
  var  marker3 = new google.maps.Marker
  ({
     position: pos_alerta,
     map: map,
	 icon: image
  });
  var recorrido = new google.maps.Polyline({
	  path: GPSpositions,
	  geodesic: true,
	  strokeColor: '#FF0000',
	  strokeOpacity:1.0,
	  strokeWeight: 2
  });
  recorrido.setMap(map);
  
}
</script>

<!--script src="https://maps.googleapis.com/maps/api/js?callback=myMap"></script-->
<script async defer

        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9ZEnLBOQxKVFE6r5hKdu_mPJhmGDZcLQ&signed_in=true&callback=myMap"></script>
<?php
echo "ALERTAS:"."<br>";
echo "Ultima Alerta recibida a las: [", $tiempo, "]"."<br>";
//if ($agente == 0) echo "Emitida por el agente", $agente, ": [carro]"."<br>";
//else echo "Emitida por el agente", $agente, ": [caja]"."<br>";
//echo "Emitida por el agente [", $agente, "]"."<br>";
echo "Emitida por el agente [caja]"."<br>";
echo "Coordenada GPS de la alerta: [", $latitud2, ",", $longitud2, "]"."<br>";
//echo "La alerta reportada es una alerta tipo: [", $alerta, "]"."<br>";
echo "La alerta reportadas fueron las siguientes: [", $alertas10, "]"."<br>";
echo "<br>";
echo "Leyenda de alertas, a continuacion:"."<br>";
echo "Alerta tipo 1: [Caja esta fuera del rango del carro]"."<br>";
echo "Alerta tipo 2: [Peligro!! Caja perdida!!]"."<br>";
echo "Alerta tipo 3: [Se ha ingresado una tarjeta erronea]"."<br>";
echo "Alerta tipo 4: [Clave Wifi Incorrecta]"."<br>";
echo "Alerta tipo 5: [Fuera de Rango del Destino]"."<br>";
echo "Alerta tipo 6: [Clave de cohersion detectada]"."<br>";
echo "Alerta tipo 7: [Llegada y apertura exitosa en destino]"."<br>";
?>
</body>
</html>
