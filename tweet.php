<?php
	//This page is also used for ajax call
	if($_GET['type']=='follower')
	{
		getTweets($_GET['screenname'],$_GET['type']);
	}
	function getTweets($sname,$type)
	{
		$tweeturl = 'https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=false&screen_name='.$sname.'&count=10';
		
		//create a channel using curl
		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL, $tweeturl);
		// Set ReturnTransfer constant so that curl_exec returns the result instead of dumping it.
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
		// Get the response and close the channel.
		$response = curl_exec($channel);
		curl_close($channel);
		
		//Since response from twitter api is in JSON so use json decode to convert it into readable array.
		$obj = json_decode($response);
		$divno = 1;	//For numbering divs
		$replacecount =1;	//For no. of replacement done in str_replace
		
		foreach ($obj as $item)
		{
			$text = $item->text;	//Get tweet
			$timestamp = $item->created_at;	//Get timestamp
			
			if($type == 'tweet')
			{
				echo "<div id='divTweet$divno'>";
			}
			if($type =='follower')
			{
				echo "<div id='divFollo$divno'>";
			}
			echo "<p align='center'>Tweets of $sname</p>";
			echo "<p align='center'>Tweet $divno of 10</p>";
			//the created_at contains +0000 which is not required so remove it
			print $text."<br> Tweeted on: ".str_replace("+0000","",$timestamp,$replacecount);
			echo "</div>";

			$divno = $divno + 1;
		}
	}
?>