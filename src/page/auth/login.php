<?php
namespace App\Page\Auth;
use App\User;
use Exception;
use Gt\Response\Headers;

class Login extends \Gt\Page\Logic {


	public function go() {

	}

	public function do_login ($data) {
		
		try {
			User::logIn($data['email'], $data['password']);
			Headers::redirect("/");
			die;
		}
		catch (Exception $e) {
			$t = $this->template->get("error");
			$t->textContent = $e->getMessage();
			$t->insertTemplate();
		}

	}


}
