<?php
$route = '/tool/:tool_id/notes/:note_id';
$app->put($route, function ($tool_id,$note_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);
	$note_id = prepareIdIn($note_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['Type']) && isset($param['Note']))
		{

		$Type = trim(mysql_real_escape_string($param['Type']));
		$Note = trim(mysql_real_escape_string($param['Note']));

		$query = "UPDATE tool_notes SET Type = '" . $Type . "', Note = '" . $Type . "' WHERE ID = " . $note_id;
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$Note_ID = mysql_insert_id();

		$note_id = prepareIdOut($note_id,$host);

		$F = array();
		$F['note_id'] = $note_id;
		$F['type'] = $Type;
		$F['note'] = $Note;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
