<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Danh sách nhân viên
                </h4>
                <div>
                    <a href="index.php?action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Thêm nhân viên
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Bộ lọc và tìm kiếm -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="hidden" name="action" value="index">
                            <select name="department" class="form-select me-2" onchange="this.form.submit()">
                                <option value="">Tất cả phòng ban</option>
                                <?php if (isset($departments) && !empty($departments)): ?>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept) ?>" 
                                                <?= ($filterDepartment === $dept) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm nhanh..." id="live-search">
                            <a href="index.php?action=search" class="btn btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Thống kê nhanh -->
                <?php if (isset($totalEmployees)): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <span>
                                Hiển thị <?= count($employees ?? []) ?> trong tổng số <?= $totalEmployees ?> nhân viên
                                <?php if (!empty($filterDepartment)): ?>
                                    của phòng ban <strong><?= htmlspecialchars($filterDepartment) ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Bảng danh sách -->
                <?php if (isset($employees) && !empty($employees)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Họ tên</th>
                                <th width="20%">Email</th>
                                <th width="12%">Số điện thoại</th>
                                <th width="15%">Chức vụ</th>
                                <th width="12%">Phòng ban</th>
                                <th width="10%">Trạng thái</th>
                                <th width="6%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?= $employee['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($employee['name']) ?></div>
                                            <small class="text-muted">
                                                Vào làm: <?= EmployeeController::formatDate($employee['hire_date']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($employee['email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($employee['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($employee['phone']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($employee['phone']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($employee['position']) ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($employee['department']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= EmployeeController::getStatusColor($employee['status']) ?>">
                                        <?= EmployeeController::getStatusText($employee['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?action=show&id=<?= $employee['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?action=edit&id=<?= $employee['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?action=delete&id=<?= $employee['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Xóa"
                                           onclick="return confirmDelete('<?= htmlspecialchars($employee['name']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Employee pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= !empty($filterDepartment) ? '&department=' . urlencode($filterDepartment) : '' ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($filterDepartment) ? '&department=' . urlencode($filterDepartment) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= !empty($filterDepartment) ? '&department=' . urlencode($filterDepartment) : '' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <!-- Trạng thái trống -->
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có nhân viên nào</h5>
                    <p class="text-muted mb-4">
                        <?php if (!empty($filterDepartment)): ?>
                            Không tìm thấy nhân viên nào trong phòng ban "<?= htmlspecialchars($filterDepartment) ?>"
                        <?php else: ?>
                            Hãy thêm nhân viên đầu tiên cho công ty
                        <?php endif; ?>
                    </p>
                    <a href="index.php?action=create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Thêm nhân viên đầu tiên
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.btn-group .btn {
    margin: 0 1px;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}
</style> 