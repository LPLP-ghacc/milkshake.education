<?php
session_start();

// Проверка, если пользователь не авторизован, перенаправление на страницу входа
if (!isset($_SESSION['user'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ms.education";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Извлечение данных расписания
$sql = "SELECT 
          lessons.LessonID,
          lessons.Name AS LessonName, 
          `groups`.Name AS GroupName, 
          teachers.FirstName AS TeacherFirstName, 
          teachers.LastName AS TeacherLastName, 
          classrooms.Name AS ClassroomName, 
          classrooms.Number AS ClassroomNumber, 
          lessons.StartDateTime, 
          lessons.EndDateTime 
        FROM lessons 
        JOIN `groups` ON lessons.GroupID = `groups`.GroupID 
        JOIN teachers ON lessons.TeacherID = teachers.TeacherID 
        JOIN classrooms ON lessons.ClassroomID = classrooms.ClassroomID 
        LIMIT 0, 25";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>
    <style>
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        a.edit {
            background-color: #0288d1;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        a.edit:hover {
            background-color: #0277bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Schedule</h2>
        <table>
            <thead>
                <tr>
                    <th>Lesson Name</th>
                    <th>Group</th>
                    <th>Teacher</th>
                    <th>Classroom</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['LessonName']) ?></td>
                            <td><?= htmlspecialchars($row['GroupName']) ?></td>
                            <td><?= htmlspecialchars($row['TeacherFirstName']) ?> <?= htmlspecialchars($row['TeacherLastName']) ?></td>
                            <td><?= htmlspecialchars($row['ClassroomName']) ?> (<?= htmlspecialchars($row['ClassroomNumber']) ?>)</td>
                            <td><?= htmlspecialchars($row['StartDateTime']) ?></td>
                            <td><?= htmlspecialchars($row['EndDateTime']) ?></td>
                            <td><a class="edit" href="edit_lesson.php?id=<?= $row['LessonID'] ?>">Редактировать</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No lessons scheduled for this period.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="admin.dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>