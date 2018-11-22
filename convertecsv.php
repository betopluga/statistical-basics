<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('memory_limit', '-1');
$dire		= $argv[1];
$ngram		= $argv[2];
$conteudo 	= '';
$termos 	= array();

//abre arquivo e carrega os termos
$arq = $dire.'/'.$ngram.'-gram.txt';
$fp = fopen($arq, "r");
$conteudo = fread($fp, filesize($arq));
fclose($fp);
$termo = explode(chr(10), $conteudo);

//cria array de termos para ordenar por frequencia
echo "iniciou filtro...\n";
foreach ($termo as $key => $termo_value) {
	//desconsidera primeira linha
	if($key==0){ continue; }

	$termocomfrequencia = explode("<>", $termo_value);

	//pega termo
	$termo_aux = '';
	for($i=0;$i<count($termocomfrequencia)-1;$i++){
		$termo_aux .= $termocomfrequencia[$i]. ' ';
	}
	if(trim($termo_aux)==''||trim($termo_aux)==NULL||trim($termo_aux)==' '){ continue; }

	//pega frequencia
	$frequencia_temp  = $termocomfrequencia[count($termocomfrequencia)-1];
	$frequencia_array = explode(" ", $frequencia_temp);
	$frequencia 	  = $frequencia_array[0];
	if(trim($frequencia)==''||trim($frequencia)==NULL){ continue; }

	###ADICIONA TERMO
	$termo  = array();
	$termo[0]  = trim($termo_aux);
	$termo[1]  = trim(strtolower($termo_aux));
	$termo[2]  = trim($frequencia);

	array_push($termos, $termo);

	unset($termo);
}

echo "ordenando termos $ngram-gram...\n";
aasort($termos,"1", "c");

$termos_final = array();
echo "eliminando repetidos $ngram-gram...\n";
$totalreg = count($termos);
$k=1;
foreach ($termos as $key1 => $value1) { 	

	if(count($termos_final)==0){
		$ultimo = 0;
	} else {
		$ultimo = count($termos_final)-1;
		if($termos_final[$ultimo][1]==$termos[$key1][1]){
			$termos_final[$ultimo][2] += $termos[$key1][2];
			continue;
		}		
	}
		
	$termo = array();	
	$termo[0]  = $value1[0];
	$termo[1]  = $value1[1];
	$termo[2]  = $value1[2];
	array_push($termos_final, $termo);
	unset($termo);

	$k++;
}
echo "ordenando pela frequencia $ngram-gram...\n";
aasort($termos_final,"2", "d");

echo "imprimindo $ngram-gram...\n";
$saida = '';
foreach ($termos_final as $key => $value) {
	$saida .= $value[0].",".$value[2].chr(10);
}
$arq = fopen($dire."out$ngram.csv", "w");
fwrite($arq,$saida);
fclose($arq);

function aasort (&$array, $key, $dir) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    if($dir=="c"){ asort($sorter); }
    if($dir=="d"){ arsort($sorter); }
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

?>