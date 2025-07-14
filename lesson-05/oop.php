/**
 * Constructor & Destructor
 * Getter & Setter
 * Static
 * Abstract
 * Interface
 * Trait
 */

<?php
    // Ví dụ 1: Constructor & Destructor
    class Person {
        private $name;
        private $age;
        private $email;

        public function __construct($name, $age, $email) {
            $this->name = $name;
            $this->age = $age;
            $this->email = $email;
        }

        public function __destruct() {
            echo "Destructor called";
        }
    }

    $person = new Person("John", 20, "john@example.com");
    echo $person->name;

    // Ví dụ 2: Getter & Setter
    class Person {
        private $name;
        private $age;
        private $email;

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getAge() {
            return $this->age;
        }

        public function setAge($age) {
            $this->age = $age;
        }

        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email) {
            $this->email = $email;
        }
    }

    $person = new Person();
    $person->setName("John");
    echo $person->getName();

    // Ví dụ 3: Static
    class Person {
        public static $count = 0;

        public function __construct() {
            self::$count++;
        }
    }

    $person = new Person();
    echo Person::$count;

    // Ví dụ 4: Abstract
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

    // Ví dụ 5: Interface
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

    // Ví dụ 6: Trait
    trait Person {
        public function getName() {
            return "John";
        }
    }

    class Employee {
        use Person;
    }

    $employee = new Employee();
    echo $employee->getName();
?>