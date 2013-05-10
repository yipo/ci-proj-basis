<?php if (!defined('ENTRANCE')) exit;

class Model {
	public $config = array();
	
	function get_ready() {
		foreach ($this->config as $obj) $obj->get_ready();
	}
}

class Config {
	const PROJ_PATH = '../';
	const TPLT_PATH = 'template/';
	
	public $subject;
	public $target;
	public $source;
	
	function __construct($subject,$target,$source) {
		$this->subject = $subject;
		$this->target  = $target;
		$this->source  = $source;
	}
	
	function reset() {
		copy($this->source,$this->target);
	}
	
	function is_ready() {
		return (file_exists($this->target)===TRUE);
	}
	
	function get_ready() {
		if (!$this->is_ready()) $this->reset();
	}
}

/*
* Create the instance.
*/

$model = new Model;

$model->config = array(
	'ar' => new Config('Access Restriction',
		'.htaccess',
		Config::TPLT_PATH.'admin/.htaccess'
	),
	'rb' => new Config('Rewrite Base',
		Config::PROJ_PATH.'.htaccess',
		Config::TPLT_PATH.'root/.htaccess'
	),
	'ev' => new Config('Environment',
		Config::PROJ_PATH.'index.php',
		Config::TPLT_PATH.'root/index.php'
	),
	'db' => new Config('Datebase',
		Config::PROJ_PATH.'private/application/config/database.php',
		Config::TPLT_PATH.'config/database.php'
	)
);

