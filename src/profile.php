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
$account_id = $_GET['id'];
$stmt = $conn->prepare("SELECT username, email, AES_DECRYPT(password, :encryptionKey) AS password FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->bindParam(':encryptionKey', $_SESSION['password']);

$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

function fetchLoginHistory($conn, $userId)
{
    $stmt = $conn->prepare("SELECT * FROM login_track WHERE user_id = :user_id ORDER BY login_time DESC");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch login history for the current user
$login_history = fetchLoginHistory($conn, $userId);

// Handle saving information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, password = AES_ENCRYPT(:password, :encryptionKey) WHERE user_id = :user_id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':encryptionKey', $_SESSION['password']);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Update the user variable
    $user['username'] = $username;
    $user['email'] = $email;
    $user['password'] = $password;
}
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

                                <h1 class="mt-4 mb-3 display-3 fw-bold ls-tight"> User Profile <br></h1>

                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-9">
                                        
                                        <!-- Username -->
                                        <!-- Can view and edit it -->
                                        <form action="" method="POST">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Username</span>
                                                <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" name="username" value="<?php echo $user['username']; ?>">
                                            </div>
                                            <!-- Email -->
                                            <!-- Can view and edit it -->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Email</span>
                                                <input type="text" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1" name="email" value="<?php echo $user['email']; ?>">
                                            </div>
                                            <!-- Password -->
                                            <!-- Can view and edit it and eye button to toggle it-->
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Password</span>
                                                <input type="text" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1" name="password" value="<?php echo $user['password']; ?>">
                                            </div>
                                            <!-- Save Button-->
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </form>

                                        <div class="table-responsive mt-3">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Session ID</th>
                                                        <th>Login Time</th>
                                                        <th>Logout Time</th>
                                                        <th>Duration (Seconds)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($login_history as $login) {
                                                        echo '<tr>';
                                                        echo '<td>' . $login['session_id'] . '</td>';
                                                        echo '<td>' . $login['login_time'] . '</td>';
                                                        echo '<td>' . ($login['logout_time'] ? $login['logout_time'] : 'Not logged out') . '</td>';
                                                        echo '<td>' . ($login['duration'] ? $login['duration'] : '-') . '</td>';
                                                        echo '</tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Back Button-->
                                        <a href="index.php"><button type="button" class="btn btn-primary"><i class="fas fa-arrow-circle-left"></i></button></a>
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