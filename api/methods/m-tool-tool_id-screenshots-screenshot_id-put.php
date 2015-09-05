<?php
$route = '/tool/:tool_id/screenshots/:screenshot_id';
$app->put($route, function ($tool_id,$screenshot_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$screenshot_id = prepareIdIn($screenshot_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['path']))
		{
		$type = trim(mysql_real_escape_string($param['type']));
		$path = trim(mysql_real_escape_string($param['path']));
		$name = trim(mysql_real_escape_string($param['name']));

		$query = "UPDATE tool_screenshot SET Type = '" . $type . "', Image_URL = '" . $path . "', Image_Name = '" . $name . "' WHERE ID = " . $screenshot_id;
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$screenshot_ID = mysql_insert_id();

		$screenshot_id = prepareIdOut($screenshot_id,$host);

		$F = array();
		$F['screenshot_id'] = $screenshot_id;
		$F['name'] = $name;
		$F['path'] = $path;
		$F['type'] = $type;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
