<?php if (!defined('ENTRANCE')) exit;

class Model {
	public $subject;
	public $config = array();
	
	function __construct($subject) {
		$this->subject = $subject;
	}
	
	function get_ready() {
		foreach ($this->config as $obj) $obj->get_ready();
	}
	
	function load() {
		foreach ($this->config as $obj) $obj->load();
	}
}

class Config {
	const PROJ_PATH = '../';
	const TPLT_PATH = 'template/';
	
	public $subject;
	public $target;
	public $source;
	public $field = array();
	
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
	
	function load() {
		$data = file_get_contents($this->target);
		if ($data===FALSE) exit('fail to open the file.');
		
		$rt = preg_match_all("%/\*{{(\w+)}-->}\*/ ('[^']*'|TRUE|FALSE)%",$data,$match);
		if ($rt===FALSE) exit('some error occurred while matching tags');
		
		foreach ($match[1] as $i => $key) {
			if (array_key_exists($key,$this->field)) {
				if ($this->field[$key]->type=='password') continue;
				$this->field[$key]->value = trim($match[2][$i],"'");
			}
		}
	}
	
	function validate($data) {
	}
	
	function save($data) {
		$this->validate($data);
	}
}

class Field {
	const VALID_WORD = '^[\w-]*$';
	const VALID_HOST = '^([A-z][\w-]*\.)*[A-z][\w-]*$|^(\d{1,3}\.){3}\d{1,3}$';
	
	public $subject;
	public $type;
	public $valid;
	public $value = NULL;
	
	function __construct($subject,$type,$valid = Field::VALID_WORD) {
		$this->subject = $subject;
		$this->type    = $type;
		$this->valid   = $valid;
	}
}

/*
* Create the instance.
*/

$model = new Model('ci-proj-admin');

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

$model->config['ar']->field = array(
	'method' => new Field('Method','radio',array(
		'local' => 'Local machine only',
		'auth'  => 'Basic authentication'
	)),
	'user'   => new Field('User','text'),
	'passwd' => new Field('Password','password'),
	'retype' => new Field('Retype password','password')
);

$model->config['rb']->field = array(
	'enable' => new Field('Base URL','radio',array(
		'false' => 'is relative to the document root.',
		'true'  => 'set as <code>'.'foo/bar/'.'</code>.'
	))
);

$model->config['ev']->field = array(
	'environment' => new Field('Environment','radio',array(
		'development' => 'Development',
		'testing'     => 'Testing',
		'production'  => 'Production'
	))
);

$model->config['db']->field = array(
	'hostname' => new Field('Host','text',Field::VALID_HOST),
	'username' => new Field('User','text'),
	'password' => new Field('Password','password'),
	'database' => new Field('Database','text'),
	'dbdriver' => new Field('Driver','select',array(
		'mysql'   => 'MySQL',
		'mysqli'  => 'MySQLi',
		'postgre' => 'PostgreSQL',
		'odbc'    => 'ODBC',
		'mssql'   => 'Microsoft SQL',
		'sqlite'  => 'SQLite',
		'oci8'    => 'OCI8'
	))
);

