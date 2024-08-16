<?php
session_start();

// Проверка, авторизован ли администратор
if (!isset($_SESSION['user']) || $_SESSION['user']['TypeID'] != 1) {
    header("Location: admin_login.php");
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
        <li><a href="manage.schedule.php">Manage Schedule</a></li>
        <li><a href="view.schedule.php">View Schedule</a></li>
        <li><a href="add.students.php">Add Students</a></li>
        <li><a href="add.teachers.php">Add Teachers</a></li>
        <li><a href="add.groups.php">Add Groups</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>