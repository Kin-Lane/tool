<?php
$route = '/tool/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['query'])){ $query = trim(mysql_real_escape_string($param['query'])); } else { $query = '';}
	if(isset($param['page'])){ $page = trim(mysql_real_escape_string($param['page'])); } else { $page = 0;}
	if(isset($param['count'])){ $count = trim(mysql_real_escape_string($param['count'])); } else { $count = 250;}
	if(isset($param['sort'])){ $sort = trim(mysql_real_escape_string($param['sort'])); } else { $sort = 'Name';}
	if(isset($param['order'])){ $order = trim(mysql_real_escape_string($param['order'])); } else { $order = 'ASC';}

	if($query!='')
		{
		$Query = "SELECT * FROM tools t WHERE t.Name = '%" . $query . "%' OR t.Details LIKE '%" . $query . "%'";
		}
	else
		{
		$Query = "SELECT * FROM tools t";
		}

	$Query .= " ORDER BY t." . $sort . " " . $order . " LIMIT " . $page . "," . $count;
	//echo $Query . "<br />";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$tool_id = $Database['Tools_ID'];
		$name = $Database['Name'];
		$user = $Database['Github_User'];
		$details = $Database['Details'];
		$details = scrub($details);
		$post_date = $Database['Post_Date'];
		$url = $Database['URL'];
		$logo = $Database['Logo'];

		$forks = $Database['Github_Forks'];
		$followers = $Database['Github_Followers'];
		$watchers = $Database['Github_Watchers'];

		// manipulation zone

		$F = array();
		$F['tool_id'] = $tool_id;
		$F['name'] = $name;
		$F['user'] = $user;
		$F['details'] = scrub($details);
		$F['post_date'] = $post_date;
		$F['url'] = $url;
		$F['logo'] = $logo;

		$F['forks'] = $forks;
		$F['followers'] = $followers;
		$F['watchers'] = $watchers;

		$F['tags'] = array();

		$TagQuery = "SELECT t.tag_id, t.tag from tags t";
		$TagQuery .= " INNER JOIN tools_tag_pivot ttp ON t.tag_id = ttp.tag_id";
		$TagQuery .= " WHERE ttp.Tools_ID = " . $tool_id;
		$TagQuery .= " ORDER BY t.tag DESC";
		$TagResult = mysql_query($TagQuery) or die('Query failed: ' . mysql_error());

		while ($Tag = mysql_fetch_assoc($TagResult))
			{
			$thistag = $Tag['tag'];

			$T = array();
			$T = $thistag;
			array_push($F['tags'], $T);
			//echo $thistag . "<br />";
			if($thistag=='Archive')
				{
				$archive = 1;
				}
			}

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
