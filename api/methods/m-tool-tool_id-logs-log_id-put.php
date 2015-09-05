<?php
$route = '/tool/:tool_id/logs/:log_id';
$app->put($route, function ($tool_id,$log_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$log_id = prepareIdIn($log_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['Type']) && isset($param['log']))
		{

		$type = trim(mysql_real_escape_string($param['type']));
		$details = trim(mysql_real_escape_string($param['details']));

		$query = "UPDATE tool_log SET Type = '" . $type . "', About = '" . $details . "' WHERE Tool_Log_ID = " . $log_id;
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$log_ID = mysql_insert_id();

		$log_id = prepareIdOut($log_id,$host);

		$F = array();
		$F['log_id'] = $log_id;
		$F['type'] = $Type;
		$F['details'] = scrub($details);
		$F['log_date'] = $log_date;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
