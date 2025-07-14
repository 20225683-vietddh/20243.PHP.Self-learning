/**
 * Nội dung bài học: Transaction
 * Transaction
 * Commit
 * Rollback
 */

<?php
    echo "<h2>CÁC VÍ DỤ VỀ TRANSACTION TRONG PHP</h2>";

    // Ví dụ 1: Transaction cơ bản - Chuyển tiền giữa hai tài khoản
    echo "<h3>Ví dụ 1: Transaction cơ bản - Chuyển tiền</h3>";
    try {
        $conn = new mysqli("localhost", "root", "", "bank_system");
        
        // Bắt đầu transaction
        $conn->begin_transaction();
        
        // Trừ tiền tài khoản A
        $stmt1 = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
        $amount = 1000000;
        $account_from = 1;
        $stmt1->bind_param("di", $amount, $account_from);
        $stmt1->execute();
        
        // Cộng tiền tài khoản B
        $stmt2 = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
        $account_to = 2;
        $stmt2->bind_param("di", $amount, $account_to);
        $stmt2->execute();
        
        // Ghi log giao dịch
        $stmt3 = $conn->prepare("INSERT INTO transaction_logs (from_account, to_account, amount, transaction_date) VALUES (?, ?, ?, NOW())");
        $stmt3->bind_param("iid", $account_from, $account_to, $amount);
        $stmt3->execute();
        
        // Commit transaction
        $conn->commit();
        echo "Chuyển tiền thành công: " . number_format($amount) . " VND<br>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi chuyển tiền: " . $e->getMessage() . "<br>";
    }
    $conn->close();

    // Ví dụ 2: Transaction với nhiều bảng - Đặt hàng online
    echo "<h3>Ví dụ 2: Transaction đặt hàng online</h3>";
    try {
        $conn = new mysqli("localhost", "root", "", "ecommerce");
        $conn->begin_transaction();
        
        // Tạo đơn hàng
        $customer_id = 123;
        $total_amount = 2500000;
        $stmt1 = $conn->prepare("INSERT INTO orders (customer_id, total_amount, order_date, status) VALUES (?, ?, NOW(), 'pending')");
        $stmt1->bind_param("id", $customer_id, $total_amount);
        $stmt1->execute();
        $order_id = $conn->insert_id;
        
        // Thêm chi tiết đơn hàng
        $products = [
            ['product_id' => 101, 'quantity' => 2, 'price' => 1000000],
            ['product_id' => 102, 'quantity' => 1, 'price' => 500000]
        ];
        
        foreach ($products as $product) {
            // Thêm vào order_items
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiid", $order_id, $product['product_id'], $product['quantity'], $product['price']);
            $stmt2->execute();
            
            // Cập nhật kho hàng
            $stmt3 = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stmt3->bind_param("ii", $product['quantity'], $product['product_id']);
            $stmt3->execute();
            
            // Kiểm tra kho hàng còn đủ không
            $stmt4 = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $stmt4->bind_param("i", $product['product_id']);
            $stmt4->execute();
            $result = $stmt4->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['stock_quantity'] < 0) {
                throw new Exception("Sản phẩm ID {$product['product_id']} không đủ hàng trong kho");
            }
        }
        
        $conn->commit();
        echo "Đặt hàng thành công! Mã đơn hàng: #$order_id<br>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi đặt hàng: " . $e->getMessage() . "<br>";
    }
    $conn->close();

    // Ví dụ 3: Rollback khi có lỗi - Xử lý thanh toán
    echo "<h3>Ví dụ 3: Rollback khi thanh toán thất bại</h3>";
    try {
        $conn = new mysqli("localhost", "root", "", "payment_system");
        $conn->begin_transaction();
        
        $user_id = 456;
        $payment_amount = 500000;
        
        // Kiểm tra số dư tài khoản
        $stmt1 = $conn->prepare("SELECT balance FROM user_wallets WHERE user_id = ?");
        $stmt1->bind_param("i", $user_id);
        $stmt1->execute();
        $result = $stmt1->get_result();
        $wallet = $result->fetch_assoc();
        
        if ($wallet['balance'] < $payment_amount) {
            throw new Exception("Số dư không đủ để thanh toán");
        }
        
        // Trừ tiền từ ví
        $stmt2 = $conn->prepare("UPDATE user_wallets SET balance = balance - ? WHERE user_id = ?");
        $stmt2->bind_param("di", $payment_amount, $user_id);
        $stmt2->execute();
        
        // Mô phỏng lỗi từ gateway thanh toán
        $payment_gateway_success = false; // Giả lập lỗi gateway
        
        if (!$payment_gateway_success) {
            throw new Exception("Gateway thanh toán bị lỗi, không thể xử lý giao dịch");
        }
        
        // Ghi log thanh toán
        $stmt3 = $conn->prepare("INSERT INTO payment_logs (user_id, amount, status, created_at) VALUES (?, ?, 'success', NOW())");
        $stmt3->bind_param("id", $user_id, $payment_amount);
        $stmt3->execute();
        
        $conn->commit();
        echo "Thanh toán thành công<br>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Thanh toán thất bại: " . $e->getMessage() . " (Đã hoàn tiền)<br>";
    }
    $conn->close();

    // Ví dụ 4: Transaction với try-catch và auto-rollback
    echo "<h3>Ví dụ 4: Transaction với xử lý lỗi nâng cao</h3>";
    $conn = new mysqli("localhost", "root", "", "inventory_system");
    
    try {
        // Bật autocommit = false
        $conn->autocommit(false);
        
        // Nhập hàng vào kho
        $warehouse_id = 1;
        $products_import = [
            ['sku' => 'LAPTOP001', 'quantity' => 50, 'cost_price' => 15000000],
            ['sku' => 'MOUSE001', 'quantity' => 100, 'cost_price' => 500000],
            ['sku' => 'KEYBOARD001', 'quantity' => 75, 'cost_price' => 1200000]
        ];
        
        $total_import_value = 0;
        
        foreach ($products_import as $product) {
            // Cập nhật tồn kho
            $stmt1 = $conn->prepare("UPDATE inventory SET quantity = quantity + ?, last_updated = NOW() WHERE warehouse_id = ? AND sku = ?");
            $stmt1->bind_param("iis", $product['quantity'], $warehouse_id, $product['sku']);
            $stmt1->execute();
            
            // Ghi log nhập kho
            $stmt2 = $conn->prepare("INSERT INTO inventory_logs (warehouse_id, sku, action, quantity, cost_price, log_date) VALUES (?, ?, 'import', ?, ?, NOW())");
            $stmt2->bind_param("isid", $warehouse_id, $product['sku'], $product['quantity'], $product['cost_price']);
            $stmt2->execute();
            
            $total_import_value += $product['quantity'] * $product['cost_price'];
            
            // Kiểm tra ràng buộc nghiệp vụ
            if ($product['quantity'] > 1000) {
                throw new Exception("Không thể nhập quá 1000 sản phẩm cùng lúc");
            }
        }
        
        // Cập nhật tổng giá trị kho
        $stmt3 = $conn->prepare("UPDATE warehouses SET total_value = total_value + ? WHERE warehouse_id = ?");
        $stmt3->bind_param("di", $total_import_value, $warehouse_id);
        $stmt3->execute();
        
        // Commit thủ công
        $conn->commit();
        echo "Nhập kho thành công! Tổng giá trị: " . number_format($total_import_value) . " VND<br>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi nhập kho: " . $e->getMessage() . "<br>";
    } finally {
        // Khôi phục autocommit
        $conn->autocommit(true);
        $conn->close();
    }

    // Ví dụ 5: Savepoint - Transaction phức tạp với điểm lưu
    echo "<h3>Ví dụ 5: Savepoint - Xử lý đơn hàng phức tạp</h3>";
    try {
        $conn = new mysqli("localhost", "root", "", "complex_order_system");
        $conn->begin_transaction();
        
        // Bước 1: Tạo đơn hàng
        $customer_id = 789;
        $stmt1 = $conn->prepare("INSERT INTO orders (customer_id, created_at) VALUES (?, NOW())");
        $stmt1->bind_param("i", $customer_id);
        $stmt1->execute();
        $order_id = $conn->insert_id;
        
        // Tạo savepoint sau khi tạo đơn hàng
        $conn->query("SAVEPOINT order_created");
        echo "Savepoint 'order_created' đã được tạo<br>";
        
        // Bước 2: Thêm sản phẩm
        $products = [
            ['id' => 201, 'qty' => 3, 'price' => 2000000],
            ['id' => 202, 'qty' => 1, 'price' => 5000000]
        ];
        
        foreach ($products as $product) {
            $stmt2 = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiid", $order_id, $product['id'], $product['qty'], $product['price']);
            $stmt2->execute();
        }
        
        // Tạo savepoint sau khi thêm sản phẩm
        $conn->query("SAVEPOINT products_added");
        echo "Savepoint 'products_added' đã được tạo<br>";
        
        // Bước 3: Áp dụng voucher (có thể thất bại)
        $voucher_code = "DISCOUNT50";
        $stmt3 = $conn->prepare("SELECT discount_percent FROM vouchers WHERE code = ? AND is_active = 1 AND expiry_date > NOW()");
        $stmt3->bind_param("s", $voucher_code);
        $stmt3->execute();
        $voucher_result = $stmt3->get_result();
        
        if ($voucher_result->num_rows > 0) {
            $voucher = $voucher_result->fetch_assoc();
            $stmt4 = $conn->prepare("UPDATE orders SET voucher_code = ?, discount_percent = ? WHERE order_id = ?");
            $stmt4->bind_param("sdi", $voucher_code, $voucher['discount_percent'], $order_id);
            $stmt4->execute();
            echo "Áp dụng voucher thành công: {$voucher['discount_percent']}%<br>";
        } else {
            // Rollback về savepoint products_added (giữ lại đơn hàng và sản phẩm)
            $conn->query("ROLLBACK TO SAVEPOINT products_added");
            echo "Voucher không hợp lệ, đã rollback về savepoint 'products_added'<br>";
        }
        
        // Bước 4: Xử lý thanh toán
        $payment_success = true; // Giả lập thanh toán thành công
        
        if ($payment_success) {
            $stmt5 = $conn->prepare("UPDATE orders SET status = 'paid', payment_date = NOW() WHERE order_id = ?");
            $stmt5->bind_param("i", $order_id);
            $stmt5->execute();
            
            // Commit toàn bộ transaction
            $conn->commit();
            echo "Đơn hàng hoàn tất! Mã đơn: #$order_id<br>";
        } else {
            // Rollback về savepoint order_created (chỉ giữ lại đơn hàng trống)
            $conn->query("ROLLBACK TO SAVEPOINT order_created");
            echo "Thanh toán thất bại, đã rollback về savepoint 'order_created'<br>";
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi xử lý đơn hàng: " . $e->getMessage() . "<br>";
    }
    $conn->close();
?>