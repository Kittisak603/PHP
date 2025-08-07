    <?php
        require_once 'W07_01_ConnectDB.php';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loop for Show Product</title>

     

    <style>
        .container {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <?php
            $sql = "SELECT * FROM products";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);






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
                    <th>Category</th>
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
                        $filterProduct =  $data;
                    };
                
                foreach ($filterProduct as $index =>  $product): ?>
                    <tr>
                        <td><?= $index +1 ; ?></td>
                        <td><?= $product['product_id']; ?></td>
                        <td><?= $product['product_name']; ?></td>
                        <td><?= $product['category']; ?></td>
                        <td><?= $product['price']; ?></td>
                        <td><?= $product['stock_quantity']; ?></td>
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
