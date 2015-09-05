<?php
$route = '/tool/:tool_id/buildingblocks/';
$app->get($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

	$BuildingBlockQuery = "SELECT abb.Tool_Building_Block_ID as ID, bb.Building_Block_ID, bb.Building_Block_Category_ID, bb.Name, bb.About, bbc.Name AS Building_Block_Category, bbc.Type, abb.URL AS URL FROM building_block bb JOIN tool_building_block_pivot abb ON bb.Building_Block_ID = abb.Building_Block_ID JOIN building_block_category bbc ON bb.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID WHERE abb.Tools_ID = " . $tool_id;
	$BuildingBlockQuery .= " UNION SELECT cu.Tool_URL_ID as ID, bb.Building_Block_ID, bb.Building_Block_Category_ID, bb.Name, bb.About, bbc.Name AS Building_Block_Category, bbc.Type AS Type, cu.URL as URL FROM building_block bb JOIN tool_url cu ON bb.Building_Block_ID = cu.Building_Block_ID JOIN building_block_category bbc ON bb.Building_Block_Category_ID = bbc.BuildingBlockCategory_ID WHERE cu.Tools_ID = " . $tool_id;
	$BuildingBlockQuery .= " ORDER BY Type ASC, Building_Block_Category ASC, Name ASC";

	$DatabaseResult = mysql_query($BuildingBlockQuery) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$building_block_id = $Database['Building_Block_ID'];
		$tool_id = $tool_id;
		$url = $Database['URL'];
		$name = $Database['Name'];
		$about = $Database['About'];
		$building_block_category = $Database['Building_Block_Category_ID'];

		$pattern = '/[^\w ]+/';
		$replacement = '';
		$about = preg_replace($pattern, $replacement, $about);

		$building_block_category = $Database['Building_Block_Category'];
		$url = $Database['URL'];

		$building_block_id = prepareIdOut($building_block_id,$host);

		$F = array();
		$F['buildingblock_id'] = $building_block_id;
		$F['tool_id'] = $tool_id;
		$F['url'] = $url;
		$F['name'] = $name;
		$F['about'] = $about;
		$F['building_block_category'] = $building_block_category;
		$F['url'] = $url;

		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
