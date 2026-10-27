<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "smart_agriculture"
);

if ($conn->connect_error) {
    die("Database connection failed!");
}

$user_id = $_SESSION["user_id"];


/* Get farmer and farming details */

$sql = "
SELECT
    u.region,
    u.district,
    f.previous_crop,
    f.land_area,
    f.crop,
    f.soil_type,
    f.irrigation,
    f.category
FROM users u
JOIN farmer_profiles f
ON u.user_id = f.user_id
WHERE u.user_id = ?
ORDER BY f.profile_id DESC
LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$farmer = $result->fetch_assoc();


if (!$farmer) {

    echo "<h2>Farming details not found.</h2>";
    echo "<p>Please enter your farming details first.</p>";
    echo '<a href="farmer_profile.php">Enter Farming Details</a>';

    exit();
}


/* Farmer details */

$region = $farmer["region"];
$crop = $farmer["crop"];
$land_area = $farmer["land_area"];
$irrigation = $farmer["irrigation"];
$category = $farmer["category"];


/* Find suitable schemes */

$sql = "
SELECT DISTINCT
    s.scheme_id,
    s.scheme_name,
    s.department,
    s.description,
    s.benefits,
    s.last_date,
    s.region

FROM schemes s

JOIN eligibility e
ON s.scheme_id = e.scheme_id

WHERE
    FIND_IN_SET(?, e.region)

    AND
    (e.crop = 'All' OR e.crop = ?)

    AND
    ? BETWEEN e.min_land AND e.max_land

    AND
    (e.irrigation = 'All' OR e.irrigation = ?)

    AND
    (e.category = 'All' OR e.category = ?)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdss",
    $region,
    $crop,
    $land_area,
    $irrigation,
    $category
);

$stmt->execute();

$schemes = $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

    <title>Recommended Schemes</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f8f4;
            margin: 0;
        }


        /* Header */

        header {
            background: #2e7d32;
            color: white;

            padding: 18px 50px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }


        /* Main */

        .container {
            width: 90%;
            max-width: 1100px;

            margin: 35px auto;
        }


        h1 {
            color: #1b5e20;
        }


        /* Farmer Information */

        .farmer-info {
            background: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

            margin-bottom: 30px;
        }

        .farmer-info h2 {
            color: #2e7d32;
        }

        .farmer-info p {
            margin: 8px 0;
        }


        /* Scheme Card */

        .scheme-card {
            background: white;

            padding: 25px;

            margin-bottom: 25px;

            border-radius: 10px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }

        .scheme-card h2 {
            color: #2e7d32;

            margin-top: 0;
        }

        .scheme-card p {
            line-height: 1.6;
        }


        /* Buttons */

        .buttons {
            margin-top: 20px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }

        .buttons a {
            display: inline-block;

            padding: 10px 16px;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            background: #2e7d32;
        }

        .buttons a:hover {
            background: #1b5e20;
        }


        .details-btn {
            background: #1976d2 !important;
        }

        .documents-btn {
            background: #ef6c00 !important;
        }

        .application-btn {
            background: #6a1b9a !important;
        }


        /* No Scheme */

        .no-scheme {
            background: #fff3cd;

            padding: 20px;

            border-radius: 8px;

            color: #856404;
        }


        /* Footer */

        footer {
            margin-top: 50px;

            background: #1b5e20;

            color: white;

            text-align: center;

            padding: 20px;
        }

    </style>

</head>


<body>


<!-- Header -->

<header>

    <strong>
        🌱 Smart Agriculture
    </strong>

    <div>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="index.php">
            Home
        </a>

    </div>

</header>


<div class="container">


    <h1>
        🔍 Recommended Agriculture Schemes
    </h1>


    <!-- Farmer Information -->

    <div class="farmer-info">

        <h2>
            👨‍🌾 Your Farming Details
        </h2>

        <p>
            <strong>Region:</strong>
            <?php echo htmlspecialchars($region); ?>
        </p>

        <p>
            <strong>District:</strong>
            <?php echo htmlspecialchars($farmer["district"]); ?>
        </p>

        <p>
            <strong>Previous Crop:</strong>
            <?php echo htmlspecialchars($farmer["previous_crop"]); ?>
        </p>

        <p>
            <strong>Land Area:</strong>
            <?php echo htmlspecialchars($land_area); ?>
        </p>

        <p>
            <strong>Current Crop:</strong>
            <?php echo htmlspecialchars($crop); ?>
        </p>

        <p>
            <strong>Soil Type:</strong>
            <?php echo htmlspecialchars($farmer["soil_type"]); ?>
        </p>

        <p>
            <strong>Irrigation:</strong>
            <?php echo htmlspecialchars($irrigation); ?>
        </p>

        <p>
            <strong>Category:</strong>
            <?php echo htmlspecialchars($category); ?>
        </p>

    </div>


    <!-- Recommended Schemes -->

    <?php if ($schemes->num_rows > 0) { ?>


        <h2>
            Suitable Schemes For You
        </h2>


        <?php while ($scheme = $schemes->fetch_assoc()) { ?>


            <div class="scheme-card">


                <h2>
                    🌾
                    <?php echo htmlspecialchars($scheme["scheme_name"]); ?>
                </h2>


                <p>

                    <strong>Department:</strong>

                    <?php echo htmlspecialchars($scheme["department"]); ?>

                </p>


                <p>

                    <strong>Description:</strong>

                    <?php echo htmlspecialchars($scheme["description"]); ?>

                </p>


                <p>

                    <strong>Benefits:</strong>

                    <?php echo htmlspecialchars($scheme["benefits"]); ?>

                </p>


                <p>

                    <strong>Applicable Region:</strong>

                    <?php echo htmlspecialchars($scheme["region"]); ?>

                </p>


                <p>

                    <strong>Last Date:</strong>

                    <?php

                    if (!empty($scheme["last_date"])) {

                        echo date(
                            "d-m-Y",
                            strtotime($scheme["last_date"])
                        );

                    } else {

                        echo "Not specified";

                    }

                    ?>

                </p>


                <!-- Buttons -->

                <div class="buttons">


                    <a
                        class="details-btn"
                        href="scheme_details.php?id=<?php echo $scheme["scheme_id"]; ?>"
                    >
                        📋 View Details
                    </a>


                    <a
                        class="documents-btn"
                        href="documents.php?id=<?php echo $scheme["scheme_id"]; ?>"
                    >
                        📄 Required Documents
                    </a>


                    <a
                        class="application-btn"
                        href="application.php?id=<?php echo $scheme["scheme_id"]; ?>"
                    >
                        📝 Application Guide
                    </a>


                </div>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="no-scheme">

            <h2>
                No Suitable Scheme Found
            </h2>

            <p>
                Currently, no scheme matches your entered
                farming details.
            </p>

            <p>
                Please check your region, crop, land area,
                irrigation and category details.
            </p>

        </div>


    <?php } ?>


</div>


<footer>

    <p>
        Smart Agriculture Scheme Recommender & Eligibility Checker
    </p>

    <p>
        Mini Project | B.Sc. Computer Science
    </p>

</footer>


</body>

</html>