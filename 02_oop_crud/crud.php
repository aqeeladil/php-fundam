<?php

require "User.php";
require "DataStore.php";

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = new DataStore();
    }

    public function getAll() {
        return $this->db->read();
    }

    public function find($id) {
        $users = $this->db->read();
        if (empty($users)) return null;
        foreach ($users as $user) {
            if ($user["id"] == $id) return $user;
        }
        return null;
    }

    public function create($name, $email) {
        $users = $this->db->read();
        
        $newUser = new User($name, $email);
        $users[] = (array)$newUser;
        $this->db->save($users);
        return $newUser;
    }

    public function update($id, $name, $email) {
        $users = $this->db->read();
        foreach ($users as $i => $user) {
            if ($user["id"] == $id) {
                $users[$i]["name"] = $name;
                $users[$i]["email"] = $email;
                $this->db->save($users);
                return $users[$i];
            }
        }
        return false;
    }

    public function delete($id) {
        $users = $this->db->read();
        
        foreach ($users as $i => $user) {
            if ($user["id"] == $id) {
                array_splice($users, $i, 1);
                $this->db->save($users);
                return true;
            }
        }
        return false;
    }
}
