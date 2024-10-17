<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once('../model/database_oo.php');

if (!class_exists('Database')) {
    die('Error: Database class not found. Please check your include paths.');
}

$db = Database::getDB();

try {
    $query = 'SELECT * FROM products';
    $statement = $db->prepare($query);
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC); 
    $statement->closeCursor();
} catch (PDOException $e) {
    
    error_log('Database Error: ' . $e->getMessage());
    
    $_SESSION['error_message'] = 'Unable to retrieve products. Please try again later.';
    
    header('Location: ../errors/database_error.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Product Manager</title>
        <link rel="stylesheet" type="text/css" href="/PHPAssignment5/tech_support/css/main.css">
    </head>
<body>
    <?php
    include('../view/header.php');
    ?>
    <main>
        <div class="container">
            <h1>Product List</h1>
            
            <!-- Display Error Message if Exists -->
            <?php
            if (isset($_SESSION['error_message'])):
            ?>
                <p class="error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
                <?php
                // Unset the error message after displaying it
                unset($_SESSION['error_message']);
                ?>
            <?php endif; ?>
    
            <table>
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Release Date</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products): ?>
                        <?php foreach ($products as $product) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['productCode']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['version']); ?></td>
                            <td>
                                <?php
                                // Format the release date as mm-dd-yyyy (without leading zeros)
                                $release_date = date('n-j-Y', strtotime($product['releaseDate']));
                                echo htmlspecialchars($release_date);
                                ?>
                            </td>
                            <td>
                                <form action="delete_product.php" method="post">
                                    <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['productCode']); ?>">
                                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="/PHPAssignment5/tech_support/product_manager/add_product_form.php" class="button">Add Product</a>
        </div>
    </main>
    <?php
    include('../view/footer.php');
    ?>
</body>
</html>
