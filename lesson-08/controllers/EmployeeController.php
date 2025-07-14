<?php
/**
 * Employee Controller
 * Chịu trách nhiệm xử lý logic nghiệp vụ và điều phối giữa Model và View
 */

require_once __DIR__ . '/../models/Employee.php';

class EmployeeController {
    private $employeeModel;
    private $errors = [];
    private $success = '';
    
    public function __construct() {
        $this->employeeModel = new Employee();
    }
    
    // Xử lý các action từ URL
    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                $this->index();
                break;
            case 'show':
                $this->show();
                break;
            case 'create':
                $this->create();
                break;
            case 'store':
                $this->store();
                break;
            case 'edit':
                $this->edit();
                break;
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'search':
                $this->search();
                break;
            case 'statistics':
                $this->statistics();
                break;
            default:
                $this->index();
                break;
        }
    }
    
    // Hiển thị danh sách tất cả nhân viên
    private function index() {
        try {
            $employees = $this->employeeModel->getAll();
            $departments = $this->employeeModel->getDepartments();
            
            // Filter theo phòng ban nếu có
            $filterDepartment = $_GET['department'] ?? '';
            if (!empty($filterDepartment)) {
                $employees = $this->employeeModel->getByDepartment($filterDepartment);
            }
            
            // Pagination
            $page = $_GET['page'] ?? 1;
            $perPage = 10;
            $totalEmployees = count($employees);
            $totalPages = ceil($totalEmployees / $perPage);
            $offset = ($page - 1) * $perPage;
            $employees = array_slice($employees, $offset, $perPage);
            
            $data = [
                'employees' => $employees,
                'departments' => $departments,
                'filterDepartment' => $filterDepartment,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalEmployees' => $totalEmployees
            ];
            
            $this->loadView('employees/index', $data);
        } catch (Exception $e) {
            $this->errors[] = 'Có lỗi xảy ra khi tải danh sách nhân viên: ' . $e->getMessage();
            $this->loadView('employees/index', ['employees' => []]);
        }
    }
    
    // Hiển thị chi tiết nhân viên
    private function show() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id || !is_numeric($id)) {
            $this->errors[] = 'ID nhân viên không hợp lệ';
            header('Location: index.php');
            exit;
        }
        
        $employee = $this->employeeModel->getById($id);
        
        if (!$employee) {
            $this->errors[] = 'Không tìm thấy nhân viên';
            header('Location: index.php');
            exit;
        }
        
        $this->loadView('employees/show', ['employee' => $employee]);
    }
    
    // Hiển thị form tạo nhân viên mới
    private function create() {
        $departments = $this->employeeModel->getDepartments();
        $this->loadView('employees/create', ['departments' => $departments]);
    }
    
    // Xử lý lưu nhân viên mới
    private function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=create');
            exit;
        }
        
        $data = $this->sanitizeInput($_POST);
        
        // Validate dữ liệu
        $errors = $this->employeeModel->validate($data);
        
        if (empty($errors)) {
            if ($this->employeeModel->create($data)) {
                $this->success = 'Thêm nhân viên thành công!';
                header('Location: index.php?success=' . urlencode($this->success));
                exit;
            } else {
                $this->errors[] = 'Có lỗi xảy ra khi thêm nhân viên';
            }
        } else {
            $this->errors = array_values($errors);
        }
        
        $departments = $this->employeeModel->getDepartments();
        $this->loadView('employees/create', [
            'departments' => $departments,
            'formData' => $data,
            'errors' => $this->errors
        ]);
    }
    
    // Hiển thị form sửa nhân viên
    private function edit() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id || !is_numeric($id)) {
            $this->errors[] = 'ID nhân viên không hợp lệ';
            header('Location: index.php');
            exit;
        }
        
        $employee = $this->employeeModel->getById($id);
        
        if (!$employee) {
            $this->errors[] = 'Không tìm thấy nhân viên';
            header('Location: index.php');
            exit;
        }
        
        $departments = $this->employeeModel->getDepartments();
        $this->loadView('employees/edit', [
            'employee' => $employee,
            'departments' => $departments
        ]);
    }
    
    // Xử lý cập nhật thông tin nhân viên
    private function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        $id = $_GET['id'] ?? 0;
        
        if (!$id || !is_numeric($id)) {
            $this->errors[] = 'ID nhân viên không hợp lệ';
            header('Location: index.php');
            exit;
        }
        
        $data = $this->sanitizeInput($_POST);
        $data['id'] = $id;
        
        // Validate dữ liệu
        $errors = $this->employeeModel->validate($data, true);
        
        if (empty($errors)) {
            if ($this->employeeModel->update($id, $data)) {
                $this->success = 'Cập nhật thông tin nhân viên thành công!';
                header('Location: index.php?success=' . urlencode($this->success));
                exit;
            } else {
                $this->errors[] = 'Có lỗi xảy ra khi cập nhật thông tin nhân viên';
            }
        } else {
            $this->errors = array_values($errors);
        }
        
        $employee = $this->employeeModel->getById($id);
        $departments = $this->employeeModel->getDepartments();
        
        $this->loadView('employees/edit', [
            'employee' => $employee,
            'departments' => $departments,
            'formData' => $data,
            'errors' => $this->errors
        ]);
    }
    
    // Xóa nhân viên
    private function delete() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id || !is_numeric($id)) {
            $this->errors[] = 'ID nhân viên không hợp lệ';
            header('Location: index.php');
            exit;
        }
        
        // Kiểm tra nhân viên có tồn tại không
        $employee = $this->employeeModel->getById($id);
        if (!$employee) {
            $this->errors[] = 'Không tìm thấy nhân viên';
            header('Location: index.php');
            exit;
        }
        
        if ($this->employeeModel->delete($id)) {
            $this->success = 'Xóa nhân viên thành công!';
            header('Location: index.php?success=' . urlencode($this->success));
        } else {
            $this->errors[] = 'Có lỗi xảy ra khi xóa nhân viên';
            header('Location: index.php?error=' . urlencode(implode(', ', $this->errors)));
        }
        exit;
    }
    
    // Tìm kiếm nhân viên
    private function search() {
        $keyword = $_GET['keyword'] ?? '';
        $employees = [];
        
        if (!empty($keyword)) {
            $employees = $this->employeeModel->search($keyword);
        }
        
        $departments = $this->employeeModel->getDepartments();
        
        $this->loadView('employees/search', [
            'employees' => $employees,
            'keyword' => $keyword,
            'departments' => $departments
        ]);
    }
    
    // Hiển thị thống kê
    private function statistics() {
        $stats = $this->employeeModel->getStatistics();
        $this->loadView('employees/statistics', ['stats' => $stats]);
    }
    
    // Load view với dữ liệu
    private function loadView($view, $data = []) {
        // Extract dữ liệu thành biến
        extract($data);
        
        // Thêm thông báo từ URL nếu có
        $success = $_GET['success'] ?? '';
        $error = $_GET['error'] ?? '';
        
        // Load header
        include __DIR__ . '/../views/layout/header.php';
        
        // Load view chính
        include __DIR__ . "/../views/{$view}.php";
        
        // Load footer
        include __DIR__ . '/../views/layout/footer.php';
    }
    
    // Làm sạch dữ liệu đầu vào
    private function sanitizeInput($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
    
    // Format số tiền
    public static function formatCurrency($amount) {
        return number_format($amount, 0, ',', '.') . ' VND';
    }
    
    // Format ngày tháng
    public static function formatDate($date) {
        return date('d/m/Y', strtotime($date));
    }
    
    // Lấy màu sắc cho status
    public static function getStatusColor($status) {
        switch ($status) {
            case 'active':
                return 'success';
            case 'inactive':
                return 'danger';
            case 'on_leave':
                return 'warning';
            default:
                return 'secondary';
        }
    }
    
    // Lấy text cho status
    public static function getStatusText($status) {
        switch ($status) {
            case 'active':
                return 'Đang làm việc';
            case 'inactive':
                return 'Đã nghỉ việc';
            case 'on_leave':
                return 'Đang nghỉ phép';
            default:
                return 'Không xác định';
        }
    }
}
?> 