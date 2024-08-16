<?php
session_start();

// Проверка, если пользователь не авторизован или не администратор, перенаправление на страницу входа
if (!isset($_SESSION['user']) || $_SESSION['user']['TypeID'] != 1) {
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

// Обработка данных после редактирования урока
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateLesson'])) {
    $lessonID = $_POST['lessonID'];
    $name = $_POST['name'];
    $groupID = $_POST['groupID'];
    $teacherID = $_POST['teacherID'];
    $classroomID = $_POST['classroomID'];
    $startDateTime = $_POST['startDateTime'];
    $endDateTime = $_POST['endDateTime'];

    $stmt = $conn->prepare("UPDATE Lessons SET Name=?, GroupID=?, TeacherID=?, ClassroomID=?, StartDateTime=?, EndDateTime=? WHERE LessonID=?");
    $stmt->bind_param("siiissi", $name, $groupID, $teacherID, $classroomID, $startDateTime, $endDateTime, $lessonID);

    if ($stmt->execute() === TRUE) {
        header("Location: view_schedule.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Получение данных урока для редактирования
if (isset($_GET['id'])) {
    $lessonID = $_GET['id'];
    $sql = "SELECT * FROM Lessons WHERE LessonID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lessonID);
    $stmt->execute();
    $result = $stmt->get_result();
    $lesson = $result->fetch_assoc();
    $stmt->close();
}

// Получение списка групп, учителей и классов для формы
$groups = $conn->query("SELECT * FROM `groups`");
$teachers = $conn->query("SELECT * FROM Teachers");
$classrooms = $conn->query("SELECT * FROM Classrooms");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson</title>
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form h2 {
            margin-top: 0;
        }

        .form input, .form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form button {
            width: 100%;
            padding: 10px;
            background-color: #0288d1;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form button:hover {
            background-color: #0277bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Lesson</h2>
        <?php if (isset($lesson)): ?>
            <form method="POST" action="">
                <input type="hidden" name="lessonID" value="<?= htmlspecialchars($lesson['LessonID']) ?>">
                <input type="text" name="name" placeholder="Lesson Name" value="<?= htmlspecialchars($lesson['Name']) ?>" required>
                <label for="groupID">Group:</label>
                <select name="groupID" required>
                    <?php while ($group = $groups->fetch_assoc()): ?>
                        <option value="<?= $group['GroupID'] ?>" <?= $group['GroupID'] == $lesson['GroupID'] ? 'selected' : '' ?>><?= $group['Name'] ?></option>
                    <?php endwhile; ?>
                </select>
                
                <label for="teacherID">Teacher:</label>
                <select name="teacherID" required>
                    <?php while ($teacher = $teachers->fetch_assoc()): ?>
                        <option value="<?= $teacher['TeacherID'] ?>" <?= $teacher['TeacherID'] == $lesson['TeacherID'] ? 'selected' : '' ?>><?= $teacher['FirstName'] ?> <?= $teacher['LastName'] ?></option>
                    <?php endwhile; ?>
                </select>
                
                <label for="classroomID">Classroom:</label>
                <select name="classroomID" required>
                    <?php while ($classroom = $classrooms->fetch_assoc()): ?>
                        <option value="<?= $classroom['ClassroomID'] ?>" <?= $classroom['ClassroomID'] == $lesson['ClassroomID'] ? 'selected' : '' ?>><?= $classroom['Name'] ?> (<?= $classroom['Number'] ?>)</option>
                    <?php endwhile; ?>
                </select>
                
                <label for="startDateTime">Start Date and Time:</label>
                <input type="datetime-local" name="startDateTime" value="<?= date('Y-m-d\TH:i', strtotime($lesson['StartDateTime'])) ?>" required>
                
                <label for="endDateTime">End Date and Time:</label>
                <input type="datetime-local" name="endDateTime" value="<?= date('Y-m-d\TH:i', strtotime($lesson['EndDateTime'])) ?>" required>

                <button type="submit" name="updateLesson">Update Lesson</button>
            </form>
        <?php else: ?>
            <p>Lesson not found.</p>
        <?php endif; ?>
        <a href="view_schedule.php">Back to Schedule</a>
    </div>
</body>
</html>