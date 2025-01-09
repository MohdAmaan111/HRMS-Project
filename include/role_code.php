<?php
// Create employee details
$sql = "CREATE TABLE IF NOT EXISTS role (
    roleID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rolename VARCHAR(30) NOT NULL,
    status VARCHAR(50) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
// use exec() because no results are returned
$conn->exec($sql);
// Table created successfully

$rolename = $status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rolename = test_input($_POST["rolename"]);
    // $status = test_input($_POST["status"]);

    // prepare sql and bind parameters
    $sql = "INSERT INTO role (rolename)
        VALUES (:rolename)";
    $stmt = $conn->prepare($sql);
    $parameter = array(
        ':rolename' => $rolename
        //':status' => $status
    );
    $stmt->execute($parameter);

    // header('Location:login.php');
    // echo "<script>alert('Data saved successfully');</script>";
}
