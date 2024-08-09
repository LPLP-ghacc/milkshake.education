<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['TypeID'] != 1) {
    header("Location: admin.login.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['FirstName']); ?></h1>
    <p>You are logged in as Admin</p>
    <ul>
        <li><a href="admin.create.user.php">Create New User</a></li>
        <li><a href="../schedule/manage.schedule.php">Manage Schedule</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>