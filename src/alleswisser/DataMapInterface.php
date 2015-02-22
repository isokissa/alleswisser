<?php
namespace Isokissa\Alleswisser;


interface DataMapInterface {

    public function set( $mapName, $key, $value );

    public function get( $mapName, $key );
    
    public function count( $mapName );
    
    public function deleteAll();

}

?>