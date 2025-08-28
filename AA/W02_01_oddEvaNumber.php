<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Odd Even Numder Checker</h1>
    <hr>
    <p class="text-center">กรุณากรองตัวเลขเพี่อตรวจสอบว่าเป็นเลขคู่รึเลขคี่</p>
    
    <form action="" method="post" class="text-center">
        <div class="form-group">
            <input type="number" name="number" id="number" class="form-control w-50 mx-auto" placeholder="Enter a number" >
    </div>
    <button type="submit" class="btn btn-primary mt-5" >Check</button>
    </form>
    <!--แสดงผลลัพธ์-->
    
    
    <!-- คำนวณเลขคู่คี่-->
    <?php
            $number = $_POST['number'] ?? null;
            //$mod = 7;
                if($number % 2 ==0 ){
                echo "<h3 class='text-success text-center'>The number $number Even Number</h3>";
            }else{
                echo "<h3 class='text-danger text-center'>The number $number Odd Number</h3>";
            }
            ?>
</div>
<hr>
<a href="index.php">HOME</a>


</body>
</html>