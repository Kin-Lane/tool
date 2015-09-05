<?php
$route = '/tool/:tool_id/buildingblocks/:building_block_id';
$app->delete($route, function ($tool_id,$building_block_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$building_block_id = prepareIdIn($building_block_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	$DeleteQuery = "DELETE FROM tools_building_block_pivot WHERE Building_Block_ID = " . $building_block_id . " AND Tools_ID = " . $tool_id;
	$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());

	$building_block_id = prepareIdOut($building_block_id,$host);

	$F = array();
	$F['building_block_id'] = $building_block_id;
	$F['tools_id'] = 0;
	$F['url'] = '';

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
