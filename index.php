<?php
include 'main.php';

if (isset($_SESSION['logged_in'])) {
    header('Location: home.php');
    exit();
}


if (isset($_COOKIE['remember_me']) && !empty($_COOKIE['remember_me'])) {
	$stmt = $con->prepare('SELECT id, username, role FROM accounts WHERE rememberme = ?');
	$stmt->bind_param('s', $_COOKIE['remember_me']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
		$stmt->bind_result($id, $username, $role);
		$stmt->fetch();
		$stmt->close();
		session_regenerate_id();
		$_SESSION['logged_in'] = TRUE;
		$_SESSION['name'] = $username;
		$_SESSION['id'] = $id;
		$_SESSION['role'] = $role;
		$date = date('Y-m-d\TH:i:s');
		$stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
		$stmt->bind_param('si', $date, $id);
		$stmt->execute();
		$stmt->close();
		header('Location: home.php');
		exit;
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Login</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
		<div class="login">

			<h1>Login</h1>

			<div class="links">
				<a href="index.php" class="active">Login</a>
				<a href="register.php">Register</a>
			</div>

			<form action="authenticate.php" method="post">

				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>

				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>

				<label id="rememberme">
					<input type="checkbox" name="rememberme">Remember me
				</label>

				<div class="msg"></div>

				<input type="submit" value="Login">

			</form>

		</div>

		<script>
		// AJAX code
		let loginForm = document.querySelector(".login form");
		loginForm.onsubmit = event => {
			event.preventDefault();
			fetch(loginForm.action, { method: 'POST', body: new FormData(loginForm) }).then(response => response.text()).then(result => {
				if (result.toLowerCase().includes("success")) {
					window.location.href = "home.php";
				} else {
					document.querySelector(".msg").innerHTML = result;
				}
			});
		};
		</script>
	</body>
</html>
