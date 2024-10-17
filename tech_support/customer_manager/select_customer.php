<?php
require_once('../model/database_oo.php');

// error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// fetch customer ID and validate it
$customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);

// If the customer ID is invalid, redirect back to the customer search page
if (!$customer_id) {
    header('Location: index.php');
    exit();
}

// get customer data by ID
$db = Database::getDB(); 
$query = 'SELECT * FROM customers WHERE customerID = :customer_id';
$statement = $db->prepare($query);
$statement->bindValue(':customer_id', $customer_id);
$statement->execute();
$customer = $statement->fetch();
$statement->closeCursor();

// if no customer is found, redirect back to the search page
if (!$customer) {
    header('Location: index.php?error=Customer+not+found');
    exit();
}

// fetch countries for the dropdown
$query = 'SELECT * FROM countries';
$statement = $db->prepare($query);
$statement->execute();
$countries = $statement->fetchAll();
$statement->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Update Customer</title>
        <link rel="stylesheet" type="text/css" href="/PHPAssignment5/tech_support/css/main.css">
    </head>
    <body>
        <?php include('../view/header.php'); ?>
        <main>
            <h1>View/Edit Customer</h1>
            <form action="update_customer.php" method="post" id="aligned">
                <div id="data">
                    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['customerID']); ?>">
                    
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($customer['firstName']); ?>"><br>
                    
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($customer['lastName']); ?>"><br>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>"><br>
                    
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>"><br>
                    
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>"><br>
                    
                    <label for="state">State:</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>"><br>
                    
                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($customer['postalCode']); ?>"><br>
                    
                    <label for="country_code">Country:</label>
                    <select name="country_code" id="country_code">
                        <?php foreach ($countries as $country) : ?>
                            <option value="<?php echo htmlspecialchars($country['countryCode']); ?>"
                                <?php if ($customer['countryCode'] == $country['countryCode']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($country['countryName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>"><br>
                    
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($customer['password']); ?>"><br>
                </div>
                
                <div id="buttons">
                    <label>&nbsp;</label>
                    <input type="submit" value="Update">
                </div>
            </form>

            <p><a href="index.php">Search Customers</a></p>
            <?php include('../view/footer.php'); ?>
        </main>
    </body> 
</html>
