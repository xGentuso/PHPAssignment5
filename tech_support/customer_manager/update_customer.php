<?php
require_once('../model/database_oo.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// retrieve and sanitize form inputs
$customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
$first_name = filter_input(INPUT_POST, 'first_name');
$last_name = filter_input(INPUT_POST, 'last_name');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$address = filter_input(INPUT_POST, 'address');
$city = filter_input(INPUT_POST, 'city');
$state = filter_input(INPUT_POST, 'state');
$postal_code = filter_input(INPUT_POST, 'postal_code');
$country_code = filter_input(INPUT_POST, 'country_code');
$phone = filter_input(INPUT_POST, 'phone');
$password = filter_input(INPUT_POST, 'password');

// check for required fields
if (!$customer_id || !$first_name || !$last_name || !$email || !$address) {
    
    header("Location: select_customer.php?error=Invalid+input");
    exit();
}

// check if the password was updated
if (!empty($password)) {
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
} else {
    // if no password update, keep the existing password in the database
    $query = 'SELECT password FROM customers WHERE customerID = :customer_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':customer_id', $customer_id);
    $statement->execute();
    $existing_customer = $statement->fetch();
    $statement->closeCursor();
    
    // if no password is provided, use the current password from the database
    $hashed_password = $existing_customer['password'];
}

// update the customer in the database
$db = Database::getDB();
$query = 'UPDATE customers
          SET firstName = :first_name, lastName = :last_name, email = :email, 
              address = :address, city = :city, state = :state, postalCode = :postal_code, 
              countryCode = :country_code, phone = :phone, password = :password
          WHERE customerID = :customer_id';
$statement = $db->prepare($query);
$statement->bindValue(':customer_id', $customer_id);
$statement->bindValue(':first_name', $first_name);
$statement->bindValue(':last_name', $last_name);
$statement->bindValue(':email', $email);
$statement->bindValue(':address', $address);
$statement->bindValue(':city', $city);
$statement->bindValue(':state', $state);
$statement->bindValue(':postal_code', $postal_code);
$statement->bindValue(':country_code', $country_code);
$statement->bindValue(':phone', $phone);
$statement->bindValue(':password', $hashed_password);
$statement->execute();
$statement->closeCursor();

// redirect to a confirmation page or back to the customer list
header('Location: index.php?success=Customer+updated');
exit();
?>
