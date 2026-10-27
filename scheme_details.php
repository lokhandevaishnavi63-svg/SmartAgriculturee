<?php
$conn = new mysqli("localhost", "root", "", "smart_agriculture");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_GET["id"])) {
    die("Scheme not selected.");
}

$scheme_id = $_GET["id"];

$sql = "SELECT * FROM schemes WHERE scheme_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $scheme_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Scheme not found.");
}

$scheme = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>

<title>Scheme Details</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f1f8e9;
    margin: 0;
}

.container {
    width: 80%;
    max-width: 800px;
    margin: 50px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 10px #ccc;
}

h1 {
    color: #2e7d32;
}

h3 {
    color: #388e3c;
    margin-top: 25px;
}

.back {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 15px;
    background: #2e7d32;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

</style>

</head>

<body>

<div class="container">

<div class="card">

<h1>
<?php echo htmlspecialchars($scheme["scheme_name"]); ?>
</h1>

<h3>Department</h3>

<p>
<?php echo htmlspecialchars($scheme["department"]); ?>
</p>

<h3>Description</h3>

<p>
<?php echo htmlspecialchars($scheme["description"]); ?>
</p>

<h3>Benefits</h3>

<p>
<?php echo htmlspecialchars($scheme["benefits"]); ?>
</p>

<h3>Last Date</h3>

<p>
<?php echo htmlspecialchars($scheme["last_date"]); ?>
</p>

<h3>Applicable Region</h3>

<p>
<?php echo htmlspecialchars($scheme["region"]); ?>
</p>

<a class="back" href="recommendation.php">
← Back to Recommendations
</a>

</div>

</div>

</body>

</html>