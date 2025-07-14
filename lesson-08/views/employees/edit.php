<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Chỉnh sửa thông tin nhân viên
                </h4>
            </div>
            <div class="card-body">
                <?php if (isset($employee)): ?>
                <form method="POST" action="index.php?action=update&id=<?= $employee->id ?>" id="employee-form">
                    <div class="row">
                        <!-- Thông tin cá nhân -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-1"></i>
                                Thông tin cá nhân
                            </h5>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= htmlspecialchars($formData['name'] ?? $employee->name) ?>"
                                       required
                                       placeholder="Nhập họ và tên đầy đủ">
                                <div class="invalid-feedback">
                                    Vui lòng nhập họ và tên
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($formData['email'] ?? $employee->email) ?>"
                                       required
                                       placeholder="example@company.com">
                                <div class="invalid-feedback">
                                    Vui lòng nhập email hợp lệ
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?= htmlspecialchars($formData['phone'] ?? $employee->phone) ?>"
                                       required
                                       placeholder="0123456789"
                                       maxlength="11"
                                       oninput="formatPhone(this)">
                                <div class="invalid-feedback">
                                    Vui lòng nhập số điện thoại (10-11 chữ số)
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="hire_date" class="form-label">
                                    Ngày vào làm <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="hire_date" 
                                       name="hire_date" 
                                       value="<?= htmlspecialchars($formData['hire_date'] ?? $employee->hire_date) ?>"
                                       required
                                       max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">
                                    Vui lòng chọn ngày vào làm
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin công việc -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-briefcase me-1"></i>
                                Thông tin công việc
                            </h5>

                            <div class="mb-3">
                                <label for="position" class="form-label">
                                    Chức vụ <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="position" 
                                       name="position" 
                                       value="<?= htmlspecialchars($formData['position'] ?? $employee->position) ?>"
                                       required
                                       placeholder="Ví dụ: Lập trình viên, Kế toán...">
                                <div class="invalid-feedback">
                                    Vui lòng nhập chức vụ
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="department" class="form-label">
                                    Phòng ban <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Chọn phòng ban</option>
                                    <?php 
                                    $commonDepartments = [
                                        'IT', 'Kế toán', 'Nhân sự', 'Marketing', 
                                        'Kinh doanh', 'Kỹ thuật', 'Hành chính'
                                    ];
                                    $currentDept = $formData['department'] ?? $employee->department;
                                    foreach ($commonDepartments as $dept): 
                                    ?>
                                        <option value="<?= $dept ?>" 
                                                <?= ($currentDept === $dept) ? 'selected' : '' ?>>
                                            <?= $dept ?>
                                        </option>
                                    <?php endforeach; ?>
                                    
                                    <?php if (isset($departments) && !empty($departments)): ?>
                                        <optgroup label="Phòng ban hiện có">
                                            <?php foreach ($departments as $dept): ?>
                                                <?php if (!in_array($dept, $commonDepartments)): ?>
                                                    <option value="<?= htmlspecialchars($dept) ?>" 
                                                            <?= ($currentDept === $dept) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($dept) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Vui lòng chọn phòng ban
                                </div>
                                <div class="form-text">
                                    Hoặc nhập trực tiếp vào ô bên dưới nếu muốn thay đổi
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="custom_department" class="form-label">Phòng ban khác</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="custom_department" 
                                       placeholder="Nhập tên phòng ban mới">
                                <div class="form-text">
                                    Nếu nhập vào đây, hệ thống sẽ sử dụng tên này thay vì phòng ban được chọn ở trên
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="salary" class="form-label">
                                    Lương <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="salary" 
                                           name="salary" 
                                           value="<?= htmlspecialchars($formData['salary'] ?? number_format($employee->salary, 0, ',', '.')) ?>"
                                           required
                                           placeholder="15000000">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <div class="invalid-feedback">
                                    Vui lòng nhập mức lương
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <?php 
                                    $currentStatus = $formData['status'] ?? $employee->status;
                                    $statuses = [
                                        'active' => 'Đang làm việc',
                                        'on_leave' => 'Đang nghỉ phép',
                                        'inactive' => 'Đã nghỉ việc'
                                    ];
                                    foreach ($statuses as $value => $text):
                                    ?>
                                        <option value="<?= $value ?>" <?= ($currentStatus === $value) ? 'selected' : '' ?>>
                                            <?= $text ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin bổ sung -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>
                                            <i class="fas fa-calendar-plus me-1"></i>
                                            <strong>Ngày tạo:</strong> <?= EmployeeController::formatDate($employee->created_at) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small>
                                            <i class="fas fa-calendar-edit me-1"></i>
                                            <strong>Cập nhật lần cuối:</strong> 
                                            <?= $employee->updated_at ? EmployeeController::formatDate($employee->updated_at) : 'Chưa cập nhật' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay lại danh sách
                                </a>
                                <div>
                                    <a href="index.php?action=show&id=<?= $employee->id ?>" class="btn btn-outline-info me-2">
                                        <i class="fas fa-eye me-1"></i>
                                        Xem chi tiết
                                    </a>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo me-1"></i>
                                        Khôi phục
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Cập nhật
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Không tìm thấy thông tin nhân viên!
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const customDepartmentInput = document.getElementById('custom_department');
    const form = document.getElementById('employee-form');

    // Xử lý phòng ban tùy chỉnh
    customDepartmentInput.addEventListener('input', function() {
        if (this.value.trim()) {
            departmentSelect.value = '';
            departmentSelect.removeAttribute('required');
        } else {
            departmentSelect.setAttribute('required', '');
        }
    });

    departmentSelect.addEventListener('change', function() {
        if (this.value) {
            customDepartmentInput.value = '';
        }
    });

    // Xử lý submit form
    form.addEventListener('submit', function(e) {
        // Nếu có phòng ban tùy chỉnh, sử dụng nó
        if (customDepartmentInput.value.trim()) {
            departmentSelect.value = customDepartmentInput.value.trim();
        }

        // Xử lý format lương trước khi submit
        const salaryInput = document.getElementById('salary');
        if (salaryInput.value) {
            salaryInput.value = salaryInput.value.replace(/[^0-9]/g, '');
        }
    });

    // Format lương khi nhập
    document.getElementById('salary').addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('vi-VN');
        }
        this.value = value;
    });
});
</script> 