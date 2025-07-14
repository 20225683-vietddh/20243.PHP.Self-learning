<?php
/**
 * Nội dung bài học: Namespaces trong PHP
 * - Namespace cơ bản
 * - Use statement
 * - Alias
 * - Nested namespaces
 * - Global namespace
 * - Autoloading
 */

echo "<h1>CÁC VÍ DỤ VỀ NAMESPACES TRONG PHP</h1>";

// =============================================================================
// VÍ DỤ 1: NAMESPACE CƠ BẢN
// =============================================================================
echo "<h2>Ví dụ 1: Namespace cơ bản</h2>";

namespace App\Models;

class User {
    public $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getInfo() {
        return "User: " . $this->name . " trong namespace App\Models";
    }
}

class Product {
    public $title;
    public $price;
    
    public function __construct($title, $price) {
        $this->title = $title;
        $this->price = $price;
    }
    
    public function getInfo() {
        return "Product: " . $this->title . " - Price: " . number_format($this->price) . " VND";
    }
}

// Sử dụng class trong cùng namespace
$user1 = new User("Nguyễn Văn A");
echo $user1->getInfo() . "<br>";

$product1 = new Product("Laptop Dell", 15000000);
echo $product1->getInfo() . "<br>";

// =============================================================================
// VÍ DỤ 2: NAMESPACE KHÁC NHAU
// =============================================================================
echo "<h2>Ví dụ 2: Classes cùng tên trong namespace khác nhau</h2>";

namespace App\Controllers;

class User {
    public $role;
    
    public function __construct($role) {
        $this->role = $role;
    }
    
    public function getInfo() {
        return "User Controller với role: " . $this->role . " trong namespace App\Controllers";
    }
}

// Sử dụng class User từ namespace Controllers
$userController = new User("admin");
echo $userController->getInfo() . "<br>";

// Sử dụng class User từ namespace Models (cần fully qualified name)
$userModel = new \App\Models\User("Trần Thị B");
echo $userModel->getInfo() . "<br>";

// =============================================================================
// VÍ DỤ 3: USE STATEMENT
// =============================================================================
echo "<h2>Ví dụ 3: Use statement</h2>";

namespace App\Services;

// Import class từ namespace khác
use App\Models\User as ModelUser;
use App\Models\Product;
use App\Controllers\User as ControllerUser;

class UserService {
    public function createUser($name) {
        $user = new ModelUser($name);
        echo "Tạo user mới: " . $user->getInfo() . "<br>";
        return $user;
    }
    
    public function getUserController($role) {
        $controller = new ControllerUser($role);
        echo "Khởi tạo controller: " . $controller->getInfo() . "<br>";
        return $controller;
    }
    
    public function createProduct($title, $price) {
        $product = new Product($title, $price);
        echo "Tạo sản phẩm: " . $product->getInfo() . "<br>";
        return $product;
    }
}

$service = new UserService();
$service->createUser("Lê Văn C");
$service->getUserController("editor");
$service->createProduct("iPhone 15", 25000000);

// =============================================================================
// VÍ DỤ 4: NESTED NAMESPACES VÀ SUB-NAMESPACES
// =============================================================================
echo "<h2>Ví dụ 4: Nested namespaces</h2>";

namespace App\Database\Connections;

class MySQLConnection {
    private $host;
    private $database;
    
    public function __construct($host, $database) {
        $this->host = $host;
        $this->database = $database;
    }
    
    public function connect() {
        return "Connected to MySQL: {$this->host}/{$this->database}";
    }
}

namespace App\Database\Queries;

class QueryBuilder {
    private $table;
    private $conditions = [];
    
    public function table($table) {
        $this->table = $table;
        return $this;
    }
    
    public function where($column, $value) {
        $this->conditions[] = "$column = '$value'";
        return $this;
    }
    
    public function build() {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(" AND ", $this->conditions);
        }
        return $sql;
    }
}

// Sử dụng nested namespaces
use App\Database\Connections\MySQLConnection;
use App\Database\Queries\QueryBuilder;

$connection = new MySQLConnection("localhost", "my_database");
echo $connection->connect() . "<br>";

$query = new QueryBuilder();
$sql = $query->table("users")->where("status", "active")->where("role", "admin")->build();
echo "SQL Query: " . $sql . "<br>";
?>