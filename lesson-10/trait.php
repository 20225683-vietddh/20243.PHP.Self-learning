<?php
/**
 * Nội dung bài học: Traits trong PHP
 * - Trait cơ bản
 * - Multiple traits
 * - Trait conflicts
 * - Abstract methods trong traits
 * - Properties và static methods
 */

echo "<h1>CÁC VÍ DỤ VỀ TRAITS TRONG PHP</h1>";

// =============================================================================
// VÍ DỤ 1: TRAIT CƠ BẢN
// =============================================================================
echo "<h2>Ví dụ 1: Trait cơ bản - Chia sẻ code giữa các class</h2>";

trait Timestampable {
    protected $created_at;
    protected $updated_at;
    
    public function setCreatedAt() {
        $this->created_at = date('Y-m-d H:i:s');
    }
    
    public function setUpdatedAt() {
        $this->updated_at = date('Y-m-d H:i:s');
    }
    
    public function getCreatedAt() {
        return $this->created_at ?? 'Chưa thiết lập';
    }
    
    public function getUpdatedAt() {
        return $this->updated_at ?? 'Chưa cập nhật';
    }
    
    public function touch() {
        $this->setUpdatedAt();
        if (!$this->created_at) {
            $this->setCreatedAt();
        }
    }
}

class User {
    use Timestampable;
    
    private $name;
    private $email;
    
    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
        $this->setCreatedAt();
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function updateProfile($name, $email) {
        $this->name = $name;
        $this->email = $email;
        $this->touch();
    }
    
    public function getInfo() {
        return "User: {$this->name} ({$this->email}) - Created: {$this->getCreatedAt()}, Updated: {$this->getUpdatedAt()}";
    }
}

class Post {
    use Timestampable;
    
    private $title;
    private $content;
    
    public function __construct($title, $content) {
        $this->title = $title;
        $this->content = $content;
        $this->setCreatedAt();
    }
    
    public function updateContent($content) {
        $this->content = $content;
        $this->touch();
    }
    
    public function getInfo() {
        return "Post: {$this->title} - Created: {$this->getCreatedAt()}, Updated: {$this->getUpdatedAt()}";
    }
}

// Sử dụng trait
$user = new User("Nguyễn Văn A", "a@example.com");
echo $user->getInfo() . "<br>";

sleep(1); // Để thấy sự khác biệt về thời gian

$user->updateProfile("Nguyễn Văn A (Updated)", "a.updated@example.com");
echo $user->getInfo() . "<br>";

$post = new Post("Bài viết về PHP Traits", "Nội dung bài viết...");
echo $post->getInfo() . "<br>";

// =============================================================================
// VÍ DỤ 2: MULTIPLE TRAITS
// =============================================================================
echo "<h2>Ví dụ 2: Sử dụng nhiều traits trong một class</h2>";

trait Loggable {
    protected $logs = [];
    
    public function log($message) {
        $this->logs[] = [
            'time' => date('H:i:s'),
            'message' => $message
        ];
    }
    
    public function getLogs() {
        return $this->logs;
    }
    
    public function getLastLog() {
        return end($this->logs);
    }
}

trait Cacheable {
    protected $cache = [];
    
    public function cache($key, $value) {
        $this->cache[$key] = $value;
        return $value;
    }
    
    public function getFromCache($key) {
        return $this->cache[$key] ?? null;
    }
    
    public function clearCache() {
        $this->cache = [];
    }
    
    public function getCacheSize() {
        return count($this->cache);
    }
}

trait Validatable {
    protected $errors = [];
    
    public function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function clearErrors() {
        $this->errors = [];
    }
}

class Product {
    use Timestampable, Loggable, Cacheable, Validatable;
    
    private $name;
    private $price;
    private $stock;
    
    public function __construct($name, $price, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->setCreatedAt();
        $this->log("Product created: {$name}");
    }
    
    public function setPrice($price) {
        if ($price < 0) {
            $this->addError('price', 'Giá không thể âm');
            return false;
        }
        
        $oldPrice = $this->price;
        $this->price = $price;
        $this->touch();
        $this->log("Price changed from {$oldPrice} to {$price}");
        
        // Cache lại giá cũ
        $this->cache('previous_price', $oldPrice);
        
        return true;
    }
    
    public function updateStock($quantity) {
        if ($quantity < 0) {
            $this->addError('stock', 'Số lượng không thể âm');
            return false;
        }
        
        $this->stock = $quantity;
        $this->touch();
        $this->log("Stock updated to {$quantity}");
        return true;
    }
    
    public function getInfo() {
        $info = "Product: {$this->name} - Price: " . number_format($this->price) . " VND - Stock: {$this->stock}";
        $info .= " | Logs: " . count($this->getLogs()) . " | Cache: " . $this->getCacheSize() . " items";
        
        if ($this->hasErrors()) {
            $info .= " | Errors: " . count($this->getErrors());
        }
        
        return $info;
    }
}

