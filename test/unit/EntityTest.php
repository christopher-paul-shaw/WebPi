<?php
namespace App\Test;
use App\Entity;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase {
    public function testICanCreateEntity() {
   
        $payload = [
           'id_entity' => 'testId',
           'test' => 'case'
        ];
              
        $setup_entity = new Entity();
        $this->assertExceptionNotThrown('Exception');
        $setup_entity->create($payload);
        
        $e = new Entity($payload['id_entity']);      
        $this->assertEquals($payload['test'], $e->getValue('test'));    
    }
    
    public function testICanDeleteEntity() {
 
        $payload = [
           'id_entity' => 'testId1',
           'test' => 'case'
        ];
              
        $setup_entity = new Entity();
        $setup_entity->create($payload);
     
        $e = new Entity($payload['id_entity']);
        $e->delete();
        
        $e = new Entity($payload['id_entity']); 
        $this->assertEquals(false, $e->getValue('test'));
      
    }   
       
    public function testICantDuplicateEntity() {
    
        $payload = [
           'id_entity' => 'testId3',
           'test' => 'case'
        ];
              
        $n = new Entity();
        $n->create($payload);
        
        $this->expectExceptionMessage('Entity already Exists');
        $e = new Entity();
        $e->create($payload);  
    }   
    
    public function testICanUpdateEntity() {    
        $payload = [
           'id_entity' => 'testId',
           'test' => 'case'
        ];
         
        $string = 'something';    
        $e = new Entity();
        $e->create($payload);
        $e->setValue('test', $string);      
        $this->assertEquals($string, $e->getValue('test'));  
    }

}
