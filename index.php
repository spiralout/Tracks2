<?php
require_once 'vendor/autoload.php';
require_once 'Repository.php';
require_once 'Person.php';
require_once 'Update.php';

use Phonads\ListM, Phonads\Match;
use Tracks2\Event\Repository, Tracks2\Domain\Update, Phonads\Success;

class PersonService {
    
    function __construct() {
        $this->repo = new Repository('Person');
    }

    function createPerson($name, $age) {
        return Match::on(Person::create($name, $age)->result())
            ->Tuple(['Failure', ''], function($_) { throw new Exception("Error!"); })
            ->Tuple(['Success', ''], function($result, $events) {
                $this->repo->save($result->value(), $events);                
                return $result->value()->id();                
            })->value();
    }

    function setPersonName($id, $name) {  
        $person = Match::on($this->repo->load($id))
            ->None(function($_) use ($id) { throw new Exception("Couldn't find id: $id"); })                
            ->Some(function($somePerson) use ($name) {
                return Update::Success($somePerson->value())
                    ->map(function($success) use ($name) { 
                        return $success->value()->setName($name);                         
                    })->result();
            })->value();
                
        return Match::on($person)
            ->Tuple(['Failure', ''], function($_) use ($id) { throw new Exception("Failed to update Person id: $id"); })
            ->Tuple(['Success', ''], function($result, $events) {
                $this->repo->save($result->value(), $events);
            })->value();
    }
    
    function setPersonAge($id, $age) {  
        $person = Match::on($this->repo->load($id))
            ->None(function($_) use ($id) { throw new Exception("Couldn't find id: $id"); })                
            ->Some(function($somePerson) use ($age) {
                return Update::Success($somePerson->value())
                    ->map(function($success) use ($age) {
                        return $success->value()->setAge($age);
                    })->result();
                })->value();
        
        return Match::on($person)
            ->Tuple(['Failure', ''], function($_) { throw new Exception("Failed to update Person id: $id"); })
            ->Tuple(['Success', ''], function($result, $events) {
                $this->repo->save($result->value(), $events);
            })->value();
    }

}






$service = new PersonService;
$id = $service->createPerson("George Michael", 16);
$service->setPersonName($id, "Bob Loblaw");
$service->setPersonAge($id, 44);
$service->setPersonName($id, "Bob Loblawasdadasd");

