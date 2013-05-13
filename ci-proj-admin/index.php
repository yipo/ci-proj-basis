<?php define('ENTRANCE',__FILE__);

require_once('model.inc.php');

$model->get_ready();
$model->load();

require_once('view.inc.php');

