<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nice_admin";


try {
  // Create connection with MySQL
  $conn = new PDO("mysql:host=$servername", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
  // use exec() because no results are returned
  $conn->exec($sql);

  // Database created successfully

  // Create connection with database
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // sql to create table
  $sql = "CREATE TABLE IF NOT EXISTS userid (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(30),
    username VARCHAR(50),
    password VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

  // use exec() because no results are returned
  $conn->exec($sql);

  // Table created successfully
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

date_default_timezone_set('Asia/Kolkata');

function generateToken()
{
  return bin2hex(random_bytes(16));  // Generates a 32-character random string
}

function showTable()
{
  $conn = new PDO("mysql:host=localhost;dbname=nice_admin", 'root', '');
  // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "SELECT * FROM emloyee";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $show = '<table class="table table-bordered">
          <tr>
            <th>Name</th>
            <th>Email</th>
          </tr>';
  foreach ($results as $row) {
    $show .= '<tr>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
              </tr>';
  }
  $show .= '</table>';

  echo $show;
}
