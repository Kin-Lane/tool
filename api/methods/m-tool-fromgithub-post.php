<?php
$route = '/tool/fromgithub/';
$app->post($route, function () use ($app){

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['name'])){ $name = trim(mysql_real_escape_string($param['name'])); } else { $name = 'No Name'; }
	if(isset($param['username'])){ $username = trim(mysql_real_escape_string($param['username'])); } else { $username = ''; }
	if(isset($param['description'])){ $description = trim(mysql_real_escape_string($param['description'])); } else { $description = ''; }
	if(isset($param['url'])){ $url = trim(mysql_real_escape_string($param['url'])); } else { $url = ''; }
	if(isset($param['logo'])){ $logo = trim(mysql_real_escape_string($param['logo'])); } else { $logo = 'https://s3.amazonaws.com/kinlane-productions/bw-icons/bw-github.png'; }
	if(isset($param['forks'])){ $forks = trim(mysql_real_escape_string($param['forks'])); } else { $forks = 0; }
	if(isset($param['stargazers'])){ $stargazers = trim(mysql_real_escape_string($param['stargazers'])); } else { $stargazers = 0; }
	if(isset($param['watchers'])){ $watchers = trim(mysql_real_escape_string($param['watchers'])); } else { $watchers = 0; }

	$post_date = date('Y-m-d H:i:s');

  	//$Query = "SELECT * FROM tools WHERE Name = '" . $name . "'";
	$Query = "SELECT * FROM tools WHERE Name = '" . $name . "' AND Github_User = '" . $username . "'";
	echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$ThisItem = mysql_fetch_assoc($Database);
		$tool_id = $ThisItem['Tools_ID'];

		$query = "UPDATE tools SET";

		if($name!='') { $query .= " name = '" . mysql_real_escape_string($name) . "'"; }
		if($username!='') { $query .= ", Github_User = '" . mysql_real_escape_string($username) . "'"; }
		if($description!='') { $query .= ", Details = '" . mysql_real_escape_string($description) . "'"; }
		if($url!='') { $query .= ", URL = '" . mysql_real_escape_string($url) . "'"; }
		if($logo!='') { $query .= ", Logo = '" . mysql_real_escape_string($logo) . "'"; }
		if($forks!='') { $query .= ", Github_Forks = '" . mysql_real_escape_string($forks) . "'"; }
		if($stargazers!='') { $query .= ", Github_Followers = '" . mysql_real_escape_string($stargazers) . "'"; }
		if($watchers!='') { $query .= ", Github_Watchers = '" . mysql_real_escape_string($watchers) . "'"; }

		$query .= " WHERE Tools_ID = " . $tool_id;

		echo $Query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());

		}
	else
		{
		$Query = "INSERT INTO tools(";
		$Query .= "Name,";
		$Query .= "Post_Date,";

		if($description!='') { $Query .= "Details,"; }
		if($username!='') { $Query .= "Github_User,"; }
		if($url!='') { $Query .= "URL,"; }
		if($logo!='') { $Query .= "Logo,"; }

		if($forks!='') { $Query .= "Github_Forks,"; }
		if($stargazers!='') { $Query .= "Github_Followers,"; }
		if($watchers!='') { $Query .= "Github_Watchers,"; }

		$Query .= "Closing";
		$Query .= ") VALUES(";
		$Query .= "'" . mysql_real_escape_string($name) . "',";
		$Query .= "'" . mysql_real_escape_string($post_date) . "',";

		if($description!='') { $Query .= "'" . mysql_real_escape_string($description) . "',"; }
		if($username!='') { $Query .= "'" . mysql_real_escape_string($username) . "',"; }
		if($url!='') { $Query .= "'" . mysql_real_escape_string($url) . "',"; }
		if($logo!='') { $Query .= "'" . mysql_real_escape_string($logo) . "',"; }

		if($forks!='') { $Query .= "'" . mysql_real_escape_string($forks) . "',"; }
		if($stargazers!='') { $Query .= "'" . mysql_real_escape_string($stargazers) . "',"; }
		if($watchers!='') { $Query .= "'" . mysql_real_escape_string($watchers) . "',"; }

		$Query .= "'nothing'";

		$Query .= ")";

		echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$tool_id = mysql_insert_id();
		}

	// Tag it
	$tag = 'Github';
	$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
	$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
	if($CheckTagResults && mysql_num_rows($CheckTagResults))
		{
		$Tag = mysql_fetch_assoc($CheckTagResults);
		$Tag_ID = $Tag['Tag_ID'];
		}
	else
		{
		$query = "INSERT INTO tags(Tag) VALUES('" . trim($param['Tag']) . "'); ";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		$Tag_ID = mysql_insert_id();
		}

	$CheckTagPivotQuery = "SELECT * FROM tools_tag_pivot where Tag_ID = " . trim($Tag_ID) . " AND Tools_ID = " . $tool_id;
	$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());
	if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
		{
		$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);
		}
	else
		{
		$query = "INSERT INTO tools_tag_pivot(Tag_ID,Tools_ID) VALUES(" . $Tag_ID . "," . $tool_id . "); ";
		mysql_query($query) or die('Query failed: ' . mysql_error());
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
