<?php if (!defined('ENTRANCE')) exit;

class Model {
	public $subject;
	public $config = array();
	
	function __construct($subject) {
		$this->subject = $subject;
	}
	
	function get_ready() {
		foreach ($this->config as $config) $config->get_ready();
	}
	
	function load() {
		foreach ($this->config as $config) $config->load();
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
		$text = file_get_contents($this->target);
		if ($text===FALSE) exit('fail to open the file "'.$this->target.'".');
		
		$rt = preg_match_all($this->patt_tag(),$text,$match);
		if ($rt===FALSE) exit('some error occurred while matching tags of the file "'.$this->target.'".');
		
		foreach ($match[2] as $i => $key) {
			if (array_key_exists($key,$this->field)) {
				if ($this->field[$key]->type=='password') continue;
				$this->field[$key]->value = trim($match[3][$i],"'");
			}
		}
	}
	
	function validate($data) {
		foreach ($this->field as $fld => $field) {
			if (!array_key_exists($fld,$data)) exit('lack of the "'.$fld.'" variable.');
			if (!$field->validate($data[$fld])) exit('invalid value for the "'.$fld.'" variable.');
		}
	}
	
	function save($data) {
		$this->validate($data);
		
		$text = file_get_contents($this->target);
		if ($text===FALSE) exit('fail to open the file "'.$this->target.'".');
		
		foreach ($this->field as $fld => $field) {
			$value = $data[$fld];
			if (in_array($value,array('true','false'))) {
				$value = ($value=='true'?'TRUE':'FALSE');
			} else {
				$value = "'{$value}'";
			}
			
			$text = preg_replace($this->patt_tag($fld),"$1{$value}",$text);
			if ($text===NULL) exit('some error occurred while filling in fields of the file "'.$this->target.'".');
		}
		
		$rt = file_put_contents($this->target,$text);
		if ($rt===FALSE) exit('fail to save the file "'.$this->target.'".');
	}
	
	protected function patt_tag($tag = '\w+') {
		return "%(/\*{{({$tag})}-->}\*/ )('[^']*'|TRUE|FALSE)%";
	}

	protected function patt_hta($tag) {
		return "%^(# )?({$tag} )(.*)$%m";
	}
}

class ConfigAR extends Config {
	function validate($data) {
		if (!array_key_exists('method',$data)) exit('lack of the "method" variable.');
		if ($data['method']!='local') {
			parent::validate($data);
			if ($data['passwd']!=$data['retype']) exit('passwords are not match.');
		}
	}
	
	function save($data) {
		$this->validate($data);
		
		$method = $data['method'];
		
		$source['local'] = Config::TPLT_PATH.'admin/local/.htaccess';
		$source['auth']  = Config::TPLT_PATH.'admin/auth/.htaccess';
		
		$this->reset();
		
		$text = file_get_contents($source[$method]);
		if ($text===FALSE) exit('fail to open the file "'.$source[$method].'".');
		
		if ($method=='auth') $text = $this->set_auth($text,$data['user'],$data['passwd']);
		
		$rt = file_put_contents($this->target,$text,FILE_APPEND);
		if ($rt===FALSE) exit('fail to save the file "'.$this->target.'".');
	}
	
	private function set_auth($text,$user,$passwd) {
		$file = '.htpasswd';
		$rt = file_put_contents($file,$this->gen_sha($user,$passwd));
		if ($rt===FALSE) exit('fail to save the file "'.$file.'".');
		
		$text = preg_replace($this->patt_hta('AuthUserFile'),'$2'.realpath($file),$text);
		if ($text===NULL) exit('some error occurred while setting the auth user file.');
		return $text;
	}
	
	private function gen_sha($user,$passwd) {
		return $user.':{SHA}'.base64_encode(sha1($passwd,TRUE));
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
	
	function validate($value) {
		if (is_string($this->valid)) {
			$rt = preg_match("%{$this->valid}%",$value);
			if ($rt===FALSE) exit('some error occurred while validating the "'.$this->subject.'" field.');
			return ($rt===1);
		}
		if (is_array($this->valid)) {
			return array_key_exists($value,$this->valid);
		}
		exit('invalid value of the "valid" variable of the "'.$this->subject.'" field.');
	}
}

/*
* Create the instance.
*/

$model = new Model('ci-proj-admin');

$model->config = array(
	'ar' => new ConfigAR('Access Restriction',
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

