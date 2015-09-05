<?php
$route = '/tool/:tool_id/';
$app->delete($route, function ($tool_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$params = $request->params();

	$query = "DELETE FROM tools WHERE Tools_ID = " . $tool_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	$tool_id = prepareIdOut($tool_id,$host);

	$F = array();
	$F['tool_id'] = $tool_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
