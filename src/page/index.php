<?php
namespace App\Page;
use Gt\Core\Path;
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


class SpeedTest {

	public $path = false;

	public function __construct () {
		$this->path = Path::get(Path::DATA).'/speedtest/';
	}

	public function getLatest () {
		$files = $this->listLogs();
		$date = current(array_keys($files));
		$log = $this->analyseLog($date);
		return end($log);
	}

	public function listLogs () {
		$files = new \DirectoryIterator($this->path);
    	foreach($files as $file) {
    		if (in_array($file->getFilename(),['.','..'])) continue;
    		$items[explode('.',$file->getFilename())[0]] = $file->getFilename();
    	}
    	krsort($items);
        return $items ?? false;
	}

	public function analyseLog($date) {

		$file = $this->path."/{$date}.log";

		if (!file_exists($file)) {
			return false;
		}

		$handle = fopen($file, "r");
		while (($data = fgetcsv($handle)) !== FALSE) {
		    $rows[] = $data;
		}

		return $rows;

	}






}
