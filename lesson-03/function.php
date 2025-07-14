/**
 * Nội dung bài học: function trong PHP
 */
 <?php
    // Ví dụ 1: Function không tham số
    function writeMessage() {
        echo "Hello World!";
    }

    writeMessage();

    // Ví dụ 2: Function có tham số
    function familyName($name, $year) {
        echo "$name Refsnes. Born in $year <br>";
    }

    familyName("Hege", 1975);
    familyName("Stale", 1978);
    familyName("Kai Jim", 1983);

    // Ví dụ 3: Function có tham số mặc định
    function setHeight($minheight = 50) {
        echo "The height is : $minheight <br>";
    }

    setHeight(350);
    setHeight();
    setHeight(135);
    setHeight(80);

    // Ví dụ 4: Function có trả về giá trị
    function sum($x, $y) {
        $z = $x + $y;
        return $z;
    }   

    echo "5 + 10 = " . sum(5, 10) . "<br>";
    echo "7 + 13 = " . sum(7, 13) . "<br>";
    echo "2 + 4 = " . sum(2, 4) . "<br>";
 ?>