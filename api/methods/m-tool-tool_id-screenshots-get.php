<?php
$route = '/tool/:tool_id/screenshots/';
$app->get($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

	$Query = "SELECT * FROM tools_screenshot cs";
	$Query .= " WHERE cs.Tools_ID = " . $tool_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$screenshot_id = $Database['ID'];
		$path = $Database['Image_URL'];
		$name = $Database['Image_Name'];
		$type = $Database['Type'];

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
