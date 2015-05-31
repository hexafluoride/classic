<?php

error_reporting(E_ALL ^ E_NOTICE);
function ReplaceKeywords($Matches)
{
	if($Matches[2])
		$GoodStuff = $Matches[3];
	else
		$GoodStuff = $Matches[4];

	if($GoodStuff)
	{		
		switch(strtolower($Matches[1]))
		{
			case "total":
				switch(strtolower($GoodStuff))
				{
					case "pages":
						$pageTotal = mysql_query("Select `ID` from `Wiki_Pages`");
						$totalPages = mysql_num_rows($pageTotal);
						
						return number_format($totalPages);
					break;
				}
			break;
			
			case "ad":
				$position = strtolower($GoodStuff);
				
				if($position == 'right')
					$class = 'right';
				
				elseif($position == 'left')
					$class = 'left';
			
				$ID = uuid();
				return "<div id='$ID' class='$class'><iframe id='scraper-friend' src='http://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>";
			break; 
			
			case "ads":
				$ID = uuid();
				$ID2 = uuid();
				return "<div id='$ID' class='left'><iframe id='scraper-friend' src='http://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>"
						."<div id='$ID2' class='right'><iframe id='scraper-friend' src='http://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>";
 
			break;
			
			case "pre":
				$GoodStuff = trim(str_replace(array("{", "}", '[', ']'), array("&#123;", "&#125;", '&#91;', '&#93;'), $GoodStuff));
				return "<pre>$GoodStuff</pre>";
			break;

			case "center":
				return "<center>$GoodStuff</center>";
			break;
			
			case "magic":
				$Words = str_split_unicode(html_entity_decode($GoodStuff, false, 'UTF-8'));
				$Splitter = '';
							
				foreach($Words as $Word)
				{
					$magic .= "<span class='magic'>$Word</span>";
				}
				
				return "<span class='brew'>$GoodStuff</span>";
			break;
			
			case "color":
				$args = explode("|", $GoodStuff, 3);

				if(count($args) == 3)
				{
					if($args[1])
						$args[1] = "background-color:{$args[1]}";
					
					return "<span style='color:{$args[0]}; {$args[1]}'>{$args[2]}</span>";					
				}
				elseif(count($args) == 2)
				{
					return "<span style='color:{$args[0]}'>{$args[1]}</span>";
				}
				else
					return $GoodStuff;
				
			break;
			
			case "infobox":
				$args = explode("|", $GoodStuff, 3);

				if($args[2])
					return "<div class='wiki-box' style='{$args[0]}'><div class='wiki-box-title'>{$args[1]}</div>{$args[2]}</div>";
				else
					return "<div class='wiki-box'><div class='wiki-box-title'>{$args[0]}</div>{$args[1]}</div>";
			break;

			case "box":
				$args = explode("|", $GoodStuff, 2);

				if($args[1])
					return "<div class='wiki-box' style='{$args[0]}'>{$args[1]}</div>";
				else
					return "<div class='wiki-box'>{$args[0]}</div>";
			break;
			
			case "title":
				return "<div class='wiki-box-title'>$GoodStuff</div>";
			break;

			case "style":
				$args = explode("|", $GoodStuff, 2);
				
				return "<div style='{$args[0]}'>{$args[1]}</div>";
			break;

			case "right":
				return "<div class='right'>$GoodStuff</div>";
			break;
			
			case "left":
				return "<div class='left'>$GoodStuff</div>";
			break;

			case "bold":
			case "b":
				return "<b>$GoodStuff</b>";
			break;

			case "underline":
			case "u":
				return "<u>$GoodStuff</u>";
			break;

			case "italic":
			case "italics":
			case "i":
				return "<i>$GoodStuff</i>";
			break;

			case "strike":
			case "s":
				return "<span class='strike'>$GoodStuff</span>";
			break;

			case "big":
				return "<span class='big'>$GoodStuff</span>";
			break;

			case "medium":
			case "med":
				return "<span class='medium'>$GoodStuff</span>";
			break;

			case "small":
			case "sml":
				return "<span class='small'>$GoodStuff</span>";
			break;

			case "redirect":
                if(isset($_GET['random']))
                    return "<meta http-equiv='refresh' content='1;url=/$GoodStuff/?random'>You're being brought to '$GoodStuff'...";
                else
                    return "<meta http-equiv='refresh' content='1;url=/$GoodStuff'>You're being brought to '$GoodStuff'...";
			break;
			
			case "heading":
				//return "<a name='".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", " "), "", $GoodStuff)."'></a>$GoodStuff<hr />";
				return "<div class='clear'>$GoodStuff</div><hr />";
			break;
			
			case "subheading":
				//return "<a name='".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", " "), "", $GoodStuff)."'></a>$GoodStuff<hr />";
				return "$GoodStuff<hr />";
			break;
			
			case "url":
				list($Link, $Text) = explode("|", $GoodStuff, 2);

				$Link = trim($Link);
				$Text = trim($Text);

				if(preg_match("{(\S*)://(.*)}", $Link, $LinkMatches))
				{
					$LinkMatches[1] = trim($LinkMatches[1]);
					$LinkMatches[2] = trim($LinkMatches[2]);

					switch(strtolower($LinkMatches[1]))
					{
						case "http":
						case "https":
						case "ftp":
						case "irc":
							$URL = "$LinkMatches[1]://$LinkMatches[2]";

							if(empty($Text))
								$Text = $URL;

							return "<a href='$URL' target='_blank'>$Text</a>";
						break;

						case "fish":
							$URL = "http://wiki.wetfish.net/$LinkMatches[2]";

							if(empty($Text))
								$Text = $URL;

							return "<a href='$URL'>$Text</a>";
						break;
					}
				}
				else
				{
					if(empty($Text))
						$Text = "http://$Link";

					return "<a href='http://$Link' target='_blank'>$Text</a>";
				}
			break;

			case "image":
			case "img":
				//list($Link, $Size, $Position, $Border, $Text) = explode("|", $GoodStuff, 5);
		
				$args = explode('|', $GoodStuff, 6);
				$Link = trim($args[0]);
				$Border = trim($args[3]);			
				$Text = trim($args[4]);
				$rand = trim($args[5]);
				
//				$args[1] = trim(str_replace('px', '', strtolower($args[1])));
//				$args[2] = trim(str_replace('px', '', strtolower($args[2])));
				
				$args[1] = trim($args[1]);
				$args[2] = trim($args[2]);
				
				if(empty($args[1]))
					$args[1] = 0;
				
				if(is_numeric($args[1]) and (is_string($args[2]) or empty($args[2])))
				{
					$Size = $args[1];
					$Position = $args[2];
				}
				elseif(is_string($args[1]) and (is_numeric($args[2]) or empty($args[2])))
				{
					$Size = $args[2];
					$Position = $args[1];
				}
				
				if(strtolower($Position) == "right" or strtolower($Position) == "left")
					$Position = "float:$Position;";
				elseif(strtolower($Position) == "border")
					$Border = true;
				elseif(empty($Text))
					$Text = $Position;

				if($Border)
					$Border = "border: 4px solid #C17EE8; padding:4px; margin-bottom:16px; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;";
				else
					unset($Border);
				
				if(is_numeric($Size) and $Size < 1600 and $Size > 0 and strpos($Link, 'http://') === false)
				{
					$Info = pathinfo($Link);

                    // Make sure the file actually exists
                    if(!file_exists("upload/{$Info['basename']}"))
                    {
                        // Display an icon if the image can't be loaded
                        $ImageText = "<a href='#error' class='exempt'><img src='/upload/apple.gif' title='There was an error loading this image' border='0' /></a>";
                    }
                    else
                    {
                        if(!file_exists("upload/{$Size}_{$Info['basename']}"))
                            ResizeImage($Link, "upload/{$Size}_{$Info['basename']}", $Size);
                            
                        $ImageText = "<a href='/$Link' class='exempt'><img src='/upload/{$Size}_{$Info['basename']}' border='0' /></a>";
                    }
				}
				else
				{
					if(strpos($Link, 'http://') === false)
						$Link = "/$Link";
				
					unset($Size);
				
					$URL = parse_url($Link);

                    // Make sure wiki images exist
                    if(preg_match("{^/upload}", $URL['path']) && !file_exists(".{$URL['path']}"))
                    {
                        // Display an icon if the image can't be loaded
                        $ImageText = "<a href='#error' class='exempt'><img src='/upload/apple.gif' title='There was an error loading this image' border='0' /></a>";
                    }
                    else
                    {
                        if($URL['host'] == "glitch.wetfish.net")
                            $class = "class='glitchme'";
                        
                        // This is a terrible hack and really should be expanded to parse and reconstruct the url
                        if($rand)
                            $rand = "&rand=".mt_rand();
                        
                        // Lol I should finish this some time
                        if(is_numeric($Size) and $Size < 1600 and $Size > 0)
                            $size = "style=''";
                        
                        $ImageText = "<img src='$Link$rand' $class />";
                    }
				}
				
				if($Text)
					$Text = "<div class='small'>$Text</div>";
				
				return "<div style='margin:0px 8px; display:inline-block; max-width:100%; $Position $Border'>$ImageText $Text</div>";
			break;

			case "red":
			case "redtube":
				$URL = parse_url($GoodStuff);
				$Video = trim($URL['path'], '/');
				
				return "<object height='344' width='434'> <param name='movie' value='http://embed.redtube.com/player/'> <param name='FlashVars' value='id=$Video&style=redtube&autostart=false'> <embed src='http://embed.redtube.com/player/?id=$Video&style=redtube' flashvars='autostart=false' pluginspage='http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' height='344' width='434' /> </object>";
			break;
			
			case "motherless":
				$URL = parse_url($GoodStuff);
				
				if($URL['host']	== "motherless.com")
				{
					$Video = trim($URL['path'], '/');
					$Data = file_get_contents($GoodStuff);
					
					preg_match("/var __file_url = '(.*?)';/", $Data, $Filename);
					
					return "<embed type='application/x-shockwave-flash' src='http://motherless.com/flash/player.swf' style='' id='player' name='player' quality='high' allowfullscreen='true' allowscriptaccess='always' wmode='transparent' flashvars='file=http://members.motherless.com/movies/$Filename[1].flv&amp;image=http://motherless.com/thumbs/$Video.jpg&amp;mute=false&amp;streamer=lighttpd&amp;link=http://motherless.com/$Video' width='680' height='560'>";
				}
			break;
			
			case "video":
			case "youtube":
				$URL = parse_url($GoodStuff);
				parse_str($URL['query'], $Query);
				
				return "<iframe width='640' height='360' src='http://www.youtube.com/embed/{$Query['v']}' frameborder='0' allowfullscreen></iframe>";
				return "<object width='640' height='360'><param name='movie' value='http://www.youtube.com/v/{$Query['v']}&hl=en_US&fs=1&'></param><param name='allowFullScreen' value='true'></param><param name='allowscriptaccess' value='always'></param><embed src='http://www.youtube.com/v/{$Query['v']}&hl=en_US&fs=1&' type='application/x-shockwave-flash' allowscriptaccess='always' allowfullscreen='true' width='480' height='295'></embed></object>";
			break;

			case "playlist":
				$url = parse_url($GoodStuff);
				parse_str($url['query'], $query);

				return "<iframe width='640' height='360' src='http://www.youtube.com/embed/videoseries?list={$query['list']}&index={$query['index']}' frameborder='0' allowfullscreen></iframe>";			
			break;
			
			case "vimeo":
				$URL = parse_url($GoodStuff);
				$videoID = preg_replace("/[^0-9]/", "", $URL['path']);
				
				return "<iframe src='http://player.vimeo.com/video/$videoID?byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff' width='640' height='360' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
			break;
			
			case "soundcloud":
				$URL = parse_url($GoodStuff);
				parse_str($URL['query'], $Query);
				
				return "<iframe width='100%' height='166' scrolling='no' frameborder='no' src='http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F{$Query['id']}&show_artwork=true'></iframe>";
			break;		
			
			case "ytcracker":
				return "<iframe width='100%' height='450' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/users/2977873&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_artwork=true'></iframe>";
			break;
			case "load":
			case "embed":
				$URL = parse_url($GoodStuff);
				
				if(empty($URL['scheme']))
					$URL['scheme'] = "http";

				if(empty($URL['host']))
					$URL['host'] = "wiki.wetfish.net";

				if($URL['path'] == 'index.php' or empty($URL['path']) or $URL['path'] == $_GET['SUPERdickPAGE'] or !empty($_GET['load']))
					$URL['path'] = 'yousosilly.php';
					
				if(empty($URL['query']))
				{
					$Query = array();
				
					foreach($_GET as $Key => $Value)
					{
						if($Key != 'SUPERdickPAGE')
							$Query[] = "$Key=".urlencode($Value);
					}
					
					$URL['query'] = implode('&', $Query);
				}
				
				if(preg_match('/^.*\.?wetfish.net$/i', $URL['host']))
				{
					$ID = uuid();				
					
					if($URL['host'] == 'danger.wetfish.net')
					{
						$URL['path'] = substr($URL['path'], 1);
					
					
						if(preg_match("/^[a-f0-9]+$/", $URL['path']))
						{
							$URL['query'] = "hash={$URL['path']}";
							$URL['path'] = "view.php";
							
						}
					}
					
					//return "<iframe src='{$URL['scheme']}://{$URL['host']}/{$URL['path']}?{$URL['query']}' style='height:0px; width:0px; display:none;'></iframe>
					
					
					/**/if($URL['host'] != "wiki.wetfish.net")
						return "<div id='$ID'><script>$('#$ID').load('/load.php?id=$ID&url={$URL['scheme']}://{$URL['host']}/{$URL['path']}?".urlencode($URL['query'])."');</script></div>";
					else
						return "<div id='$ID'><script>$('#$ID').load('/{$URL['path']}?{$URL['query']}&load=true');</script></div>";
					/**/
					//return "LOL GOOGLE HACKED WETFISH";
				}
			break;
			
			case "music":
				list($Derp, $Delay) = explode("|", $GoodStuff, 2);

				$URL = parse_url($Derp);
				parse_str($URL['query'], $Query);

				if((strpos($Derp, 'http://') === false) or ((strpos($Derp, 'http://') !== false) and (preg_match('/^.*\.?wetfish.net$/i', $URL['host']))))
				{
					if($Delay)
					{
						$Unique = uuid();
						$Delay *= 1000;
						$Javascript = "<script>setTimeout(\"$('#$Unique').show()\", $Delay)</script>";
						return "$Javascript<embed id='$Unique' src='$Derp' width='0' height='0' autostart='true' loop='true' hidden='true' style='display:none;'></embed>";
					}
					else
						return "<embed src='$GoodStuff' width='0' height='0' autostart='true' loop='true' hidden='true'></embed>";
				}
			break;
			
			case "flash":
				list($url, $width, $height) = explode("|", $GoodStuff);

				return "<object width='$width' height='$height'><param name='movie' value='$url'><embed src='$url' width='$width' height='$height'></embed></object>";
			break;

			case "drainbow":
			case "dblrainbow":
			case "doublerainbow":
			case "rainbow2":
				$Words = str_split_unicode(html_entity_decode($GoodStuff, ENT_QUOTES, 'UTF-8'));
				$Splitter = '';
							
				foreach($Words as $Word)
				{
					/*
					$RandomRed = rand(0, 255);
					$RandomGreen = rand(0, 255);
					$RandomBlue  = rand(0, 255);
					
					$InvertedRed = 255 - $RandomRed;
					$InvertedGreen = 255 - $RandomGreen;
					$InvertedBlue = 255 - $RandomBlue;
					*/

					$rainbowCounter++;

					$randomHue = $rainbowCounter * 24 % 360;
					$randomSaturation = rand(0, 5) + 95;
					$randomLuminosity = rand(0, 24) + 44;

					$invertedHue = ($randomHue + 180) % 360;

					//background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);
					//$Stuff .= "<span style='color:rgb($RandomRed, $RandomGreen, $RandomBlue); background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);'>$Word</span>$Splitter";

					$Stuff .= "<span style='font-size:110%; color:hsla($randomHue, $randomSaturation%, $randomLuminosity%, 0.8); text-shadow:1px 1px #000; background-color:hsla($invertedHue, $randomSaturation%, $randomLuminosity%, 0.6);'>".$Word."</span>$Splitter";
				}
							
				return $Stuff;
			break;

            case "snow":
                return '<script type="text/javascript" src="/snowstorm.js"></script>';
            break;
                    
			case "rainbow":
				$Words = str_split_unicode(html_entity_decode($GoodStuff, ENT_QUOTES, 'UTF-8'));
				$Splitter = '';
							
				foreach($Words as $Word)
				{
					/*
					$RandomRed = rand(0, 255);
					$RandomGreen = rand(0, 255);
					$RandomBlue  = rand(0, 255);
					
					$InvertedRed = 255 - $RandomRed;
					$InvertedGreen = 255 - $RandomGreen;
					$InvertedBlue = 255 - $RandomBlue;
					*/

					$rainbowCounter++;

					$randomHue = $rainbowCounter * 24 % 360;
					$randomSaturation = rand(0, 5) + 95;
					$randomLuminosity = rand(0, 24) + 44;

					$invertedHue = ($randomHue + 180) % 360;

					//background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);
					//$Stuff .= "<span style='color:rgb($RandomRed, $RandomGreen, $RandomBlue); background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);'>$Word</span>$Splitter";

					$Stuff .= "<span style='font-size:110%; color:hsla($randomHue, $randomSaturation%, $randomLuminosity%, 0.8); text-shadow:1px 1px #000;'>".$Word."</span>$Splitter";
				}
							
				return $Stuff;
			break;
			
			case "glitch":
				$Splitter = ' ';
				$Words = preg_split("/$Splitter/", $GoodStuff);
				
				if(count($Words) == 1)
				{
					$Words = str_split($GoodStuff);
					$Splitter = '';
				}
				
				foreach($Words as $Word)
				{
					$rainbowCounter++;

					$randomHue = rand(0, 360);
					$randomSaturation = rand(0, 5) + 95;
					$randomLuminosity = rand(0, 44) + 14;

					$invertedHue = ($randomHue + 180) % 360;

					$Stuff .= "<span style='font-size:110%; color:hsl($randomHue, $randomSaturation%, $randomLuminosity%); background-color:hsl($invertedHue, $randomSaturation%, $randomLuminosity%);'>$Word</span>$Splitter";
				}
				
				return $Stuff;
			break;

            case "anchor":
                return "<a name='$GoodStuff'>&nbsp;</a>";
            break;
		}
	}
	else
	{
		// Self documenting!
		if($Matches[1])
			return $Matches[1]."&#91;&#93;";
	}
}

