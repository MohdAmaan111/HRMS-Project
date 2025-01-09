<?php
include_once('config.php');

// Create types of leave
$sql = "CREATE TABLE IF NOT EXISTS leave_types (
    leaveTypeID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    leaveTypeName VARCHAR(50) NOT NULL UNIQUE,
    defaultDays INT(3) NOT NULL
);";
// use exec() because no results are returned
$conn->exec($sql);
// Table created successfully

// Create employee details
$sql = "CREATE TABLE IF NOT EXISTS leave_req (
    leaveID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employeeID INT(6) NOT NULL,
    leave_type VARCHAR(30) NOT NULL,
    fromDate DATE NOT NULL,
    toDate Date NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
)";
// use exec() because no results are returned
$conn->exec($sql);
// Table created successfully

// Create leave left count table
$sql = "CREATE TABLE IF NOT EXISTS employee_leaves (
    leaveCountID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empID INT(6) NOT NULL,
    leaveTypeID INT(6) NOT NULL,
    remainingDays INT(3) NOT NULL
    -- FOREIGN KEY (leaveTypeID) REFERENCES leave_types(leaveTypeID),
    -- FOREIGN KEY (empID) REFERENCES employee(empID)
);";
// use exec() because no results are returned
$conn->exec($sql);
// Table created successfully


