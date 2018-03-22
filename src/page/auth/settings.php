<?php
namespace App\Page\Auth;
use App\User;
use Exception;
use Gt\Response\Headers;

class Settings extends \Gt\Page\Logic {


	public function go() {
		if (!User::isLoggedIn()) {
			Headers::redirect("/auth/login");
		}
	}

	public function do_update ($data) {
		
		try {
			$user = new User($_SESSION['email']);
			$user->changePassword($data['current_password'], $data['new_password'], $data['confirm_password']);
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