function ReplaceKeyPENIS($Matches)
{
	$FixedMatches[] = $Matches[0];
	$FixedMatches[] = $Matches[1];
	$FixedMatches[] = $Matches[3];
	$FixedMatches[] = $Matches[4];
	$FixedMatches[] = $Matches[5];


	return $Matches[2]."[".ReplaceKeywords($FixedMatches)."]";
}

function ReplaceLinks($Matches)
{
	if($Matches[2])
		$GoodStuff = $Matches[3];
	else
		$GoodStuff = $Matches[4];

	switch(strtolower($Matches[1]))
	{
		case "img":
		case "image":
			list($Link, $Size, $Position, $Border, $Text) = explode("|", $GoodStuff, 5);

			$Link = trim($Link);
			$Size = trim($Size);
			$Position = trim(strtolower($Position));
			$Border = trim($Border);
			$Text = trim($Text);
			$URL = parse_url($Link);
									
			if(preg_match("{https?}", $URL['scheme']) and !preg_match("/.wetfish.net$/", $URL['host']))
			{				
				$Path = pathinfo($Link);
				$Filename = uuid();
				$Extension = $Path['extension'];
				
				if(strpos($Extension, '?') !== FALSE)
					$Extension = substr($Extension, 0, strpos($Extension, '?'));
				
				if(preg_match('/^(jpe?g|gif|png)$/i', $Extension))
				{
					while(file_exists("upload/$Filename.$Extension"))
					{
						$Filename = uuid();
					}
						
					file_put_contents("upload/$Filename.$Extension", file_get_contents($Link));
					chmod("upload/$Filename.$Extension", 0644);

					$Time = time();
					$Account = $_SERVER['REMOTE_ADDR'];
					mysql_query("Insert into `Images` values ('NULL', '$Time', '', '$Account', '$Link', 'upload/$Filename.$Extension')");
	
					$Text = trim("upload/$Filename.$Extension|$Size|$Position|$Border|$Text", '|');
					return "img[$Text]";
				}
				else
				{
					return "HACKER!!!!!!!!!!!!!!";
				}
			}
			
			return "img[$GoodStuff]";
		break;
		
		case "soundcloud":
			$URL = parse_url($GoodStuff);
			parse_str($URL['query'], $Query);

			if($Query['id'])
				return "soundcloud[$GoodStuff]";
			else
			{
				$html = file_get_html($GoodStuff);
				$trackID = $html->find("div[data-sc-track]", 0)->getAttribute('data-sc-track');
			}
			
			return "soundcloud[$GoodStuff?id=$trackID]";
		break;
	}
}

