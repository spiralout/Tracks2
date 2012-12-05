<?php
namespace Tracks2\Domain;

use Phonads\StateM, Phonads\ListM, Phonads\ProxyMap, Phonads\Match, Phonads\Success, Phonads\Failure;

class Update extends StateM {
//    use ProxyMap;
    
    function __construct($value, $state = null) {
        $this->value = $value;
        $this->state = $state ?: new ListM();
    }

    function apply($value, $state) {    
        return Match::on($this->value)
            ->Failure(function($_) { return $this; })
            ->Success(function($_) use ($value, $state) {
                return Match::on($value)
                    ->Failure(function($value) use ($state) { return new self($value, $state); })
                    ->Success(function($value) use ($state) { return new self($value, $this->state->append($state)); })
                    ->value();
            })->value();
    }
    
    static function Success($value, $state = null) {
        return new self(new Success($value), $state);
    }
    
    static function Failure($value, $state = null) {
        return new self(new Failure($value), $state);
    }
}