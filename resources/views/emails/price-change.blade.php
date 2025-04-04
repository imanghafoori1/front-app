<!DOCTYPE html>
<html>
<head>
    <title>Price Change Notification</title>
    @include('emails.email_styles')
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Product Price Change Notification</h2>
        </div>

        <p>Hello,</p>

        <p>We wanted to inform you that the price of the following product has been updated:</p>

        <h3>{{ $product->name }}</h3>

        <div class="price-change">
            <p><strong>Old Price:</strong> <span class="old-price">${{ number_format($oldPrice, 2) }}</span></p>
            <p><strong>New Price:</strong> <span class="new-price">${{ number_format($newPrice, 2) }}</span></p>
        </div>

        <p>Thank you for your attention to this update.</p>

        <p>Best regards,<br>Your Store Team</p>
    </div>
</body>
</html>
