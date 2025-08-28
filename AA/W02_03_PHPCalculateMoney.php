<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Calculate Money</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <h1 class="text-center">PHP Calculate Money</h1>
    <hr>
    <div class="container ">
    <p class="text-center">กรุณากรอกข้อมูลเพื่อทำการคำนวณยอดเงิน</p>
    <form action="" method="post" class="text-center">
        <div class="row justify-content-center mb-3">
            <div class="col-md-5">
                <label>Price</label>
                <input type="number" name="price" class="form-control" value="<?  $_POST['price'] ?? ''; ?>">
            </div>
            <div class="col-md-5">
                <label>Amount</label>
                <input type="number" name="amount" class="form-control" value="<? $_POST['amount'] ?? ''; ?>">
            </div>
        </div>
        <button type="submit" name="calculate" class="btn btn-primary">Calculate</button>
        <a href="" class="btn btn-secondary">Reset All</a>
    </form>

    

</div>
<hr>
<a href="index.php" class="mx-3">Home</a>
</body>
</html> 
   