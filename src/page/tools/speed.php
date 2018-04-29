<?php
namespace App\Page\Tools;
use App\SpeedTest;
use App\RPI;

class Speed extends \Gt\Page\Logic {

	public function go() {
		$this->outputSpeedTest();
	}

	public function outputSpeedTest () {

		$this->date = $_GET['date'] ?? false;

		$speedtest = new SpeedTest();

		if ($this->date) {
			$logs = $speedtest->analyseLog($this->date);

			$t = $this->template->get('log-row');
			$t->querySelector('.php-date')->textContent = "Date";
			$t->querySelector('.php-time')->textContent = "Time";
			$t->querySelector('.php-down')->textContent = "Down";
			$t->querySelector('.php-up')->textContent = "Up";
			$t->querySelector('.php-ping')->textContent = "Ping";
			$t->insertTemplate();
			
			foreach ($logs as $l) {
				$t = $this->template->get('log-row');
				$t->querySelector('.php-date')->html = $l[0];
				$t->querySelector('.php-time')->textContent = $l[1];
				$t->querySelector('.php-down')->textContent = $l[2];
				$t->querySelector('.php-up')->textContent = $l[3];
				$t->querySelector('.php-ping')->textContent = $l[4];
				$t->insertTemplate();
			}
			return;
		}

		$files = $speedtest->listLogs();
		
		foreach ($files as $date => $name) {
			$t = $this->template->get('log-list');
			$t->querySelector('a')->textContent = $date;
			$t->querySelector('a')->setAttribute('href',"/tools/speed?date={$date}");
			$t->insertTemplate();
		}
	
		

	}

}