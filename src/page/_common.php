<?php
namespace App\Page;
use App\User;
use Gt\Response\Headers;

class _Common extends \Gt\Page\Logic {

	public function go() {
		$this->handleLogin();
	}

	public function handleLogin() {

		if (strstr(strtolower($_SERVER['REQUEST_URI']), '/auth/')) {
			return;
		}

		if (!User::isLoggedIn()) {
			Headers::redirect("/auth/login");
			die;
		}

	}

}
