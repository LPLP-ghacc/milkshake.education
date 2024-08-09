<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['TypeID'] != 1) {
    header("Location: admin.login.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createUser'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $login = $_POST['login'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $typeID = $_POST['typeID'];

    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password, TypeID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $firstName, $lastName, $login, $pass, $typeID);

    if ($stmt->execute() === TRUE) {
        $message = "New user created successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$userTypes = $conn->query("SELECT * FROM UserTypes");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
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
        <h2>Create User</h2>
        <form method="POST" action="">
            <input type="text" name="firstName" placeholder="First Name" required>
            <input type="text" name="lastName" placeholder="Last Name" required>
            <input type="text" name="login" placeholder="Login" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="typeID" required>
                <?php while ($type = $userTypes->fetch_assoc()): ?>
                    <option value="<?= $type['TypeID'] ?>"><?= $type['TypeName'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="createUser">Create User</button>
        </form>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>