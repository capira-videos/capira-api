<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta charset="utf-8">
<title>Crawler</title>
</head>
<body>
<form action="youtubeCrawler.php" method="get">
<input type="text" name="user" placeholder="YT Username" value="JoernLoviscach" />
<input type="text" name="parent" placeholder="Folder/Channel id to put videos in" value="140" />
<input type="submit" value="Crawl!" />
</form>
</body>
</html>
<?php
include 'common.php';
//unset($mysqli);
//include 'libraries/debug.php';
include 'libraries/crawler.php';

function fetchPlaylist($ytPlaylistId, $authorId, $playlistId) {
	$start = 1;
	$results = 25;
	while ($results > 24) {
		$results = fetchPlaylistPage($ytPlaylistId, $start, $authorId, $playlistId);
		$start += $results;
	}
}

function fetchPlaylistPage($ytPlaylistId, $start, $authorId, $playlistId) {

	$url = "http://gdata.youtube.com/feeds/api/playlists/" . $ytPlaylistId . "?v=2&alt=json&start-index=" . $start;
	$data = json_decode(file_get_contents($url), true);
	$info = $data["feed"];
	$video = $info["entry"];
	$nVideo = count($video);

	echo "<br><br>Playlist Name: " . $info["title"]['$t'] . '<br/>';
	echo "Number of Videos (" . $nVideo . "):<br/><br/>";
	for ($i = 0; $i < $nVideo; $i++) {
		$title = $video[$i]['title']['$t'];

		$videoId = $video[$i]['media$group']['yt$videoid']['$t'];

		$tags = fetchTags($videoId);


		$unitId = insertUnitIfNotExists($title, $videoId, $tags, $authorId);
		//echo 'title=' . $title . " videoId=" . $videoId . " tags=" . $tags . " authorId=" . $authorId. "unitId=".$unitId;

		if($unitId !== false) insertUnitInPlaylist($unitId, $playlistId);

		//echo "Name: " . $title . '<br/>';
		//echo "Link: " . $videoId . '<br/>';
		//echo "Keywords: " . $tags . "<br><br>";
	}
	return $nVideo;
}



function fetchTags($videoId) {
	$tags = array('keywords' => ''); //get_meta_tags("https://www.youtube.com/watch?v=" . $videoId); // macht momentan fehler
	return $tags['keywords']; // komma getrennt
}

function fetchPlaylists($userId, $parent) {

	$url = "http://gdata.youtube.com/feeds/api/users/" . $userId . "/playlists?v=2&alt=json";
	$data = json_decode(file_get_contents($url), true);
	$info = $data["feed"];
	$video = $info["entry"];
	$nVideo = count($video);
	$userName = $info['author'][0]['name']['$t'];
	$authorId = insertAuthor($userId, $userName);

	echo "Channel Name: " . $info["title"]['$t'] . '<br/>Author:' . $authorId . '<br><br>';

	for ($i = 0; $i < $nVideo; $i++) {
		$title = $video[$i]['title']['$t'];
		$description = $video[$i]['summary']['$t'];

		$ytPlaylistId = $video[$i]['yt$playlistId']['$t'];
		$tags = '';
		$playlistId = insertPlaylist($ytPlaylistId, $title, $description, $authorId, $tags, $parent);
		if ($playlistId != 0) {
			fetchPlaylist($ytPlaylistId, $authorId, $playlistId);
		}
		echo "Name: " . $title . '<br/>';
		echo "Link: " . $ytPlaylistId . '<br/>';
		echo "Description: " . $description . '<br/><br/>';

	}

}



if (isset($_GET['user']) && isset($_GET['parent'])) {
	fetchPlaylists($_GET['user'], $_GET['parent']);
}
?>
