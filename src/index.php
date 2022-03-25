<?php 
    session_start();
    if(!isset($_SESSION['unique_id'])) {
        header("Location:login.php");
    }
?>
<?php 
    include_once "php/head.php";
    include_once "php/conn.php";

    $userId = $_SESSION['unique_id'];
    $res = $conn->query("SELECT * FROM passwords WHERE user_id = {$userId} ORDER BY (favorite IS TRUE) DESC, username;");
    $array = array();

    while($item = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $array[] = $item;
    }

    $conn->close();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 mt-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="display-5 align-center">GitPass</h3>
                    <hr class="mt-3 mb-4">
                    <a href="new.php" class="mb-4"><button class="btn btn-success mb-2"><i class="far fa-bookmark"></i> &nbsp;New</button></a>
                    <a href="logout.php?id=<?php echo $_SESSION['unique_id']; ?>" class="mb-4"><button class="btn btn-danger mb-2"><i class="fas fa-sign-out-alt"></i> &nbsp;Log Out</button></a>

                    <?php 
                        $counter = 1;
                        foreach($array as $item) {
                            echo '
                            <div class="card mt-3">
                                <div class="card-body">
                                    <div class="row px-3">
                                        <div class="col-11"><h5><a href="'.$item["site"].'" target="_blank">'.$item["site"].'</a></h5></div>
                                        <div class="col-1 d-flex" style="text-align: right;">
                                            <form method="post" class="me-4" action="php/favorite.php" id="favorite-'.$item['id'].'">
                                                <input type="hidden" name="favid" value="'.$item['id'].'">';
                                                if($item["favorite"]) {
                                                    echo '<i class="fas fa-star text-warning" style="cursor: pointer;" title="Remove from favorites" onclick="document.getElementById(\'favorite-'.$item['id'].'\').submit();"></i>';
                                                } else {
                                                    echo '<i class="far fa-star" style="cursor: pointer;" title="Add to favorites" onclick="document.getElementById(\'favorite-'.$item['id'].'\').submit();"></i>';
                                                }
                                        echo '</form>
                                            <form method="post" action="php/delete.php" id="delete-'.$item['id'].'">
                                                <input type="hidden" name="delid" value="'.$item['id'].'">
                                                <i class="fas fa-trash-alt text-danger" style="cursor: pointer;" title="Delete" onclick="document.getElementById(\'delete-'.$item['id'].'\').submit();"></i>
                                            </form>
                                        </div>
        
                                        <hr class="mt-2">
                                    </div>
        
                                    <div class="row px-3">
                                        <div class="col-6">
                                            <label for="username-'.$counter.'" class="form-label">Username</label>
                                            <input id="username-'.$counter.'" type="text" class="form-control" value="'.$item['username'].'" disabled>
                                        </div>
                                        <div class="col-6">
                                            <label for="password-'.$counter.'" class="form-label">Password</label>
                                            <div class="input-group mb-3">
                                                <input id="password-'.$counter.'" type="password" class="form-control" value="'.$item["password"].'" disabled>
                                                <span class="input-group-text"><i class="fas fa-eye" style="cursor: pointer;" id="password-'.$counter.'-eye" onclick="showPw(\'password-'.$counter.'\')"> </i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $counter++;
                        }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showPw(id) {
        const field = document.getElementById(id);
        const eye = document.getElementById(`${id}-eye`);
        if(field.type == "text") {
            field.type = "password";
            eye.classList.remove("fa-eye-slash");
            eye.classList.add("fa-eye");
        } else {
            field.type = "text";
            eye.classList.add("fa-eye-slash");
            eye.classList.remove("fa-eye");
        }
    }
</script>

<?php 
    include_once "php/footer.php";
?>