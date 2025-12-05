<?php

// Represents a user model.
class User {
    public $id;
    public $name;
    public $email;

    public function __construct($name, $email, $id = null) {
        $this->id = $id ?? time();
        $this->name = $name;
        $this->email = $email;
    }
}
