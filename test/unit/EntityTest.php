<?php
namespace App\Test;
use App\Entity;
use PHPUnit\Framework\TestCase;
use Gt\Core\Path;

class EntityTest extends TestCase {

    public function setUp () {
        $path = Path::get(Path::DATA)."/default/";
        $this->removeDirectory($path);
    }

    public function testICanCreateEntity() {
        $payload = [
           'id_entity' => 'testId',
           'test' => 'case'
        ];
              
        $setup_entity = new Entity('exampleID');
        $setup_entity->create($payload);
        
        $e = new Entity('exampleID');      
        $this->assertEquals($payload['test'], $e->getValue('test'));    
    }

    public function testICanDeleteEntity() {
 
        $payload = [
           'id_entity' => 'testId1',
           'test' => 'case'
        ];
              
        $setup_entity = new Entity('exampleID');
        $setup_entity->create($payload);
     
        $e = new Entity('exampleID');
        $e->delete();
        
        $e = new Entity('exampleID'); 
        $this->assertEquals(false, $e->getValue('test'));
      
    }   

    public function testICantUseUnsafeFields () {
        $payload = [
           '../id_entity' => 'testId1',
           './test' => 'case'
        ];
              
        $setup_entity = new Entity('exampleID');
        $this->expectExceptionMessage('Invalid Field');
        $setup_entity->create($payload);
    }

     public function testICanSearch () {
        $payload = [
           '../id_entity' => 'testId1',
           './test' => 'case'
        ];
              
        $e1 = new Entity('exampleID');
        $e1->create(['test' => 1, 'foo' => 'bar']);
        $e2 = new Entity('exampleID2');
        $e2->create(['test' => 2, 'foo' => 'bar']);
        $e3 = new Entity('exampleID3');
        $e3->create(['test' => 3, 'foo' => 'bar']);
 
        $search = new Entity();
        $results = $search->search();

        $this->assertTrue(count($results) > 0);

    }
       
    public function testICantDuplicateEntity() {
    
        $payload = [
           'id_entity' => 'testId3',
           'test' => 'case'
        ];
              
        $n = new Entity('exampleID');
        $n->create($payload);
        
        $this->expectExceptionMessage('Entity Already Exists');
        $e = new Entity('exampleID');
        $e->create($payload);  
    }   
    
    public function testICanUpdateEntity() {    
        $payload = [
           'id_entity' => 'testId',
           'test' => 'case'
        ];
         
        $string = 'something';    
        $e = new Entity('exampleID');
        $e->create($payload);
        $e->setValue('test', $string);      
        $this->assertEquals($string, $e->getValue('test'));  
    }


    public function testICanPayloadUpdateEntity() {    
        $payload = [
           'id_entity' => 'testId',
           'test' => 'case'
        ];
         
        $string = 'something';    
        $e = new Entity('exampleID');
        $e->create($payload);

	$new_payload = [
		'test' => $string,
		'id_entity' => 'new'
	];

        $e->update($new_payload);      
        $this->assertEquals($string, $e->getValue('test'));  
    }



    public function removeDirectory($path) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }
}



