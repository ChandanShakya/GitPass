<?php
session_start();

include_once "php/head.php";
include_once "php/conn.php";
?>


<!-- Start your project here-->

<div id="preview" class="preview">
    <div>
        <!-- Hero -->
        <div data-draggable="true" style="position: relative;">
            <section draggable="false" class="overflow-hidden pt-0">
                <section class="background-radial-gradient">
                    <style>
                        .background-radial-gradient {
                            background-color: hsl(218, 41%, 15%);
                            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%), radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);

                        }
                    </style>
                    <div class="container px-4 py-5 px-md-5 text-center text-lg-start">
                        <div class="row gx-lg-5 align-items-center">
                            <div class="col-lg-6 mb-5 mb-lg-0">
                                <h1 class="my-5 display-3 fw-bold ls-tight" style="color: #1266f1">
                                    <span>GitPass</span><br>
                                </h1>
                                <h1 class="my-5 display-3 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                                    <span>You don't have to</span> <br>
                                    <span style="color: hsl(218, 81%, 75%)">remember<br>your passwords.<br></span>
                                </h1>
                                <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">GitPass remembers them all for you.<br>Save your passwords and log into site with a single click.<br>It's that easy.</p>

                                <?php if (!isset($_SESSION['unique_id'])) {
                                ?>

                                    <a class="btn   btn-primary btn-lg py-3 px-5 mb-2 mb-md-0 me-md-2" data-ripple-color="primary" href="login.php" role="button" aria-controls="#picker-editor">Login</a>
                                    <a class="btn   btn-outline-secondary btn-lg py-3 px-5 mb-2 mb-md-0 me-md-2 text-white" style="background-color: transparent" href="registration.php" role="button" aria-controls="#picker-editor">Register</a>
                                <?php
                                } else { ?>
                                        <a class="btn btn-primary btn-lg py-3 px-5 mb-2 mb-md-0 me-md-2" data-ripple-color="primary" href="index.php" role="button" aria-controls="#picker-editor">Dashboard</a>
                                <?php
                                } ?>
                            </div>
                            <div class="col-lg-6 mb-5 mb-lg-0"> <img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg" class="w-100 rounded-4" alt="Logo" aria-controls="#picker-editor" style="width:50vw;height:50vh"> </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
        <!-- Hero -->


        <div data-draggable="true" style="position: relative;">
            <!---->
            <!---->
            <section draggable="false" class="container pt-5" data-v-271253ee="">
                <section class="text-center">
                    <h2 class="fw-bold mb-9 text-center">Why choose GitPass?</h2>
                    <div class="row gx-lg-5">
                        <div class="col-lg-4 col-md-12 mb-8 mb-lg-0">
                            <div class="card shadow-2-strong h-100">
                                <div class="d-flex justify-content-center" style="margin-top: -43px">
                                    <div class="p-4 bg-primary rounded-circle shadow-5-strong d-inline-block"> <i class="fas fa-database fa-4x text-white" aria-controls="#picker-editor"></i> </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Easier Management</h5>
                                    <p class="card-text"><br>Adding, editing, deleting and viewing<br>password has never been easier.<br><br>No bunch of forms to fill up,<br>no unnecessary questions to answer.<br></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-8 mb-lg-0">
                            <div class="card shadow-2-strong h-100">
                                <div class="d-flex justify-content-center" style="margin-top: -43px">
                                    <div class="p-4 bg-primary rounded-circle shadow-5-strong d-inline-block"> <i class="fas fa-lock fa-4x text-white" aria-controls="#picker-editor"></i>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Safe and solid</h5>
                                    <p class="card-text"><br>Storing passwords aren't a big deal but<br>doing it safely and simply is.<br><br>Your data is yours and is safe.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-8 mb-lg-0">
                            <div class="card shadow-2-strong h-100">
                                <div class="d-flex justify-content-center" style="margin-top: -43px">
                                    <div class="p-4 bg-primary rounded-circle shadow-5-strong d-inline-block"> <i class="fas fa-boxes fa-4x text-white" aria-controls="#picker-editor"></i> </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Cross Platform</h5>
                                    <p class="card-text"><br>Having Internet and a modern browser<br>is enough.<br><br>Use<br>mobile, desktop, or any sized device.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
            <!---->
        </div>

        <div data-draggable="true" style="position: relative;">
            <!---->
            <!---->
            <section draggable="false" class="container pt-5" data-v-271253ee="">
                <section class="text-center">
                    <div class="row d-flex justify-content-center">
                        <div class="col-lg-6">
                            <h2 class="fw-bold mb-5">Contact us</h2>


                            <?php

                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
                                $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
                                $message = htmlspecialchars($_POST['message'], ENT_QUOTES);

                                $to = "notch0andan@gmail.com";
                                $subject = "Contact Form";
                                $headers = "From: $name <$email>\r\n";
                                $headers .= "Reply-To: $name <$email>\r\n";
                                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                                $body = "From: $name <br> E-Mail: $email<br> Message: $message";

                                if (mail($to, $subject, $body, $headers)) {
                                    echo "<p>Your message has been sent!</p>";
                                } else {
                                    echo "<p>Something went wrong, please try again.</p>";
                                }
                            }
                            ?>


                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <!-- Message input -->
                                <div class="form-outline mb-4"> <textarea class="form-control" id="form4Example3" rows="4" name="message" oninput="validateMessage();validate()"></textarea>
                                    <label class="form-label" for="form4Example3" style="margin-left: 0px;">Message</label>
                                    <div class="form-notch">
                                        <div class="form-notch-leading" style="width: 9px;"></div>
                                        <div class="form-notch-middle" style="width: 60px;"></div>
                                        <div class="form-notch-trailing"></div>
                                    </div>
                                    <div id="div3"></div>
                                </div>
                                <!-- Name input -->
                                <div class="form-outline mb-4"> <input type="text" id="form4Example1" class="form-control" name="name" oninput="validateUsername();validate()"> <label class="form-label" for="form4Example1" style="margin-left: 0px;">Username</label>
                                    <div class="form-notch">
                                        <div class="form-notch-leading" style="width: 9px;"></div>
                                        <div class="form-notch-middle" style="width: 42.4px;"></div>
                                        <div class="form-notch-trailing"></div>
                                    </div>
                                    <div id="div1"></div>

                                </div> <!-- Email input -->
                                <div class="form-outline mb-4"> <input type="email" id="form4Example2" class="form-control" name="email" oninput="validateEmail();validate()"> <label class="form-label" for="form4Example2" style="margin-left: 0px;">E-mail address</label>
                                    <div class="form-notch">
                                        <div class="form-notch-leading" style="width: 9px;"></div>
                                        <div class="form-notch-middle" style="width: 88.8px;"></div>
                                        <div class="form-notch-trailing"></div>
                                    </div>
                                    <div id="div2"></div>

                                </div> <!-- Submit button --> <button type="submit" class="btn  btn-primary btn-block mb-4" aria-controls="#picker-editor" disabled="disabled" id="send" name="submit">Send</button>

                            </form>
                            <script type="text/javascript" src="js/email_validate.js"></script>



                        </div>
                    </div>
                </section>
            </section>
            <!---->
        </div>
        <div data-draggable="true" style="position: relative;">
            <!---->
            <!---->
            <section draggable="false" class="overflow-hidden" data-v-271253ee="">
                <section class="text-center text-md-start">

                    <div class="row align-items-center">
                        <div class="col-md-8 mb-4 mb-md-0  text-center">
                            <h2 class="fw-bold"> <span>View out source code</span> <br> <span class="text-primary">Contribute</span> </h2>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <!-- GitHub --> <a class="btn btn-primary btn-floating me-2" style="background-color: #4f45c6" href="https://github.com/ZXY-CC-3ag13/GitPass" target="blank" role="button"> <i class="fab fa-github" aria-controls="#picker-editor"></i> </a>
                        </div>
                    </div>
                </section>
            </section>
            <!---->
        </div>
    </div>
</div>

<!-- End your project here-->
<?php
include_once "php/footer.php";
?>