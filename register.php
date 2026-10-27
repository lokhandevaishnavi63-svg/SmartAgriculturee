<?php
$conn = new mysqli("localhost", "root", "", "smart_agriculture");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $age = $_POST["age"];
    $mobile = $_POST["mobile"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $region = $_POST["region"];
    $district = $_POST["district"];

    $sql = "INSERT INTO users (name, age, mobile, password, region, district)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissss", $name, $age, $mobile, $password, $region, $district);

    if ($stmt->execute()) {
        $message = "Registration successful!";
    } else {
        $message = "Registration failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f8e9;
            margin: 0;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
        }

        input, select {
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
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Farmer Registration</h2>

    <?php if ($message != "") { ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Age</label>
        <input type="number" name="age" required>

        <label>Mobile Number</label>
        <input type="text" name="mobile" maxlength="10" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Region</label>
        <select name="region" required>
            <option value="">Select Region</option>
            <option value="Konkan">Konkan</option>
            <option value="Marathwada">Marathwada</option>
        </select>

        <label>District</label>
        <input type="text" name="district" required>

        <button type="submit">Register</button>

    </form>

</div>

</body>
</html>