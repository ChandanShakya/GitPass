<?php
session_start();

if (isset($_SESSION['unique_id'])) {
    header("Location:index.php");
}

include_once "php/head.php";
include_once "php/conn.php";
?>
<!-- Animated SVG -->
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="position-absolute" style=" margin: auto; display: block; width: 100%; z-index: 0; position: relative;" preserveAspectRatio="none" viewBox="0 0 1920 880">
    <g transform="translate(960,440) scale(1,1) translate(-960,-440)">
        <linearGradient id="lg-0.047955344060927496" x1="0" x2="1" y1="0" y2="0">
            <stop stop-color="hsl(217, 88%, 33.7%)" offset="0"></stop>
            <stop stop-color="hsl(217, 88%, 75.1%)" offset="1"></stop>
        </linearGradient>
        <path d="" fill="url(#lg-0.047955344060927496)" opacity="0.4">
            <animate attributeName="d" dur="33.333333333333336s" repeatCount="indefinite" keyTimes="0;0.333;0.667;1" calcmod="spline" keySplines="0.2 0 0.2 1;0.2 0 0.2 1;0.2 0 0.2 1" begin="0s" values="M0 0L 0 804.2328934685746Q 320 597.3613372284876 640 571.0708916590191T 1280 512.0661063245175T 1920 301.8788007488083L 1920 0 Z;M0 0L 0 877.6839081951588Q 320 668.0720922803877 640 649.0018928349388T 1280 328.7087077664202T 1920 162.95038242563396L 1920 0 Z;M0 0L 0 724.9886210051687Q 320 661.4364572061575 640 623.2173947479624T 1280 359.20353038907734T 1920 135.51673041732283L 1920 0 Z;M0 0L 0 804.2328934685746Q 320 597.3613372284876 640 571.0708916590191T 1280 512.0661063245175T 1920 301.8788007488083L 1920 0 Z">
            </animate>
        </path>
        <path d="" fill="url(#lg-0.047955344060927496)" opacity="0.4">
            <animate attributeName="d" dur="33.333333333333336s" repeatCount="indefinite" keyTimes="0;0.333;0.667;1" calcmod="spline" keySplines="0.2 0 0.2 1;0.2 0 0.2 1;0.2 0 0.2 1" begin="-6.666666666666667s" values="M0 0L 0 765.7607191473613Q 320 641.7853945676919 640 624.2534689988059T 1280 365.27264408032966T 1920 190.38947978522663L 1920 0 Z;M0 0L 0 842.1984196370487Q 320 570.6690721707517 640 540.6844954979398T 1280 439.92879442880593T 1920 200.29713960445451L 1920 0 Z;M0 0L 0 796.6802345094818Q 320 721.9216894353016 640 696.8815669355181T 1280 373.6367381440213T 1920 196.63169821789495L 1920 0 Z;M0 0L 0 765.7607191473613Q 320 641.7853945676919 640 624.2534689988059T 1280 365.27264408032966T 1920 190.38947978522663L 1920 0 Z">
            </animate>
        </path>
        <path d="" fill="url(#lg-0.047955344060927496)" opacity="0.4">
            <animate attributeName="d" dur="33.333333333333336s" repeatCount="indefinite" keyTimes="0;0.333;0.667;1" calcmod="spline" keySplines="0.2 0 0.2 1;0.2 0 0.2 1;0.2 0 0.2 1" begin="-13.333333333333334s" values="M0 0L 0 801.7562714943509Q 320 634.0247183381232 640 605.7090791951217T 1280 503.9393370140325T 1920 224.7551247480177L 1920 0 Z;M0 0L 0 821.0401780336218Q 320 670.8690783540507 640 637.0744123031742T 1280 456.40745286432224T 1920 278.1294357804296L 1920 0 Z;M0 0L 0 744.0534225112256Q 320 637.6425395409125 640 593.2079605185819T 1280 457.03995196824286T 1920 254.87693899994804L 1920 0 Z;M0 0L 0 801.7562714943509Q 320 634.0247183381232 640 605.7090791951217T 1280 503.9393370140325T 1920 224.7551247480177L 1920 0 Z">
            </animate>
        </path>
        <path d="" fill="url(#lg-0.047955344060927496)" opacity="0.4">
            <animate attributeName="d" dur="33.333333333333336s" repeatCount="indefinite" keyTimes="0;0.333;0.667;1" calcmod="spline" keySplines="0.2 0 0.2 1;0.2 0 0.2 1;0.2 0 0.2 1" begin="-20s" values="M0 0L 0 817.8603658675457Q 320 592.9404308081629 640 559.1126621853513T 1280 428.9912604821798T 1920 209.017381620229L 1920 0 Z;M0 0L 0 802.0504889976935Q 320 561.3963273210122 640 537.6024084387631T 1280 430.41283267566695T 1920 256.1972069733954L 1920 0 Z;M0 0L 0 789.4448177495887Q 320 561.9675446430498 640 531.6192318019404T 1280 414.76018143244175T 1920 265.9163329632971L 1920 0 Z;M0 0L 0 817.8603658675457Q 320 592.9404308081629 640 559.1126621853513T 1280 428.9912604821798T 1920 209.017381620229L 1920 0 Z">
            </animate>
        </path>
        <path d="" fill="url(#lg-0.047955344060927496)" opacity="0.4">
            <animate attributeName="d" dur="33.333333333333336s" repeatCount="indefinite" keyTimes="0;0.333;0.667;1" calcmod="spline" keySplines="0.2 0 0.2 1;0.2 0 0.2 1;0.2 0 0.2 1" begin="-26.666666666666668s" values="M0 0L 0 844.0541574423102Q 320 623.0697081316591 640 592.8483890737847T 1280 469.85448734523794T 1920 190.81850676853674L 1920 0 Z;M0 0L 0 871.4928294956283Q 320 618.9784567388518 640 593.1183717103518T 1280 376.5051942642811T 1920 141.32293927545027L 1920 0 Z;M0 0L 0 782.0118384610068Q 320 727.3267836497654 640 694.0476176759635T 1280 518.1545471640493T 1920 276.0053882957168L 1920 0 Z;M0 0L 0 844.0541574423102Q 320 623.0697081316591 640 592.8483890737847T 1280 469.85448734523794T 1920 190.81850676853674L 1920 0 Z">
            </animate>
        </path>
    </g>
