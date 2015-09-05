<?php
$route = '/tool/:tool_id/screenshots/';
$app->post($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['type']) && isset($param['path']) && isset($param['name']))
		{
		$type = trim(mysql_real_escape_string($param['type']));
		$path = trim(mysql_real_escape_string($param['path']));
		$name = trim(mysql_real_escape_string($param['name']));

		$query = "INSERT INTO tool_screenshot(Tools_ID,Type,Image_Name,Image_URL) VALUES(" . $tool_id . ",'" . $type . "','" . $path . "','" . $name . "')";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$screenshot_id = mysql_insert_id();

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
