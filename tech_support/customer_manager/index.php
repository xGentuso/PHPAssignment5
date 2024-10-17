<?php
require_once('../model/database_oo.php'); // Ensure this path correctly points to where your database_oo.php file is located.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = Database::getDB();  // initialize the $db object using the singleton pattern.

$last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

try {
    if ($last_name) {
        $query = 'SELECT * FROM customers WHERE lastName LIKE :last_name ORDER BY lastName';
        $statement = $db->prepare($query);
        $statement->bindValue(':last_name', '%' . $last_name . '%');
    } else {
        $query = 'SELECT * FROM customers ORDER BY lastName';
        $statement = $db->prepare($query);
    }
    $statement->execute();
    $customers = $statement->fetchAll();
    $statement->closeCursor();
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Manager</title>
    <link rel="stylesheet" type="text/css" href="/PHPAssignment5/tech_support/css/main.css">
</head>
<body>
    <?php include('../view/header.php'); ?>
    <main>
        <h1>Customer Search</h1>
        <form action="." method="post">
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name ?? ''); ?>">
            <input type="submit" value="Search">
        </form>

        <h2>Customer List</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>City</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)) : ?>
                    <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['city']); ?></td>
                            <td>
                                <form action="select_customer.php" method="post">
                                    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['customerID']); ?>">
                                    <input type="submit" value="Update">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php include('../view/footer.php'); ?>
    </main>
</body>
</html>
