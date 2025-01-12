
<?php
include_once('config.php');

// Create the employee table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS employee (
    empID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    role VARCHAR(50) NOT NULL,
    mobile VARCHAR(10) NOT NULL,
    email VARCHAR(30) UNIQUE,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    employee_status TINYINT(4) NOT NULL DEFAULT '1',
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->exec($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'filterdata') {
    $emp_name = $_POST['filter_emp'] ?? null;
    $role = $_POST['filter_role'] ?? null;
    $department = $_POST['filter_dept'] ?? null;

    // Construct the base SQL query
    $sql = "SELECT * FROM employee INNER JOIN role ON role.roleID=employee.role WHERE 1=1";

    // Add conditions based on the selected filters
    if (!empty($emp_name)) {
        $sql .= " AND name LIKE :emp_name";
    }
    if (!empty($role)) {
        $sql .= " AND role = :role";
    }
    if (!empty($department)) {
        $sql .= " AND department = :department";
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);

    // Bind parameters
    if (!empty($emp_name)) {
        $emp_name = "%" . $emp_name . "%"; // Add wildcards for partial matching
        $stmt->bindParam(':emp_name', $emp_name, PDO::PARAM_STR);
    }
    if (!empty($role)) {
        $stmt->bindParam(':role', $role, PDO::PARAM_INT);
    }
    if (!empty($department)) {
        $stmt->bindParam(':department', $department, PDO::PARAM_INT);
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if results are empty
    if (empty($results)) {
        echo json_encode(['status' => 'error']);
    } else {
        echo json_encode(['status' => 'success', 'data' => $results]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'adddata') {
    // echo "<pre>";
    // print_r($_POST);
    // die;
    $name = test_input($_POST["name"]);
    $employeeID = $_POST['employeeID'];
    $role = $_POST['role'];
    $mobile = $_POST['mobile'];
    $email = test_input($_POST["email"]);
    $username = test_input($_POST["username"]);
    $password = test_input($_POST["password"]);

    // Process data, save to database, etc.
    // echo "Hello, $name! Your email is $email and ID is $employeeID.";

    // Ensure all required fields are present
    if (!empty($name) && !empty($role) && !empty($mobile) && !empty($email) && !empty($username) && !empty($password)) {

        if (empty($employeeID)) {
            // Insert new record
            $sql = "INSERT INTO employee (name, role, mobile, email, username, password)
            VALUES (:name, :role, :mobile, :email, :username, :password)";
            $stmt = $conn->prepare($sql);
            $parameter = [
                ':name' => $name,
                ':role' => $role,
                ':mobile' => $mobile,
                ':email' => $email,
                ':username' => $username,
                ':password' => $password,
            ];
            $stmt->execute($parameter);

            // Get the newly inserted employee's ID
            $newEmployeeID = $conn->lastInsertId();

            // Call the function to assign default leave types
            assignDefaultLeaveTypes($conn, $newEmployeeID);

            // echo "<script>alert('Record added successfully');</script>";
        } else {
            $sql = "UPDATE employee 
                SET name=:name, role=:role, mobile=:mobile, email=:email, username=:username, password=:password 
                WHERE empID=:id";
            $stmt = $conn->prepare($sql);
            $parameter = [
                ':id' => $employeeID,
                ':name' => $name,
                ':role' => $role,
                ':mobile' => $mobile,
                ':email' => $email,
                ':username' => $username,
                ':password' => $password,
            ];
            $stmt->execute($parameter);
            // echo "<script>alert('Record updated successfully');</script>";
        }
    } else {
        // echo "<script>alert('Invalid input');</script>";
    }
    // header('Location:employee_registraion.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'status') {
    $sql = "UPDATE employee SET employee_status=:status-employee_status WHERE empID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':status' => 1,
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);
    echo json_encode(array("statusCode" => 200));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'getdata') {
    $sql = "SELECT * FROM employee WHERE empID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $sql = "SELECT * FROM employee WHERE empID = :id";
    $stmt = $conn->prepare($sql);
    $param1 = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param1);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "DELETE FROM employee WHERE empID = :id";
    $stmt = $conn->prepare($sql);
    $param = array(
        ':id' => $_POST['id'],
    );
    $stmt->execute($param);
    echo json_encode(array("statusCode" => 200));
    exit;
}
/**
 * Function to assign default leave types to a new employee
 * @param PDO $conn - The database connection
 * @param int $employeeID - The ID of the new employee
 */
function assignDefaultLeaveTypes($conn, $employeeID)
{
    // Fetch leave types and their default days
    $sql = "SELECT leaveTypeID, defaultDays FROM leave_types";
    $stmt = $conn->query($sql);
    $leaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insert leave types for the new employee
    $sql = "INSERT INTO employee_leaves (empID, leaveTypeID, remainingDays)
            VALUES (:empID, :leaveTypeID, :remainingDays)";
    $stmt = $conn->prepare($sql);

    foreach ($leaveTypes as $leaveType) {
        $stmt->execute([
            ':empID' => $employeeID,
            ':leaveTypeID' => $leaveType['leaveTypeID'],
            ':remainingDays' => $leaveType['defaultDays']
        ]);
    }
    // echo $employeeID;
    // echo "<script>alert('Leave types inserted successfully');</script>";
}
