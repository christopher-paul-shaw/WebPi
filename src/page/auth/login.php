<?php
namespace App\Page\Auth;
use App\User;

class Login extends \Gt\Page\Logic {


	public function go() {
		var_dump($_SESSION);
	

	}

	public function do_login ($data) {
		var_dump($data);

		$result = User::logIn($data['email'], $data['password']);
		var_dump($result); print 'x';




		die;

	}


}
