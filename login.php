<?php
session_start();

$conn = new mysqli("localhost", "root", "", "smart_agriculture");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mobile = $_POST["mobile"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE mobile = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["name"] = $user["name"];

            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Incorrect password!";
        }

    } else {
        $message = "Mobile number not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Farmer Login</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f1f8e9;
    margin: 0;
}

.container {
    width: 380px;
    margin: 70px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #aaa;
}

h2 {
    text-align: center;
    color: #2e7d32;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 12px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #1b5e20;
}

.message {
    text-align: center;
    color: red;
    margin-bottom: 15px;
}

.register {
    text-align: center;
    margin-top: 15px;
}

</style>

</head>

<body>

<div class="container">

<h2>Farmer Login</h2>

<?php if ($message != "") { ?>

<div class="message">
    <?php echo $message; ?>
</div>

<?php } ?>

<form method="POST">

<label>Mobile Number</label>

<input type="text"
       name="mobile"
       maxlength="10"
       required>

<label>Password</label>

<input type="password"
       name="password"
       required>

<button type="submit">Login</button>

</form>

<div class="register">
    Don't have an account?
    <a href="register.php">Register Here</a>
</div>

</div>

</body>
</html>