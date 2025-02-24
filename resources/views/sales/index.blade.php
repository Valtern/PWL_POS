<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Transactions - POS</title>
</head>
<body>
    <h1>Sales Transactions</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>001</td>
                <td>Mineral Water</td>
                <td>5</td>
                <td>$5.00</td>
            </tr>
            <tr>
                <td>002</td>
                <td>Shampoo</td>
                <td>2</td>
                <td>$10.00</td>
            </tr>
            <tr>
                <td>003</td>
                <td>Baby Diapers</td>
                <td>1</td>
                <td>$12.00</td>
            </tr>
            <tr>
                <td>004</td>
                <td>Detergent</td>
                <td>3</td>
                <td>$24.00</td>
            </tr>
        </tbody>
    </table><br>
    <a href="{{ url('/') }}">Back to Home</a>
</body>
</html>