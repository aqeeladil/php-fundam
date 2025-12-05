<?php

$DB_FILE = "data.json";

// Read data from JSON file
function readDB() {
    global $DB_FILE;

    if (!is_readable($DB_FILE)) {
        throw new RuntimeException("Cannot read DB file: $DB_FILE");
    }

    $json = file_get_contents($DB_FILE);
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException("JSON decode error: " . json_last_error_msg());
    }
    return $data;
}


// Save data to JSON file
function saveDB($data) {
    global $DB_FILE;

     // Convert PHP data to JSON
    $json = json_encode($data, JSON_PRETTY_PRINT);

    // Check if JSON encoding failed
    if ($json === false) {
        throw new RuntimeException('json_encode error: ' . json_last_error_msg());
    }

    // Write JSON to file and check for errors
    if (file_put_contents($DB_FILE, $json) === false) {
        throw new RuntimeException("Failed to write to $DB_FILE");
    }

}

// CREATE
function createUser($name, $email) {
    $db = readDB();
    
    $new = [
        "id" => time(), // unique id
        "name" => $name,
        "email" => $email
    ];

    $db[] = $new;
    saveDB($db);

    return $new;
}

// READ (get all)
function getUsers() {
    return readDB();
}

// READ (get single by id)
function getUser($id) {
    $db = readDB();

    
    // Make sure $db is an array
    if (!is_array($db)) {
        // return null;
        throw new RuntimeException("Database is invalid or unreadable.");
    }

    foreach ($db as $user) {

        // Check if 'id' exists and compare strictly
        if (isset($user["id"]) && $user["id"] === $id) {
            return $user;
        }
    }
    // return null;
    throw new RuntimeException("User with id '$id' not found.");
}

// UPDATE
function updateUser($id, $newName, $newEmail) {
    $db = readDB();
    
    foreach ($db as $i => $user) {
        if ($user["id"] === $id) {
            $db[$i]["name"] = $newName;
            $db[$i]["email"] = $newEmail;
            saveDB($db);
            return $db[$i];
        }
    }
    return false;
}

// DELETE
function deleteUser($id) {
    $db = readDB();
    foreach ($db as $i => $user) {
        if ($user["id"] == $id) {
            array_splice($db, $i, 1);
            saveDB($db);
            return true;
        }
    }
    return false;
}
?>
