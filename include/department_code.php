<?php
include_once('config.php');

// Create employee details
$sql = "CREATE TABLE IF NOT EXISTS department (
    departmentID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    departmentname VARCHAR(30) NULL,
    status VARCHAR(50) NOT NULL DEFAULT '1',
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
// use exec() because no results are returned
$conn->exec($sql);
// Table created successfully

$departmentname = $status = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] == 'addDepartment') {
    // echo print_r($_POST["departmentname"]);
    $departmentname = test_input($_POST["departmentname"]);

    // prepare sql and bind parameters
    $sql = "INSERT INTO department (departmentname)
        VALUES (:departmentname)";
    $stmt = $conn->prepare($sql);
    $parameter = array(
        ':departmentname' => $departmentname
    );
    $stmt->execute($parameter);

    // echo "<script>alert('Data saved successfully');</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'updateDepartment') {

    // echo print_r($_POST);

    $employeeID = $_POST["employeeID"];
    $department = $_POST["department"];
    $dessignation = $_POST['dessignation'];
    $salary = $_POST['salary'];

    if (!empty($department) || !empty($dessignation) || !empty($salary)) {
        $sql = "UPDATE employee 
                SET department=:department, dessignation=:dessignation, salary=:salary 
                WHERE empID=:id";
        $stmt = $conn->prepare($sql);
        $parameter = [
            ':id' => $employeeID,
            ':department' => $department,
            ':dessignation' => $dessignation,
            ':salary' => $salary,
        ];
        $stmt->execute($parameter);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'status') {
    $sql = "UPDATE department SET status=:status-status WHERE departmentID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':status' => 1,
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);
    echo json_encode(array("statusCode" => 200));
    exit;
}