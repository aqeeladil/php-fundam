<?php

require "crud.php";

$repo = new UserRepository();

while (true) {
    echo "\n=== OOP CLI CRUD ===\n";
    echo "1. Create user\n";
    echo "2. List users\n";
    echo "3. View user\n";
    echo "4. Update user\n";
    echo "5. Delete user\n";
    echo "6. Exit\n";

    $choice = readline("Option: ");

    switch ($choice) {
        case 1:
            $name = readline("Enter name: ");
            $email = readline("Enter email: ");
            $user = $repo->create($name, $email);
            print_r($user);
            break;

        case 2:
            print_r($repo->getAll());
            break;

        case 3:
            $id = readline("Enter ID: ");
            $user = $repo->find($id);

            print_r($user ?? "User not found.");
            break;

        case 4:
            $id = readline("Enter ID: ");
            $name = readline("Enter new name: ");
            $email = readline("Enter new email: ");
            $updatedUser = $repo->update($id, $name, $email);
            print_r($updatedUser ? "User updated successfully": "User with ID $id not found.");
            break;

        case 5:
            $id = readline("Enter ID: ");
            echo $repo->delete($id) ? "Deleted\n" : "Not found\n";
            break;

        case 6:
            exit;

        default:
            echo "Invalid option.\n";
            break;
    }
}
