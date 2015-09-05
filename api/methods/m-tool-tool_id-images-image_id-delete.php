<?php
$route = '/tool/:tool_id/images/:image_id';
$app->delete($route, function ($tool_id,$image_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM tools_image WHERE Tool_Image_ID = " . $image_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$image_id = prepareIdOut($image_id,$host);

	$F = array();
	$F['image_id'] = $image_id;
	$F['type'] = "";
	$F['path'] = "";
	$F['name'] = "";

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
