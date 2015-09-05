<?php
$route = '/tool/:tool_id/buildingblocks/:buildingblock_id';
$app->put($route, function ($tool_id,$building_block_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($tool_id) && isset($param['building_block_id']))
		{

		$tool_id = trim(mysql_real_escape_string($param['tool_id']));
		$building_block_id = trim(mysql_real_escape_string($param['building_block_id']));

		$tools_id = trim(mysql_real_escape_string($param['tools_id']));
		if($tools_id==''){ $tools_id = 0; }

		$url = trim(mysql_real_escape_string($param['url']));

		$query = "UPDATE tool_building_block_pivot SET";
		$query .= "  Tools_ID = " . $tools_id . ",";
		$query .= "  URL = '" . $url . "'";
		$query .= "  WHERE Tools_ID = " . $tool_id . " AND Building_Block_ID = " . $building_block_id;

		mysql_query($query) or die('Query failed: ' . mysql_error());
		$buildingblock_ID = mysql_insert_id();

		$building_block_id = prepareIdOut($building_block_id,$host);

		$F = array();
		$F['building_block_id'] = $building_block_id;
		$F['tools_id'] = $tools_id;
		$F['url'] = $url;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
