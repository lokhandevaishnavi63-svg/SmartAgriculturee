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


/* Get farmer details */

$stmt = $conn->prepare(
    "SELECT name, age, mobile, region, district
     FROM users
     WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Farmer Dashboard</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f8f4;

            color: #333;
        }


        /* ================= HEADER ================= */

        header {
            background: #2e7d32;

            color: white;

            padding: 18px 50px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .logo {
            font-size: 23px;

            font-weight: bold;
        }

        .header-links {
            display: flex;

            gap: 10px;
        }

        .header-links a {
            color: white;

            text-decoration: none;

            background: #1b5e20;

            padding: 9px 16px;

            border-radius: 5px;
        }

        .header-links a:hover {
            background: #145218;
        }


        /* ================= MAIN ================= */

        .container {
            width: 90%;

            max-width: 1100px;

            margin: 35px auto;
        }


        /* ================= WELCOME ================= */

        .welcome {
            background: #e8f5e9;

            padding: 28px;

            border-radius: 12px;

            margin-bottom: 30px;

            border-left: 5px solid #2e7d32;
        }

        .welcome h1 {
            margin-top: 0;

            color: #1b5e20;

            font-size: 30px;
        }

        .welcome p {
            font-size: 16px;

            line-height: 1.6;
        }

        .farmer-info {
            margin-top: 15px;

            font-size: 16px;
        }


        /* ================= DASHBOARD TITLE ================= */

        .section-title {
            color: #1b5e20;

            margin-bottom: 20px;

            font-size: 25px;
        }


        /* ================= CARDS ================= */

        .card-container {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 25px;
        }

        .card {
            background: white;

            padding: 30px;

            border-radius: 12px;

            text-align: center;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);

            box-shadow: 0 6px 15px rgba(0,0,0,0.17);
        }

        .card-icon {
            font-size: 50px;

            margin-bottom: 10px;
        }

        .card h2 {
            color: #2e7d32;

            margin: 10px 0;
        }

        .card p {
            color: #555;

            line-height: 1.6;

            min-height: 50px;
        }

        .card a {
            display: inline-block;

            background: #2e7d32;

            color: white;

            text-decoration: none;

            padding: 11px 22px;

            border-radius: 5px;

            margin-top: 10px;

            font-weight: bold;
        }

        .card a:hover {
            background: #1b5e20;
        }


        /* Different button colors */

        .profile-btn {
            background: #1976d2 !important;
        }

        .farming-btn {
            background: #388e3c !important;
        }

        .eligibility-btn {
            background: #ef6c00 !important;
        }

        .scheme-btn {
            background: #6a1b9a !important;
        }


        /* ================= QUICK INFO ================= */

        .quick-info {
            margin-top: 35px;

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.10);
        }

        .quick-info h2 {
            color: #2e7d32;

            margin-top: 0;
        }

        .quick-info ul {
            line-height: 2;

            padding-left: 20px;
        }


        /* ================= FOOTER ================= */

        footer {
            background: #1b5e20;

            color: white;

            text-align: center;

            padding: 22px;

            margin-top: 50px;
        }

        footer p {
            margin: 5px;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 700px) {

            header {
                padding: 15px 20px;

                flex-direction: column;

                gap: 15px;
            }

            .container {
                width: 94%;
            }

            .card-container {
                grid-template-columns: 1fr;
            }

            .welcome h1 {
                font-size: 25px;
            }

        }

    </style>

</head>


<body>


<!-- ================= HEADER ================= -->

<header>

    <div class="logo">
        🌱 Smart Agriculture
    </div>


    <div class="header-links">

        <a href="index.php">
            Home
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</header>



<!-- ================= MAIN CONTAINER ================= -->

<div class="container">


    <!-- ================= WELCOME ================= -->

    <div class="welcome">

        <h1>

            👨‍🌾 Welcome,
            <?php echo htmlspecialchars($user["name"]); ?>!

        </h1>


        <p>

            Welcome to the
            <strong>
                Smart Agriculture Scheme Recommender
                & Eligibility Checker
            </strong>.

        </p>


        <div class="farmer-info">

            <strong>Region:</strong>

            <?php echo htmlspecialchars($user["region"]); ?>

            &nbsp;&nbsp; | &nbsp;&nbsp;

            <strong>District:</strong>

            <?php echo htmlspecialchars($user["district"]); ?>

        </div>

    </div>



    <!-- ================= DASHBOARD ================= -->

    <h2 class="section-title">

        🌾 Farmer Dashboard

    </h2>


    <div class="card-container">


        <!-- ================= MY PROFILE ================= -->

        <div class="card">

            <div class="card-icon">
                👤
            </div>


            <h2>
                My Profile
            </h2>


            <p>

                View and update your personal,
                location and farmer information.

            </p>


            <a
                class="profile-btn"
                href="update_profile.php"
            >

                View / Update Profile

            </a>

        </div>



        <!-- ================= FARMING DETAILS ================= -->

        <div class="card">

            <div class="card-icon">
                🌾
            </div>


            <h2>
                Farming Details
            </h2>


            <p>

                Enter or update your crop,
                land area, soil and irrigation details.

            </p>


            <a
                class="farming-btn"
                href="update_profile.php"
            >

                Farming Details

            </a>

        </div>



        <!-- ================= ELIGIBILITY ================= -->

        <div class="card">

            <div class="card-icon">
                ✅
            </div>


            <h2>
                Check Eligibility
            </h2>


            <p>

                Check suitable government
                agricultural schemes based on
                your farming details.

            </p>


            <a
                class="eligibility-btn"
                href="recommendation.php"
            >

                Check Eligibility

            </a>

        </div>



        <!-- ================= RECOMMENDATION ================= -->

        <div class="card">

            <div class="card-icon">
                🔍
            </div>


            <h2>
                Recommended Schemes
            </h2>


            <p>

                View agricultural schemes
                recommended according to
                your entered details.

            </p>


            <a
                class="scheme-btn"
                href="recommendation.php"
            >

                View Schemes

            </a>

        </div>


    </div>



    <!-- ================= QUICK INFORMATION ================= -->

    <div class="quick-info">

        <h2>
            📌 How to Use the System
        </h2>


        <ul>

            <li>
                Update your farmer profile and location details.
            </li>

            <li>
                Enter your land, crop, soil and irrigation information.
            </li>

            <li>
                Click on <strong>Check Eligibility</strong>.
            </li>

            <li>
                View government schemes suitable for your details.
            </li>

            <li>
                Open scheme details to check benefits,
                required documents and application procedure.
            </li>

        </ul>

    </div>


</div>



<!-- ================= FOOTER ================= -->

<footer>

    <p>
        Smart Agriculture Scheme Recommender
        & Eligibility Checker
    </p>

    <p>
        Mini Project | B.Sc. Computer Science
    </p>

</footer>


</body>

</html>