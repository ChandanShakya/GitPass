<?php 
    session_start();
    if(!isset($_SESSION['unique_id'])) {
        header("Location:login.php");
    }
?>
<?php 
    include_once "php/head.php";
    include_once "php/conn.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $site = $_POST['site'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $userId = $_SESSION['unique_id'];
        $conn->query("INSERT INTO passwords(site, username, password, user_id) VALUES ('{$site}', '{$username}', '{$password}', {$userId});");
        //header("Location:index.php");
        //header("Location:http://" . $_SERVER['HTTP_HOST']."/index.php");
        echo "<script>window.open('index.php','_self') </script>"; 

    }

    $conn->close();
?>

<div class="container">
    <div class="row">
        <div class="col-12 mt-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <h1 class="display-6 align-center">New</h1>
                    <hr>
                    <form name="submit-new" id="submit-new" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
                        <div class="mb-3">
                            <label for="site" class="form-label">Site</label>
                            <input type="url" class="form-control" id="site" name="site" placeholder="https://www.example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <div class="form-text">We'll never share your password with anyone else.</div>
                        </div>
                        <a href="index.php"><button type="button" class="btn btn-secondary"><i class="fas fa-arrow-circle-left"></i> &nbsp;Back</button></a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle"></i> &nbsp;Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    include_once "php/footer.php";
?>