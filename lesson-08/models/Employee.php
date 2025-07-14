<?php
/**
 * Employee Model
 * Chịu trách nhiệm xử lý dữ liệu và logic nghiệp vụ liên quan đến nhân viên
 */

require_once __DIR__ . '/../config/database.php';

class Employee {
    private $db;
    private $table = 'employees';
    
    // Thuộc tính của nhân viên
    public $id;
    public $name;
    public $email;
    public $phone;
    public $position;
    public $department;
    public $salary;
    public $hire_date;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Lấy tất cả nhân viên
    public function getAll() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }
    
    // Lấy nhân viên theo ID
    public function getById($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            $result = $stmt->fetch();
            if ($result) {
                $this->mapData($result);
                return $this;
            }
            return null;
        } catch(PDOException $e) {
            error_log("Error in getById: " . $e->getMessage());
            return null;
        }
    }
    
    // Tạo nhân viên mới
    public function create($data) {
        try {
            $query = "INSERT INTO {$this->table} 
                     (name, email, phone, position, department, salary, hire_date, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['position'],
                $data['department'],
                $data['salary'],
                $data['hire_date'],
                $data['status'] ?? 'active'
            ]);
            
            if ($result) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error in create: " . $e->getMessage());
            return false;
        }
    }
    
    // Cập nhật thông tin nhân viên
    public function update($id, $data) {
        try {
            $query = "UPDATE {$this->table} 
                     SET name = ?, email = ?, phone = ?, position = ?, 
                         department = ?, salary = ?, hire_date = ?, status = ?,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['position'],
                $data['department'],
                $data['salary'],
                $data['hire_date'],
                $data['status'],
                $id
            ]);
        } catch(PDOException $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }
    
    // Xóa nhân viên
    public function delete($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }
    
    // Tìm kiếm nhân viên
    public function search($keyword) {
        try {
            $query = "SELECT * FROM {$this->table} 
                     WHERE name LIKE ? OR email LIKE ? OR position LIKE ? OR department LIKE ?
                     ORDER BY name ASC";
            
            $searchTerm = "%{$keyword}%";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in search: " . $e->getMessage());
            return [];
        }
    }
    
    // Lấy nhân viên theo phòng ban
    public function getByDepartment($department) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE department = ? ORDER BY name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$department]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error in getByDepartment: " . $e->getMessage());
            return [];
        }
    }
    
    // Lấy danh sách phòng ban
    public function getDepartments() {
        try {
            $query = "SELECT DISTINCT department FROM {$this->table} WHERE department IS NOT NULL ORDER BY department ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            error_log("Error in getDepartments: " . $e->getMessage());
            return [];
        }
    }
    
    // Kiểm tra email có tồn tại không
    public function emailExists($email, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $query .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Error in emailExists: " . $e->getMessage());
            return false;
        }
    }
    
    // Thống kê số lượng nhân viên
    public function getStatistics() {
        try {
            $stats = [];
            
            // Tổng số nhân viên
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['total'] = $stmt->fetchColumn();
            
            // Số nhân viên đang làm việc
            $query = "SELECT COUNT(*) as active FROM {$this->table} WHERE status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['active'] = $stmt->fetchColumn();
            
            // Số nhân viên theo phòng ban
            $query = "SELECT department, COUNT(*) as count FROM {$this->table} GROUP BY department";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['by_department'] = $stmt->fetchAll();
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error in getStatistics: " . $e->getMessage());
            return [];
        }
    }
    
    // Validate dữ liệu đầu vào
    public function validate($data, $isUpdate = false) {
        $errors = [];
        
        // Kiểm tra tên
        if (empty($data['name'])) {
            $errors['name'] = 'Tên nhân viên không được để trống';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Tên nhân viên phải có ít nhất 2 ký tự';
        }
        
        // Kiểm tra email
        if (empty($data['email'])) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        } else {
            $excludeId = $isUpdate && isset($data['id']) ? $data['id'] : null;
            if ($this->emailExists($data['email'], $excludeId)) {
                $errors['email'] = 'Email đã tồn tại trong hệ thống';
            }
        }
        
        // Kiểm tra số điện thoại
        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại phải có 10-11 chữ số';
        }
        
        // Kiểm tra chức vụ
        if (empty($data['position'])) {
            $errors['position'] = 'Chức vụ không được để trống';
        }
        
        // Kiểm tra phòng ban
        if (empty($data['department'])) {
            $errors['department'] = 'Phòng ban không được để trống';
        }
        
        // Kiểm tra lương
        if (empty($data['salary'])) {
            $errors['salary'] = 'Lương không được để trống';
        } elseif (!is_numeric($data['salary']) || $data['salary'] < 0) {
            $errors['salary'] = 'Lương phải là số dương';
        }
        
        // Kiểm tra ngày vào làm
        if (empty($data['hire_date'])) {
            $errors['hire_date'] = 'Ngày vào làm không được để trống';
        } elseif (!strtotime($data['hire_date'])) {
            $errors['hire_date'] = 'Ngày vào làm không hợp lệ';
        }
        
        return $errors;
    }
    
    // Map dữ liệu từ database vào object
    private function mapData($data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->position = $data['position'];
        $this->department = $data['department'];
        $this->salary = $data['salary'];
        $this->hire_date = $data['hire_date'];
        $this->status = $data['status'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }
}
?> 