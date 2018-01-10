<!doctype html>
<html>

<head>
<meta charset="utf-8" http-equiv="refresh" content="10">
<title>Localizando GPS</title>
</head>

<body>
<h4>Posicion actual de caja segura y blindado</h4>

<?php

//$n_coords = htmlspecialchars($_GET['nc']);
$nc = 15;

$longarray = array($nc);
$latarray = array($nc);

$i=0;
//echo 'Coordenada GPS recibida: [', $latitud, ',', $longitud, ']';}
//$myFile = "C:\Users\Administrator\Downloads\coordenadas.txt";
//$myFile = "/home/pm/Downloads/LoTienesQueEncontrar.txt";
$myFile = "C:\Users\Administrator\Downloads\logProyecto.txt";
if (!file_exists($myFile)) {
  print 'File not found'."<br>";
  }
  else if(!$fh = fopen($myFile, 'r')) {
    print 'Can\'t open file'."<br>";
    }
    else {
      print 'Success open file'."<br>";
      }


//Obtenemos ultimo registro GPS

$cursor = -1;

fseek($fh, $cursor, SEEK_END); //obtenemos fin del archivo
$char = fgetc($fh);
echo "Se procede a imprimir los $nc valores mas recientes:"."<br>";
	
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

	//Se obvia caracteres intermedios entre tiempo y latitud
	while ($char === " " || $char === ",") {
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}

	//Se obtiene el ultimo tiempo
	while ($char !== false && $char !== "\n" && $char !== "\r" && $char !== " ") {
		/**
		 * Prepend the new char
		 */
		$tiempo = $char . $tiempo;
		fseek($fh, $cursor--, SEEK_END);
		$char = fgetc($fh);
	}
	
	echo "Coordenada GPS nro. $i leida:";
	$longarray[$i] = $longitud;
	$latarray[$i] = $latitud;	
	echo '[', $latarray[$i], '|', $longarray[$i], ']'." llegó a las ",$tiempo."<br>";
	$longitud = '';
	$latitud = '';
	$tiempo = '';
	$i = $i+1;
}

fclose($fh);
?> 

<h4>Lista obtenida!!</h4>
<?php
$myFile2 = "C:\Users\Administrator\Downloads\alertas.txt";

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

//Se obvia caracteres intermedios entre latitud y alerta
while ($char2 === " " || $char2 === ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene la ultima alerta
while ($char2 !== " " && $char2 !== ",") {
    /**
     * Prepend the new char
     */
    $alerta = $char2 . $alerta;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obvia caracteres intermedios entre alerta y tiempo
while ($char2 === " " || $char2 == ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene el ultimo tiempo
while ($char2 !== " " && $char2 !== ",") {
    /**
     * Prepend the new char
     */
    $tiempo = $char2 . $tiempo;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obvia caracteres intermedios entre tiempo y agente
while ($char2 === " " || $char2 === ",") {
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}

//Se obtiene el ultimo agente reportando
while ($char2 !== false && $char2 !== "\n" && $char2 !== "\r" && $char2 !== " ") {
    /**
     * Prepend the new char
     */
    $agente = $char2 . $agente;
    fseek($fh2, $cursor--, SEEK_END);
    $char2 = fgetc($fh2);
}
fclose($fh2);
?>

<?php
echo "ALERTAS:"."<br>";
echo "Ultima Alerta recibida a las: [", $tiempo, "]"."<br>";
echo "Emitida por el agente: [", $agente, "]"."<br>";
echo "Coordenada GPS de la alerta: [", $latitud2, ",", $longitud2, "]"."<br>";
echo "La alerta reportada es una alerta tipo: [", $alerta, "]"."<br>";
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