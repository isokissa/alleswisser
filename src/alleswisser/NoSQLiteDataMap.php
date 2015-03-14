<?php

namespace Isokissa\Alleswisser;

class NoSQLiteDataMap implements DataMapInterface {

    private $dataMaps = array();
    private $nsql = null; 
    private $dataMapFileName; 
    
    public function __construct( $dataMapFileName ) {
        $this->dataMapFileName = $dataMapFileName;
    }
    
    public function count($mapName) {
        return $this->getDataMap($mapName)->count();
    }

    public function get($mapName, $key) {
        return $this->getDataMap($mapName)->get($key);
    }

    public function set($mapName, $key, $value) {
        $this->getDataMap($mapName)->set($key, $value);
    }

    private function getDataMap($mapName){
        if( $this->nsql == null ){
            $this->nsql = new \NoSQLite\NoSQLite( $this->dataMapFileName );
        }
        if( !array_key_exists( $mapName, $this->dataMaps ) ){
            $this->dataMaps[$mapName] = $this->nsql->getStore($mapName);
        }
        return $this->dataMaps[$mapName];
    }

    public function deleteAll() {
        $this->nsql = null;
        $this->dataMaps = array();
        @unlink( $this->dataMapFileName );
    }

}

?>
