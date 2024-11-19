<?php
require 'init.php';
$products = $stripe->products->all();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Products</title>
    <link rel="stylesheet" href="product.css">
</head>
<body>
    <h1>Our Products</h1>

    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <h2><?= htmlspecialchars($product->name) ?></h2>
                <?php if (!empty($product->images)): ?>
                    <img src="<?= htmlspecialchars($product->images[0]) ?>" alt="<?= htmlspecialchars($product->name) ?>" width="200" />
                <?php endif; ?>
                <p><strong>Price: </strong>
                    <?php
                        $price = $stripe->prices->retrieve($product->default_price);
                        echo strtoupper($price->currency) . ' ' . number_format($price->unit_amount / 100, 2);
                    ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
