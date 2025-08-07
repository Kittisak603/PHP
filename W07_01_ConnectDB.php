<?php
// connect database ด้วย mysqli
//$host="localhost";
//$username= "root";
//$password= "";
//$database="db68_product";

//$conn = new mysqli($host, $username, $password, $database);

//if ($conn->connect_error) {
//die("Connection failed(เชื่อมต่อไม่สำเร็จ) ". $conn->connect_error);
//}else{
//echo "Connected successfully(เชื่อมต่อสำเร็จ)";
//}


// connect database ด้วย PDO
$host = "localhost";
$username = "root";
$password = "";
$database = "db68_product";



try {
    //$conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo " PDO : Connected successfully (เชื่อมต่อสำเร็จ)";

} catch (PDOException $e) {
    echo " PDO : Connection failed(เชื่อมต่อไม่สำเร็จ)" . $e->getMessage();
}


?>