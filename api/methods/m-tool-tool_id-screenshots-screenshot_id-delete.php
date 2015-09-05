<?php
$route = '/tool/:tool_id/screenshots/:screenshot_id';
$app->delete($route, function ($tool_id,$screenshot_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$screenshot_id = prepareIdIn($screenshot_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM tools_screenshot WHERE ID = " . $screenshot_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$screenshot_id = prepareIdOut($screenshot_id,$host);

	$F = array();
	$F['screenshot_id'] = $screenshot_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
