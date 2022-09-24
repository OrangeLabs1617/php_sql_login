<?php
include_once 'config.php';
session_start();
$con = mysqli_connect(db_host, db_user, db_name);

if(mysqli_connect_errno()) 
    exit('Failed to connect to MySQL: ' .mysqli_connect_errno());

mysqli_set_charset($con, db_charset);

function check_loggedin($con, $redirect = 'index.php') {

    if(isset($_COOKIE['remember_me']) && !empty($_COOKIE) && !isset(['logged_in'])){
        $stmt = $con->prepare('SELECT id, username, role FROM accounts WHERE rememberme = ?');
		$stmt->bind_param('s', $_COOKIE['rememberme']);
		$stmt->execute();
		$stmt->store_result();

        if ($stmt->num_rows > 0) {

            $stmt->bind_result($id, $username, $role);
            $stmt->featch();
            $stmt->close();
            session_regenerate_id();
            $_SESSION['logged_in'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $role;
            $date = date('Y-m-d\TH:i:s');
            $stmt = $con->pre('UPDATE accounts SET last_seen = ? WHERE id = ?');
            $stmt->bind_param('si', $date, $id);
            $stmt->execute();
            $stmt->close();
        } 
        
        else{
            header('Location: ' .$redirect);
            exit;
        }

    else if(!isset($_SESSION['logged_in'])) {
        header('Location: ' .$redirect);
        exit;
    }
}




?>