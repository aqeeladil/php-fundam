<?php

// Handles reading/writing JSON.
class DataStore {
    private $file;

    public function __construct($file = "data.json") {
        $this->file = $file;
    }

    public function read() {
        return json_decode(file_get_contents($this->file), true);
    }

    public function save($data) {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }
}
