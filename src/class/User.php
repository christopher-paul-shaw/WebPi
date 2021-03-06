<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;
use CPS\DataStore;

class User extends DataStore {

	public $type = 'user';
	public $storage = '/data';
	
	public $blockFields = [
		'current_password',
		'new_password',
		'confirm_password',    
	];   

	public $privateFields = [
		'password',    
	];     
	
	public function __construct ($identifier=false) {
    		parent::__construct($identifier);
    		$this->config = new Config();
        	$this->ipLocked = !empty($this->config['user']->ipLocked); 
    		$this->multiLogin = !empty($this->config['user']->multiLogin); 
	}

	public function changePassword (
		$current=false, 
		$new=false, 
		$confirm=false) {

		$realPassword = $this->getValue('password');
		if (!$current) {
			throw new Exception("Current Password Can Not Be Blank");
		}

		if (!password_verify($current,$realPassword)) {
			throw new Exception("Current Password Incorrect");
		}

		if (empty($new)) {
			throw new Exception("New Password can not be blank");
		}

		if ($new != $confirm) {
			throw new Exception("New Passwords Do Not Match");
		}

		$this->setValue('password',$new);
	}

	private function password_hash ($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public function setValue ($field, $value=false) {
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if (strstr($k,'password')) {
					$field[$k] = $this->password_hash($v);
				}
			}
		}
		else if (strstr($field, 'password')) {
			$value = $this->password_hash($value);
		
		}
		parent::setValue($field, $value);
	}

	public static function isAdmin () {
		
		$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;
		$user = new self($email);
		$level = $user->getValue('permission');
		return strtolower($level) == 'admin';
	}

	public static function isLoggedIn ($ip_locked = true) {

		if (empty($_SESSION['email'])) {
			return false;
		}

		$user = new self($_SESSION['email']);

		if (empty($user->multiLogin) && (empty($_SESSION['token']) || $_SESSION['token'] != $user->getValue('token'))) {
			$user->logOut();
			return false;
		}

		if ($user->ipLocked && ($_SERVER['REMOTE_ADDR'] != $user->getValue('ip'))) {
			return false;
		}

		return true;

	}

	public static function logIn ($email, $password) {

		$user = new self($email);

		$realPassword = $user->getValue('password');
		if (!password_verify($password, $realPassword)) {
			throw new Exception("Failed to Login");
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$token = rand(0,9000);
		$_SESSION['email'] = $email;
		$_SESSION['token'] = $token;
		$user->setValue('ip', $_SERVER['REMOTE_ADDR']);
		$user->setValue('token', $token);

	}

	public static function logOut () {
		session_destroy();
	}
	
}
