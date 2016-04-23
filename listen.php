<?php
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
                        <a href="http://news.ferretradio.com/" target="_blank">News</a>
                    </li>
                    <li>
                        <a href="http://discord.me/ferret" target="_blank">Discord</a>
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
                                    <source src="http://us2.internet-radio.com:8272/live" type='audio/mp4'>
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
                                    <a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://us2.internet-radio.com:8272/live.m3u&t=.m3u">.m3u</a>&nbsp;
                                    <a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://us2.internet-radio.com:8272/live.m3u&t=.pls">.pls</a>&nbsp;
                                    <a href="http://www.internet-radio.com/servers/tools/playlistgenerator/?u=http://us2.internet-radio.com:8272/live.m3u&t=.ram">.ram</a>&nbsp;
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

                                // This configures the interval to recheck for a new list
                                // I recommend using 180000 for this so your host doesn't complain, but set to 3000 so you can see it working
                                // 180000 ms = 3 minutes
                                setInterval(function(){ UpdateList(); }, 10000);
                                </script>
                        </div>
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
                        <div style="cursor:pointer" id="comment-update" onclick="return UpdateComments()">Update Comments</div>
                    </font>
                </div>
            </div>
        </div>
    </body>
</html>
