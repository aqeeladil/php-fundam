<?php

require "crud.php";

while (true) {
    echo "\n=== Simple CLI CRUD App ===\n";
    echo "1. Create user\n";
    echo "2. List users\n";
    echo "3. View user by ID\n";
    echo "4. Update user\n";
    echo "5. Delete user\n";
    echo "6. Exit\n";

    $choice = readline("Enter option: ");

    switch ($choice) {
        case 1:
            $name = readline("Enter name: ");
            $email = readline("Enter email: ");
            $user = createUser($name, $email);
            echo "User created!\n";
            print_r($user);
            break;

        case 2:
            $users = getUsers();
            echo "All Users:\n";
            print_r($users);
            break;

        case 3:
            $id = readline("Enter user ID: ");
            $user = getUser($id);
            if ($user) print_r($user);
            else echo "User not found.\n";
            break;

        case 4:
            $id = readline("Enter user ID: ");
            $name = readline("Enter new name: ");
            $email = readline("Enter new email: ");
            $updated = updateUser($id, $name, $email);
            if ($updated) {
                echo "User updated:\n";
                print_r($updated);
            } else {
                echo "User not found.\n";
            }
            break;

        case 5:
            $id = readline("Enter user ID: ");
            if (deleteUser($id)) echo "User deleted.\n";
            else echo "User not found.\n";
            break;

        case 6:
            echo "Exiting...\n";
            exit;

        default:
            echo "Invalid option.\n";
            break;
    }
}

?>
