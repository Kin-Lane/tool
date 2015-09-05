<?php
$route = '/tool/:tool_id/';
$app->get($route, function ($tool_ID)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

	$Query = "SELECT * FROM tools WHERE Tools_ID = " . $tool_ID;
	//echo $Query . "<br />";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$tool_id = $Database['Tools_ID'];
		$name = $Database['Name'];
		$details = trim(strip_tags($Database['Details']));
		//echo "HERE:" . $details . "<br />";
		$post_date = $Database['Post_Date'];
		$url = $Database['URL'];

		$logo = "";
		$PostCheckQuery = "SELECT Tools_Image_ID,Image_Path FROM tools_image WHERE Tools_ID = " . $tool_id . " ORDER BY Tools_Image_ID DESC LIMIT 1";
		//echo $PostCheckQuery . "<br />";
		$CheckResult = mysql_query($PostCheckQuery) or die('Query failed: ' . mysql_error());

		if($CheckResult && mysql_num_rows($CheckResult))
			{
			$CheckResult = mysql_fetch_assoc($CheckResult);
			$logo = $CheckResult['Image_Path'];
			}

		// manipulation zone

		$tool_id = prepareIdOut($tool_id,$host);

		$F = array();
		$F['tool_id'] = $tool_id;
		$F['name'] = $name;
		$F['details'] = scrub($details);
		$F['post_date'] = $post_date;
		$F['url'] = $url;
		$F['logo'] = $logo;

		$ReturnObject = $F;
		}
		//var_dump($ReturnObject);
		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
