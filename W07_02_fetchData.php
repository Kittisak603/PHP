<?php
require_once 'W07_01_ConnectDB.php';

$sql = "SELECT * FROM products";

$result = $conn->query($sql); //ใช่ query() เพื่อดรันคำสั่ง SQL และแสดงผลลัพธ์

// ตรวจสอบว่ามีผลลัพธ์หรือไม่
if ($result->rowCount() > 0) {
    // output data of each row
    //echo "<h2>พบ ข้อมูลในตราราง Product</h2><br>";
 
    $data = $result->fetchAll(PDO::FETCH_NUM);

    print_r($data); // แสดงข้อมูลทั้งหมดในรูปแบบ array

    //echo "$data[0][0] <br>";
     //  ============================================================================================================================================
     //ใช้ prepared statemen เพื่อป้องกัน SQL imjection
     //ใช้ execute() เพื่อรันคำสั่ง SQL 
     //ใช้ fetchAll() เพื่อดึงข้อมูลทั้งหมดในครั้งเดียว
     //ใช้ print_r เพื่อแสดงข้อมูลทั้งหมดในรูปแบบ array
    //echo "<br>";
    //echo "==============================================================================================================================================";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //echo "<br>";
            //echo "<pre>";
                //print_r($data);
            //echo "</pre>";

 //=====================================================================================================================================================
    // แสดงผลข้อมูลที่ดึงมา
            header('Content-Type: application/json'); // ระบุ Content-Type เป็น JSON
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); 
            // แปลงข้อมูลใน $arr เป็น JSON และแสดงผล
} else {
    echo "<h2>ไม่พบข้อมูลในตาราง Product</h2>";
}

?>