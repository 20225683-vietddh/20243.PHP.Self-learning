<?php
    /**
     * Nội dung bài học: Các câu lệnh điều kiện trong PHP
     * if
     * if else
     * if else if else
     * switch
     */

    // Ví dụ 1: if
    $a = 10;
    $b = 20;
    if ($a > $b) {
        echo "a lớn hơn b";
    }

    // Ví dụ 2: if else
    $a = 10;
    $b = 20;
    if ($a > $b) {
        echo "a lớn hơn b";
    } else {
        echo "a nhỏ hơn b";
    }

    // Ví dụ 3: if else if else
    $a = 10;
    $b = 20;
    if ($a > $b) {
        echo "a lớn hơn b";
    } else if ($a == $b) {
        echo "a bằng b";
    } else {
        echo "a nhỏ hơn b";
    }

    // Ví dụ 4: switch
    $a = 10;
    switch ($a) {
        case 10:
            echo "a bằng 10";
            break;
        case 20:
            echo "a bằng 20";
            break;
        case 30:
            echo "a bằng 30";
            break;
        default:
            echo "a không bằng 10 hoặc 20 hoặc 30";
            break;
    }
?>