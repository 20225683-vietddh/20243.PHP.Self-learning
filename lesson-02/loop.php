<?php
    /**
     * Nội dung bài học: Các vòng lặp trong PHP
     * while
     * do while
     * for
     * foreach
     */

    // Ví dụ 1: while
    $a = 1;
    while ($a <= 5) {   
        echo "a = $a <br>";
        $a++;
    }

    // Ví dụ 2: do while
    $a = 1;
    do {
        echo "a = $a <br>";
        $a++;
    } while ($a <= 5);

    // Ví dụ 3: for
    for ($a = 1; $a <= 5; $a++) {
        echo "a = $a <br>";
    }

    // Ví dụ 4: foreach
    $array = array(1, 2, 3, 4, 5);
    foreach ($array as $value) {
        echo "value = $value <br>";
    }

    // Ví dụ 5: foreach với key và value
    $array = array("name" => "John", "age" => 20, "city" => "New York");
    foreach ($array as $key => $value) {
        echo "key = $key, value = $value <br>";
    }
?>