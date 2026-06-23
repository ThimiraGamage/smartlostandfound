<?php

session_start();

/* DATABASE CONNECTION */

$conn = new mysqli("localhost", "root", "", "smartlostfound", 3306);

if($conn->connect_error)
{
    die("Connection Failed : " . $conn->connect_error);
}

$message = "";
$messageType = "";

/* LOGIN PROCESS */

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    /* GET USER USING EMAIL */

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = $conn->query($sql);

    if($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();

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

    <style>

        .message
        {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .success
        {
            background-color: #d4edda;
            color: #155724;
        }

        .error
        {
            background-color: #f8d7da;
            color: #721c24;
        }

    </style>

</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- LOGIN SECTION -->

    <section class="login-section">

        <div class="login-container">

            <h1>
                Welcome Back
            </h1>

            <p>
                Login to continue
            </p>

            <!-- MESSAGE -->

            <?php if($message != "") { ?>

                <div class="message <?php echo $messageType; ?>">

                    <?php echo $message; ?>

                </div>

            <?php } ?>

            <form method="POST">

                <!-- EMAIL -->

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

                <!-- PASSWORD -->

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

                <!-- REMEMBER -->

                <div class="remember-section">

                    <input type="checkbox" id="remember">

                    <label for="remember">
                        Remember Me
                    </label>

                </div>

                <!-- BUTTON -->

                <button 
                    type="submit" 
                    name="login"
                    class="login-btn"
                >

                    Login

                </button>

            </form>

            <!-- REGISTER LINK -->

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