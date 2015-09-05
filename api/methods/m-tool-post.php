<?php
$route = '/tool/';
$app->post($route, function () use ($app){

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = 'No Name'; }
	if(isset($param['details'])){ $details = trim(mysql_real_escape_string($param['details'])); } else { $details = ''; }
	if(isset($param['url'])){ $url = trim(mysql_real_escape_string($param['url'])); } else { $url = ''; }
	if(isset($param['logo'])){ $logo = trim(mysql_real_escape_string($param['logo'])); } else { $logo = ''; }

	$post_date = date('Y-m-d H:i:s');

  	$Query = "SELECT * FROM tools WHERE Name = '" . $name . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$ThisItem = mysql_fetch_assoc($Database);
		$tool_id = $ThisItem['Tools_ID'];
		}
	else
		{
		$Query = "INSERT INTO tools(";
		$Query .= "Name,";
		$Query .= "Post_Date,";

		if($details!='') { $Query .= "Details,"; }
		if($url!='') { $Query .= "URL,"; }
		if($logo!='') { $Query .= "Logo,"; }

		$Query .= "Closing";
		$Query .= ") VALUES(";
		$Query .= "'" . mysql_real_escape_string($name) . "',";
		$Query .= "'" . mysql_real_escape_string($post_date) . "',";

		if($details!='') { $Query .= "'" . mysql_real_escape_string($details) . "',"; }
		if($url!='') { $Query .= "'" . mysql_real_escape_string($url) . "',"; }
		if($logo!='') { $Query .= "'" . mysql_real_escape_string($logo) . "',"; }

		$Query .= "'nothing'";

		$Query .= ")";

		//echo $query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$tool_id = mysql_insert_id();
		}

	$host = $_SERVER['HTTP_HOST'];
	$tool_id = prepareIdOut($tool_id,$host);

	$F = array();
	$F['tool_id'] = $tool_id;
	$F['name'] = $name;

	$ReturnObject = $F;

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
