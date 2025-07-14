/**
 * Nội dung bài học: Include và Require file trong PHP
 * Include: Nếu file không tồn tại thì PHP sẽ báo lỗi và tiếp tục chạy chương trình
 * Require: Nếu file không tồn tại thì PHP sẽ báo lỗi và dừng chương trình
 */
 <?php
    // Ví dụ 1: include
    include 'function.php';
    writeMessage();
    familyName("Hege", 1975);

    // Ví dụ 2: require 
    require 'function.php';
    writeMessage();
    familyName("Hege", 1975);

    // Ví dụ 3: include_once 
    include_once 'function.php';
    writeMessage();

    // Ví dụ 4: require_once
    require_once 'function.php';
    writeMessage();
 ?> 