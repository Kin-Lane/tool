<?php
$route = '/tool/:tool_id/notes/';
$app->post($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['type']) && isset($param['note']))
		{

		$type = trim(mysql_real_escape_string($param['type']));
		$note = trim(mysql_real_escape_string($param['note']));

		$query = "INSERT INTO tool_notes(Tools_ID,Type,Note) VALUES(" . $tool_id . ",'" . $type . "','" . $note . "'); ";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$note_id = mysql_insert_id();

		$note_id = prepareIdOut($note_id,$host);

		$F = array();
		$F['note_id'] = $note_id;
		$F['type'] = $type;
		$F['note'] = $note;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
