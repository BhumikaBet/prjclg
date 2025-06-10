<?php
// add_payment.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['project_id'], $data['amount'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$project_id = intval($data['project_id']);
$amount = floatval($data['amount']);

// Database connection parameters
$host = 'localhost';
$dbname = 'your_database_name';
$user = 'your_mysql_username';
$pass = 'your_mysql_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert payment record
    $stmt = $pdo->prepare("INSERT INTO payments (project_id, amount, status) VALUES (?, ?, 'paid')");
    $stmt->execute([$project_id, $amount]);

    echo json_encode(['message' => 'Payment recorded successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
