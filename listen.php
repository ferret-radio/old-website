<?php
    session_name("ferretradio_cookie");
    if(!isset($_SESSION))
    {
        session_start();
    }
    
	if(!file_exists("comments.json"))
	{
		$file = fopen("comments.json", "w");
		fwrite($file, "");
		fclose($file);
	}

	if(isset($_POST['message']))
	{
		if($_POST['message'] != "" and !ctype_space($_POST['message']))
		{
			$name = "";
			$message = $_POST['message'];
			$message = htmlentities(strip_tags($message));

			if(isset($_POST['name']))
			{
				if($_POST['name'] != "")
				{
					$name = $_POST['name'];
					$name = htmlentities(strip_tags($name));
				}
			}

			if(file_exists("comments.json"))
			{
				$commentsjson = file_get_contents("comments.json");
				$comments = json_decode($commentsjson, true);
				$id = count($comments);

				$comments["$id"]['name'] = $name;
				$comments["$id"]['message'] = $message;

				$file = fopen("comments.json", "w");
				fwrite($file, json_encode($comments));
				fclose($file);

				header("Location: ./listen.php");
			}
			else
			{
				$comments["0"]['name'] = $name;
				$comments["0"]['message'] = $message;

				$file = fopen("comments.json", "w");
				fwrite($file, json_encode($comments));
				fclose($file);

				header("Location: ./listen.php");
			}
		}
	}
?>

