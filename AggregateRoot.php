<?php
namespace Tracks2\Domain;

require_once 'Update.php';

use Tracks2\Domain\Update;
use Phonads\Success, Phonads\ListM;

trait AggregateRoot {
    
    function id() {
        return $this->id;
    }

    function accept($event) {
        return Update::Success($this->handle($event), new ListM([$event]));
    }

    function reject($msg) {
        return Update::Failure($msg);
    }    

    abstract function handle($event);
    
    protected $id;
    protected $version = 0;
}

trait Copyable {
    
    function copy(array $overwrite) {
        $copy = clone $this;

        foreach ($overwrite as $property => $value) {
            $copy->$property = $value;
        }

        return $copy;
    }
}

trait Snapshot {
    function snapshot() {
        
    }
}