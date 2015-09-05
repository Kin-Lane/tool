<?php
$route = '/tool/tags/:tag/groupbytag/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	trim(mysql_real_escape_string($tag));

	$tools = "";
	$Query = "SELECT DISTINCT t.* FROM tools t";
	$Query .= " JOIN tools_tag_pivot ttp ON t.Tools_ID = ttp.Tools_ID";
	$Query .= " JOIN tags ON ttp.Tag_ID = tags.Tag_ID";
	$Query .= " WHERE tags.Tag = '" . $tag . "'";
	$Query .= " ORDER BY t.Name ASC";
	//echo $Query . "<br />";
	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
	$First = 1;
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{
		$tool_id = $Database['Tools_ID'];
		if($First==1)
			{
			$tools = $tool_id;
			$First = 0;
			}
		else
			{
			$tools .= "," . $tool_id;
			}
		}

	$GroupTagQuery = "SELECT DISTINCT";
	$GroupTagQuery .= " tt.Tag as Group_Tag";
	$GroupTagQuery .= " FROM tools t";
	$GroupTagQuery .= " JOIN tools_tag_pivot ttp ON t.Tools_ID = ttp.Tools_ID";
	$GroupTagQuery .= " JOIN tags tt ON ttp.Tag_ID = tt.Tag_ID";
	$GroupTagQuery .= " WHERE ttp.Tools_ID IN(" . $tools . ")";
	$GroupTagQuery .= " ORDER BY Group_Tag, t.Name ASC";
	//echo $GroupTagQuery . "<br />";
	$GroupTagResult = mysql_query($GroupTagQuery) or die('Query failed: ' . mysql_error());

	while ($GroupTag = mysql_fetch_assoc($GroupTagResult))
		{
		$group_tag = $GroupTag['Group_Tag'];

		$Query = "SELECT";
		$Query .= "  t.*,";
		$Query .= " tt.Tag as Group_Tag,";
		$Query .= " (SELECT Image_Path FROM tools_image WHERE Tools_ID = t.Tools_ID ORDER BY Tools_Image_ID DESC LIMIT 1) AS Logo,";
		$Query .= " (SELECT URL FROM tools_url WHERE Tools_ID = t.Tools_ID AND Type = 'Website' ORDER BY Tools_URL_ID DESC LIMIT 1) AS Website_URL,";
		$Query .= " (SELECT URL FROM tools_url WHERE Tools_ID = t.Tools_ID AND Type = 'Blog' ORDER BY Tools_URL_ID DESC LIMIT 1) AS Blog_URL,";
		$Query .= " (SELECT URL FROM tools_url WHERE Tools_ID = t.Tools_ID AND Type = 'Blog RSS' ORDER BY Tools_URL_ID DESC LIMIT 1) AS Blog_RSS_URL,";
		$Query .= " (SELECT URL FROM tools_url WHERE Tools_ID = t.Tools_ID AND Type = 'Twitter' ORDER BY Tools_URL_ID DESC LIMIT 1) AS Twitter_URL,";
		$Query .= " (SELECT URL FROM tools_url WHERE Tools_ID = t.Tools_ID AND Type = 'Github' ORDER BY Tools_URL_ID DESC LIMIT 1) AS Github_URL";
		$Query .= " FROM tools t";
		$Query .= " JOIN tools_tag_pivot ttp ON t.Tools_ID = ttp.Tools_ID";
		$Query .= " JOIN tags tt ON ttp.Tag_ID = tt.Tag_ID";
		$Query .= " WHERE tt.Tag = '" . $group_tag . "' AND ttp.Tools_ID IN(" . $tools . ")";
		$Query .= " ORDER BY t.Name ASC";
		//echo $Query . "<br />";
		$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

		if(mysql_num_rows($DatabaseResult) > 0)
			{
			if($group_tag != $tag && $group_tag != 'Github' && $group_tag != 'Swagger-Core' && $group_tag != 'API Blueprint Core')
				{
		  		$ReturnObject[$group_tag] = array();
		  		}
		  	}

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

			$logo = $Database['Logo'];
			$group_tag = $Database['Group_Tag'];

			$url = $Database['Website_URL'];
			$Blog_URL = $Database['Blog_URL'];
			$Blog_RSS_URL = $Database['Blog_RSS_URL'];
			$Twitter_URL = $Database['Twitter_URL'];
			$Github_URL = $Database['Github_URL'];

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

			$F['group_tag'] = $group_tag;

			if($group_tag != $tag && $group_tag != 'Github' && $group_tag != 'Swagger-Core' && $group_tag != 'API Blueprint Core')
				{
				array_push($ReturnObject[$group_tag], $F);
				}
			}
		}

		$app->response()->header("Content-Type", "application/json");
		echo stripslashes(format_json(json_encode($ReturnObject)));
	});
?>
