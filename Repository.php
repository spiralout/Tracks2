<?php
namespace Tracks2\Event;

require_once 'Event.php';
require_once 'JSON.php';

use Tracks2\Domain\Update;
use Phonads\ListM, Phonads\Match, Phonads\Success, Phonads\Some;

class Repository {
    
    function __construct($type) {
        $this->eventStore = new EventStore();
        $this->type = $type;
    }
    
    function load($id) {
        return Match::on($this->eventStore->getFor($id))
            ->None(function($x) { return $x; })
            ->Some(function ($someDomain) {                                
                $update = $someDomain->map(function($events) {
                    return $this->loadEvents($events, $this->type);
                })->value();

                return Match::on($update->result())      
                    ->Tuple(['Failure', ''], function($_) { throw new Exception("Failed to load domain id $id"); })
                    ->Tuple(['Success', ''], function($success, $_) {
                        return new Some($success->value());
                    })->value();
        })->value();
    }
    
    private function loadEvents(ListM $events, $type) {
        return $events->foldL(
            Update::Success(new $type),
            function($update, $event) { 
                return $update->map(function($success) use ($event) { 
                    return $success->value()->accept($event);
                });
            });
    }
    
    function save($entity, ListM $events) {
        echo "Repository::save: ", print_r($entity, true);
        $this->eventStore->store($events);
    }
}