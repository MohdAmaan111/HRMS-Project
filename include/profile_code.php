<?php
include_once('config.php');
// include_once('./upload');

$fullName = "";

// Create the employee table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS personal_details (
    profile_id INT(6) PRIMARY KEY,
    fullname VARCHAR(30) NULL,
    image VARCHAR(150) NULL,
    about VARCHAR(120) NULL,
    company VARCHAR(60) NULL,
    job VARCHAR(50) NULL,
    country VARCHAR(20) NULL,
    address VARCHAR(60) NULL,
    phone VARCHAR(20) UNIQUE,
    email VARCHAR(50) UNIQUE,
    twitter VARCHAR(50) NULL,
    github VARCHAR(50) NULL,
    instagram VARCHAR(50) NULL,
    linkedin VARCHAR(50) NULL,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->exec($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'profile') {
    // echo "<pre>";
    // print_r($_POST); // Check if other POST data is received
    // echo "Your user id is = " . $_SESSION['userid'];
    // echo "</pre>";

    // echo '<pre>';
    // print_r($_FILES); // Check the contents of $_FILES
    // echo "Your profile name is " . $_FILES['image']['name'];
    // echo '</pre>';

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
        $github = $_POST["github"];
        $instagram = $_POST["instagram"];
        $linkedin = $_POST["linkedin"];

        $sql = "SELECT * FROM personal_details WHERE profile_id={$_SESSION['userid']}";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentImage = $results['image'] ?? "default.jpg";

        // Handle image upload if a new file is provided
        if (!empty($_FILES['image'])) {
            $uniquePrefix = uniqid($profileID . '_', true); // Combines user ID with a unique identifier
            $imageName = $uniquePrefix . '_image.jpg';
            // $imageName = $_FILES['image']['name'];
            // $imageName = str_replace("'", "", $imageName); // Remove single quotes
            // $imageName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $imageName); // Replace other invalid characters with underscores

            $imageTmpName = $_FILES['image']['tmp_name'];
            $folder = '../upload/userProfile/' . $imageName;

            if (!is_dir($folder)) {
                // echo "<h2>Directory is not present</h2>";
            } else {
                // echo "<h2>Directory is present</h2>";
            }

            //echo "Target file path: " . $folder; // Debugging purpose

            //move an uploaded file from its temporary location to a new destination on the server
            if (move_uploaded_file($imageTmpName, $folder)) {
                // Delete old image if not the default
                if ($currentImage !== "default.jpg" && file_exists('../upload/userProfile/' . $currentImage)) {
                    unlink('../upload/userProfile/' . $currentImage);
                }

                $currentImage = $imageName; // Update current image to the new one
            }
        }

        // echo "<pre>";
        // echo print_r($results);
        // echo "</pre>";
        if ($results) {
            $sql = "UPDATE personal_details 
                    SET about = :about, fullname = :fullname, image = :image, company = :company, job = :job, country = :country, address = :address, phone = :phone, email = :email, twitter = :twitter, github = :github, instagram = :instagram , linkedin = :linkedin 
                    WHERE profile_id = :id";
        } else {
            $sql = "INSERT INTO personal_details 
                    (profile_id, fullName, image, about, company, job, country, address, phone, email, twitter, github, instagram, linkedin) 
                    VALUES (:id, :fullname, :image, :about, :company, :job, :country, :address, :phone, :email, :twitter, :github, :instagram, :linkedin)";
        }

        $stmt = $conn->prepare($sql);
        $parameters = [
            ':id' => $profileID,
            ':fullname' => $fullName,
            ':image' => $currentImage,
            ':about' => $about,
            ':company' => $company,
            ':job' => $job,
            ':country' => $country,
            ':address' => $address,
            ':phone' => $phone,
            ':email' => $email,
            ':twitter' => $twitter,
            ':github' => $github,
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
