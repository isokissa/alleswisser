<?php

namespace Isokissa\Alleswisser;

require_once( "src/alleswisser/NoSQLiteDataMap.php" );


class NoSQLiteDataMapTest extends \PHPUnit_Framework_TestCase {

    const DATAMAP_FILE = 'test-nosqlite-datamap.db';
    private $testDataMap; 
    
    public function setup(){
        $this->testDataMap = new NoSQLiteDataMap( self::DATAMAP_FILE );
    }
    
    public function tearDown(){
        $this->testDataMap->deleteAll();
    }

    public function testCorrectInstance(){
        $this->assertInstanceOf("Isokissa\Alleswisser\NoSQLiteDataMap", $this->testDataMap);
    }
    
    public function testEmpty_CountIsZero() {
        $this->assertEquals( 0, $this->testDataMap->count("abc") );
    }
    
    public function testSetNew_CountIncreases() {
        $this->testDataMap->set("map1", "key", "value");
        $this->assertEquals( 1, $this->testDataMap->count("map1") );
    }
    
    public function testSetNew_DataMapRemembersCorrectly() {
        $this->testDataMap->set( "a", "b", "c" );
        $this->assertEquals( "c", $this->testDataMap->get( "a", "b" ) );
    }
    
    public function testSet_DataMapRemembersPersistently() {
        $this->testDataMap->set( "a", "b", "c" );
        $otherInstance = new NoSQLiteDataMap( self::DATAMAP_FILE );
        $this->assertEquals( "c", $otherInstance->get( "a", "b" ) );
    }
    
    public function testDeleteAll_CountComesBackToZero() {
        $this->testDataMap->set("map1", "key", "value");
        $this->testDataMap->deleteAll();
        $this->assertEquals( 0, $this->testDataMap->count("map1") );
    }
    
}
