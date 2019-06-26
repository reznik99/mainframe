<?php
//error reporting
//ini_set('display_errors', 1); error_reporting(E_ALL);

$mysql_con = mysqli_connect("localhost", "root", "mainframe451", "MainframeDB");
if($mysql_con->connect_error) {
	die($mysql_con->connect_error);
}
date_default_timezone_set('Pacific/Auckland');
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$users_name = htmlspecialchars($_POST['name']);
	$users_comment = htmlspecialchars($_POST['text']);
	$users_comment = $mysql_con->escape_string($users_comment);
	$sql = "INSERT INTO `COMMENTS` (`name`, `comment`, `datetime`) VALUES ('$users_name', '$users_comment', NOW())";
	if ($mysql_con->query($sql) != TRUE) {
		echo "Error: " . $sql . "<br>" . $link->error;
	}
}
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" media="screen and (min-device-width: 800px)" href="styles/style.css" />
	<link rel="stylesheet" media="screen and (max-device-width: 799px)" href="styles/style_mobile.css" />
</head>
<body>
	<div id="chat_wrapper">
		<h1>THE MAINFRAME<span class="blinking-cursor">_</span></h1>
		<ul id="chat_recieved"></ul>
		<form method="POST" id="textarea">
			<input type="text" name="name" id="name" autocomplete="off" required maxlength="10">
			<input type="text" name="text" id="input" autocomplete="off" required maxlength="70">
			<input type="submit" name="submit" value="Submit" id="send_button">
		</form>
	</div>
	<i class="fa fa-caret-left fa-2x" id="settings_open" aria-hidden="true"></i>
	<div id="menu">
		<ul>
			<li></li>
			<li></li>
			<li></li>
		</ul>
		<i class="fa fa-caret-right fa-2x" id="settings_close" aria-hidden="true"></i>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		//read cookies for name
		var name = localStorage.getItem("name");
		if (name !== null){
			$('#name').val(name);
		}
		//quality of life
		$("#textarea").focus();
		// Let's check whether notification permissions have already been granted
		if (Notification.permission !== "granted") {
			Notification.requestPermission();
		}


		//functions
		var open_menu = function(){
			$("#menu").animate({
				right: "0px"
			});
		}
		var close_menu = function(){
			width = $("#menu").width();
			$("#menu").animate({
				right: -width
			});
		}

		function loadChat(){
			var previous = $("#chat_recieved").children().length;
			//get comments (only new ones)
			$.get('chat.php?prev='+previous, function(data){
				$('#chat_recieved').append(data);
				//if new comments was made, then scroll down and alert user
				let newContent = $("#chat_recieved").children().length != previous;
				if(newContent){
					$('#chat_recieved').animate({ scrollTop: $('#chat_recieved').get(0).scrollHeight}, 200);
					notifyUser();
				}
			});
			//used .load before but $.get allows to append only the necessary comments(not redownload them all each time)
		};

		function notifyUser(){
			// Let's check whether notification permissions have already been granted
			if (Notification.permission === "granted") {
				// If it's okay let's create a notification
				var notification = new Notification('New message', { body: $("#chat_recieved").last(), icon: img });
			}
			// Otherwise, we need to ask the user for permission
			else if (Notification.permission !== 'denied') {
				Notification.requestPermission(function (permission) {
					// If the user accepts, let's create a notification
					if (permission === "granted") {
						//yay
					}
				});
			}
		};

		//load comments and ask permission
		loadChat();

		//event listeners
		$("#settings_open").click(open_menu);
		$("#settings_close").click(close_menu);
		$('#textarea').on('keydown', function(e) {
			if (e.which == 13) {
				$("#send_button").click();
			}
		});

		//load chat every 2 seconds
		window.setInterval(function(){
			loadChat();
		}, 2000);

	});

	window.onbeforeunload = function() {
		localStorage.setItem("name", $('#name').val());
	}
	</script>
</body>
</html>
