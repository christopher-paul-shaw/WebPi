<?php
namespace App\Page;
use App\User;
use App\Tools;
use Gt\Response\Headers;

class _Common extends \Gt\Page\Logic {

	public function go() {
		$this->handleLogin();
		$this->navigation();
		$this->toolMenu();
	}

	public function navigation () {
		if (User::isLoggedIn()) {
			$t = $this->template->get('logged-in');
			if (!is_null($t)) {
				$t->insertTemplate();
			}

			$user = new User($_SESSION['email']);
			$name = $user->getValue('name');

			$nameElement = $this->document->querySelectorAll('.php-user-name');
			foreach ($nameElement as $e) {
				$e->textContent = $name;
			}
		}

		if (User::isAdmin()) {
			$t = $this->template->get('admin');
			if (!is_null($t)) {
				$t->insertTemplate();
			}
		}
	}

	public function toolMenu () {
		$tools = new Tools();
	
		$options = $tools->list();
		foreach ($options as $name => $url) {	
			$t = $this->template->get('tool');
			$t->setAttribute('href',$url);
			$t->textContent = ucfirst($name);
			$t->insertTemplate();
		}
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
