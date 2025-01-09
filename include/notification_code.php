<?php
include_once('config.php');

$response = ['success' => false, 'notifications' => []];

// Check if the user is an admin
if (isset($_SESSION['role']) && $_SESSION['role'] === '1') {
    $sql = "SELECT leave_req.*,  -- selects all columns from the leave_req table
        employee.name   -- selects only the name column from the employee table
        FROM leave_req 
        JOIN employee ON leave_req.employeeID = employee.empID 
        WHERE leave_req.status = 'Pending' 
        ORDER BY leave_req.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $response['success'] = true;
        $response['notifications'] = $result;
    }
}

// echo "<pre>";
// print_r(($result));
// echo "</pre>";

echo json_encode($response);
