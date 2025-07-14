/**
 * Nội dung bài học: OOP trong PHP
 * Class
 * Object
 * Inheritance
 * Polymorphism
 * Encapsulation
 * Abstraction
 */

<?php
    // Ví dụ 1: Class
    class Person {
        private $name;
        private $age;
        private $email;

        public function __construct($name, $age, $email) {
            $this->name = $name;
            $this->age = $age;
            $this->email = $email;
        }
    }

    // Ví dụ 2: Object
    $person = new Person("John", 20, "john@example.com");
    echo $person->name;

    // Ví dụ 3: Inheritance
    class Employee extends Person {
        private $salary;

        public function __construct($name, $age, $email, $salary) {
            parent::__construct($name, $age, $email);
            $this->salary = $salary;
        }
    }

    $employee = new Employee("John", 20, "john@example.com", 1000);
    echo $employee->name;

    // Ví dụ 4: Polymorphism
    class Animal {
        public function makeSound() {
            echo "Animal makes sound";
        }
    }

    class Dog extends Animal {
        public function makeSound() {
            echo "Dog makes sound";
        }
    }

    $animal = new Animal();
    $animal->makeSound();

    $dog = new Dog();
    $dog->makeSound();
?>