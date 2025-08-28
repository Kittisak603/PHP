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

    <h2 style="color: blue;" >IE-Else</h2>
    <!-- เกณฑ์ผ่านการสอบ ต้องได้คะแนน มากกว่า 60 คะแนน-->
    <?php
        $score = 80 ;
        if ($score > 60) {
            echo "คะแนนของคุณคือ $score <br>";
            echo "ผลลัพท์ : สอบผ่าน";
        } else {
            echo "ผลลัพท์ : สอบไม่ผ่าน";
        }    
    ?> <hr>

    <h2 style="color: blue;" >Boolean Variable</h2>         
    <!-- เกณฑ์รสอบว่าเป็นนศ.รึไม่-->
    <?php
    echo "<h3>คุณเป็นนักเรียนใช่รึไม่</h3>" ;
        $istudent = true;
    if ( $istudent) {
        echo "ใช่";
    } else {
        echo "ม่ใช่";
    }  
    ?>
        <hr>
        <h2 style="color: blue;" >Loop</h2>
        <h2>======Loop for======</h2>
        <h3>แสดงตัวเลข1-10</h3>
        <?php
        $sum = 0 ;
            for($i=5;$i<=9; $i++){
                $sum = $sum+$i;
                if ($i < 9) {
                    echo " $i + " ;      
                }else{
                    echo " $i";
                }            
            } 
            echo  " =  $sum";
        ?><hr>
            <h2>======While Loop======</h2>
            <?php
            $n = 1 ;
            while ($n <= 12) {
                echo "2 x $n = ".(2 * $n ). "<br>";
                $n++;
            }
            ?><hr>
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
                    for ($i=1 ; $i<= 12 ;$i++ ){
                        echo  "<tr>";
                        echo "<td>$i</td>";
                        echo "<td>2 x $i</td>";
                        echo "<td>".(2 * $i)."</td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
</table>




















</body>
</html>