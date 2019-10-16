<?php
require __DIR__ . '/../src/treealgorithms.php';

function consoleLog($k, $v, $bIsVarDump = false) {
	echo "{$k}:\n";
	if ($bIsVarDump) {
		var_dump($v);
	} else {
		print_r($v);
	}
}

$data = new StdClass();
/** @property array данные дерева категорий */
$data->tree = [
	[
		"id" =>  2222,
		"name" => "Venus"
	],
	[
		"id" => 3333,
		"parent_id" => 2222,
		"name" => "Neptune"
	],
	[ 
		"id" => 4444,
		"parent_id" => 2222,
		"name" => "Stratus"
	] 
];
 
$data->tree2 = [
	[
		"id" => 2222,
		"name" => "Venus",
		"parent_id" => 33,
		"children" => []
	]
];


$arr = $data->tree2;
echo 'arr:' . "\n";
print_r($arr);
$oTree = TreeAlgorithms::buildTreeFromFlatList($arr);
echo 'oTree:' . "\n";
print_r($oTree);
$k = TreeAlgorithms::remove($oTree[0], 3333);
consoleLog('k', $k, true);



