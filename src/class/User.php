<?php
namespace App;
use Gt\Core\Path;
use Exception;

class User {
	
	public function __construct ($email=false) {
		$this->email = $email;
		$this->path = Path::get(Path::DATA).'/user/';
	}

	public function getValue ($field) {
		$path = $this->path."{$this->email}/{$field}.dat";
		return file_get_contents($path) ?? false;
	}

	public function setValue ($field,$value=false) {
		$path = $this->path."{$this->email}/{$field}.dat";
		return file_put_contents($path, $value);
	}

	public static function isLoggedIn ($ip_locked = true) {

		if (empty($_SESSION['email'])) {
			return false;
		}
		
		$user = new self($_SESSION['email']);
		if (empty($_SESSION['token']) || $_SESSION['token'] != $user->getValue('token')) {
			return false;
		}

		if ($ip_locked && 
			($_SERVER['REMOTE_ADDR'] != $user->getValue('ip'))
		) {
			return false;
		}

		return true;			
	}

	public static function logIn ($email, $password) {

		$user = new self($email);
		$realPassword = $user->getValue('password');
		if (!$realPassword || $password != $realPassword) {
			throw new Exception("Invalid Login");
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$token = rand(0,9000);

		$_SESSION['email'] = $email;
		$_SESSION['token'] = $_SERVER['token'];
		$user->setValue('ip', $_SERVER['REMOTE_ADDR']);
		$user->setValue('token', $token);
		

	}

	public static function logOut () {
		session_destroy();
	}

}