<?php
	function getFollo($sname)
	{
		/*Use this to get ids of followers of logged in user. Since some of the integer user_ids are large
		to hold so here I have used stringify_ids option as true to convert the integer user_ids into string for ease*/
		$follourl = 'https://api.twitter.com/1/followers/ids.json?cursor=-1&stringify_ids=true&screen_name='.$sname;
		
		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL, $follourl);
		// Set ReturnTransfer constant so that curl_exec returns the result instead of dumping it.
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
		// Get the response and close the channel.
		$response = curl_exec($channel);
		curl_close($channel);
		
		/* The ids JSON returns the user_ids in array in sider array format. And the Lookup api needs the comma seperated
		list of user ids. So following is the code to create a comma seperated list of user ids */
		$obj = json_decode($response);
		echo "<br>";
		$idSet = "";
		$limit = 0;
		foreach ($obj as $item)
		{
			//Run one more loop for "ids" array
			if ( is_array( $item ) )
			{
				foreach ( $item as $sub_item )
				{
					//There is a limit to get only 10 Followers, So...
				 	if ($limit <10)
				 	{
					  $id = $sub_item;
					  $idSet = $idSet.",".$id;	//Concatenate ids in comma seperated format
					  $limit = $limit +1;
					}
				}
		  	} 
		}
		
		//Now attach it in a twitter url. Lookup url sends requested user info and it accepts cs format of user ids max to 100
		$lookurl='https://api.twitter.com/1/users/lookup.json?user_id='.$idSet.'&include_entities=true';
		
		//Now get JSON user data thru curl
		$channel = curl_init();
		curl_setopt($channel, CURLOPT_URL, $lookurl);
		// Set ReturnTransfer constant so that curl_exec returns the result instead of dumping it.
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
		// Get the response and close the channel.
		$response = curl_exec($channel);
		curl_close($channel);
		
		$obj = json_decode($response);
		echo "<div id='divList' style='height: 150px; width: 700px; max-height: 150px; max-width: 700px;'>";
		echo "<font style='text-decoration:underline;'>Followers of $sname:</font><br><br>";
		foreach ($obj as $item)
		{
			$name = $item->name;	//Get name
			$screenname = $item->screen_name;	//Get screen name
			$profilepic = $item->profile_image_url_https;	//Get profile image url(HTTPS)
			
			echo "<div>";
				echo "<img src=$profilepic style='vertical-align:middle;' title='Profile Picture'>";
				echo "<span style='padding-left:10px;'>Name: ".$name." | Screen Name: ".$screenname." | ";
				?><a href="#" onClick="ajaxLoad('<?php echo $screenname?>')">View Tweets</a><?php
			echo "</span></div><hr>";
		}
		echo "</div>";
	}
?>
<script type="text/javascript">
	function ajaxLoad(sname)
	{
		var screenname = sname;
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
		  	if (xmlhttp.readyState==4 && xmlhttp.status==200) //When completed request
		  	{
				document.getElementById("divTweetContainer").innerHTML=xmlhttp.responseText;
				repeatSlideshow();
		  	}
		}
		xmlhttp.open("GET","tweet.php?screenname="+screenname+"&type=follower",true);
		xmlhttp.send();
	}
	function repeatSlideshow()
	{
		$('.slideshowTweet').cycle({
			fx: 'fade'
		});
	}
</script>