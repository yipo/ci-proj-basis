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
			<div class="container"><a class="brand" href="#"><?=$model->subject?></a></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="span4">
				<ul class="nav nav-tabs nav-stacked" style="margin-top:20px;">
<?php foreach ($model->config as $cfg => $config): ?>
					<li><a href="#sec-<?=$cfg?>"><?=$config->subject?></a></li>
<?php endforeach; ?>
				</ul>
			</div>
			<div class="span8">
<?php foreach ($model->config as $cfg => $config): ?>
				<section id="sec-<?=$cfg?>">
					<div class="page-header"><h4><?=$config->subject?></h4></div>
					<form class="form-horizontal" action="">
				</section>
<?php endforeach; ?>
			</div>
		</div>
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
</body>
</html>