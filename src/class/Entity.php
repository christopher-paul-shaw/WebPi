<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;

class Entity {

	public $type = 'default';
	
	public $blockFields = [];
	protected $privateFields = [];

	public $readOnly = false;
	public $ipLocked = false;

	public function __construct ($identifier=false) {
		$this->identifier = strtolower($identifier);
		$this->path = Path::get(Path::DATA)."/{$this->type}/";
		$this->currentDirectory =  $this->path.$this->identifier;   

		$this->config = new Config();
		$this->readOnly = !empty($this->config['general']->readOnly); 
	}

	public function create ($payload) {  
		if (file_exists($this->currentDirectory)) {
			throw new Exception("Entity Already Exists");
		}      

		mkdir($this->currentDirectory, 0777, true);    
		if (!file_exists($this->currentDirectory)) { 
		   throw new Exception("Unable to Create"); 
		} 
		
		foreach ($payload as $field => $value) {
			$this->setValue($field,$value);
		}
	}
	
	public function update ($payload) {
		foreach ($payload as $field => $value) {
			$this->setValue($field,$value);
		}
	}
	
	public function delete () {
		$this->removeDirectory($this->currentDirectory);
	} 
	
	public function search ($filters=false) { 
		$items = [];

		$dir = new DirectoryIterator($this->path);

		foreach ($dir as $fileinfo) {
			if (!$fileinfo->isDir() || $fileinfo->isDot()) continue;
			$identifier = $fileinfo->getFilename();
			$items[$identifier]['id'] = $identifier;
			$this->currentDirectory = $this->path.$identifier.'/';
			$valuesDirectory = new DirectoryIterator($this->currentDirectory);
			foreach ($valuesDirectory as $valueinfo) {
				if ($valueinfo->isDot()) continue;
				$field = explode('.',$valueinfo->getFilename())[0];
				if (in_array($field, $this->privateFields)) continue;
				$items[$identifier][$field] = $this->getValue($field);
			}
		 
		}

		return $items;  
	}

	public function protectField($field) {
		if (strstr($field,'./')) {
			throw new Exception("Invalid Field");
		}
	}

	public function getValue ($field) {
		$this->protectField($field);
		$path = "{$this->currentDirectory}/{$field}.dat";
		return file_exists($path) ? file_get_contents($path) : false;
	}

	public function setValue ($field,$value=false) {
		$this->protectField($field);
		if (in_array($field,$this->blockFields) || $this->readOnly) return;
		$path = "{$this->currentDirectory}/{$field}.dat";
		return file_put_contents($path, $value);
	}

	public function blankValue ($field) {
		$this->protectField($field);
		$path = "{$this->currentDirectory}/{$field}.dat";
		return file_put_contents($path, '');
	}

	public function removeDirectory($path) {
		if ($this->readOnly) return; 
		$files = glob($path . '/*');
		foreach ($files as $file) {
			is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}
		rmdir($path);
	}

	public function tearDown() {
		$this->removeDirectory(Path::get(Path::DATA)."/{$this->type}/");
	}
}