if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] == 'addLeaveType') {
    // echo print_r($_POST["departmentname"]);
    $leavetype = test_input($_POST["leavetype"]);
    $leavedays = test_input($_POST["leavedays"]);

    // prepare sql and bind parameters
    $sql = "INSERT INTO leave_types (leaveTypeName, defaultDays)
        VALUES (:leavetype, :leavedays)";
    $stmt = $conn->prepare($sql);
    $parameter = array(
        ':leavetype' => $leavetype,
        ':leavedays' => $leavedays
    );
    $stmt->execute($parameter);


    // Get the newly inserted leaveType's ID
    $leaveTypeID = $conn->lastInsertId();

    // Call the function to assign newly inserted leaveType
    assignNewlyLeaveTypes($conn, $leaveTypeID);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] == 'updateLeaveType') {
    // echo print_r($_POST["leaveTypeName"]);

    $leaveTypeName = $_POST['leaveTypeName'];
    $numberOfDays = $_POST['numberOfDays'];

    for ($i = 0; $i < count($leaveTypeName); $i++) {
        echo $leaveTypeName[$i];
        echo $numberOfDays[$i];
    }

    // prepare sql and bind parameters
    // $sql = "INSERT INTO leave_types (leaveTypeName, defaultDays)
    //     VALUES (:leavetype, :leavedays)";
    // $stmt = $conn->prepare($sql);
    // $parameter = array(
    //     ':leavetype' => $leavetype,
    //     ':leavedays' => $leavedays
    // );
    // $stmt->execute($parameter);


    // // Get the newly inserted leaveType's ID
    // $leaveTypeID = $conn->lastInsertId();

    // // Call the function to assign newly inserted leaveType
    // assignNewlyLeaveTypes($conn, $leaveTypeID);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'leaveRequest') {
    $employeeID = $_SESSION['userid'];
    $leaveType = $_POST["leave_type"];
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];
    $reason = $_POST['reason'];

    $response = ['success' => false, 'message' => ''];

    // Calculate the difference in days
    $fromDateTime = new DateTime($fromDate);
    $toDateTime = new DateTime($toDate);

    if ($toDateTime >= $fromDateTime) {
        $dateDiff = $fromDateTime->diff($toDateTime);
        $numberOfDays = $dateDiff->days + 1; // Include both start and end dates

        // Fetch remaining days
        $sql = "SELECT remainingDays FROM employee_leaves WHERE leaveTypeID=:leaveTypeID AND empID = :empID";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':leaveTypeID' => $leaveType,
            ':empID' => $_SESSION['userid']
        );
        $stmt->execute($param);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $remainingDays = $result['remainingDays'];

        // Check if sufficient leave balance is available
        if ($numberOfDays > $remainingDays) {
            $response['message'] = 'Insufficient leave balance.';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Invalid date range selected.';
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT * FROM leave_req WHERE employeeID=:empID AND fromDate=:fromDate AND toDate=:toDate";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':empID' => $employeeID,
        ':fromDate' => $fromDate,
        ':toDate' => $toDate
    );
    $stmt->execute($param);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    // echo "<pre>";
    // print_r($results);
    // echo "</pre>";
    // die;

    if (!empty($results)) {
        $response['success'] = false;
        $response['message'] = 'Leave request already exists.';
    } else {
        // Insert new record
        $sql = "INSERT INTO leave_req (employeeID, leave_type, fromDate, toDate, reason)
        VALUES (:empID, :leave_type, :fromDate, :toDate, :reason)";
        $stmt = $conn->prepare($sql);
        $parameter = [
            ':empID' => $employeeID,
            ':leave_type' => $leaveType,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
            ':reason' => $reason,
        ];
        $stmt->execute($parameter);
        $response['success'] = true;
        $response['message'] = 'Leave request processed successfully.';
    }

    // Return the response as JSON
    echo json_encode($response);
    exit;

    // echo "<script>alert('Record added successfully');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deleteLeaveType') {
    $sql = "DELETE FROM leave_types WHERE leaveTypeID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);

    $sql = "DELETE FROM employee_leaves WHERE leaveTypeID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);

    echo json_encode(array("statusCode" => 200));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $sql = "DELETE FROM leave_req WHERE leaveID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);

    echo json_encode(array("statusCode" => 200));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'empDetail') {

    $response = ['success' => false, 'data' => null];

    $sql = "SELECT leave_req.*, -- selects all columns from the leave_req table
            employee.name  -- selects only the name column from the employee table
            FROM leave_req 
            JOIN employee ON leave_req.employeeID = employee.empID 
            WHERE leave_req.leaveID = :leaveID";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':leaveID' => $_POST['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response['success'] = true;
        $response['data'] = $result;
    }
    echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'changeStatus') {
    // When leave request is approved by admin update in employee_leaves table
    if ($_POST['leaveStatus'] == "Approved") {
        // To get number of days leave is taken
        $sql = "SELECT * FROM leave_req WHERE leaveID=:id";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':id' => $_POST['leaveID']
        );
        $stmt->execute($param);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $leaveType = $result['leave_type'];
        $fromDate = $result['fromDate'];
        $toDate = $result['toDate'];

        // Calculate the difference in days
        $fromDateTime = new DateTime($fromDate);
        $toDateTime = new DateTime($toDate);

        $dateDiff = $fromDateTime->diff($toDateTime);
        $numberOfDays = $dateDiff->days + 1; // Include both start and end dates

        // Fetch remaining days
        $sql = "SELECT remainingDays FROM employee_leaves WHERE leaveTypeID=:leaveTypeID AND empID = :empID";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':leaveTypeID' => $leaveType,
            ':empID' => $_SESSION['userid']
        );
        $stmt->execute($param);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $daysLeft = $result['remainingDays'] - $numberOfDays;

        // Update the remaining days in the employee_leaves table
        $updateSql = "UPDATE employee_leaves SET remainingDays=:remainingDays WHERE leaveTypeID=:leaveTypeID AND empID=:empID";
        $updateStmt = $conn->prepare($updateSql);
        $updateParams = [
            ':remainingDays' => $daysLeft,
            ':leaveTypeID' => $leaveType,
            ':empID' => $_SESSION['userid'],
        ];
        $updateStmt->execute($updateParams);
    }

    // Update pending status in leave_req table
    $sql = "UPDATE leave_req SET status=:status WHERE leaveID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':status' => $_POST['leaveStatus'],
        ':id' => $_POST['leaveID'],
    );
    $stmt->execute($param);
    echo json_encode(array(
        "statusCode" => 200,
        "leaveStatus" => $_POST['leaveStatus']
    ));
    exit;
}

// Function to assign new leave type to all employee
function assignNewlyLeaveTypes($conn, $leaveTypeID)
{
    // Fetch new leave type and their default days
    $sql = "SELECT * FROM leave_types WHERE leaveTypeID = $leaveTypeID";
    $stmt = $conn->query($sql);
    $newLeaveType = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch every employee id from employee_leaves
    $sql = "SELECT empID FROM employee";
    $stmt = $conn->query($sql);
    $employeeIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo "<script>alert('Leave type = " . $newLeaveType['leaveTypeName'] . "');</script>";

    // Insert new leave type for every employee
    $sql = "INSERT INTO employee_leaves (empID, leaveTypeID, remainingDays)
                VALUES (:empID, :leaveTypeID, :remainingDays)";
    $stmt = $conn->prepare($sql);

    foreach ($employeeIDs as $employeeID) {
        $stmt->execute([
            ':empID' => $employeeID['empID'],
            ':leaveTypeID' => $newLeaveType['leaveTypeID'],
            ':remainingDays' => $newLeaveType['defaultDays']
        ]);
    }
    // echo "<script>alert('Leave types inserted successfully');</script>";
}
