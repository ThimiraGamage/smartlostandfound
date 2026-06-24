<?php

session_start();

include 'includes/connection.php';
include 'includes/google-config.php';

$message = "";

if(isset($_POST['register'])){

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $country_code = $_POST['country_code'] ?? '';
    $phone = trim($_POST['phone_number'] ?? '');

    /* REMOVE STARTING 0 */

    if (substr($phone, 0, 1) == "0") {
        $phone = substr($phone, 1);
    }

    /* FINAL PHONE NUMBER */

    $phone_number = $country_code . $phone;

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (
        empty($full_name) ||
        empty($email) ||
        empty($phone_number) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "All fields are required!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email address!";

    } elseif ($password != $confirm_password) {

        $message = "Passwords do not match!";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters!";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        /* CHECK IF EMAIL EXISTS */

        $check_email = "SELECT user_id FROM users WHERE email = ?";

        $stmt = mysqli_prepare($conn, $check_email);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $message = "Email already exists!";

            mysqli_stmt_close($stmt);

        } else {

            mysqli_stmt_close($stmt);

            /* INSERT USER */

            $sql = "INSERT INTO users
            (
                full_name,
                email,
                phone_number,
                password
            )
            VALUES
            (
                ?, ?, ?, ?
            )";

            $stmt = mysqli_prepare($conn, $sql);

            if(!$stmt){
                $message = "Error preparing statement: " . $conn->error;
            }else{
                $stmt->bind_param
                (
                    "ssss", 
                    $full_name, 
                    $email,
                    $phone_number,
                    $hashed_password
                );

                if($stmt->execute()){

                    // Get the newly created user ID
                    $user_id = $conn->insert_id;

                    // Set user data in session
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;
                    $_SESSION['phone_number'] = $phone_number;

                    header("Location: dashboard.php");
                    exit();

                }else{
                    $message = "Error : " . $conn->error;
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/register.css">
    <!-- International Telephone Input Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/css/intlTelInput.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="register-section">

    <div class="register-container">

        <h1>Create Your Account</h1>

        <p>Join the Smart Lost & Found platform</p>

        <?php
        if ($message != "") {
            echo "<div class='message'>$message</div>";
        }
        ?>

        <form method="POST" action="register.php">

            <!-- FULL NAME -->

            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="full_name"
                    placeholder="Enter full name"
                    required
                >

            </div>

            <!-- EMAIL -->

            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter email"
                    required
                >

            </div>

            <!-- PHONE -->

            <div class="form-group">

                <label>Phone Number</label>

                <input
                    id="phone_number_input"
                    type="tel"
                    name="phone_number"
                    placeholder="Enter phone number"
                    required
                >
                <input type="hidden" name="country_code" id="country_code">

            </div>

            <!-- PASSWORD -->

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                >

            </div>

            <!-- REGISTER BUTTON -->

            <button
                type="submit"
                name="register"
                class="register-btn"
            >

                Register

            </button>

            <div class="google-divider">
                <span>or</span>
            </div>

            <a href="<?php echo getGoogleLoginUrl(); ?>" class="google-btn">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                Sign in with Google
            </a>

        </form>

        <div class="login-link">

            <p>

                Already have an account?

                <a href="login.php">

                    Login

                </a>

            </p>

        </div>
    </div>
</section>

<!-- International Telephone Input Library JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/intlTelInput.min.js"></script>

<script>
    // Initialize intl-tel-input
    const phoneInputField = document.querySelector("#phone_number_input");
    const phoneInput = window.intlTelInput(phoneInputField, {
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch("https://ipapi.co/json")
                .then(res => res.json())
                .then(data => callback(data.country_code))
                .catch(() => callback("us"));
        },
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/utils.js",
        preferredCountries: ["us", "gb", "in", "lk", "au", "ca"]
    });

    // Update hidden country code field when user changes country
    phoneInputField.addEventListener("countrychange", function() {
        const selectedCountryData = phoneInput.getSelectedCountryData();
        document.getElementById("country_code").value = selectedCountryData.dialCode;
    });

    // Set initial country code on page load
    window.addEventListener('load', function() {
        const selectedCountryData = phoneInput.getSelectedCountryData();
        document.getElementById("country_code").value = selectedCountryData.dialCode;
    });

    // Validate phone number on form submission
    document.querySelector("form").addEventListener("submit", function(e) {
        const isValid = phoneInput.isValidNumber();
        if (!isValid) {
            e.preventDefault();
            phoneInputField.classList.add("error");
            alert("Please enter a valid phone number");
        } else {
            phoneInputField.classList.remove("error");
        }
    });
</script>

</body>
</html>
