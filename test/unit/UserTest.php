<?php
namespace App\Test;
use App\User;
use PHPUnit\Framework\TestCase;
use Gt\Core\Path;

class UserTest extends TestCase {

	public function tearDown () {
		$path = Path::get(Path::DATA)."/user/";
		$this->removeDirectory($path);
	}

	public function testICanCreateUser() {
		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$setup_entity = new User($identifier);
		$setup_entity->create($payload);
		
		$e = new User($identifier);      
		$this->assertNotFalse($e->getValue('name'));    
	}

	public function testCurrentPasswordCantBeBlank() {

		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$e = new User($identifier);
		$e->create($payload);

		$this->expectExceptionMessage('Current Password Can Not Be Blank');
		$e->changePassword(false,false,fALSE);
	}


	public function testCurrentPasswordCantBeWrong() {

		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$e = new User($identifier);
		$e->create($payload);

		$this->expectExceptionMessage('Current Password Incorrect');
		$e->changePassword('testcase1',false,false);
	}

	public function testNewPasswordCantBeBlank() {

		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$e = new User($identifier);
		$e->create($payload);

		$this->expectExceptionMessage('New Password can not be blank');
		$e->changePassword('testcase',false,false);
	}

	public function testNewPasswordsMustMatch() {

		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$e = new User($identifier);
		$e->create($payload);

		$this->expectExceptionMessage('New Passwords Do Not Match');
		$e->changePassword('testcase','example',false);
	}

	public function testCanChangePassword() {

		$identifier = 'test-'.time();
		$payload = [
		   'name' => 'test user',
		   'password' => 'testcase'
		];
			  
		$e = new User($identifier);
		$e->create($payload);
		$e->changePassword('testcase','example','example');
		echo $e->getValue('password');
		$this->assertTrue(password_verify('example',$e->getValue('password')));
	}

	public function removeDirectory($path) {
		$files = glob($path . '/*');
		
		foreach ($files as $file) {
			if (!strstr($file, 'test-')) continue;
			is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}

		if ($path != Path::get(Path::DATA)."/user/") {
			rmdir($path);
		}
		
	}
}




