<?php
$temp_pass = "admin";

session_start();
if(isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
$pass = $_POST['password'] ?? null;
if(isset($pass) && $pass == $temp_pass) {
    $_SESSION['user'] = $pass;
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method='POST' action='login.php'>
        <input type="password" name="password" placeholder="Haslo">
        <input type="submit" value="Zaloguj sie">
    </form>
</body>
</html>