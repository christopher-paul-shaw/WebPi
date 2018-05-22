<?php
namespace App;
use Gt\Core\Path;
use Exception;
use DirectoryIterator;
use Gt\Core\Config;

class Entity {

    public $type = 'default';
    
    public $ignore = [];
    public $readOnly = false;
    public $ipLocked = false;

    public function __construct ($identifier=false) {
        $this->identifier = strtolower($identifier);
        $this->path = Path::get(Path::DATA)."/{$this->type}/";
        $this->currentDirectory =  $this->path.$this->identifier;    
    }

    public function create ($payload) {  
        if (file_exists($this->currentDirectory)) {
           	throw new Exception("Entity Already Exists");
        }        
        mkdir($this->currentDirectory, 0777, true);        
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
          
            $items[$identifier] = [];
            $this->currentDirectory = str_replace('.','',$this->path.$identifier);



            // Load Values 
            $current = new DirectoryIterator($this->currentDirectory);
            var_dump($current);
           
		    foreach ($current as $field) {
               $items[$identifier][$field] = $this->getValue($field);
            }
        }


        var_dump($items);
        return $items;  
    }

    public function getValue ($field) {
        $path = "{$this->currentDirectory}/{$field}.dat";
        return file_get_contents($path) ?? false;
    }

    public function setValue ($field,$value=false) {
   
        if (in_array($field,$ignore) || $readOnly) return;
        
        $path = "{$this->currentDirectory}/{$field}.dat";
        return file_put_contents($path, $value);
    }

    public function blankValue ($field) {
        $path = "{$this->currentDirectory}/{$field}.dat";
        return file_put_contents($path, '');
    }

    public function removeDirectory($path) {
    
        if ($readOnly) return; 
     
	 	     $files = glob($path . '/*');
		      foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		      }
        rmdir($path);
    }
}