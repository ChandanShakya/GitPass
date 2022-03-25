<?php 
    session_start();

    if(isset($_SESSION['unique_id'])) {
        header("Location:index.php");
    }

    include_once "php/head.php";
    include_once "php/conn.php";
?>
<div class="container">
    <div class="row">
        <div class="col-12 mt-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <h1 class="display-6 align-center">Log In to GitPass</h1>
                    <hr>

                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $username = mysqli_real_escape_string($conn, $_POST['username']);
                            $password = mysqli_real_escape_string($conn, $_POST['password']);

                            if(!empty($username) && !empty($password)) {
                                $sql = mysqli_query($conn, "SELECT * FROM login WHERE username = '{$username}' AND password = '{$password}';");
                                if(mysqli_num_rows($sql) > 0) {
                                    $row = mysqli_fetch_assoc($sql);
                                    $_SESSION['unique_id'] = $row['id'];
                                    //header("Refresh:0");
                                    //header("Location:index.php");
                                    echo "<meta http-equiv='refresh' content='0'>";
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">Incorrect username or password. Please try again.</div>';
                                }
                            } else {
                                echo '<div class="alert alert-danger" role="alert">Please fill all fields.</div>';
                            }
                        }
                    ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" onblur="validateLogin();">
                        <label for="basic-url" class="form-label">Username</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input id="username" name="username" type="text" class="form-control" placeholder="Username">
                        </div>
                        <div clas="error" id="username_error"></div>

                        <label for="basic-url" class="form-label">Password</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input id="password" name="password" type="password" class="form-control" placeholder="Password">
                        </div>
                        <div clas="error" id="password_error"></div>

                        <div>Don't have an account? <a href="registration.php">Sign Up here!</a></div>
                        <script src="js/login_validate.js"></script>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-sign-in-alt"></i> &nbsp;Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    include_once "php/footer.php";
?>