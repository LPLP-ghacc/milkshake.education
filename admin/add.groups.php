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

// Обработка создания групп
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createGroups'])) {
    $numGroups = intval($_POST['numGroups']);

    for ($i = 1; $i <= $numGroups; $i++) {
        $groupName = trim($_POST['groupName' . $i]);

        if (!empty($groupName)) {
            $stmt = $conn->prepare("INSERT INTO `groups` (Name) VALUES (?)");
            $stmt->bind_param("s", $groupName);

            if (!$stmt->execute()) {
                $message = "Error: " . $stmt->error;
                break;
            }

            $stmt->close();
        }
    }

    if (!isset($message)) {
        $message = "Groups created successfully";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mass Add Groups</title>
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
        <h2>Add Groups</h2>
        <form method="POST" action="">
            <label for="numGroups">Number of Groups:</label>
            <input type="number" name="numGroups" min="1" required>

            <button type="submit" name="generateFields">Generate Fields</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generateFields'])): ?>
            <form method="POST" action="">
                <input type="hidden" name="numGroups" value="<?= htmlspecialchars($_POST['numGroups']) ?>">
                <?php for ($i = 1; $i <= intval($_POST['numGroups']); $i++): ?>
                    <h3>Group <?= $i ?></h3>
                    <input type="text" name="groupName<?= $i ?>" placeholder="Group Name" required>
                <?php endfor; ?>
                <button type="submit" name="createGroups">Create Groups</button>
            </form>
        <?php endif; ?>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>