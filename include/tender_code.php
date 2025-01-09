<?php
    include_once('../config.php');
   
        
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {

        // Ensure all required fields are present
        if (!empty($_POST['tenderid']) && !empty($_POST['publishedby']) && !empty($_POST['brief']) && !empty($_POST['location']) && !empty($_POST['mode']) && !empty($_POST['lastdate'])) {

            // Check if tid is present for updating
            if (!empty($_POST['tid'])) {

                // Update existing tender
                $sql = "UPDATE tender SET tenderid = :tenderid, publishedby = :publishedby, brief = :brief, location = :location, mode = :mode, lastdate = :lastdate WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $param = array(
                    ':id' => $_POST['tid'],
                    ':tenderid' => $_POST['tenderid'],
                    ':publishedby' => $_POST['publishedby'],
                    ':brief' => $_POST['brief'],
                    ':location' => $_POST['location'],
                    ':mode' => $_POST['mode'],
                    ':lastdate' => $_POST['lastdate']
                );
                $stmt->execute($param);

                // Confirmation message for update
                echo "<script>alert('Tender updated successfully')</script>";

            } else {
                // Insert new tender
                $sql = "INSERT INTO tender (tenderid, publishedby, brief, location, mode, lastdate) VALUES (:tenderid, :publishedby, :brief, :location, :mode, :lastdate)";
                $stmt = $conn->prepare($sql);
                $param = array(
                    ':tenderid' => $_POST['tenderid'],
                    ':publishedby' => $_POST['publishedby'],
                    ':brief' => $_POST['brief'],
                    ':location' => $_POST['location'],
                    ':mode' => $_POST['mode'],
                    ':lastdate' => $_POST['lastdate']
                );
                $stmt->execute($param);

                // Confirmation message for insert
                 echo "<script>alert('Tender saved successfully')</script>";
                // $_SESSION['message'] = 'Tender saved successfully';
            }

            // Redirect to tender.php after execution
            header("Location: ../../tender.php");
            exit; // Ensure no further code is executed after redirect
        } else {
            // Handle missing fields
            echo "<script>alert('Please fill in all required fields.')</script>";
            header("Location: ../../tender.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'getdata') {
        $sql = "SELECT * FROM tender WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':id' => $_POST['id'],
        );
        $stmt->execute($param);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
        exit; 

    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'status') {
        $sql = "UPDATE tender SET status=:status-status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':status' => 1,
            ':id' => $_POST['id'],
        );
        $stmt->execute($param);
        echo json_encode(array("statusCode" => 200));
        exit; 

    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
        $sql = "SELECT * FROM tender WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $param1 = array(
            ':id' => $_POST['id'],
        );
        $stmt->execute($param1);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdfFile= $result['pdf'];

        $sql = "DELETE FROM tender WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $param = array(
            ':id' => $_POST['id'],
        );
        if($stmt->execute($param))
        {
            $pdfFilePath = '../../assets/img/pdf/' . $pdfFile;
            
            if (file_exists($pdfFilePath)) {
                if (unlink($pdfFilePath)) {
                    echo json_encode(array("statusCode" => 200));
                }
            }
        }
        exit; 
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action']=='upload'){
        //echo"<pre>"; print_r($_POST);
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
          $uploadDir = '../../assets/img/logo/'; // Specify your upload directory
          $filename=uniqid() . basename($_FILES['image']['name']);
          $uploadFile = $uploadDir .$filename;
          
          if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
              
              $sql="UPDATE tender SET image=:image WHERE id=:id";            
              $stmt = $conn->prepare($sql);
                $param = array(
                    ':image' => $filename,
                    ':id' => $_POST['lid'],
                );                              
              if ($stmt->execute($param) === TRUE)

                  echo json_encode(array("statusCode" => 200));
              else
                  echo json_encode(array("statusCode" => 201));
          } else {
            echo json_encode(array("statusCode" => 201));
          }
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action']=='uploadpdf'){
        //echo"<pre>"; print_r($_POST);
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
          $uploadDir = '../../assets/img/pdf/'; // Specify your upload directory
          $filename=uniqid() . basename($_FILES['image']['name']);
          $uploadFile = $uploadDir .$filename;
          
          if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $sql = "SELECT * FROM tender WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $param1 = array(
                ':id' => $_POST['did'],
            );
            $stmt->execute($param1);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdfFile= $result['pdf'];  

            $sql="UPDATE tender SET pdf=:pdf WHERE id=:id ";
              $stmt = $conn->prepare($sql);
                $param = array(
                    ':pdf' => $filename,
                    ':id' => $_POST['did'],
                );
                
                if($stmt->execute($param))
                {
                    $pdfFilePath = '../../assets/img/pdf/' . $pdfFile;
                    
                    if (file_exists($pdfFilePath)) {
                        if (unlink($pdfFilePath)) {
                            echo json_encode(array("statusCode" => 200));
                        }
                    }
                }
              else
                  echo json_encode(array("statusCode" => 201));
          } else {
                    echo json_encode(array("statusCode" => 201));
          }
        }
      }

      if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action']=='uploaddpdf'){
        //echo"<pre>"; print_r($_POST);
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
          $uploadDir = '../../assets/img/dpdf/'; // Specify your upload directory
          $filename=uniqid() . basename($_FILES['image']['name']);
          $uploadFile = $uploadDir .$filename;
          
          if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $sql = "SELECT * FROM tender WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $param1 = array(
                ':id' => $_POST['dtid'],
            );
            $stmt->execute($param1);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdfFile= $result['dpdf'];  

            $sql="UPDATE tender SET dpdf=:dpdf WHERE id=:id ";
              $stmt = $conn->prepare($sql);
                $param = array(
                    ':dpdf' => $filename,
                    ':id' => $_POST['dtid'],
                );
                
                if($stmt->execute($param))
                {
                    $pdfFilePath = '../../assets/img/dpdf/' . $pdfFile;
                    
                    if (file_exists($pdfFilePath)) {
                        if (unlink($pdfFilePath)) {
                            echo json_encode(array("statusCode" => 200));
                        }
                    }
                }
              else
                  echo json_encode(array("statusCode" => 201));
          } else {
                    echo json_encode(array("statusCode" => 201));
          }
        }
      }

 
?>