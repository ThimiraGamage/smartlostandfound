<?php

session_start();

/* DATABASE CONNECTION */
include 'includes/connection.php';
/* GOOGLE CONFIGURATION */
include 'includes/google-config.php';

$message = "";
$messageType = "";

/* GOOGLE OAUTH CALLBACK */
if (isset($_GET['code'])) {
    $google_user = handleGoogleCallback($_GET['code']);
    
    if ($google_user && isset($google_user['email'])) {
        $email = $google_user['email'];
        $full_name = $google_user['name'] ?? $google_user['given_name'] ?? 'Google User';
        $google_id = $google_user['sub']; // Unique Google User ID

        // Check if user exists with this Google ID or Email
        $sql = "SELECT * FROM users WHERE google_id = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $google_id, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // If user exists by email but Google ID is not linked yet, link it
            if (empty($row['google_id'])) {
                $update_sql = "UPDATE users SET google_id = ? WHERE user_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "si", $google_id, $row['user_id']);
                mysqli_stmt_execute($update_stmt);
            }

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['email'] = $row['email'];

            // Check if phone number is missing, set flag to show modal
            if (empty($row['phone_number'])) {
                $_SESSION['show_phone_modal'] = true;
                $message = "Please add your phone number to complete setup";
                $messageType = "info";
            } else {
                $message = "Login Successful!";
                $messageType = "success";
                // Redirect to dashboard
                header("refresh:2;url=dashboard.php");
                exit();
            }
        } else {
            // User does not exist, auto-register them
            // Since password is not needed for social login, we leave it empty (ensure your database schema allows this)
            $insert_sql = "INSERT INTO users (full_name, email, google_id, password) VALUES (?, ?, ?, '')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sss", $full_name, $email, $google_id);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $_SESSION['show_phone_modal'] = true;

                $message = "Account created! Please add your phone number";
                $messageType = "info";
            } else {
                $message = "Database registration error. Please try again.";
                $messageType = "error";
            }
        }
    } else {
        $message = "Failed to authenticate with Google.";
        $messageType = "error";
    }
}

/* LOGIN PROCESS */

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    /* GET USER USING EMAIL */

    $sql = "SELECT * FROM users WHERE email='$email'";

    // Procedural approach
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);

        /* VERIFY HASHED PASSWORD */

        if(password_verify($password, $row['password']))
        {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['email'] = $row['email'];

            $message = "Login Successful!";
            $messageType = "success";

            header("refresh:2;url=dashboard.php");
        }
        else
        {
            $message = "Invalid Email or Password!";
            $messageType = "error";
        }
    }
    else
    {
        $message = "Invalid Email or Password!";
        $messageType = "error";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/login.css">

 

</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <section class="login-section">

        <div class="login-container">

            <h1>
                Welcome Back
            </h1>

            <p>
                Login to continue
            </p>

            <?php if($message != "") { ?>

                <div class="message <?php echo $messageType; ?>">

                    <?php echo $message; ?>

                </div>

            <?php } ?>

            <form method="POST">

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input 
                        type="email"
                        name="email"
                        placeholder="Enter email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input 
                        type="password"
                        name="password"
                        placeholder="Enter password"
                        required
                    >

                </div>

                <div class="remember-section">

                    <input type="checkbox" id="remember">

                    <label for="remember">
                        Remember Mee 
                    </label>

                </div>

                <button 
                    type="submit" 
                    name="login"
                    class="login-btn"
                >

                    Login

                </button>

                <div class="google-divider">
                    <span>or</span>
                </div>

                <a href="<?php echo getGoogleLoginUrl(); ?>" class="google-btn">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                    Sign in with Google
                </a>

            </form>

            <div class="register-link">

                <p>

                    Don't have an account?

                    <a href="register.php">
                        Register
                    </a>

                </p>

            </div>

        </div>

    </section>

</body>

</html>