function FishFormat($Input, $Action='markup')
{
	switch($Action)
	{
		case "strip":
			$Output = str_replace("\n", "", $Input);
		break;

		case "edit":
			$Output = str_replace("\t", "    ", $Input);

			while(preg_match('/^ *:+/m', $Output)) {
				$Output = preg_replace('/^( *):/m','\1    ', $Output); }

			preg_match_all("/\b(Image|IMG|SoundCloud) \s* (?:(\S) [\[{] \s* (.*) \s* [\]}] \\2 | [\[{] \s* (.*?) \s* [\]}])/xis", $Output, $Matches);
			
			foreach($Matches[0] as $Key => $String)
			{
				$Derp[0] = $Matches[0][$Key];
				$Derp[1] = $Matches[1][$Key];
				$Derp[2] = $Matches[2][$Key];
				$Derp[3] = $Matches[3][$Key];
				$Derp[4] = $Matches[4][$Key];

				$Output = str_replace($String, ReplaceLinks($Derp), $Output);
			}			
		break;

		case "ross":
			$Output = $Input;

			$Output = preg_replace('/\bhe\b/i', 'she', $Output);
			$Output = preg_replace('/\bhis\b/i', 'her', $Output);
			$Output = preg_replace('/\bross\b/i', 'Roseline', $Output);
			$Output = preg_replace('/\bmale\b/i', 'female', $Output);
			$Output = preg_replace('/\bnot a furry\b/i', 'a really, REALLY REALLY BIG FURRYY!!!!!!!!', $Output);
			$Output = preg_replace('/\bnot a tranny\b/i', 'absolutely in love with makeup and SHOES *^___^*', $Output);
		break;

		case "format":
			$Output = preg_replace('/(\w{14})/', "$1&#8203;", $Input);
		break;

		default:
			$Output = $Input;
			$Output = str_replace("    ", "&emsp;&emsp;&emsp;", $Output);

            if(isset($_GET['random']))
                $random = "/?random";

			$Output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)\|([\w -@\/~]+?)(?:\]\]|}})/',
			                                create_function('$Match', 'return "<a href=\'http://wiki.wetfish.net/".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;"), "", str_replace(" ", "-", $Match[1]))."'.$random.'\'>$Match[2]</a>";'),
			                                 $Output);
			$Output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)(?:\]\]|}})/', 
			                                create_function('$Match', 'return "<a href=\'http://wiki.wetfish.net/".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;"), "", str_replace(" ", "-", $Match[1]))."'.$random.'\'>$Match[1]</a>";'),
			                                $Output);
			
			$Output = str_replace(array(":{", "}:", ':[', ']:'), array("&#123;", "&#125;", "&#91;", "&#93;"), $Output);

			
			$Keywords = array('Ad|Ads',
							'Pre',
							'Flash',
							'Color',
							'Magic',
							'Bold|B',
							'Box|Title|Infobox',
							'Underline|U',
							'Italics?|I',
							'Strike|S',
							'Heading|SubHeading',
							'Big',
							'Medium|Med',
							'Small|Sml',
							'URL',
							'Image|IMG',
							'Redirect',
							'Video|Youtube',
							'Vimeo',
							'SoundCloud',
							'Load',
							'Music',
                            'Snow',
							'(?:Double|Dbl)?Rainbow2?',
							'Glitch',
							'Red',
							'Redtube',
							'Motherless',
							'Embed',
							'Center',
							'Right|Left',
							'Playlist',
							'Style',
							'Total',
                            'Anchor');
							
			$Keywords = implode('|', $Keywords);

			while(preg_match("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [}\]] \\3 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", $Output)) {
				$Output = preg_replace_callback("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [\]}] \\3 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", "ReplaceKeyPENIS", $Output); }

			while(preg_match("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [}\]] \\3 | [\[{] \s* (.*?) \s* [\]}])/xis", $Output)) {
				$Output = preg_replace_callback("/\b($Keywords), [^\S\n]* ($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [\]}] \\3 | [\[{] \s* (.*?) \s* [\]}])/xis", "ReplaceKeyPENIS", $Output); }

			while(preg_match("/\b($Keywords) [^\S\n]* (?:(\S) [\[{] \s* ([^\[{]*) \s* [\]}] \\2 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", $Output)) {
				$Output = preg_replace_callback("/\b($Keywords) [^\S\n]* (?:(.) [\[{] \s* ([^\[{]*) \s* [\]}] \\2 | [\[{] \s* ([^\[{]*?) \s* [\]}])/xis", "ReplaceKeywords", $Output); }

			while(preg_match("/\b($Keywords) [^\S\n]* (?:(\S) [\[{] \s* (.*) \s* [\]}] \\2 | [\[{] \s* (.*?) \s* [\]}])/xis", $Output)) {
				$Output = preg_replace_callback("/\b($Keywords) [^\S\n]* (?:(.) [\[{] \s* (.*) \s* [\]}] \\2 | [\[{] \s* (.*?) \s* [\]}])/xis", "ReplaceKeywords", $Output); }

			#$Output = preg_replace('/#(\D\w+)/', "<a href='irc://irc.wetfish.net/$1'>#$1</a>", $Output);
			#$Output = preg_replace('/([\S\/<>&]{14})/', "$1&#8203;", $Output);

			$Search[':Z'] = "<span class='warning'>:Z</span>";
			$Search[':downy:'] = "<span class='warning medium' style='font-family:helvetica'>.'<u>/</u>)</span>";
			$Search[':dunno'] = "<span class='warning'>�\(�_o)/�</span>";
			
			foreach($Search as $Key => $Value)
			{
				$Output = str_replace($Key, $Value, $Output);
			}
			
			#$Output = str_replace(":2", "<span class='warning'>:2</span>", $Output);
			$Output = str_replace("&lt;3", "<span class='error'>&lt;3</span>", $Output);

#			$Output = preg_replace('{<div>\n+}', "<div>", $Output); # Strip newlines before stuff too.
			$Output = preg_replace('{</div>\n+}', "</div>", $Output); # Strip newlines after images.
			$Output = preg_replace('{</a></div>\n+}', '</a></div>', $Output); # Strip newlines after images.
			$Output = preg_replace('{<hr /></span>\n+}', '<hr /></span>', $Output); # Strip newlines after images.
			$Output = str_replace("<hr />\n", "<hr />", $Output); # No need for a <br /> after a <hr />, unless there's a lot.
			$Output = str_replace("\n", "<br />", $Output);
		break;
	}
	
		return $Output;
}

?>