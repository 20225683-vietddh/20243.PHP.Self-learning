/**
 * Nội dung bài học: Overloading & Overriding
 * Abstraction class & Interface
 */

<?php
    // Ví dụ 1: Overloading & Overriding
    class Person {
        private $name;
        private $age;
        private $email;

        public function __construct($name, $age, $email) {
            $this->name = $name;
            $this->age = $age;
            $this->email = $email;
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }
    }

    $person = new Person("John", 20, "john@example.com");
    echo $person->getName();
    $person->setName("Jane");
    echo $person->getName();

    // Ví dụ 2: Abstraction class & Interface
    abstract class Person {
        abstract public function getName();
    }

    class Employee extends Person {
        public function getName() {
            return "John";
        }
    }

    $employee = new Employee();
    echo $employee->getName();

    // Ví dụ 3: Interface
    interface Person {
        public function getName();
    }

    class Employee implements Person {
        public function getName() {
            return "John";
        }
    }

    $employee = new Employee();
    echo $employee->getName();
?>