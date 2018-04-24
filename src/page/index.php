<?php
namespace App\Page;
use App\SpeedTest;
use App\RPI;

class Index extends \Gt\Page\Logic {

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
		
		$rpi = new RPI();
		$stats = $rpi->stats();

		$this->document->querySelector('.php-ip-local')->textContent = $stats['ip_local'];
		$this->document->querySelector('.php-ip-remote')->textContent = $stats['ip_remote'];
		$this->document->querySelector('.php-cpu-cores')->textContent = $stats['cpu_cores'];
		$this->document->querySelector('.php-cpu-load')->textContent = $stats['load'];
		$this->document->querySelector('.php-memory-usage')->textContent = $stats['memory_usage'];
		$this->document->querySelector('.php-disk-usage')->textContent = $stats['disk_usage'];

	}

}