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

// Обработка создания студентов
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createStudents'])) {
    $groupID = $_POST['groupID'];
    $numStudents = intval($_POST['numStudents']);

    for ($i = 1; $i <= $numStudents; $i++) {
        $firstName = trim($_POST['firstName' . $i]);
        $lastName = trim($_POST['lastName' . $i]);
        $middleName = trim($_POST['middleName' . $i]);

        $stmt = $conn->prepare("INSERT INTO Students (FirstName, LastName, MiddleName, GroupID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $firstName, $lastName, $middleName, $groupID);

        if (!$stmt->execute()) {
            $message = "Error: " . $stmt->error;
            break;
        }

        $stmt->close();
    }

    if (!isset($message)) {
        $message = "Students created successfully";
    }
}

// Получение списка групп для формы
$groups = $conn->query("SELECT * FROM `groups`");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mass Add Students</title>
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

        .form input, .form select, .form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form button {
            background-color: #0288d1;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .form button:hover {
            background-color: #0277bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Students</h2>
        <form method="POST" action="">
            <label for="groupID">Group:</label>
            <select name="groupID" required>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <option value="<?= $group['GroupID'] ?>"><?= $group['Name'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="numStudents">Number of Students:</label>
            <input type="number" name="numStudents" min="1" required>

            <button type="submit" name="generateFields">Generate Fields</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generateFields'])): ?>
            <form method="POST" action="">
                <input type="hidden" name="groupID" value="<?= htmlspecialchars($_POST['groupID']) ?>">
                <input type="hidden" name="numStudents" value="<?= htmlspecialchars($_POST['numStudents']) ?>">
                <?php for ($i = 1; $i <= intval($_POST['numStudents']); $i++): ?>
                    <h3>Student <?= $i ?></h3>
                    <input type="text" name="firstName<?= $i ?>" placeholder="First Name" required>
                    <input type="text" name="lastName<?= $i ?>" placeholder="Last Name" required>
                    <input type="text" name="middleName<?= $i ?>" placeholder="Middle Name">
                <?php endfor; ?>
                <button type="submit" name="createStudents">Create Students</button>
            </form>
        <?php endif; ?>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>