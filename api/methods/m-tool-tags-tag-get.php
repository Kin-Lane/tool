<?php
$route = '/tool/tags/:tag/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	trim(mysql_real_escape_string($tag));

	$Query = "SELECT DISTINCT t.* FROM tools t";
	$Query .= " JOIN tools_tag_pivot ttp ON t.Tools_ID = ttp.Tools_ID";
	$Query .= " JOIN tags ON ttp.Tag_ID = tags.Tag_ID";
	$Query .= " WHERE tags.Tag = '" . $tag . "'";
	$Query .= " ORDER BY t.Name ASC";
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

		$logo = "";
		$PostCheckQuery = "SELECT Tools_Image_ID,Image_Path FROM tools_image WHERE Tools_ID = " . $tool_id . " ORDER BY Tools_Image_ID DESC LIMIT 1";
		//echo $PostCheckQuery . "<br />";
		$CheckResult = mysql_query($PostCheckQuery) or die('Query failed: ' . mysql_error());

		if($CheckResult && mysql_num_rows($CheckResult))
			{
			$CheckResult = mysql_fetch_assoc($CheckResult);
			$logo = $CheckResult['Image_Path'];
			}

		// Website
		$Website_URL = "";
		$query = "SELECT Tools_URL_ID,Type,URL FROM tools_url WHERE Tools_ID = " . $tool_id . " AND Type = 'Website'";
		$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($link = mysql_fetch_assoc($linkResult))
			{
			$url = $link['URL'];
			}

		// BLog
		$Blog_URL = "";
		$query = "SELECT Tools_URL_ID,Type,URL FROM tools_url WHERE Tools_ID = " . $tool_id . " AND Type = 'Blog'";
		$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($link = mysql_fetch_assoc($linkResult))
			{
			$Blog_URL = $link['URL'];
			}

		// BLog	RSS
		$Blog_RSS_URL = "";
		$query = "SELECT Tools_URL_ID,Type,URL FROM tools_url WHERE Tools_ID = " . $tool_id . " AND Type = 'Blog RSS'";
		$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($link = mysql_fetch_assoc($linkResult))
			{
			$Blog_RSS_URL = $link['URL'];
			}

		// Twitter
		$Twitter_URL = "";
		$query = "SELECT Tools_URL_ID,Type,URL FROM tools_url WHERE Tools_ID = " . $tool_id . " AND Type = 'Twitter'";
		$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($link = mysql_fetch_assoc($linkResult))
			{
			$Twitter_URL = $link['URL'];
			}

		// Github
		$Github_URL = "";
		$query = "SELECT Tools_URL_ID,Type,URL FROM tools_url WHERE Tools_ID = " . $tool_id . " AND Type = 'Github'";
		$linkResult = mysql_query($query) or die('Query failed: ' . mysql_error());
		while ($link = mysql_fetch_assoc($linkResult))
			{
			$Github_URL = $link['URL'];
			}

		// manipulation zone

		$F = array();
		$F['tool_id'] = $tool_id;
		$F['name'] = $name;
		$F['user'] = $user;
		$F['details'] = scrub($details);
		$F['post_date'] = $post_date;

		$F['url'] = $url;
		$F['logo'] = $logo;

		$F['blog_url'] = $Blog_URL;
		$F['blog_rss_url'] = $Blog_RSS_URL;
		$F['twitter_url'] = $Twitter_URL;
		$F['github_url'] = $Github_URL;

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
