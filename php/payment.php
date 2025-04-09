<?php
session_start();
require_once 'config.php'; // Assumes database connection details are here

class PaymentProcessor {
    private $db;
    private $errors = [];

    public function __construct($db) {
        $this->db = $db;
    }

    public function validatePayment($data) {
        // Sanitize and validate amount
        $amount = filter_var($data['amount'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (!$amount || $amount <= 0) {
            $this->errors[] = "Invalid payment amount";
        }

        // Validate payment method
        $paymentMethod = trim(filter_var($data['payment_method'] ?? '', FILTER_SANITIZE_STRING));
        if (!in_array($paymentMethod, ['credit_card', 'debit_card', 'bank_transfer'])) {
            $this->errors[] = "Invalid payment method";
        }

        // Validate transaction ID
        $transactionId = trim(filter_var($data['transaction_id'] ?? '', FILTER_SANITIZE_STRING));
        if (empty($transactionId)) {
            $this->errors[] = "Transaction ID is required";
        }

        return empty($this->errors);
    }

    public function processPayment($data) {
        if (!$this->validatePayment($data)) {
            return ['success' => false, 'errors' => $this->errors];
        }

        try {
            // Prepare the SQL statement
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    amount, 
                    payment_method, 
                    transaction_id, 
                    payment_date, 
                    status
                ) VALUES (?, ?, ?, NOW(), ?)
            ");

            // Execute with sanitized values
            $stmt->execute([
                filter_var($data['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                trim(filter_var($data['payment_method'], FILTER_SANITIZE_STRING)),
                trim(filter_var($data['transaction_id'], FILTER_SANITIZE_STRING)),
                'completed'
            ]);

            // Log the successful payment
            $this->logPayment($data, $this->db->lastInsertId());

            return ['success' => true, 'message' => 'Payment processed successfully'];

        } catch (PDOException $e) {
            error_log("Payment Processing Error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['System error occurred. Please try again later.']];
        }
    }

    private function logPayment($data, $paymentId) {
        $logFile = __DIR__ . '/logs/payments.log';
        $logEntry = date('Y-m-d H:i:s') . " - Payment ID: $paymentId - Amount: {$data['amount']} - Method: {$data['payment_method']}\n";
        
        if (!is_dir(__DIR__ . '/logs')) {
            mkdir(__DIR__ . '/logs', 0755, true);
        }
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    public function getErrors() {
        return $this->errors;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create database connection
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $processor = new PaymentProcessor($db);
        $result = $processor->processPayment($_POST);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($result);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => ['Connection error']]);
    }
}
?>