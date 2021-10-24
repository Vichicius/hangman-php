<?php
session_id("ahorcado");
session_start();

define("palabras", explode(",", file_get_contents("palabrasAhorcado")));

if (isset($_SESSION["intentos"])) {
	$intentos = $_SESSION["intentos"];
	define("palabra", $_SESSION["palabra"]);
	$letras = $_SESSION["letras"];
}else{
	$intentos = 10;
	define("palabra", palabras[rand(0,count(palabras)-1)]);
	$letras=[];
}

//crear palabra censurada
$palabraCensurada = "";
for ($i=0; $i < strlen(palabra); $i++) { 
	$palabraCensurada .= "*";
}
//Actualizar la palabra censurada
for ($i=0; $i < strlen(palabra); $i++) { //for que recorre las letras de la palabra original
	if(in_array(substr(palabra, $i, 1),$letras)){ //si esta la letra en el array de las letras ya dichas
		$palabraCensurada = substr_replace($palabraCensurada, substr(palabra, $i, 1), $i,1); //destapa las letras acertadas
	}
}

$output = "\n---------------------------------------\n";

define("letrasPalabra", str_split(palabra, 1)); //creo un array con las letras de la palabra a adivinar

//Si has puesto una letra
if (isset($argv[1]) && strlen($argv[1]) == 1 && !is_numeric($argv[1])) { 
	define("input", $argv[1]);
	if (!in_array(input, $letras)) { //si la letra es nueva
		array_push($letras, input); //la añade al array
		$stringLetrasUsadas = implode(",", $letras);//string con todas las letras usadas 
		//te dice si has acertado o no  $letraCorrecta = false;
		if(in_array(input, letrasPalabra)){
			$output .= "Has acertado!\n";
		}else{
			$output .= "Has fallado :(\n";
			$intentos--;
			if($intentos > 0){
				$output .= "Te quedan $intentos intentos
					\rHas probado con las letras: $stringLetrasUsadas\n";
			}else{
				$output .= "
					\rTe has quedado sin vidas jejeje, has perdido
					\rLa palabra era ".palabra."\n";
				session_destroy();
			}
		}
	} else { //si no, te dice que ya está guardada
		$output .= "Esa letra ya está guardada\n";
	}
	//Actualizar la palabra censurada
	for ($i=0; $i < strlen(palabra); $i++) { //for que recorre las letras de la palabra original
		if(in_array(substr(palabra, $i, 1),$letras)){ //si esta la letra en el array de las letras ya dichas
			$palabraCensurada = substr_replace($palabraCensurada, substr(palabra, $i, 1), $i,1); //destapa las letras acertadas
		}
	}
	//añadir el progreso de la palabra
	if ($intentos > 0) {
		$output .= "\nTu progreso es -> ".$palabraCensurada;
		$output .= "\n";
	}
	

} else {
	if (!isset($argv[1])) {
		$stringLetrasUsadas = implode(",", $letras);//string con todas las letras usadas 
		$output .= "Estado de la palabra: $palabraCensurada
			\rHas utilizado las letras: $stringLetrasUsadas
			\rTe quedan $intentos intentos\n";
	} else {
		//Para resetear el juego
		if($argv[1] == "reset"){
			$output .= "Reseteando...\n";
			session_destroy();
		}else $output .= "Introduce solo 1 letra\n";
	}
}

if (palabra == $palabraCensurada){ //Comprueba que la palabra censurada es igual que la original
	$output .= "\n---------------------------------------\n
		\rHas ganado! FELICIDADES!!!\n";
	session_destroy();
}
$output .= "---------------------------------------\n";

//Printear todo lo concatenado en el $output
print($output);

//guardar todo lo importante en la sesion
$_SESSION["intentos"] = $intentos;
$_SESSION["palabra"] = palabra;
$_SESSION["letras"] = $letras;
?>