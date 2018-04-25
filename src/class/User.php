<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;

class User {
        public $mutliLogin = false;
        public $ipLocked = false;
        public $readOnly = false;

        public function __construct ($email=false) {
                $this->email = $email;
                $this->path = Path::get(Path::DATA).'/user/';
                $this->config = new Config();
   
        		$this->multiLogin = !empty($this->config['user']->multiLogin);
        		$this->ipLocked = !empty($this->config['user']->ipLocked);
        		$this->readOnly = !empty($this->config['user']->readOnly);
        }



        public function getValue ($field) {
                $path = $this->path."{$this->email}/{$field}.dat";
                return file_get_contents($path) ?? false;
        }

        public function setValue ($field,$value=false) {

                if($this->readOnly) {
                        return false;
                }

                $path = $this->path."{$this->email}/{$field}.dat";
                if ($field == 'password') {
                        $value = $this->password_hash($value);
                }

                return file_put_contents($path, $value);
        }

        public function blankValue ($field) {
                $path = $this->path."{$this->email}/{$field}.dat";
                return file_put_contents($path, '');
        }
	
	public function createUser ($payload) {

		if (empty($payload['email'])) {
			throw new Exception("Email can not be blank");
		}
		if (empty($payload['name'])) {
			throw new Exception("Name can not be blank");
		}
		if (empty($payload['password'])) {
			throw new Exception("Password can not be blank");
		}
		$this->email = $payload['email'];
		$this->user_path = $this->path.$payload['email'].'/';
		$user_directory = file_exists($this->user_path);

		if ($user_directory) {
			throw new Exception("Email is currently in use");
		}
		else {
			mkdir($this->user_path, 0777, true);
		}

		$this->setValue('name',$payload['name']);
		$this->setValue('permission',$payload['permission']);
		$this->setValue('password',$payload['password']);
	}
	
	public function deleteUser () {
		if($this->readOnly) {
        	return false;
        }
		$path = $this->path.$this->email;
		$this->removeDirectory($path);
	}
	
	public function removeDirectory($path) {
	 	$files = glob($path . '/*');
		foreach ($files as $file) {
			is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}
		rmdir($path);
	 	return;
	}

	public function listUsers () {

		$users = [];

		$dir = new DirectoryIterator($this->path);
		foreach ($dir as $fileinfo) {
		    if (!$fileinfo->isDir() || $fileinfo->isDot()) continue;
	
		    $current = new User($fileinfo->getFilename());
		    $users[$fileinfo->getFilename()] = [
		       	'email' => $fileinfo->getFilename(),
		       	'name' => $current->getValue('name'),
		       	'permission' => $current->getValue('permission'),
		       	'ip' => $current->getValue('ip')
			];
		}

		return $users;
	}

	public function changePassword ($current=false, $new=false, $confirm=false) {
		
		$realPassword = $this->getValue('password');
		
		if (!$current) {
			throw new Exception("Current Password Can Not Be Blank");
		}

		if ($this->password_hash($current) != $realPassword) {
			throw new Exception("Current Password Incorrect");
		}

		if (empty($new)) {
			throw new Exception("New Password can not be blank");
		}

		if ($new != $confirm) {
			throw new Exception("New Passwords Do Not Match");
		}

		$this->setValue('password',$new);

		throw new Exception("Password Updated");
	}

	private function password_hash ($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public static function isAdmin () {
		$user = new self($_SESSION['email']);
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

                if ($user->ipLocked &&
                        ($_SERVER['REMOTE_ADDR'] != $user->getValue('ip'))
                ) {
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
