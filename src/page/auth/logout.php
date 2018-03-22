<?php
namespace App\Page\Auth;
use App\User;
use Gt\Response\Headers;

class Logout extends \Gt\Page\Logic {

	public function go() {
		User::logOut($_SESSION['email']);
		Headers::redirect("/");
	}

}
