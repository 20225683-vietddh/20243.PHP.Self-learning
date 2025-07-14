/**
 * Nội dung bài học: Exception
 * try catch
 * throw
 * finally
 */

<?php
    // Ví dụ 1: try catch
    try {
        $a = 10;
        $b = 0;
        $c = $a / $b;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Ví dụ 2: throw
    function divide($a, $b) {
        if ($b == 0) {
            throw new Exception("Cannot divide by zero");
        }
        return $a / $b;
    }

    try {
        echo divide(10, 0);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Ví dụ 3: finally 
    try {
        echo divide(10, 0);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        echo "Finally block executed";
    }

    // Ví dụ 4: set exception handler
    set_exception_handler("handleException");
    throw new Exception("This is an exception");

    function handleException($exception) {
        echo "Error: " . $exception->getMessage();
    }

    // Ví dụ 5: set error handler
    set_error_handler("handleError");
    echo $a;
?>