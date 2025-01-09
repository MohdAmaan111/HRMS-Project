<?php
require('config.php');

$sql = "UPDATE userlog SET logouttime = :logouttime WHERE sessionid = :sessionid";
$stmt = $conn->prepare($sql);
$param = array(
    ':sessionid' => $_SESSION['sessionid'],
    ':logouttime' => date("Y-m-d H:i:s")
);

if ($stmt->execute($param)) {
    session_unset();
    session_destroy();
    header('Location:../index.php');
}
