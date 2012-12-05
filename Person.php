<?php
require_once 'AggregateRoot.php';
require_once 'Event.php';

use Tracks2\Domain\AggregateRoot, Tracks2\Domain\Copyable, Tracks2\Event\Event;

class Person {
    use AggregateRoot, Copyable;

    function setName($name) {
        if ($name == "James")
            return $this->reject("Illegal name: $name");
        else
            return $this->accept(new PersonSetName($this->id, $name));
    }

    function setAge($age) {
        return $this->accept(new PersonSetAge($this->id, $age));
    }

    function handle($event) {
        switch (get_class($event)) {
            case 'PersonCreated':
                return $this->copy(array('version' => $this->version + 1, 'id' => $event->id, 'name' => $event->name, 'age' => $event->age));
            case 'PersonSetName':
                return $this->copy(array('version' => $this->version + 1, 'name' => $event->name));
            case 'PersonSetAge':
                return $this->copy(array('version' => $this->version + 1, 'age' => $event->age));
        }
    }
    
    static function create($name, $age) {
        return (new self)->accept(new PersonCreated(uniqid('', true), $name, $age));
    }
    
    protected $name;
    protected $age;
}

class PersonCreated implements Event {
    function __construct($id, $name, $age) {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
    }
}

class PersonSetName implements Event {
    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

class PersonSetAge implements Event {
    function __construct($id, $age) {
        $this->id = $id;
        $this->age = $age;
    }
}