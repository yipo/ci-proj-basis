<?php if (!defined('ENTRANCE')) exit; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=$model->subject?></title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css">
</head>
<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container"><a href="#" class="brand"><?=$model->subject?></a></div>
		</div>
	</div>
	<div class="container"></div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
</body>
</html>