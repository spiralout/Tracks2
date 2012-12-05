<?php
namespace Tracks2\Util;

interface Serializer {
    function __toString();    
}

class JSON implements Serializer {
    
    function __construct($original) {
        $this->original = clone $original;
    }
    
    private function encode($obj) {
        if (is_object($obj)) $obj->__class = get_class($obj);
        
        return json_encode($obj);
    }
    
    function __toString() {
        if (is_null($this->json)) 
            $this->json = $this->encode($this->original);
        
        return $this->json;
    }
    
    private $original;
    private $json;
}