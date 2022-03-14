<html>
<?php
	header("Link:" .
	"<https://fonts.gstatic.com>; rel=preconnect," .
	"<img/gurka.png>; rel=prefetch," .
	"<img/moon.png>; rel=prefetch," .
	"<img/sun.png>; rel=prefetch"
	);
?>

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<title>&#x1F952; gurka.se</title>
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-S6QQ0640WP"></script>
		<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
		<script defer src="app.js"></script>
		<link href="styles.css" rel="stylesheet">
		<?php if ( isset($_REQUEST['allergisk']) ) {
			echo	"<style>#gurka{display:none}</style>";
		} ?>
	</head>
	<body>
			<img src="img/gurka.png" id="gurka"></img>
		<ul>
			<li id="mail"><a href="mailto:tim@gurka.se" target="_blank" rel="noopener noreferrer">tim@gurka.se</a></li>
			<li id="turns" title="Varv snurrade">0</li>
			<li id="tpm" title="Varv per sekund">0</li>
					</ul>
		<img id="darkmodeButton" src="img/moon.png" alt="Dark-mode"  title="Dark-mode">
	</body>
</html>
