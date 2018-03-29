<?php
namespace App;
use Gt\Core\Path;

class RPI {

	public $externalIpUrl = "http://ipecho.net/plain";
	public $cached;

	public function __construct($cached=false) {
		$this->cached = $cached;
		$this->path = Path::get(Path::DATA).'/rpi/';
		if (!file_exists($this->path)) {
    			mkdir($ths->path, 0777, true);
		}
	}

	public function stats () {

		if ($this->cached) {
			echo "Cached Verson";
			return json_decode(file_get_contents($this->path.'stats.dat'),true);
		}
		echo "Normal Version";
		return $this->process();
	}

	public function process () {

		$stats = [];

		$stats['ip_local'] = gethostbyname(trim(`hostname`));
		$stats['ip_remote'] = file_get_contents($this->externalIpUrl);

		// System Load
		$stats['load'] = sys_getloadavg()[0];
	
		// Cores
		$cmd = "uname";
		$stats['os'] = strtolower(trim(shell_exec($cmd)));
		$cpuCoreNo = null;
		switch($stats['os']) {
		    case('linux'):
				$cmd = "cat /proc/cpuinfo | grep processor | wc -l";
			break;
		    case('freebsd'):
		    	$cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
		    break;
		    default:
		        unset($cmd);
		}
		 
		if ($cmd != '') {
			$cpuCoreNo = intval(trim(shell_exec($cmd)));
			$stats['cpu_cores'] = $cpuCoreNo;
		}

		// Memory Usage
		$free = shell_exec('free');
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);
		$stats['memory_usage'] = round($mem[2] / $mem[1] * 100).'%';

		// Disk Usage
		$disktotal = disk_total_space ('/');
		$diskfree  = disk_free_space  ('/');
		$stats['disk_usage'] = round (100 - (($diskfree / $disktotal) * 100)) .'%';
	
		if ($stats['os'] == "linux") {
			$stats['uptime'] = floor(preg_replace ('/\.[0-9]+/', '', file_get_contents('/proc/uptime')) / 86400);
		}
		file_put_contents($this->path.'stats.dat',json_encode($stats));
		return $stats;
	}

}
