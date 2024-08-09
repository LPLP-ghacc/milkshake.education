<?php
session_start();

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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createLesson'])) {
    $name = $_POST['name'];
    $groupID = $_POST['groupID'];
    $teacherID = $_POST['teacherID'];
    $classroomID = $_POST['classroomID'];
    $startDateTime = $_POST['startDateTime'];
    $endDateTime = $_POST['endDateTime'];

    $stmt = $conn->prepare("INSERT INTO Lessons (Name, GroupID, TeacherID, ClassroomID, StartDateTime, EndDateTime) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiss", $name, $groupID, $teacherID, $classroomID, $startDateTime, $endDateTime);

    if ($stmt->execute() === TRUE) {
        $message = "New lesson created successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}


$groups = $conn->query("SELECT * FROM `groups`");
$teachers = $conn->query("SELECT * FROM `teachers`");
$classrooms = $conn->query("SELECT * FROM `classrooms`");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
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
        <h2>Create Lesson</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Lesson Name" required>
            <label for="groupID">Group:</label>
            <select name="groupID" required>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <option value="<?= $group['GroupID'] ?>"><?= $group['Name'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="teacherID">Teacher:</label>
            <select name="teacherID" required>
                <?php while ($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?= $teacher['TeacherID'] ?>"><?= $teacher['FirstName'] ?> <?= $teacher['LastName'] ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="classroomID">Classroom:</label>
            <select name="classroomID" required>
                <?php while ($classroom = $classrooms->fetch_assoc()): ?>
                    <option value="<?= $classroom['ClassroomID'] ?>"><?= $classroom['Name'] ?> (<?= $classroom['Number'] ?>)</option>
                <?php endwhile; ?>
            </select>
            
            <label for="startDateTime">Start Date and Time:</label>
            <input type="datetime-local" name="startDateTime" required>
            
            <label for="endDateTime">End Date and Time:</label>
            <input type="datetime-local" name="endDateTime" required>

            <button type="submit" name="createLesson">Create Lesson</button>
        </form>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>