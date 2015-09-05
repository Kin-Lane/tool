<?php
$route = '/tool/:tool_id/images/';
$app->post($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['path']))
		{

		if(isset($param['type'])){ $type = trim(mysql_real_escape_string($param['type'])); } else { $type = ''; }
		if(isset($param['path'])){ $path = trim(mysql_real_escape_string($param['path'])); } else { $path = ''; }
		if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = ''; }

		$query = "INSERT INTO tool_image(Tools_ID,Image_Name,Image_Path,Type) VALUES(" . $tool_id . ",'" . $name . "','" . $path . "','" . $type . "')";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$image_id = mysql_insert_id();

		$image_id = prepareIdOut($image_id,$host);

		$F = array();
		$F['image_id'] = $image_id;
		$F['type'] = $type;
		$F['path'] = $path;
		$F['name'] = $name;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
