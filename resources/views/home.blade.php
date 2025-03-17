<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Welcome to POS application!</h1>

    <div id="appMenu" style="margin-top: 40px">
        <h3>App Menu :</h3>
        <a href="{{ url('/sales') }}">Sales Transactions</a><br>
        <a href="{{ url('/user/1/name/Ridho Anfaal') }}">User Profile</a><br>
    </div>

    <div id="product" style="margin-top: 30px">
        <h3>Product Category List :</h3>
        <a href="{{ url('/category/food-beverage') }}">Food & Beverage</a><br>
        <a href="{{ url('/category/beauty-health') }}">Beauty & Health</a><br>
        <a href="{{ url('/category/home-care') }}">Home Care</a><br>
        <a href="{{ url('/category/baby-kid') }}">Baby & Kid</a><br>
    </div>
</body>
</html>