$product = new Product("Laptop Gaming", 25000000, 10);
echo $product->getInfo() . "<br>";

$product->setPrice(23000000);
echo $product->getInfo() . "<br>";

$product->setPrice(-100); // Sẽ tạo lỗi
echo $product->getInfo() . "<br>";

echo "Previous price from cache: " . number_format($product->getFromCache('previous_price')) . " VND<br>";
echo "Last log: " . print_r($product->getLastLog(), true) . "<br>";

if ($product->hasErrors()) {
    echo "Errors: " . print_r($product->getErrors(), true) . "<br>";
}

// =============================================================================
// VÍ DỤ 3: TRAIT CONFLICTS VÀ GIẢI QUYẾT
// =============================================================================
echo "<h2>Ví dụ 3: Xử lý xung đột khi nhiều traits có cùng method</h2>";

trait FileLogger {
    public function log($message) {
        return "[FILE] " . date('Y-m-d H:i:s') . " - " . $message;
    }
    
    public function getLogType() {
        return "File Logger";
    }
}

trait DatabaseLogger {
    public function log($message) {
        return "[DATABASE] " . date('Y-m-d H:i:s') . " - " . $message;
    }
    
    public function getLogType() {
        return "Database Logger";
    }
}

trait EmailNotifier {
    public function notify($message) {
        return "[EMAIL] Sending notification: " . $message;
    }
}

class OrderService {
    use FileLogger, DatabaseLogger, EmailNotifier {
        // Giải quyết xung đột: sử dụng DatabaseLogger::log thay cho FileLogger::log
        DatabaseLogger::log insteadof FileLogger;
        
        // Tạo alias cho FileLogger::log
        FileLogger::log as fileLog;
        
        // Tạo alias cho getLogType
        FileLogger::getLogType as getFileLogType;
        DatabaseLogger::getLogType as getDatabaseLogType;
    }
    
    private $orders = [];
    
    public function createOrder($customerName, $amount) {
        $orderId = uniqid();
        $this->orders[$orderId] = [
            'customer' => $customerName,
            'amount' => $amount,
            'status' => 'pending'
        ];
        
        // Sử dụng DatabaseLogger::log (mặc định)
        $logMessage = $this->log("Order {$orderId} created for {$customerName}");
        echo $logMessage . "<br>";
        
        // Sử dụng FileLogger::log thông qua alias
        $fileLogMessage = $this->fileLog("Order {$orderId} logged to file");
        echo $fileLogMessage . "<br>";
        
        // Gửi thông báo
        $notification = $this->notify("New order {$orderId} for " . number_format($amount) . " VND");
        echo $notification . "<br>";
        
        return $orderId;
    }
    
    public function getLoggerInfo() {
        echo "Active logger: " . $this->getLogType() . "<br>";
        echo "File logger: " . $this->getFileLogType() . "<br>";
        echo "Database logger: " . $this->getDatabaseLogType() . "<br>";
    }
}

$orderService = new OrderService();
$orderService->getLoggerInfo();
echo "<br>";

$orderId = $orderService->createOrder("Trần Thị B", 1500000);
echo "Order created with ID: {$orderId}<br>";

// =============================================================================
// VÍ DỤ 4: ABSTRACT METHODS TRONG TRAITS
// =============================================================================
echo "<h2>Ví dụ 4: Abstract methods trong traits - Bắt buộc class implement</h2>";

trait Serializable {
    // Abstract method - class sử dụng trait phải implement
    abstract protected function getSerializableData();
    
    public function serialize() {
        $data = $this->getSerializableData();
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    
    public function toArray() {
        return $this->getSerializableData();
    }
    
    public function toXml() {
        $data = $this->getSerializableData();
        $xml = new SimpleXMLElement('<root/>');
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $xml->addChild($key);
                foreach ($value as $subKey => $subValue) {
                    $child->addChild($subKey, htmlspecialchars($subValue));
                }
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
        
        return $xml->asXML();
    }
}

class Customer {
    use Serializable;
    
    private $id;
    private $name;
    private $email;
    private $phone;
    private $address;
    
    public function __construct($id, $name, $email, $phone, $address) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
    }
    
    // Bắt buộc phải implement method này
    protected function getSerializableData() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => [
                'street' => $this->address['street'] ?? '',
                'city' => $this->address['city'] ?? '',
                'country' => $this->address['country'] ?? ''
            ]
        ];
    }
    
    public function getName() {
        return $this->name;
    }
}

class Invoice {
    use Serializable;
    
    private $invoiceNumber;
    private $customer;
    private $items;
    private $total;
    
    public function __construct($invoiceNumber, Customer $customer) {
        $this->invoiceNumber = $invoiceNumber;
        $this->customer = $customer;
        $this->items = [];
        $this->total = 0;
    }
    
