<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
  header("Location:login.php");
}
?>
<?php
include_once "php/head.php";
include_once "php/conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = $_POST['title'];
  $site = $_POST['site'];
  // Add http:// if not present
  if (strpos($site, 'http://') === false && strpos($site, 'https://') === false) {
    $site = 'https://' . $site;
  }
  $username = $_POST['username'];
  $password = $_POST['password'];
  $userId = $_SESSION['unique_id'];

  $stmt = $conn->prepare("INSERT INTO social_accounts(title, site, username, password, user_id) VALUES (:title, :site, :username, :password, :userId);");
  $stmt->bindParam(':title', $title);
  $stmt->bindParam(':site', $site);
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':password', $password);
  $stmt->bindParam(':userId', $userId);
  $stmt->execute();
  $account_id = $conn->lastInsertId();
  $stmt = $conn->prepare("INSERT INTO social_account_metadata(account_id) VALUES (:account_id);");
  $stmt->bindParam(':account_id', $account_id);
  $stmt->execute();


  //header("Location:index.php");
  //header("Location:http://" . $_SERVER['HTTP_HOST']."/index.php");
  echo "<script>window.open('index.php','_self') </script>";
}

$conn = null; // close the database connection
?>



<div id="preview" class="preview">
  <div style="display: none;"></div>
  <div>
    <div data-draggable="true" style="position: relative;" class="">
      <!---->
      <!---->
      <section draggable="false" class="overflow-hidden pt-0" data-v-271253ee="">
        <section class="mb-10 position-relative">
          <!-- Gradient Background -->
          <style>
            body {
              background-color: hsl(218, 41%, 15%);
              background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%), radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
              height: 100vh;
            }
          </style> <!-- Gradient Background -->
          <!-- Features -->
          <div class="container">
            <div class="d-flex justify-content-center text-center pt-5">
              <div class="p-4 rounded-6 shadow-3-strong w-75 mt-5" style="background-color: hsla(0, 0%, 100%, 0.8) !important; backdrop-filter: saturate(200%) blur(25px);;">

                <div class="mb-4 mb-lg-0 position-relative"> <a href="home.php"><img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg" class="text-primary" alt="Logo" aria-controls="#picker-editor" style="width:5em;"> </a></div>

                <h1 class="mt-4 mb-3 display-3 fw-bold ls-tight">

                  New Entry <br></h1>

                <div class="row d-flex justify-content-center">
                  <div class="col-lg-6">

                    <form name="submit-new" id="submit-new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">


                      <!-- Title input -->
                      <div class="d-flex justify-content-center" id="div1"></div>
                      <div class="form-outline mb-4 input-group">
                        <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-angle-double-right"></i></span>
                        <input type="text" id="form4Example1" class="form-control" name="title" oninput="validateTitle();validateForm()">
                        <label class="form-label" for="form4Example1" style="margin-left: 0px;">Title</label>
                        <div class="form-notch">
                          <div class="form-notch-leading" style="width: 9px;"></div>
                          <div class="form-notch-middle" style="width: 42.4px;"></div>
                          <div class="form-notch-trailing"></div>
                        </div>

                      </div>
                      <!-- Site input -->
                      <div class="d-flex justify-content-center" id="div2"></div>
                      <div class="form-outline mb-4 input-group">
                        <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-satellite-dish"></i></span>
                        <input type="text" id="form4Example2" class="form-control" name="site" oninput="validateSite();validateForm()">
                        <label class="form-label" for="form4Example2" style="margin-left: 0px;">Site</label>
                        <div class="form-notch">
                          <div class="form-notch-leading" style="width: 9px;"></div>
                          <div class="form-notch-middle" style="width: 42.4px;"></div>
                          <div class="form-notch-trailing"></div>
                        </div>

                      </div>
                      <!-- Username input -->
                      <div class="d-flex justify-content-center" id="div3"></div>
                      <div class="form-outline mb-4 input-group">
                        <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-user"></i></span>
                        <input type="text" id="form4Example3" class="form-control" name="username" oninput="validateUsername();validateForm()">
                        <label class="form-label" for="form4Example3" style="margin-left: 0px;">Username / E-mail</label>
                        <div class="form-notch">
                          <div class="form-notch-leading" style="width: 9px;"></div>
                          <div class="form-notch-middle" style="width: 88.8px;"></div>
                          <div class="form-notch-trailing"></div>
                        </div>

                      </div>
                      <!-- Password input -->
                      <div class="d-flex justify-content-center" id="div4"></div>
                      <div class="form-outline mb-4 input-group">
                        <span class="input-group-text" style="width: 2.5em;"><i class="fas fa-key"></i></span>
                        <input type="password" id="form4Example4" class="form-control" name="password" oninput="validatePassword();validateForm()">
                        <label class="form-label" for="form4Example4" style="margin-left: 0px;">Password</label>
                        <div class="form-notch">
                          <div class="form-notch-leading" style="width: 9px;"></div>
                          <div class="form-notch-middle" style="width: 88.8px;"></div>
                          <div class="form-notch-trailing"></div>
                        </div>

                      </div>

                      <!-- Idea -->
                      <div class="form-check d-flex justify-content-center mb-4">
                        <label class="form-check-label">We'll never share your password with anyone else.</label>
                      </div>

                      <div class="row">
                        <div class="col-6 mb-lg-0">

                          <!-- Back Button-->

                          <a href="index.php"><button type="button" class="btn btn-primary"><i class="fas fa-arrow-circle-left"></i></button></a>
                        </div>

                        <!-- Submit button -->
                        <div class="col-6 mb-lg-0">
                          <button type="submit" class="btn btn-primary" id="send" disabled="disabled"><i class="fas fa-check-circle"></i></button>
                        </div>
                      </div>
                    </form>
                    <script type="text/javascript" src="js/new.js"></script>

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