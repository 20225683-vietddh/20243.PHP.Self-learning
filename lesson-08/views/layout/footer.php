    </div> <!-- End container -->

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Hệ thống quản lý nhân viên</h5>
                    <p class="mb-0">Được xây dựng với mô hình MVC trong PHP</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-code me-1"></i>
                        Lesson 08 - MVC Pattern Demo
                    </p>
                    <small class="text-muted">© 2024 - PHP Self Learning</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Tự động ẩn alert sau 5 giây
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Xác nhận trước khi xóa
        function confirmDelete(employeeName) {
            return confirm('Bạn có chắc chắn muốn xóa nhân viên "' + employeeName + '"?\nHành động này không thể hoàn tác!');
        }

        // Format số điện thoại khi nhập
        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            input.value = value;
        }

        // Format lương khi nhập
        function formatSalary(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('vi-VN');
            }
            input.value = value;
        }

        // Validate form trước khi submit
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });

            // Validate email
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    emailField.classList.remove('is-invalid');
                    emailField.classList.add('is-valid');
                }
            }

            // Validate phone
            const phoneField = form.querySelector('input[name="phone"]');
            if (phoneField && phoneField.value) {
                const phoneRegex = /^[0-9]{10,11}$/;
                if (!phoneRegex.test(phoneField.value.replace(/\s/g, ''))) {
                    phoneField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    phoneField.classList.remove('is-invalid');
                    phoneField.classList.add('is-valid');
                }
            }

            return isValid;
        }

        // Tìm kiếm real-time
        function setupLiveSearch() {
            const searchInput = document.querySelector('#live-search');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (searchInput.value.length >= 2) {
                            window.location.href = 'index.php?action=search&keyword=' + encodeURIComponent(searchInput.value);
                        }
                    }, 500);
                });
            }
        }

        // Khởi tạo các chức năng
        document.addEventListener('DOMContentLoaded', function() {
            setupLiveSearch();

            // Tooltip cho các button
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Form validation
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(form)) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            });
        });
    </script>
</body>
</html> 