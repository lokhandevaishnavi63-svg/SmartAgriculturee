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

$message = "";


/* ================= USER DETAILS ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_user"])) {

    $name = $_POST["name"];
    $age = $_POST["age"];
    $mobile = $_POST["mobile"];
    $region = $_POST["region"];
    $district = $_POST["district"];

    $stmt = $conn->prepare(
        "UPDATE users
         SET name=?, age=?, mobile=?, region=?, district=?
         WHERE user_id=?"
    );

    $stmt->bind_param(
        "sisssi",
        $name,
        $age,
        $mobile,
        $region,
        $district,
        $user_id
    );

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    }
}


/* ================= FARMING DETAILS ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_farming"])) {

    $previous_crop = $_POST["previous_crop"];
    $land_area = $_POST["land_area"];
    $crop = $_POST["crop"];
    $soil_type = $_POST["soil_type"];
    $irrigation = $_POST["irrigation"];
    $category = $_POST["category"];


    /* Check if farming profile already exists */

    $check = $conn->prepare(
        "SELECT profile_id
         FROM farmer_profiles
         WHERE user_id=?
         ORDER BY profile_id DESC
         LIMIT 1"
    );

    $check->bind_param("i", $user_id);

    $check->execute();

    $result = $check->get_result();


    if ($result->num_rows > 0) {

        $profile = $result->fetch_assoc();

        $profile_id = $profile["profile_id"];


        $stmt = $conn->prepare(
            "UPDATE farmer_profiles
             SET previous_crop=?,
                 land_area=?,
                 crop=?,
                 soil_type=?,
                 irrigation=?,
                 category=?
             WHERE profile_id=?"
        );

        $stmt->bind_param(
            "sdssssi",
            $previous_crop,
            $land_area,
            $crop,
            $soil_type,
            $irrigation,
            $category,
            $profile_id
        );

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO farmer_profiles
             (user_id, previous_crop, land_area, crop,
              soil_type, irrigation, category)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "isdssss",
            $user_id,
            $previous_crop,
            $land_area,
            $crop,
            $soil_type,
            $irrigation,
            $category
        );
    }


    if ($stmt->execute()) {
        $message = "Farming details updated successfully!";
    }
}


/* ================= GET CURRENT USER ================= */

