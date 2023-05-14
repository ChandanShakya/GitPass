<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
  header("Location:login.php");
}
?>
<?php
include_once "php/head.php";
include_once "php/conn.php";

$userId = $_SESSION['unique_id'];
$account_id = $_POST['hisid'];
$stmt = $conn->prepare("SELECT previous_username,previous_password, changed_at FROM password_history WHERE account_id = :account_id ORDER BY changed_at DESC");
$stmt->bindParam(':account_id', $account_id);
$stmt->execute();
$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
$conn = null;
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
              background-attachment: fixed;
            }
          </style> <!-- Gradient Background -->
          <!-- Features -->
          <div class="container">
            <div class="d-flex justify-content-center text-center pt-5">
              <div class="p-4 rounded-6 shadow-3-strong w-75 mt-5" style="background-color: hsla(0, 0%, 100%, 0.8) !important; backdrop-filter: saturate(200%) blur(25px);;">

                <div class="mb-4 mb-lg-0 position-relative"> <a href="home.php"><img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg" class="text-primary" alt="Logo" aria-controls="#picker-editor" style="width:5em;"> </a></div>

                <h1 class="mt-4 mb-3 display-3 fw-bold ls-tight">

                  Password History <br></h1>

                <div class="row d-flex justify-content-center">
                  <div class="col-lg-9">

                    <form name="history" id="history" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                    <?php
                    $counter = 1;
                    if (empty($array)) {
                      echo '<div class="alert alert-info">No old history</div>';
                  } else {
                    foreach ($array as $item) {
                    // favorite starts
                    echo '
                    <div class="card mt-3" style="background-color: hsla(0, 0%, 100%, 0.9) !important; backdrop-filter: saturate(200%) blur(25px);">
                        <div class="card-body">
                            <div class="row mx-3">
                                <div class="col-10"><h5>' . $item["changed_at"] . '</h5></div>
                                <div class="col-2 d-flex justify-content-between">
                            </div>
                          </div>
                          <hr class="mt-2">
                          <form method="post" class="me-4" action="php/update.php" id="update-' . $item['account_id'] . '">
                            <div class="row px-3">
                                <div class="col-lg-6 col-md-12 mb-lg-0 mb-2">
                                    <label for="username-' . $counter . '" class="form-label">Username</label>
                                    <input id="username-' . $counter . '" type="text" class="form-control"
                                        value="' . $item['previous_username'] . '" contenteditable="true" name="username">
                                </div>
                                <div class="col-lg-6 col-md-12 mb-lg-0">
                                    <label for="password-' . $counter . '" class="form-label">Password</label>
                                    <div class="input-group mb-3">
                                        <input id="password-' . $counter . '" type="text" class="form-control"
                                            value="' . $item["previous_password"] . '" contenteditable="true"
                                            name="password">
                                    </div>
                                </div>
                            </div>
                          </form>
                        </div>
                      </div>';
                    }
                      $counter++;
                    }
                    ?>
                    
                      <div class="row mt-3">
                        <div class="center">
                          <!-- Back Button-->
                          <a href="index.php"><button type="button" class="btn btn-primary"><i class="fas fa-arrow-circle-left"></i></button></a>
                        </div>
                      </div>
                    </form>

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