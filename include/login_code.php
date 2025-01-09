<?php
include_once('config.php');

// Create user log history
$sql = "CREATE TABLE IF NOT EXISTS userlog (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    userid INT(6),
    sessionid VARCHAR(50) NOT NULL,
    ipaddress VARCHAR(80) NOT NULL,
    logintime TIMESTAMP NULL,
    logouttime TIMESTAMP NULL
    )";
$conn->exec($sql);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
  $username = test_input($_POST["username"]);
  $password = test_input($_POST["password"]);

  $response = ['success' => false, 'errors' => []];

  // SQL query to fetch data from userid table
  $sql = "SELECT * FROM employee WHERE (username=:username OR email=:username)";
  $stmt = $conn->prepare($sql);
  $param = array(
    ':username' => $username,
    // ':password' => $password
  );
  $stmt->execute($param);

  // Fetch one result as an associative array
  $results = $stmt->fetch(PDO::FETCH_ASSOC);

  // To check the output array
  // var_dump($results);

  if (!empty($results)) {
    if ($password == $results['password']) {
      // echo "<script>alert(" . $results['role'] . ");</script>";
      $response['success'] = true;
      $response['message'] = "Login successfully!";

      $_SESSION['fullname'] = $results['name'];
      $_SESSION['userid'] = $results['empID'];
      $_SESSION['role'] = $results['role'];
      $_SESSION['sessionid'] = generateToken();
      $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

      // Insert data in user log table
      $sql = "INSERT INTO userlog (userid,sessionid,ipaddress,logintime)
      VALUES (:userid,:sessionid,:ipaddress,:logintime)";
      $stmt = $conn->prepare($sql);
      $parameter = array(
        ':userid' => $results['empID'],
        ':sessionid' => $_SESSION['sessionid'],
        ':ipaddress' => $ip_address,
        ':logintime' => date("Y-m-d H:i:s")
      );
      $stmt->execute($parameter);
    } else {
      // echo "<script>alert(" . $results['password'] . ");</script>";

      $response['errors']['password'] = "Incorrect password!";
    }
  } else {
    $response['errors']['username'] = "Incorrect username!";

    // echo "<script>alert('Wrong username or password')</script>";
  }

  // Return JSON response
  // header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
  // echo "<pre>";
  // print_r($_POST);
  // die;
  $name = test_input($_POST["name"]);
  $email = test_input($_POST["email"]);
  $username = test_input($_POST["username"]);
  $password = test_input($_POST["password"]);

  $response = ['success' => false, 'errors' => []];

  // SQL query to fetch data from userid table
  $sql = "SELECT * FROM userid WHERE (username=:username OR email=:email)";
  $stmt = $conn->prepare($sql);
  $param = array(
    ':username' => $username,
    ':email' => $email,
    // ':password' => $password
  );
  $stmt->execute($param);

  // Fetch one result as an associative array
  $results = $stmt->fetch(PDO::FETCH_ASSOC);
  // echo "<script>alert('username or email = " . $results['username'] . "')</script>";

  if (empty($results)) {
    // echo "<script>alert('username or email does not exist in database table')</script>";
    $response['success'] = true;
    $response['message'] = "Registered successfully!";

    // Insert data in user log table
    $sql = "INSERT INTO userid (name,email,username,password,reg_date)
      VALUES (:name,:email,:username,:password,:reg_date)";
    $stmt = $conn->prepare($sql);
    $parameter = array(
      ':name' => $name,
      ':email' => $email,
      ':username' => $username,
      ':password' => $password,
      ':reg_date' => date("Y-m-d H:i:s")
    );
    $stmt->execute($parameter);
  } else {
    // echo "<script>alert('username or email already exist in database')</script>";

    if ($email == $results['email']) {
      $response['errors']['email'] = "Email ID already exist!";
    } else if ($username == $results['username']) {
      $response['errors']['username'] = "Username already exist!";
    }
  }

  // Return JSON response
  echo json_encode($response);
  exit;
}
