<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Basic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>

<body>
    <h1>Welcom to PHP Basic</h1>
    <p>This is a simple application.</p>
    <hr>
    <h2 style="color: red; ">Basic PHP Syntax</h2>

    <pre>       &lt; ?php
        echo "Hello World  ";
        ? &gt;</pre>

    <h3>Resuit</h3>

    <div style="color:blue; ">
        <?php
        echo "Hello World <br> ";
        print "<span style='color:aqua;'> Kittisak </span> ";
        ?>
    </div>
    <hr>

    <h2 style="color: red; ">PHP Variables</h2>

    <pre>       &lt; ?php
    $greeting = "Hello World  ";
    echo $greeting;
        ? &gt;</pre>

    <h3>Resuit</h3>

    <?php
    $greeting = "Hello World <br> ";
    echo "<span style= 'color:green;'> $greeting </span>";
    ?>

    <hr>
    <h2 style="color: red; ">Integer Variable Example</h2>
    <?php
    $age = 20;
    echo "<span style='color:blue;'>i am $age year old</span>";
    ?>
    <hr>
    <h2 style="color: black; ">Calcuate</h2>
    <?php
    $age = 5;
    $s = 4;
    $e = $age + $s;
    echo "<span style='color:blue;'>sum fo $age and $s is $e</span>";
    ?>
    <h2 style="color: black; ">คำนวณพื้นที่สามเหลี่ยม </h2>
    <?php
    $a = 1 / 2;
    $d = 7;
    $u = 8;
    $s = $a * $d * $u;
    echo "<span style='color:blue;'>พื้นที่สามเหลี่ยมคือ $s</span>";
    ?>
    <h2 style="color: black; ">คำนวณอายุจากปีเกิด</h2>
    <?php
    $age = 2567;
    $s = 2547;
    $e = $age - $s;
    echo "<span style='color:blue;'>อายุของเราคือ $e</span>";
    ?>
    <hr>

    <h2 style="color: blue;">IE-Else</h2>
    <!-- เกณฑ์ผ่านการสอบ ต้องได้คะแนน มากกว่า 60 คะแนน-->
    <?php
    $score = 80;
    if ($score > 60) {
        echo "คะแนนของคุณคือ $score <br>";
        echo "ผลลัพท์ : สอบผ่าน";
    } else {
        echo "ผลลัพท์ : สอบไม่ผ่าน";
    }
    ?>
    <hr>

    <h2 style="color: blue;">Boolean Variable</h2>
    <!-- เกณฑ์รสอบว่าเป็นนศ.รึไม่-->
    <?php
    echo "<h3>คุณเป็นนักเรียนใช่รึไม่</h3>";
    $istudent = true;
    if ($istudent) {
        echo "ใช่";
    } else {
        echo "ม่ใช่";
    }
    ?>
    <hr>
    <h2 style="color: blue;">Loop</h2>
    <h2>======Loop for======</h2>
    <h3>แสดงตัวเลข1-10</h3>
    <?php
    $sum = 0;
    for ($i = 5; $i <= 9; $i++) {
        $sum = $sum + $i;
        if ($i < 9) {
            echo " $i + ";
        } else {
            echo " $i";
        }
    }
    echo " =  $sum";
    ?>
    <hr>
    <h2>======While Loop======</h2>
    <?php
    $n = 1;
    while ($n <= 12) {
        echo "2 x $n = " . (2 * $n) . "<br>";
        $n++;
    }
    ?>
    <hr>
    <h2>=====สูตรคูณใส่ตาราง========</h2>
    <table class="table table-bordered table-striped w-auto mx-auto text-center">
        <thead class="table-success">
            <tr>
                <th>ลำดับ</th>
                <th>สูตรคูณ</th>
                <th>ผลลัพท์</th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 1; $i <= 12; $i++) {
                echo "<tr>";
                echo "<td>$i</td>";
                echo "<td>2 x $i</td>";
                echo "<td>" . (2 * $i) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>



    <hr>
    <!-- ======================================================== -->
    <!-- ======================================================== -->
    <!-- ======================================================== -->
    <!-- ======================================================== -->

    <hr>
    <h2>สร้างตัวแปรอาเรย์ แบบที่ 1: Indexed Array</h2>
    <h5>PHP จะกำหนด index เป็นตัวเลขอัตโนมัติ โดยเริ่มจาก 0</h5>
    <hr>



    <h3>แสดงรายการผลไม้ โดยใช้ index</h3>
    <?php
    $fruits = array("Apple", "Banana", "Cherry");
    ?>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
        echo $fruits[0] . "<br>"; // Apple
        echo $fruits[1] . "<br>"; // Banana
        echo $fruits[2] . "<br>"; // Charry
        
        ?>
        <div style="color:red; background-color: lightgray; padding: 10px;">
            <?php
            echo "ผลไม้" . $fruits[0] . "<br>"; // Apple
            echo "ผลไม้" . $fruits[1] . "<br>"; // Banana
            echo "ผลไม้" . $fruits[2] . "<br>"; // Charry
            




            ?>
        </div>
    </div>
    <h4>แสดงรายการผลไม้ โดยใช้ print readable</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
        echo "รายการผลไม้: <br>" . count($fruits) . "<br>";
        print_r($fruits); // แสดงผลอาเรย์ทั้งหมด  print readable
        echo "<br>";

        ?>
    </div>
    <br>
    <!-- ======================================================== -->
    <br>
    <h4>แสดงรายการผลไม้ โดยใช้ implode เพื่อแปลงอาเรย์เป็นสตริง</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
        // แสดงรายการผลไม้และจำนวนสมาชิกในอาเรย์
        // ใช้ implode เพื่อแปลงอาเรย์เป็นสตริง และแสดงผลลัพธ์
        echo "รายการผลไม้: " . implode(", ", $fruits) . "<br>"; // ผลลัพธ์: Apple, Banana, Cherry
        echo "<br>";
        ?>
    </div>
    <!-- ======================================================== -->
    <br>
    <h4>แสดงรายการผลไม้ ใช้คำสั่ง foreach เพื่อวนลูป</h4>
    <div style="color:green; background-color: lightgray; padding: 10px;">
        <?php
        // ใช้คำสั่ง foreach เพื่อวนลูปค่าใน array ทีละตัว โดยในแต่ละรอบ ตัวแปร $fruit จะเก็บค่าผลไม้ 1 ชนิด
        foreach ($fruits as $fruit) {
            if ($fruit === end($fruits)) {
                echo "$fruit.";
            } else {
                echo "$fruit, ";
                # code...
            }
        }
        echo "<br>";
        ?>
    </div>

    <!-- ======================================================== -->
    <!-- ======================================================== -->

    <hr>
    <h2>สร้างตัวแปรอาเรย์ แบบที่ 2: Associative Array</h2>
    <h6>เป็น array ซ้อนกันหลายชุด (Multidimensional array)</h6>
    <h6>แต่ละชุดเป็น associative array ที่ระบุชื่อ key ชัดเจน เช่น "name" และ "price"</h6>
    <h6>ใช้สำหรับเก็บข้อมูลที่มีความสัมพันธ์กัน key => value เช่น รายการสินค้า</h6>

    <hr>

    <?php
    // สร้างอาเรย์ของผลไม้ที่มีชื่อและราคา
    $products = [
        ["name" => "Apple", "price" => 30],
        ["name" => "Banana", "price" => 20],
        ["name" => "Cherry", "price" => 15]
    ];
    ?>
    <!-- ======================================================== -->
    <br>
    <h4>แสดงรายการผลไม้ ใช้คำสั่ง key value</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
        // แสดงผลลัพธ์ของการเข้าถึงข้อมูลในอาเรย์
        echo $products[0]["name"] . "<br>";  // Apple
        echo $products[2]["price"] . "<br>"; // 15
        

        ?>
    </div>
    <h4>แสดงรายการสินค้า ใช้คำสั่ง foreach เพื่อวนลูป</h4>
    <div style="color:green; background-color: lightgray; padding: 10px;">
        <?php
        $total_price = 0;
        foreach ($products as $product) {
            echo "สินค้า: " . $product["name"] . ", ราคา: " . $product["price"] . " บาท<br>";
            $total_price += $product["price"]; // บวกทีละรายการ
        }
        echo "<br>ราคารวมทั้งหมด: " . $total_price . " บาท";
        ?>
    </div>

</body>

</html>