</svg>
<!-- Animated SVG -->
<div id="preview" class="preview">
    <div style="display: none;"></div>
    <div>
        <div data-draggable="true" style="position: relative;" class="">
            <!---->
            <!---->
            <section draggable="false" class="overflow-hidden pt-0" data-v-271253ee="">
                <section class="mb-10 position-relative">

                    <!-- Features -->
                    <div class="container">
                        <div class="d-flex justify-content-center text-center pt-5">
                            <div class="p-4 rounded-6 shadow-3-strong w-75 mt-5" style="background-color: hsla(0, 0%, 100%, 0.8) !important; backdrop-filter: saturate(200%) blur(25px);">

                                <div class="mb-4 mb-lg-0 position-relative"> <a href="home.php"><img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg" class="text-primary" alt="Logo" aria-controls="#picker-editor" style="width:5em;"> </a></div>

                                <h1 class="mt-4 mb-5 display-3 fw-bold ls-tight">

                                    <span>Sign in</span> <br>
                                </h1>

                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-6">


                                        <?php

                                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                            $username = $_POST['username'];
                                            $password = $_POST['password'];

                                            if (!empty($username) && !empty($password)) {
                                                // Get the salt for the user
                                                $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
                                                $stmt->bindParam(':username', $username);
                                                $stmt->execute();
                                                if ($stmt->rowCount() > 0) {
                                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    $salt = $row['salt'];
                                                    $iterations = 10000;
                                                    $keyLength = 24;
                                                    $key = hash_pbkdf2("sha256", $password, $salt, $iterations, $keyLength, true);
                                                    // Check if the password is correct
                                                    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = AES_ENCRYPT(:password, :key)");
                                                    $stmt->bindParam(':username', $username);
                                                    $stmt->bindParam(':password', $password);
                                                    $stmt->bindParam(':key', $key);
                                                    $stmt->execute();
                                                    if ($stmt->rowCount() > 0) {
                                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                                        
                                                        $_SESSION['unique_id'] = $row['user_id'];
                                                        $_SESSION['username'] = $row['username'];
                                                        // Fill in the values into login_track
                                                        $user_id = $row['user_id'];
                                                        $session_id = session_id();
                                                        $_SESSION['idvar']=$session_id;
                                                        $stmt = $conn->prepare("INSERT INTO login_track (session_id, user_id) VALUES (:session_id,:user_id)");
                                                        $stmt->bindParam(':session_id', $session_id);
                                                        $stmt->bindParam(':user_id', $user_id);
                                                        $stmt->execute();

                                                        // Decrypt the password using the stored salt
                                                        $salt = $row['salt']; // Convert the stored salt back to binary
                                                        $iterations = 10000;
                                                        $keyLength = 24;
                                                        $key = hash_pbkdf2("sha256", $password, $salt, $iterations, $keyLength, true);
                                                        $_SESSION['password'] = $key;
                                                        echo "<meta http-equiv='refresh' content='0'>";
                                                    } else {
                                                        echo '<div class="alert alert-danger" role="alert"  style="background-color: #2b5fb31f;">Incorrect username or password.<br>Please try again.</div>';
                                                    }
                                                } else {
                                                    echo '<div class="alert alert-danger" role="alert"  style="background-color: #2b5fb31f;">Please fill all fields.</div>';
                                                }
                                            }
                                        }

                                        ?>


                                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                            <!-- Username input -->
                                            <div class="d-flex justify-content-center" id="div1"></div>
                                            <div class="form-outline mb-4 input-group">
                                                <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-user"></i></span>
                                                <input type="text" id="form4Example1" class="form-control" name="username" oninput="validateUsername();buttonEnabler();">
                                                <label class="form-label" for="form4Example1" style="margin-left: 0px;">Username</label>
                                                <div class="form-notch">
                                                    <div class="form-notch-leading" style="width: 9px;"></div>
                                                    <div class="form-notch-middle" style="width: 42.4px;"></div>
                                                    <div class="form-notch-trailing"></div>
                                                </div>

                                            </div>
                                            <!-- Password input -->
                                            <div class="d-flex justify-content-center" id="div2"></div>
                                            <div class="form-outline mb-4 input-group">
                                                <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-key"></i></span>
                                                <input type="password" id="form4Example2" class="form-control" name="password" oninput="validatePassword();buttonEnabler();">
                                                <label class="form-label" for="form4Example2" style="margin-left: 0px;">Password</label>
                                                <div class="form-notch">
                                                    <div class="form-notch-leading" style="width: 9px;"></div>
                                                    <div class="form-notch-middle" style="width: 88.8px;"></div>
                                                    <div class="form-notch-trailing"></div>
                                                </div>

                                            </div>
                                            <!-- Register -->
                                            <div class="form-check d-flex justify-content-center mb-4">
                                                <label class="form-check-label">Don't have an account? <br><a href="registration.php">Sign Up here!</a></label>
                                            </div>

                                            <!-- Submit button -->
                                            <button type="submit" class="btn btn-primary btn-block" aria-controls="#picker-editor" id="send" disabled="disabled"><i class="fas fa-sign-in-alt"></i> &nbsp;Sign in</button>
                                        </form>
                                        <script type="text/javascript" src="js/login.js"></script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Features -->


                </section>
            </section>
            <!---->
        </div>
    </div>
</div>


<?php
include_once "php/footer.php";
?>