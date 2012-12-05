<?php
namespace Tracks2\Event;

use Tracks2\Util\JSON;
use Phonads\ListM, Phonads\Some, Phonads\None;

interface Event {}

class EventStore {
    function __construct() {
        $this->events = new ListM();        
    }
    
    function getFor($id) {
        if (isset($this->events[$id])) {
            return new Some($this->events[$id]);
        } else {
            return new None;
        }
    }

    function store(ListM $events) {
        $events->map(function($e) {
           if (!isset($this->events[$e->id])) {
                $this->events[$e->id] = new ListM();
            }
            
            echo (new JSON($e)), PHP_EOL;
            $this->events[$e->id] = $this->events[$e->id]->append(new ListM([$e]));
        });
    }
    
    private $events;
}
