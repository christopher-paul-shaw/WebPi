<?php
namespace App\Page\Tools;
use App\SpeedTest;
use App\RPI;

class Speed extends \Gt\Page\Logic {

	public function go() {
		$this->outputSpeedTest();
	}

	public function outputSpeedTest () {

		$speedtest = new SpeedTest();
		$latest = $speedtest->getLatest();

		$downSpeed = round($latest[2],2) ?? 0.00;
		$upSpeed = round($latest[3],2) ?? 0.00;
		$date = $latest[0] ?? 'n/a';
		$time = $latest[1] ?? 'n/a';

		$this->document->querySelector('.php-speed-down')->textContent = $downSpeed;
		$this->document->querySelector('.php-speed-up')->textContent = $upSpeed;
		$this->document->querySelector('.php-speed-date')->textContent = $date;
		$this->document->querySelector('.php-speed-time')->textContent = $time;

		$files = $speedtest->listLogs();
		$date = current(array_keys($files));
		$logs = $speedtest->analyseLog($date);

		foreach ($logs as $l) {
			$t = $this->template->get('log-row');
			$t->querySelector('.php-date')->html = $l[0];
			$t->querySelector('.php-time')->textContent = $l[1];
			$t->querySelector('.php-down')->textContent = $l[2];
			$t->querySelector('.php-up')->textContent = $l[3];
			$t->querySelector('.php-ping')->textContent = $l[4];
			$t->insertTemplate();
		}

	}

}