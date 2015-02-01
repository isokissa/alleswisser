<?php
namespace Isokissa\Alleswisser;


class DataMap implements DataMapInterface
{

}

interface DataMapInterface {

    public function add( $mapName, $key, $value );

    public function get( $mapName, $key );

}

?>
