<?php
$route = '/tool/:tool_id/notes/';
$app->get($route, function ($tool_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdIn($tool_id,$host);

	$ReturnObject = array();

	$Query = "SELECT ID,Tools_ID,Type,Note From tool_notes cn";
	$Query .= " WHERE cn.Tools_ID = " . $tool_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$note_id = $Database['ID'];
		$Type = $Database['Type'];
		$Note = $Database['Note'];

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