<!DOCTYPE html> 
<html lang="en">
	<head>
		<!--
			"It may be spaghetti code, but at least it works." -Chalk
		-->
		<title>Ferret Radio</title>
		    
		<!-- Google Shizz -->
    	<script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-77827411-1', 'auto');
        ga('send', 'pageview');

    	</script>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/style.css" rel="stylesheet">
		<link href="css/plugins/morris.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/plugins/morris/raphael.min.js"></script>
		<script src="js/plugins/morris/morris.min.js"></script>
		<script src="js/plugins/morris/morris-data.js"></script>
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php">Ferret Radio</a>
				</div>
				<ul class="nav navbar-right top-nav">
					<li>
						<a href="https://www.facebook.com/FerretRadio/" target="_blank">News</a>
					</li>
					<li>
						<a href="https://discord.gg/EMR5Jg2" target="_blank">Discord</a>
					</li>
				</ul>

				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav side-nav">
						<li>
							<a href="index.php"><i class="fa fa-fw fa-home"></i> Home</a>
						</li>
						<li class="active">
							<a href="listen.php"><i class="fa fa-fw fa-volume-up"></i> Listen</a>
						</li>
						<li>
							<a href="staff.php"><i class="fa fa-fw fa-group"></i> Staff</a>
						</li>
					</ul>
				</div>
			</nav>
			<div id="page-wrapper">
				<div class="container-fluid"><br/><br/>
					<font color="#fff">
						<div style="float:left; width:50%;">
								Listen to Ferret Radio here:<br/><br/>
								<audio controls>
									<!-- This should work -->
									<source src="http://uk6.internet-radio.com:8498/live" type='audio/mp4'>
									<!-- Why the fuck doesn't it work -->
									<p>You are using a shit browser. Please use a real one.</p>
								</audio>
								<br/><br/>
								<div id="current-dj">
									Current DJ: FerretBot
								</div>
								<div id="now-playing"></div>
								<br/>
								<div id="download-link">
									Download links:<br/>
									<a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://uk6.internet-radio.com:8498/live.m3u&t=.m3u">.m3u</a>&nbsp;
									<a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://uk6.internet-radio.com:8498/live.m3u&t=.pls">.pls</a>&nbsp;
									<a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://uk6.internet-radio.com:8498/live.m3u&t=.ram">.ram</a>&nbsp;
								</div>
								<br/>
						</div>

						<div style="float:right; width:50%; ">
              <div id="recently-played"></div>
              <script type="text/javascript">
                // Update function
								function UpdateList()
								{
									$.get("fetch.php", function(data){
										$("#recently-played").html(data);
									});
									$.get("fetch.php?np", function(data){
										$("#now-playing").html(data);
									});
								}
								UpdateList();

								function SendMessage()
								{
									$('#comment-update').text("Sending...");
									if($('#comment-message').val() != "")
									{
										var message = $('#comment-message').val();
										var name = $('#comment-name').val();
										$.post('comment.php', { message: message, name: name}).done(function(data)
										{
											$('#comment-update').text("Sent");

											$.get("fetch.php?comments", function(data){
												$("#radio-comments").html(data);

												$('#comment-update').text("Update Comments");

												$('#comment-message').val('');
												$('#comment-name').val('');
											});
										});
									}
								}
                                
								function UpdateTimezone()
								{
                                    var timezone = $('#session-timezone').val();
                                    $.post('session.php?set=timezone', { tz: timezone }).done(function(data)
                                    {
                                        $('#session-timezonetitle').text("Updated Timezone");
                                    });
								}
								

								// This configures the interval to recheck for a new list
								// I recommend using 180000 for this so your host doesn't complain, but set to 3000 so you can see it working
								// 180000 ms = 3 minutes
								setInterval(function(){ UpdateList(); }, 10000);
                            </script>
						</div>
						<br/>
						
						<p>Comment: </p>
						<!-- Comment Form -->
						<input id="comment-name" style="width: 300px;color:#000000;" name="name" type="text" placeholder="Enter name here... (optional)" />
						<br/>
						<textarea id="comment-message" style="width: 300px;color:#000000;" name="message" placeholder="Enter message here..."></textarea>
						<br/>
						<input onclick="return SendMessage()" style="width: 300px;color:#000000;" name="submit" type="submit" value="Comment" />

						<br/>

						<div style="border:1px solid #e1e1e1;color:#ffffff;width: 300px;height: 300px;overflow-x: hidden;overflow-y: scroll;" id="radio-comments"></div>
						<script type="text/javascript">
							function UpdateComments()
							{
								$('#comment-update').text("Updating...");

								$.get("fetch.php?comments", function(data){
									$("#radio-comments").html(data);
									$('#comment-update').text("Update Comments");
								});
							}
							UpdateComments();
							setInterval(function(){ UpdateComments(); }, 10000);
						</script>
						<div style="border:1px solid #e1e1e1;color:#ffffff;width: 300px;cursor:pointer;text-align:center;" id="comment-update" onclick="return UpdateComments()">Update Comments</div>
						
						<br/>
						
						<div id="session-timezonetitle">Set Timezone:</div>
						
						<select style="width: 300px;color:#000000;" id="session-timezone" name="timezone">
							<option value="Pacific/Midway">(GMT-11:00) Midway Island </option>
							<option value="Pacific/Samoa">(GMT-11:00) Samoa </option>
							<option value="Pacific/Honolulu">(GMT-10:00) Hawaii </option>
							<option value="America/Anchorage">(GMT-09:00) Alaska </option>
							<option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US &amp; Canada) </option>
							<option value="America/Tijuana">(GMT-08:00) Tijuana </option>
							<option value="America/Chihuahua">(GMT-07:00) Chihuahua </option>
							<option value="America/Chihuahua">(GMT-07:00) La Paz </option>
							<option value="America/Mazatlan">(GMT-07:00) Mazatlan </option>
							<option value="America/Denver">(GMT-07:00) Mountain Time (US &amp; Canada) </option>
							<option value="America/Managua">(GMT-06:00) Central America </option>
							<option value="America/Chicago">(GMT-06:00) Central Time (US &amp; Canada) </option>
							<option value="America/Mexico_City">(GMT-06:00) Guadalajara </option>
							<option value="America/Mexico_City">(GMT-06:00) Mexico City </option>
							<option value="America/Monterrey">(GMT-06:00) Monterrey </option>
							<option value="America/Bogota">(GMT-05:00) Bogota </option>
							<option value="America/New_York">(GMT-05:00) Eastern Time (US &amp; Canada) </option>
							<option value="America/Lima">(GMT-05:00) Lima </option>
							<option value="America/Bogota">(GMT-05:00) Quito </option>
							<option value="Canada/Atlantic">(GMT-04:00) Atlantic Time (Canada) </option>
							<option value="America/Caracas">(GMT-04:30) Caracas </option>
							<option value="America/La_Paz">(GMT-04:00) La Paz </option>
							<option value="America/Santiago">(GMT-04:00) Santiago </option>
							<option value="America/St_Johns">(GMT-03:30) Newfoundland </option>
							<option value="America/Sao_Paulo">(GMT-03:00) Brasilia </option>
							<option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires </option>
							<option value="America/Argentina/Buenos_Aires">(GMT-03:00) Georgetown </option>
							<option value="America/Godthab">(GMT-03:00) Greenland </option>
							<option value="America/Noronha">(GMT-02:00) Mid-Atlantic </option>
							<option value="Atlantic/Azores">(GMT-01:00) Azores </option>
							<option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is. </option>
							<option value="Africa/Casablanca">(GMT+00:00) Casablanca </option>
							<option value="Europe/London">(GMT+00:00) Edinburgh </option>
							<option value="Europe/Dublin">(GMT+00:00) Dublin </option>
							<option value="Europe/Lisbon">(GMT+00:00) Lisbon </option>
							<option value="Europe/London">(GMT+00:00) London </option>
							<option value="Africa/Monrovia">(GMT+00:00) Monrovia </option>
							<option value="UTC">(GMT+00:00) UTC </option>
							<option value="Europe/Amsterdam">(GMT+01:00) Amsterdam </option>
							<option value="Europe/Belgrade">(GMT+01:00) Belgrade </option>
							<option value="Europe/Berlin">(GMT+01:00) Berlin </option>
							<option value="Europe/Berlin">(GMT+01:00) Bern </option>
							<option value="Europe/Bratislava">(GMT+01:00) Bratislava </option>
							<option value="Europe/Brussels">(GMT+01:00) Brussels </option>
							<option value="Europe/Budapest">(GMT+01:00) Budapest </option>
							<option value="Europe/Copenhagen">(GMT+01:00) Copenhagen </option>
							<option value="Europe/Ljubljana">(GMT+01:00) Ljubljana </option>
							<option value="Europe/Madrid">(GMT+01:00) Madrid </option>
							<option value="Europe/Paris">(GMT+01:00) Paris </option>
							<option value="Europe/Prague">(GMT+01:00) Prague </option>
							<option value="Europe/Rome">(GMT+01:00) Rome </option>
							<option value="Europe/Sarajevo">(GMT+01:00) Sarajevo </option>
							<option value="Europe/Skopje">(GMT+01:00) Skopje </option>
							<option value="Europe/Stockholm">(GMT+01:00) Stockholm </option>
							<option value="Europe/Vienna">(GMT+01:00) Vienna </option>
							<option value="Europe/Warsaw">(GMT+01:00) Warsaw </option>
							<option value="Africa/Lagos">(GMT+01:00) West Central Africa </option>
							<option value="Europe/Zagreb">(GMT+01:00) Zagreb </option>
							<option value="Europe/Athens">(GMT+02:00) Athens </option>
							<option value="Europe/Bucharest">(GMT+02:00) Bucharest </option>
							<option value="Africa/Cairo">(GMT+02:00) Cairo </option>
							<option value="Africa/Harare">(GMT+02:00) Harare </option>
							<option value="Europe/Helsinki">(GMT+02:00) Helsinki </option>
							<option value="Europe/Istanbul">(GMT+02:00) Istanbul </option>
							<option value="Asia/Jerusalem">(GMT+02:00) Jerusalem </option>
							<option value="Europe/Helsinki">(GMT+02:00) Kyiv </option>
							<option value="Africa/Johannesburg">(GMT+02:00) Pretoria </option>
							<option value="Europe/Riga">(GMT+02:00) Riga </option>
							<option value="Europe/Sofia">(GMT+02:00) Sofia </option>
							<option value="Europe/Tallinn">(GMT+02:00) Tallinn </option>
							<option value="Europe/Vilnius">(GMT+02:00) Vilnius </option>
							<option value="Asia/Baghdad">(GMT+03:00) Baghdad </option>
							<option value="Asia/Kuwait">(GMT+03:00) Kuwait </option>
							<option value="Europe/Minsk">(GMT+03:00) Minsk </option>
							<option value="Africa/Nairobi">(GMT+03:00) Nairobi </option>
							<option value="Asia/Riyadh">(GMT+03:00) Riyadh </option>
							<option value="Europe/Volgograd">(GMT+03:00) Volgograd </option>
							<option value="Asia/Tehran">(GMT+03:30) Tehran </option>
							<option value="Asia/Muscat">(GMT+04:00) Abu Dhabi </option>
							<option value="Asia/Baku">(GMT+04:00) Baku </option>
							<option value="Europe/Moscow">(GMT+04:00) Moscow </option>
							<option value="Asia/Muscat">(GMT+04:00) Muscat </option>
							<option value="Europe/Moscow">(GMT+04:00) St. Petersburg </option>
							<option value="Asia/Tbilisi">(GMT+04:00) Tbilisi </option>
							<option value="Asia/Yerevan">(GMT+04:00) Yerevan </option>
							<option value="Asia/Kabul">(GMT+04:30) Kabul </option>
							<option value="Asia/Karachi">(GMT+05:00) Islamabad </option>
							<option value="Asia/Karachi">(GMT+05:00) Karachi </option>
							<option value="Asia/Tashkent">(GMT+05:00) Tashkent </option>
							<option value="Asia/Calcutta">(GMT+05:30) Chennai </option>
							<option value="Asia/Kolkata">(GMT+05:30) Kolkata </option>
							<option value="Asia/Calcutta">(GMT+05:30) Mumbai </option>
							<option value="Asia/Calcutta">(GMT+05:30) New Delhi </option>
							<option value="Asia/Calcutta">(GMT+05:30) Sri Jayawardenepura </option>
							<option value="Asia/Katmandu">(GMT+05:45) Kathmandu </option>
							<option value="Asia/Almaty">(GMT+06:00) Almaty </option>
							<option value="Asia/Dhaka">(GMT+06:00) Astana </option>
							<option value="Asia/Dhaka">(GMT+06:00) Dhaka </option>
							<option value="Asia/Yekaterinburg">(GMT+06:00) Ekaterinburg </option>
							<option value="Asia/Rangoon">(GMT+06:30) Rangoon </option>
							<option value="Asia/Bangkok">(GMT+07:00) Bangkok </option>
							<option value="Asia/Bangkok">(GMT+07:00) Hanoi </option>
							<option value="Asia/Jakarta">(GMT+07:00) Jakarta </option>
							<option value="Asia/Novosibirsk">(GMT+07:00) Novosibirsk </option>
							<option value="Asia/Hong_Kong">(GMT+08:00) Beijing </option>
							<option value="Asia/Chongqing">(GMT+08:00) Chongqing </option>
							<option value="Asia/Hong_Kong">(GMT+08:00) Hong Kong </option>
							<option value="Asia/Krasnoyarsk">(GMT+08:00) Krasnoyarsk </option>
							<option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur </option>
							<option value="Australia/Perth">(GMT+08:00) Perth </option>
							<option value="Asia/Singapore">(GMT+08:00) Singapore </option>
							<option value="Asia/Taipei">(GMT+08:00) Taipei </option>
							<option value="Asia/Ulan_Bator">(GMT+08:00) Ulaan Bataar </option>
							<option value="Asia/Urumqi">(GMT+08:00) Urumqi </option>
							<option value="Asia/Irkutsk">(GMT+09:00) Irkutsk </option>
							<option value="Asia/Tokyo">(GMT+09:00) Osaka </option>
							<option value="Asia/Tokyo">(GMT+09:00) Sapporo </option>
							<option value="Asia/Seoul">(GMT+09:00) Seoul </option>
							<option value="Asia/Tokyo">(GMT+09:00) Tokyo </option>
							<option value="Australia/Adelaide">(GMT+09:30) Adelaide </option>
							<option value="Australia/Darwin">(GMT+09:30) Darwin </option>
							<option value="Australia/Brisbane">(GMT+10:00) Brisbane </option>
							<option value="Australia/Canberra">(GMT+10:00) Canberra </option>
							<option value="Pacific/Guam">(GMT+10:00) Guam </option>
							<option value="Australia/Hobart">(GMT+10:00) Hobart </option>
							<option value="Australia/Melbourne">(GMT+10:00) Melbourne </option>
							<option value="Pacific/Port_Moresby">(GMT+10:00) Port Moresby </option>
							<option value="Australia/Sydney">(GMT+10:00) Sydney </option>
							<option value="Asia/Yakutsk">(GMT+10:00) Yakutsk </option>
							<option value="Asia/Vladivostok">(GMT+11:00) Vladivostok </option>
							<option value="Pacific/Auckland">(GMT+12:00) Auckland </option>
							<option value="Pacific/Fiji">(GMT+12:00) Fiji </option>
							<option value="Pacific/Kwajalein">(GMT+12:00) International Date Line West </option>
							<option value="Asia/Kamchatka">(GMT+12:00) Kamchatka </option>
							<option value="Asia/Magadan">(GMT+12:00) Magadan </option>
							<option value="Pacific/Fiji">(GMT+12:00) Marshall Is. </option>
							<option value="Asia/Magadan">(GMT+12:00) New Caledonia </option>
							<option value="Asia/Magadan">(GMT+12:00) Solomon Is. </option>
							<option value="Pacific/Auckland">(GMT+12:00) Wellington </option>
							<option value="Pacific/Tongatapu">(GMT+13:00) Nuku\alofa </option>
						</select>
						<br/>
						<input style="width: 300px;color:#000000;" onclick="return UpdateTimezone()" id="session-timezoneset" type="submit"/>
					</font>
				</div>
			</div>
		</div>
	</body>
</html>
