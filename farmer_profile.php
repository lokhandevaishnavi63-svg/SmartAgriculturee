<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "smart_agriculture");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $previous_crop = $_POST["previous_crop"];
    $land_area = $_POST["land_area"];
    $crop = $_POST["crop"];
    $soil_type = $_POST["soil_type"];
    $irrigation = $_POST["irrigation"];
    $category = $_POST["category"];

    $sql = "INSERT INTO farmer_profiles
            (user_id, land_area, crop, soil_type, irrigation, category)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "idssss",
        $user_id,
        $land_area,
        $crop,
        $soil_type,
        $irrigation,
        $category
    );

    if ($stmt->execute()) {
        $message = "Farming details saved successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Farming Details</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f1f8e9;
    margin: 0;
}

.container {
    width: 500px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #aaa;
}

h2 {
    text-align: center;
    color: #2e7d32;
}

label {
    font-weight: bold;
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

<h2>Farmer Farming Details</h2>

<?php if ($message != "") { ?>

<div class="message">
    <?php echo $message; ?>
</div>

<?php } ?>

<form method="POST">

<label>Previous Crop</label>

<input type="text"
       name="previous_crop"
       placeholder="Example: Rice"
       required>


<label>Land Area (Acres)</label>

<input type="number"
       name="land_area"
       step="0.01"
       placeholder="Example: 2.5"
       required>


<label>Current / Planned Crop</label>

<select name="crop" required>

<option value="">Select Crop</option>
<option value="Rice">Rice</option>
<option value="Wheat">Wheat</option>
<option value="Soybean">Soybean</option>
<option value="Cotton">Cotton</option>
<option value="Onion">Onion</option>
<option value="Sugarcane">Sugarcane</option>
<option value="Other">Other</option>

</select>


<label>Soil Type</label>

<select name="soil_type" required>

<option value="">Select Soil Type</option>
<option value="Black Soil">Black Soil</option>
<option value="Red Soil">Red Soil</option>
<option value="Laterite Soil">Laterite Soil</option>
<option value="Alluvial Soil">Alluvial Soil</option>
<option value="Other">Other</option>

</select>


<label>Irrigation Facility</label>

<select name="irrigation" required>

<option value="">Select Irrigation</option>
<option value="Rainfed">Rainfed</option>
<option value="Well">Well</option>
<option value="Borewell">Borewell</option>
<option value="Canal">Canal</option>
<option value="Drip">Drip Irrigation</option>
<option value="Sprinkler">Sprinkler</option>
<option value="Farm Pond">Farm Pond</option>

</select>


<label>Farmer Category</label>

<select name="category" required>

<option value="">Select Category</option>
<option value="General">General</option>
<option value="SC">SC</option>
<option value="ST">ST</option>
<option value="OBC">OBC</option>
<option value="Other">Other</option>

</select>


<button type="submit">
    Save Farming Details
</button>

</form>

</div>

</body>

</html>