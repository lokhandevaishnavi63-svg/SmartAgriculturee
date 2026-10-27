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

<title>Application Procedure</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f1f8e9;
}

.container {
    width: 700px;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
}

h1 {
    color: #2e7d32;
}

h2 {
    color: #388e3c;
}

.procedure {
    background: #f5f5f5;
    padding: 20px;
    line-height: 1.7;
}

.button {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 18px;
    background: #2e7d32;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.back {
    display: block;
    margin-top: 15px;
}

</style>

</head>

<body>

<div class="container">

<h1>📝 Application Procedure</h1>

<h2>
<?php echo htmlspecialchars($scheme["scheme_name"]); ?>
</h2>

<div class="procedure">

<?php echo nl2br(htmlspecialchars($scheme["application_procedure"])); ?>

</div>

<?php if (!empty($scheme["official_link"])) { ?>

<a class="button"
   href="<?php echo htmlspecialchars($scheme["official_link"]); ?>"
   target="_blank">
   Visit Official Website
</a>

<?php } ?>

<a class="back"
   href="recommendation.php">
   ← Back to Recommendations
</a>

</div>

</body>

</html>