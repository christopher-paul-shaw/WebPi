<?php
namespace App;
use Gt\Core\Path;
use \Datetime;
class SpeedTest {

	public $path = false;

	public function __construct () {
		$this->path = Path::get(Path::DATA).'/speedtest/logs/';
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
	
		$date = new Datetime($date);
		$date = $date->format('Y-m-d');

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

	public function update () {
		$script = Path::get(Path::DATA).'/speedtest/speedtest.sh';
		$log = Path::get(Path::DATA).'/speedtest/logs/'.date('Y-m-d').'.log';
		$cmd = "{$script} >> {$log}";
		var_dump($cmd);
		exec($cmd);
	}

}
