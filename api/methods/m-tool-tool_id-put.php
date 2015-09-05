<?php
$route = '/tool/:tool_id/';
$app->put($route, function ($tool_id) use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = ''; }
	if(isset($param['details'])){ $details = trim(mysql_real_escape_string($param['details'])); } else { $details = ''; }
	if(isset($param['url'])){ $url = trim(mysql_real_escape_string($param['url'])); } else { $url = ''; }
	if(isset($param['logo'])){ $logo = trim(mysql_real_escape_string($param['logo'])); } else { $logo = ''; }

	$post_date = date('Y-m-d H:i:s');

  	$Query = "SELECT * FROM tools WHERE Tools_ID = " . $tool_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{

		$Tool = mysql_fetch_assoc($Database);
		$post_date = $Tool['Post_Date'];

		$query = "UPDATE tool SET";

		if($name!='') { $query .= " name = '" . mysql_real_escape_string($name) . "'"; }
		if($details!='') { $query .= ", Details = '" . mysql_real_escape_string($details) . "'"; }
		if($url!='') { $query .= ", URL = '" . mysql_real_escape_string($url) . "'"; }
		if($logo!='') { $query .= ", Logo = '" . mysql_real_escape_string($logo) . "'"; }

		$query .= " WHERE Tools_ID = " . $tool_id;

		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

	$tool_id = prepareIdOut($tool_id,$host);

	$F = array();
	$F['tool_id'] = $tool_id;
	$F['name'] = $name;
	$F['details'] = scrub($details);
	$F['post_date'] = $post_date;
	$F['url'] = $url;
	$F['logo'] = $logo;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
