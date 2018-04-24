<?php
namespace App;
use Gt\Core\Path;

class Tools {

	public $path = false;

	public function __construct () {
		$this->path = Path::get(Path::PAGE).'/tools/';
	}


	public function list () {
		$files = new \DirectoryIterator($this->path);
    	foreach($files as $file) {
    		
    		if ($file->isDot() || $file->isDir()) continue;
    		
    		$name = $file->getFilename();
    		$ext = '.'.$file->getExtension();
    		
    		if ($ext != ".html") continue;

    		$cleanName = str_replace($ext,'',$name);
    		$items[$cleanName] = "/tools/{$cleanName}";
    	}
        return $items ?? false;
	}


}
