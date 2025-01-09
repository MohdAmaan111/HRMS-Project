<?php
include_once('config.php');

$fullName = "";

// Create the employee table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS personal_details (
    profile_id INT(6) PRIMARY KEY,
    fullname VARCHAR(30) NULL,
    about VARCHAR(120) NULL,
    company VARCHAR(60) NULL,
    job VARCHAR(50) NULL,
    country VARCHAR(20) NULL,
    address VARCHAR(60) NULL,
    phone VARCHAR(20) UNIQUE,
    email VARCHAR(50) UNIQUE,
    twitter VARCHAR(50) NULL,
    facebook VARCHAR(50) NULL,
    instagram VARCHAR(50) NULL,
    linkedin VARCHAR(50) NULL,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->exec($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'profile') {
    // echo "<pre>";
    // print_r($_POST);
    // echo "Your user id is = " . $_SESSION['userid'];
    // echo "</pre>";

    echo '<pre>';
    print_r($_POST); // Check if other POST data is received
    print_r($_FILES); // Check the contents of $_FILES
    echo '</pre>';

    if (isset($_SESSION['userid'])) {
        $profileID = $_SESSION['userid'];
        $fullName = $_POST["fullName"];
        $about = $_POST["about"];
        $company = $_POST["company"];
        $job = $_POST["job"];
        $country = $_POST["country"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $twitter = $_POST["twitter"];
        $facebook = $_POST["facebook"];
        $instagram = $_POST["instagram"];
        $linkedin = $_POST["linkedin"];

        // echo "<script>alert('Your user id is " . $profileID . "');</script>";

        $sql = "SELECT * FROM personal_details WHERE profile_id={$_SESSION['userid']}";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        // echo "<pre>";
        // echo print_r($results);
        // echo "</pre>";
        if ($results) {
            $sql = "UPDATE personal_details 
                    SET about = :about, fullname = :fullname, company = :company, job = :job, 
                        country = :country, address = :address, phone = :phone, email = :email, 
                        twitter = :twitter, facebook = :facebook, instagram = :instagram , 
                        linkedin = :linkedin 
                    WHERE profile_id = :id";
        } else {
            $sql = "INSERT INTO personal_details 
                    (profile_id, fullName, about, company, job, country, address, phone, email, twitter, 
                    facebook, instagram, linkedin) 
                    VALUES (:id, :fullname, :about, :company, :job, :country, :address, :phone, :email, 
                    :twitter, :facebook, :instagram, :linkedin)";
        }

        $stmt = $conn->prepare($sql);
        $parameters = [
            ':id' => $profileID,
            ':fullname' => $fullName,
            ':about' => $about,
            ':company' => $company,
            ':job' => $job,
            ':country' => $country,
            ':address' => $address,
            ':phone' => $phone,
            ':email' => $email,
            ':twitter' => $twitter,
            ':facebook' => $facebook,
            ':instagram' => $instagram,
            ':linkedin' => $linkedin,
        ];
        $stmt->execute($parameters);

        $_SESSION['fullname'] = $fullName;

        // echo "<script>alert('Operation completed successfully');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'password') {
    // to check post values
    // print_r($_POST);

    $response = ['success' => false, 'errors' => []];

    $password = $_POST["password"];
    $newPassword = $_POST["newpassword"];
    $renewpassword = $_POST["renewpassword"];

    $sql = "SELECT * FROM employee WHERE empID={$_SESSION['userid']}";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate current password
    if ($password != $results['password']) {
        $response['errors']['password'] = "Incorrect current password!";
    }

    // Validate new password match
    if ($newPassword !== $renewpassword) {
        $response['errors']['newpassword'] = "Password does not match!";
    }

    // If no errors, proceed with success response
    if (empty($response['errors'])) {
        $response['success'] = true;
        $response['message'] = "Password updated successfully!";
        // Perform password update logic here

        $sql = "UPDATE employee 
                SET password=:newpassword 
                WHERE empID=:userid";
        $stmt = $conn->prepare($sql);
        $parameter = [
            ':newpassword' => $newPassword,
            ':userid' => $_SESSION['userid']
        ];
        $stmt->execute($parameter);
    }

    // Return JSON response
    // header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
