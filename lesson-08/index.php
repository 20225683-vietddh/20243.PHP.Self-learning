<?php
/**
 * Entry Point của ứng dụng MVC - Quản lý nhân viên
 * File này sẽ khởi tạo controller và xử lý tất cả các request
 */

// Bắt đầu session
session_start();

// Bật hiển thị lỗi để debug (chỉ trong môi trường development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require controller chính
require_once __DIR__ . '/controllers/EmployeeController.php';

try {
    // Khởi tạo controller
    $controller = new EmployeeController();
    
    // Xử lý request
    $controller->handleRequest();
    
} catch (Exception $e) {
    // Xử lý lỗi toàn cục
    $errorMessage = "Có lỗi xảy ra: " . $e->getMessage();
    
    // Log lỗi
    error_log($errorMessage);
    
    // Hiển thị trang lỗi đơn giản
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lỗi hệ thống - Quản lý nhân viên</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Lỗi hệ thống
                            </h4>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-bug fa-3x text-danger mb-3"></i>
                            <h5>Có lỗi xảy ra trong hệ thống</h5>
                            <p class="text-muted mb-4">
                                Xin lỗi, đã có lỗi không mong muốn xảy ra. Vui lòng thử lại sau.
                            </p>
                            
                            <?php if (ini_get('display_errors')): ?>
                            <div class="alert alert-warning text-start">
                                <strong>Chi tiết lỗi (chỉ hiển thị trong development):</strong><br>
                                <code><?= htmlspecialchars($errorMessage) ?></code>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-grid gap-2 d-md-block">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-1"></i>
                                    Về trang chủ
                                </a>
                                <button onclick="window.history.back()" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay lại
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thông tin hỗ trợ -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-1"></i>
                                Thông tin hệ thống
                            </h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <strong>Phiên bản PHP:</strong> <?= PHP_VERSION ?>
                                    </small>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <strong>Thời gian:</strong> <?= date('d/m/Y H:i:s') ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

<!-- 
    =====================================================
    HƯỚNG DẪN SỬ DỤNG HỆ THỐNG MVC
    =====================================================
    
    Đây là ví dụ hoàn chỉnh về mô hình MVC trong PHP
    
    CẤU TRÚC THỰ MỤC:
    lesson-08/
    ├── index.php                 (Entry point - file này)
    ├── config/
    │   └── database.php          (Cấu hình database)
    ├── models/
    │   └── Employee.php          (Model xử lý dữ liệu)
    ├── views/
    │   ├── layout/
    │   │   ├── header.php        (Layout header)
    │   │   └── footer.php        (Layout footer)
    │   └── employees/
    │       ├── index.php         (Danh sách nhân viên)
    │       ├── create.php        (Form thêm mới)
    │       ├── edit.php          (Form chỉnh sửa)
    │       ├── show.php          (Chi tiết nhân viên)
    │       ├── search.php        (Tìm kiếm)
    │       └── statistics.php    (Thống kê)
    └── controllers/
        └── EmployeeController.php (Controller xử lý logic)
    
    CÁC TÍNH NĂNG:
    - CRUD operations (Create, Read, Update, Delete)
    - Tìm kiếm và lọc dữ liệu
    - Pagination
    - Validation dữ liệu
    - Error handling
    - Responsive design với Bootstrap
    - Ajax live search
    
    ROUTING:
    - ?action=index          : Danh sách nhân viên
    - ?action=create         : Form thêm mới
    - ?action=store          : Xử lý thêm mới
    - ?action=show&id=1      : Chi tiết nhân viên
    - ?action=edit&id=1      : Form chỉnh sửa
    - ?action=update&id=1    : Xử lý cập nhật
    - ?action=delete&id=1    : Xóa nhân viên
    - ?action=search         : Tìm kiếm
    - ?action=statistics     : Thống kê
    
    DATABASE SETUP:
    Chạy SQL sau để tạo database và bảng:
    
    CREATE DATABASE employee_management;
    USE employee_management;
    
    CREATE TABLE employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        phone VARCHAR(15) NOT NULL,
        position VARCHAR(255) NOT NULL,
        department VARCHAR(255) NOT NULL,
        salary DECIMAL(15,2) NOT NULL,
        hire_date DATE NOT NULL,
        status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    
    -- Dữ liệu mẫu
    INSERT INTO employees (name, email, phone, position, department, salary, hire_date) VALUES
    ('Nguyễn Văn An', 'an.nguyen@company.com', '0123456789', 'Lập trình viên', 'IT', 15000000, '2024-01-15'),
    ('Trần Thị Bình', 'binh.tran@company.com', '0987654321', 'Kế toán trưởng', 'Kế toán', 18000000, '2023-06-10'),
    ('Lê Văn Cường', 'cuong.le@company.com', '0369852147', 'Nhân viên kinh doanh', 'Kinh doanh', 12000000, '2024-03-20'),
    ('Phạm Thị Dung', 'dung.pham@company.com', '0147258369', 'Chuyên viên nhân sự', 'Nhân sự', 14000000, '2023-11-05'),
    ('Hoàng Văn Em', 'em.hoang@company.com', '0258147963', 'Marketing Manager', 'Marketing', 20000000, '2023-08-12');
-->

<?php
// Demo thông tin về MVC pattern
if (isset($_GET['info']) && $_GET['info'] === 'mvc') {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thông tin về MVC Pattern</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h2 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Mô hình MVC (Model-View-Controller)
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card h-100 border-success">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-database me-1"></i>
                                                Model
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Chức năng:</strong></p>
                                            <ul>
                                                <li>Xử lý dữ liệu và logic nghiệp vụ</li>
                                                <li>Tương tác với cơ sở dữ liệu</li>
                                                <li>Validate dữ liệu</li>
                                                <li>Không biết gì về View và Controller</li>
                                            </ul>
                                            <p><strong>Trong ví dụ này:</strong></p>
                                            <code>models/Employee.php</code>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card h-100 border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h5 class="mb-0">
                                                <i class="fas fa-eye me-1"></i>
                                                View
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Chức năng:</strong></p>
                                            <ul>
                                                <li>Hiển thị dữ liệu cho người dùng</li>
                                                <li>Render giao diện HTML</li>
                                                <li>Nhận input từ người dùng</li>
                                                <li>Không chứa logic nghiệp vụ</li>
                                            </ul>
                                            <p><strong>Trong ví dụ này:</strong></p>
                                            <code>views/employees/*.php</code>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card h-100 border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-cogs me-1"></i>
                                                Controller
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Chức năng:</strong></p>
                                            <ul>
                                                <li>Xử lý request từ người dùng</li>
                                                <li>Điều phối giữa Model và View</li>
                                                <li>Chứa logic điều khiển</li>
                                                <li>Quyết định View nào sẽ hiển thị</li>
                                            </ul>
                                            <p><strong>Trong ví dụ này:</strong></p>
                                            <code>controllers/EmployeeController.php</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="index.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-play me-1"></i>
                                    Chạy ứng dụng Demo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?> 