    public function addItem($description, $quantity, $price) {
        $this->items[] = [
            'description' => $description,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $quantity * $price
        ];
        $this->total += $quantity * $price;
    }
    
    // Implement abstract method từ trait
    protected function getSerializableData() {
        return [
            'invoice_number' => $this->invoiceNumber,
            'customer' => $this->customer->toArray(),
            'items' => $this->items,
            'total' => $this->total,
            'date' => date('Y-m-d H:i:s')
        ];
    }
}

// Tạo customer và invoice
$customer = new Customer(
    1, 
    "Phạm Văn C", 
    "c@example.com", 
    "0123456789",
    ['street' => '123 Main St', 'city' => 'Hanoi', 'country' => 'Vietnam']
);

$invoice = new Invoice("INV-2024-001", $customer);
$invoice->addItem("Laptop Dell XPS", 1, 25000000);
$invoice->addItem("Chuột wireless", 2, 500000);

echo "<h3>Customer JSON:</h3>";
echo "<pre>" . $customer->serialize() . "</pre>";

echo "<h3>Invoice JSON:</h3>";
echo "<pre>" . $invoice->serialize() . "</pre>";

echo "<h3>Invoice XML:</h3>";
echo "<pre>" . htmlspecialchars($invoice->toXml()) . "</pre>";

// =============================================================================
// VÍ DỤ 5: PROPERTIES VÀ STATIC METHODS TRONG TRAITS
// =============================================================================
echo "<h2>Ví dụ 5: Properties và static methods trong traits</h2>";

trait Singleton {
    private static $instances = [];
    
    // Property trong trait
    protected $instanceId;
    
    // Static method trong trait
    public static function getInstance() {
        $class = static::class;
        
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        
        return self::$instances[$class];
    }
    
    public static function hasInstance() {
        return isset(self::$instances[static::class]);
    }
    
    public static function clearInstance() {
        $class = static::class;
        if (isset(self::$instances[$class])) {
            unset(self::$instances[$class]);
        }
    }
    
    protected function __construct() {
        $this->instanceId = uniqid();
    }
    
    // Ngăn clone và unserialize
    private function __clone() {}
    private function __wakeup() {}
    
    public function getInstanceId() {
        return $this->instanceId;
    }
}

trait Counter {
    private static $globalCount = 0;
    protected $instanceCount = 0;
    
    protected function incrementGlobalCount() {
        self::$globalCount++;
    }
    
    protected function incrementInstanceCount() {
        $this->instanceCount++;
    }
    
    public static function getGlobalCount() {
        return self::$globalCount;
    }
    
    public function getInstanceCount() {
        return $this->instanceCount;
    }
    
    public function doSomething() {
        $this->incrementGlobalCount();
        $this->incrementInstanceCount();
        return "Action performed. Global: " . self::$globalCount . ", Instance: " . $this->instanceCount;
    }
}

class ConfigManager {
    use Singleton, Counter;
    
    private $config = [];
    
    public function set($key, $value) {
        $this->config[$key] = $value;
        $this->doSomething();
        return $this;
    }
    
    public function get($key) {
        $this->doSomething();
        return $this->config[$key] ?? null;
    }
    
    public function getAll() {
        return $this->config;
    }
    
    public function getInfo() {
        return [
            'instance_id' => $this->getInstanceId(),
            'config_count' => count($this->config),
            'global_actions' => self::getGlobalCount(),
            'instance_actions' => $this->getInstanceCount()
        ];
    }
}

class DatabaseManager {
    use Singleton, Counter;
    
    private $connections = [];
    
    public function connect($name, $host) {
        $this->connections[$name] = "Connected to {$host}";
        $this->doSomething();
        return $this;
    }
    
    public function getConnection($name) {
        $this->doSomething();
        return $this->connections[$name] ?? null;
    }
    
    public function getInfo() {
        return [
            'instance_id' => $this->getInstanceId(),
            'connections' => count($this->connections),
            'global_actions' => self::getGlobalCount(),
            'instance_actions' => $this->getInstanceCount()
        ];
    }
}

// Test Singleton pattern
echo "<h3>Test Singleton Pattern:</h3>";

$config1 = ConfigManager::getInstance();
$config2 = ConfigManager::getInstance();

echo "Config1 ID: " . $config1->getInstanceId() . "<br>";
echo "Config2 ID: " . $config2->getInstanceId() . "<br>";
echo "Same instance: " . ($config1 === $config2 ? 'Yes' : 'No') . "<br>";

// Test Counter trait
echo "<h3>Test Counter Trait:</h3>";

$config1->set('app_name', 'My App')->set('debug', true);
$db = DatabaseManager::getInstance();
$db->connect('main', 'localhost')->connect('cache', 'redis-server');

echo "Config info: " . print_r($config1->getInfo(), true) . "<br>";
echo "DB info: " . print_r($db->getInfo(), true) . "<br>";

echo "Global count (shared): " . ConfigManager::getGlobalCount() . "<br>";
?>
