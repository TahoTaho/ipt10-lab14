<?php
require 'init.php'; // Include your Stripe initialization file

$message = ''; // Variable to store messages

// Retrieve the list of products from Stripe
$products = $stripe->products->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $line_items = [];

    // Get selected products from the form
    if (isset($_POST['products']) && !empty($_POST['products'])) {
        foreach ($_POST['products'] as $product_id) {
            $product = $stripe->products->retrieve($product_id);
            $price_id = $product->default_price;
            // Add product to line items
            $line_items[] = [
                'price' => $price_id,
                'quantity' => 1,
            ];
        }

        // Create payment link using Stripe API
        $payment_link = $stripe->paymentLinks->create([
            'line_items' => $line_items
        ]);

        // Success message with the payment link
        $message = "Payment link created successfully!<br>
                    <a href='$payment_link->url' target='_blank' class='btn btn-success'>Pay the invoice</a>";
    } else {
        $message = "Please select at least one product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payment Link</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <h1>Generate Payment Link</h1>

        <!-- Display success or error message -->
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Payment Link Form -->
        <form action="generate-payment-link.php" method="POST">
            <div class="mb-4">
                <label for="products" class="form-label">Select Products:</label><br>
                <?php foreach ($products as $product): 
                    $price_id = $product->default_price;
                    $price = $stripe->prices->retrieve($price_id); // Retrieve the price object
                    $price_amount = $price->unit_amount / 100; // Convert to dollars (assuming USD)
                    $currency = strtoupper($price->currency);
                ?>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="product_<?= htmlspecialchars($product->id) ?>" name="products[]" value="<?= htmlspecialchars($product->id) ?>">
                        <label class="form-check-label" for="product_<?= htmlspecialchars($product->id) ?>">
                            <?= htmlspecialchars($product->name) ?> - <?= $currency . ' ' . number_format($price_amount, 2) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">Generate Payment Link</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
