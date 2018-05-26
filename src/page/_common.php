<?php
namespace App\Page;
use App\User;
use App\Tools;
use Gt\Response\Headers;

class _Common extends \Gt\Page\Logic {

	public function go() {
		$this->handleLogin();
		$this->navigation();
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
	
			$tools = new Tools();
	
			$options = $tools->list();

			$menu = $t->querySelector('.php-tools');
			$links = [];
			foreach ($options as $name => $url) {		
				$name = ucwords($name);	
				$links[]= "<a href=\"{$url}\">{$name}</a>";
			}

			$menu->innerHTML = implode('', $links);;

		}

		if (User::isAdmin()) {
			$t = $this->template->get('admin');
			if (!is_null($t)) {
				$t->insertTemplate();
			}
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
