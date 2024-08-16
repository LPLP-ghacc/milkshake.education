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

// Обработка создания преподавателей
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createTeachers'])) {
    $numTeachers = intval($_POST['numTeachers']);

    for ($i = 1; $i <= $numTeachers; $i++) {
        $firstName = trim($_POST['firstName' . $i]);
        $lastName = trim($_POST['lastName' . $i]);
        $middleName = trim($_POST['middleName' . $i]);

        $stmt = $conn->prepare("INSERT INTO Teachers (FirstName, LastName, MiddleName) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $firstName, $lastName, $middleName);

        if (!$stmt->execute()) {
            $message = "Error: " . $stmt->error;
            break;
        }

        $stmt->close();
    }

    if (!isset($message)) {
        $message = "Teachers created successfully";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mass Add Teachers</title>
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

        .form input, .form button {
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
        <h2>Add Teachers</h2>
        <form method="POST" action="">
            <label for="numTeachers">Number of Teachers:</label>
            <input type="number" name="numTeachers" min="1" required>

            <button type="submit" name="generateFields">Generate Fields</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generateFields'])): ?>
            <form method="POST" action="">
                <input type="hidden" name="numTeachers" value="<?= htmlspecialchars($_POST['numTeachers']) ?>">
                <?php for ($i = 1; $i <= intval($_POST['numTeachers']); $i++): ?>
                    <h3>Teacher <?= $i ?></h3>
                    <input type="text" name="firstName<?= $i ?>" placeholder="First Name" required>
                    <input type="text" name="lastName<?= $i ?>" placeholder="Last Name" required>
                    <input type="text" name="middleName<?= $i ?>" placeholder="Middle Name">
                <?php endfor; ?>
                <button type="submit" name="createTeachers">Create Teachers</button>
            </form>
        <?php endif; ?>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>