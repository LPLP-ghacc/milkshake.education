

<?php
session_start();

//бля я думаю проблема в нейминге, походу нельзяточки ебаные ставить


//... можно

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ms.education";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loginBtn'])) {
    $login = $_POST['login'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE Login='$login' AND TypeID=(SELECT TypeID FROM usertypes WHERE TypeName='admin')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($pass == $row['Password']) {
            $_SESSION['user'] = $row;
            header("Location: admin.dashboard.php");
            exit();
        } else {
            $message = "Invalid password";
        }
    } else {
        $message = "No admin found with that login";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form h2 {
            margin-top: 0;
        }

        .form input {
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
        <h2>Admin Login</h2>
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Login" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="loginBtn">Login</button>
        </form>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
</body>
</html>