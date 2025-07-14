<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Thêm nhân viên mới
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?action=store" id="employee-form">
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
                                       value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
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
                                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
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
                                       value="<?= htmlspecialchars($formData['phone'] ?? '') ?>"
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
                                       value="<?= htmlspecialchars($formData['hire_date'] ?? date('Y-m-d')) ?>"
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
                                       value="<?= htmlspecialchars($formData['position'] ?? '') ?>"
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
                                    foreach ($commonDepartments as $dept): 
                                    ?>
                                        <option value="<?= $dept ?>" 
                                                <?= (isset($formData['department']) && $formData['department'] === $dept) ? 'selected' : '' ?>>
                                            <?= $dept ?>
                                        </option>
                                    <?php endforeach; ?>
                                    
                                    <?php if (isset($departments) && !empty($departments)): ?>
                                        <optgroup label="Phòng ban hiện có">
                                            <?php foreach ($departments as $dept): ?>
                                                <?php if (!in_array($dept, $commonDepartments)): ?>
                                                    <option value="<?= htmlspecialchars($dept) ?>" 
                                                            <?= (isset($formData['department']) && $formData['department'] === $dept) ? 'selected' : '' ?>>
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
                                    Hoặc nhập trực tiếp vào ô bên dưới nếu phòng ban chưa có
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
                                           value="<?= htmlspecialchars($formData['salary'] ?? '') ?>"
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
                                    <option value="active" 
                                            <?= (!isset($formData['status']) || $formData['status'] === 'active') ? 'selected' : '' ?>>
                                        Đang làm việc
                                    </option>
                                    <option value="on_leave" 
                                            <?= (isset($formData['status']) && $formData['status'] === 'on_leave') ? 'selected' : '' ?>>
                                        Đang nghỉ phép
                                    </option>
                                    <option value="inactive" 
                                            <?= (isset($formData['status']) && $formData['status'] === 'inactive') ? 'selected' : '' ?>>
                                        Đã nghỉ việc
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay lại
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo me-1"></i>
                                        Làm lại
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Thêm nhân viên
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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