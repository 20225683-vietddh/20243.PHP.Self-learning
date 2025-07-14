/**
 * Nội dung bài học: Mảng trong PHP
 * Mảng 1 chiều
 * Mảng 2 chiều
 * Associative array
 */

<?php
    // Ví dụ 1: Mảng 1 chiều
    $array = array(1, 2, 3, 4, 5);
    echo $array[0];

    // Ví dụ 2: Mảng 2 chiều
    $array = array(
        array(1, 2, 3, 4, 5),
        array(6, 7, 8, 9, 10),
        array(11, 12, 13, 14, 15)
    );
    echo $array[0][0];

    // Ví dụ 3: Associative array
    $array = array("name" => "John", "age" => 20, "city" => "New York");
    echo $array["name"]; // John

    // Ví dụ 4: Thêm 1 ví dụ về associative array
    $array = array("name" => "John", "age" => 20, "city" => "New York");
    $array["email"] = "john@example.com";
    echo $array["email"]; // john@example.com

    // Ví dụ 5: Thêm 1 ví dụ về associative array
    $array = array("name" => "John", "age" => 20, "city" => "New York");
    $array["email"] = "john@example.com";
    echo $array["email"]; // john@example.com
    $array["phone"] = "1234567890";
    echo $array["phone"]; // 1234567890
?>
