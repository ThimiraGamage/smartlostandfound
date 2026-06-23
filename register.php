<?php

session_start();

include 'includes/connection.php';
include 'includes/google-config.php';

$message = "";

if (isset($_POST['register'])) {

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
                password
            )
            VALUES
            (
                ?, ?, ?
            )";

            $stmt = mysqli_prepare($conn, $sql);

            if(!$stmt){
                $message = "Error preparing statement: " . $conn->error;
            }else{
                $stmt->bind_param
                (
                    "sss", 
                    $full_name, 
                    $email,
                    $hashed_password
                );

                if($stmt->execute()){

                    // Get the newly created user ID
                    $user_id = $conn->insert_id;

                    // Start session and set user data
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;

                    header("Location: dashboard.php");
                    exit();

                }else{

                    $message = "Error : " . $conn->error;

                }

            mysqli_stmt_close($stmt);
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

                <div style="display:flex; gap:10px;">

                    <select
                        name="country_code"
                        required
                        style="width:120px;"
                    >

                        <option value="94">+94</option>

                        <option value="91">+91</option>

                        <option value="1">+1</option>

                    </select>

                    <input
                        type="text"
                        name="phone_number"
                        placeholder="771234567"
                        required
                    >

                </div>

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
                class="register-btnnn"
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
</body>
</html>
