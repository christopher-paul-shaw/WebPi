<?php
namespace App\Page;
use App\SpeedTest;

class Index extends \Gt\Page\Logic {

	public $externalIpUrl = "http://ipecho.net/plain";

	public function go() {

		$this->outputSpeedTest();
		$this->outputRpiStats();

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
	}

	public function outputRpiStats() {
		$localIP = gethostbyname(trim(`hostname`));
		$externalIP = file_get_contents($this->externalIpUrl);
		$this->document->querySelector('.php-ip-local')->textContent = $localIP;
		$this->document->querySelector('.php-ip-remote')->textContent = $externalIP;
	}

}