$stmt = $conn->prepare(
    "SELECT name, age, mobile, region, district
     FROM users
     WHERE user_id=?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$user_result = $stmt->get_result();

$user = $user_result->fetch_assoc();


/* ================= GET FARMING DETAILS ================= */

$stmt = $conn->prepare(
    "SELECT previous_crop, land_area, crop,
            soil_type, irrigation, category
     FROM farmer_profiles
     WHERE user_id=?
     ORDER BY profile_id DESC
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$farming_result = $stmt->get_result();

$farming = $farming_result->fetch_assoc();

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Update Farmer Profile</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f8f4;
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

        .logo {
            font-size: 23px;

            font-weight: bold;
        }

        header a {
            color: white;

            text-decoration: none;

            background: #1b5e20;

            padding: 9px 16px;

            border-radius: 5px;

            margin-left: 8px;
        }


        /* Main */

        .container {
            width: 90%;

            max-width: 900px;

            margin: 35px auto;
        }


        h1 {
            text-align: center;

            color: #1b5e20;

            margin-bottom: 30px;
        }


        /* Message */

        .message {
            background: #dff0d8;

            color: #256029;

            padding: 15px;

            border-radius: 6px;

            text-align: center;

            margin-bottom: 25px;
        }


        /* Box */

        .box {
            background: white;

            padding: 30px;

            margin-bottom: 30px;

            border-radius: 12px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }

        .box h2 {
            color: #2e7d32;

            border-bottom: 2px solid #e8f5e9;

            padding-bottom: 10px;

            margin-top: 0;
        }


        /* Form */

        .form-grid {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;
        }

        .form-group {
            display: flex;

            flex-direction: column;
        }

        label {
            font-weight: bold;

            margin-bottom: 7px;
        }

        input,
        select {
            padding: 11px;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-size: 15px;
        }

        input:focus,
        select:focus {
            outline: none;

            border-color: #2e7d32;
        }


        /* Button */

        button {
            width: 100%;

            padding: 13px;

            margin-top: 20px;

            border: none;

            border-radius: 5px;

            background: #2e7d32;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background: #1b5e20;
        }


        /* Back */

        .back {
            display: inline-block;

            margin-top: 5px;

            color: #2e7d32;

            text-decoration: none;

            font-weight: bold;
        }


        /* Mobile */

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            header {
                padding: 15px 20px;
            }

            .container {
                width: 94%;
            }

        }

    </style>

</head>


<body>


<!-- Header -->

<header>

    <div class="logo">
        🌱 Smart Agriculture
    </div>

    <div>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</header>


<div class="container">


    <h1>
        👨‍🌾 Update Farmer Profile
    </h1>


    <?php if ($message != "") { ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- ================= PERSONAL DETAILS ================= -->

    <div class="box">

        <h2>
            👤 Personal & Location Details
        </h2>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($user["name"]); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Age
                    </label>

                    <input
                        type="number"
                        name="age"
                        value="<?php echo htmlspecialchars($user["age"]); ?>"
                        min="18"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Mobile Number
                    </label>

                    <input
                        type="text"
                        name="mobile"
                        value="<?php echo htmlspecialchars($user["mobile"]); ?>"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Region
                    </label>

                    <select name="region" required>

                        <option value="">
                            Select Region
                        </option>

                        <option
                            value="Konkan"
                            <?php
                            if ($user["region"] == "Konkan")
                                echo "selected";
                            ?>
                        >
                            Konkan
                        </option>

                        <option
                            value="Marathwada"
                            <?php
                            if ($user["region"] == "Marathwada")
                                echo "selected";
                            ?>
                        >
                            Marathwada
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        District
                    </label>

                    <input
                        type="text"
                        name="district"
                        value="<?php echo htmlspecialchars($user["district"]); ?>"
                        required
                    >

                </div>


            </div>


            <input
                type="hidden"
                name="update_user"
                value="1"
            >


            <button type="submit">
                💾 Update Personal Details
            </button>

        </form>

    </div>


    <!-- ================= FARMING DETAILS ================= -->

    <div class="box">

        <h2>
            🌾 Farming Details
        </h2>


        <form method="POST">

            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Previous Crop
                    </label>

                    <input
                        type="text"
                        name="previous_crop"
                        value="<?php
                        echo htmlspecialchars(
                            $farming["previous_crop"] ?? ""
                        );
                        ?>"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Land Area (Acres)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="land_area"
                        value="<?php
                        echo htmlspecialchars(
                            $farming["land_area"] ?? ""
                        );
                        ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Current / Planned Crop
                    </label>

                    <input
                        type="text"
                        name="crop"
                        value="<?php
                        echo htmlspecialchars(
                            $farming["crop"] ?? ""
                        );
                        ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Soil Type
                    </label>

                    <select name="soil_type">

                        <option value="">
                            Select Soil Type
                        </option>

                        <option value="Black Soil"
                            <?php
                            if (($farming["soil_type"] ?? "") == "Black Soil")
                                echo "selected";
                            ?>>
                            Black Soil
                        </option>

                        <option value="Red Soil"
                            <?php
                            if (($farming["soil_type"] ?? "") == "Red Soil")
                                echo "selected";
                            ?>>
                            Red Soil
                        </option>

                        <option value="Laterite Soil"
                            <?php
                            if (($farming["soil_type"] ?? "") == "Laterite Soil")
                                echo "selected";
                            ?>>
                            Laterite Soil
                        </option>

                        <option value="Alluvial Soil"
                            <?php
                            if (($farming["soil_type"] ?? "") == "Alluvial Soil")
                                echo "selected";
                            ?>>
                            Alluvial Soil
                        </option>

                        <option value="Other"
                            <?php
                            if (($farming["soil_type"] ?? "") == "Other")
                                echo "selected";
                            ?>>
                            Other
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Irrigation Facility
                    </label>

                    <select name="irrigation">

                        <option value="">
                            Select Irrigation
                        </option>

                        <option value="Rainfed"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Rainfed")
                                echo "selected";
                            ?>>
                            Rainfed
                        </option>

                        <option value="Well"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Well")
                                echo "selected";
                            ?>>
                            Well
                        </option>

                        <option value="Borewell"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Borewell")
                                echo "selected";
                            ?>>
                            Borewell
                        </option>

                        <option value="Canal"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Canal")
                                echo "selected";
                            ?>>
                            Canal
                        </option>

                        <option value="Drip"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Drip")
                                echo "selected";
                            ?>>
                            Drip
                        </option>

                        <option value="Sprinkler"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Sprinkler")
                                echo "selected";
                            ?>>
                            Sprinkler
                        </option>

                        <option value="Farm Pond"
                            <?php
                            if (($farming["irrigation"] ?? "") == "Farm Pond")
                                echo "selected";
                            ?>>
                            Farm Pond
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select name="category">

                        <option value="">
                            Select Category
                        </option>

                        <option value="General"
                            <?php
                            if (($farming["category"] ?? "") == "General")
                                echo "selected";
                            ?>>
                            General
                        </option>

                        <option value="OBC"
                            <?php
                            if (($farming["category"] ?? "") == "OBC")
                                echo "selected";
                            ?>>
                            OBC
                        </option>

                        <option value="SC"
                            <?php
                            if (($farming["category"] ?? "") == "SC")
                                echo "selected";
                            ?>>
                            SC
                        </option>

                        <option value="ST"
                            <?php
                            if (($farming["category"] ?? "") == "ST")
                                echo "selected";
                            ?>>
                            ST
                        </option>

                        <option value="EWS"
                            <?php
                            if (($farming["category"] ?? "") == "EWS")
                                echo "selected";
                            ?>>
                            EWS
                        </option>

                    </select>

                </div>


            </div>


            <input
                type="hidden"
                name="update_farming"
                value="1"
            >


            <button type="submit">
                💾 Update Farming Details
            </button>

        </form>

    </div>


    <a
        class="back"
        href="dashboard.php"
    >
        ← Back to Dashboard
    </a>


</div>


</body>

</html>