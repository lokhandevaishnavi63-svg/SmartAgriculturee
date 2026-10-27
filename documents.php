<?php

$conn = new mysqli("localhost", "root", "", "smart_agriculture");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_GET["id"])) {
    die("Scheme not selected.");
}

$scheme_id = $_GET["id"];

$sql = "SELECT scheme_name FROM schemes WHERE scheme_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $scheme_id);
$stmt->execute();

$scheme_result = $stmt->get_result();

if ($scheme_result->num_rows == 0) {
    die("Scheme not found.");
}

$scheme = $scheme_result->fetch_assoc();


$sql2 = "SELECT document_name
         FROM documents
         WHERE scheme_id = ?";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $scheme_id);
$stmt2->execute();

$documents = $stmt2->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Required Documents</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f1f8e9;
}

.container {
    width: 600px;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
}

h1 {
    color: #2e7d32;
}

li {
    padding: 10px;
    margin: 5px;
    background: #f5f5f5;
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

<h1>📄 Required Documents</h1>

<h2>
<?php echo htmlspecialchars($scheme["scheme_name"]); ?>
</h2>

<?php if ($documents->num_rows > 0) { ?>

<ul>

<?php while ($document = $documents->fetch_assoc()) { ?>

<li>
📄 <?php echo htmlspecialchars($document["document_name"]); ?>
</li>

<?php } ?>

</ul>

<?php } else { ?>

<p>No documents information available.</p>

<?php } ?>

<a class="back"
   href="recommendation.php">
← Back to Recommendations
</a>

</div>

</body>

</html>