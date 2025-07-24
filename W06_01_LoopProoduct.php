<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loop for Show Product</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <style>
        .container {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <?php
        $products = [
            ['id'=> 1001,'name'=>'Apple','price'=>60,'quantity'=>50],
            ['id'=> 1002,'name'=>'Banana','price'=>30,'quantity'=>80],
            ['id'=> 1003,'name'=>'Orange','price'=>45,'quantity'=>60],
            ['id'=> 1004,'name'=>'Mango','price'=>70,'quantity'=>40],
            ['id'=> 1005,'name'=>'Grapes','price'=>55,'quantity'=>75],
            ['id'=> 1006,'name'=>'Pineapple','price'=>90,'quantity'=>20],
            ['id'=> 1007,'name'=>'Watermelon','price'=>85,'quantity'=>25],
            ['id'=> 1008,'name'=>'Peach','price'=>40,'quantity'=>90],
            ['id'=> 1009,'name'=>'Strawberry','price'=>65,'quantity'=>35],
            ['id'=> 1010,'name'=>'Cherry','price'=>95,'quantity'=>30],
            ['id'=> 1011,'name'=>'Papaya','price'=>50,'quantity'=>60],
            ['id'=> 1012,'name'=>'Kiwi','price'=>75,'quantity'=>45],
            ['id'=> 1013,'name'=>'Lemon','price'=>35,'quantity'=>70],
            ['id'=> 1014,'name'=>'Lychee','price'=>80,'quantity'=>55],
            ['id'=> 1015,'name'=>'Guava','price'=>60,'quantity'=>65]
        ];
    ?>

    <div class="container mt-5">
        <h1 class="mb-4">Product List</h1>


        <form action="" method="post" class="mb-3">
            <input type="number" name="price" placeholder="Enter Price" class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="" class="btn btn-secondary">Reset</a>
        </form>

        <table id="productTable" class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price (Baht)</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php 

                    if(isset($_POST['price']) && !empty($_POST['price'])){
                        $filterPrice = $_POST['price'];
                        $filterProduct = array_filter($products, callback: function($product) use ($filterPrice){
                            return $product['price'] == $filterPrice;
                        });

                        $filterProduct = array_values($filterProduct);
                        
                    }else{
                        $filterProduct = $products;
                    };
                
                foreach ($filterProduct as $index =>  $product): ?>
                    <tr>
                        <td><?= $index +1 ; ?></td>
                        <td><?= $product['id']; ?></td>
                        <td><?= $product['name']; ?></td>
                        <td><?= $product['price']; ?></td>
                        <td><?= $product['quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script>
        let table = new DataTable('#productTable')
    </script>
</body>
</html>
