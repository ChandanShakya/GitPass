<?php 
    session_start();
    if(!isset($_SESSION['unique_id'])) {
        header("Location:home.php");
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

<div id="preview" class="preview">
    <div style="display: none;"></div>
    <div>
        <div data-draggable="true" style="position: relative;">
            <!---->
            <!---->
            <section draggable="false" class="overflow-hidden pt-0" data-v-271253ee="">
                <section class="background-radial-gradient">
                    <style>
                        body {
                            background-color: hsl(218, 41%, 15%);
                            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%), radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
                            height: auto;
                        min-height: 100%;
                        background-size:cover;
                        }
                    </style>
                    <div>
                        <div class="container text-center">
                            <!-- Center elements -->

                            <div class="d-flex align-items-center justify-content-center row">
                                <div class="col-3">
                                    <a class="navbar-brand" href="home.php">
                                        <img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg"
                                            class="w-100 rounded-4" alt="Logo" aria-controls="#picker-editor"
                                            style="height:3em;min-width: 2em;">
                                    </a>
                                </div>
                                <div class="col-6">
                                    <h1 class="display-3 fw-bold ls-tight my-3" style="color: hsl(218, 81%, 95%)">
                                        <span>Dashboard</span>
                                    </h1>
                                </div>
                                <div class="col-3 ml-1">
                                    <a href="logout.php?id=<?php echo $_SESSION['unique_id']; ?>"><button
                                            class="btn btn-danger mb-2 "><i
                                                class="fas fa-sign-out-alt"></i></button></a>
                                </div>
                            </div>

                            <!-- Right elements -->
                        </div>




                        <div class="card mx-3"
                            style="background-color: hsla(0, 0%, 100%, 0) !important;box-shadow: none !important;">
                            <div class="card-body justify-content-center">
                                <div class="text-center">
                                    <a href="new.php" class="mb-4"><button class="btn mb-2"
                                            style="color: rgba(255, 255, 255, 0.945);box-shadow: none;font-size: 2em;"><i
                                                class="fas fa-plus-circle"></i></button></a>
                                </div>




                                <?php 
                                                $counter = 1;
                                                foreach($array as $item) {
                                                    echo '
                                                    <div class="card mt-3" style="background-color: hsla(0, 0%, 100%, 0.9) !important; backdrop-filter: saturate(200%) blur(25px);;">
                                                        <div class="card-body">
                                                            <div class="row mx-3">
                                                                <div class="col-10"><h5><a href="'.$item["site"].'" target="_blank">'.$item["title"].'</a></h5></div>
                                                                <div class="col-2 d-flex justify-content-between">
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
                                                                        <i class="fas fa-trash-alt" style="cursor: pointer;" title="Delete" onclick="document.getElementById(\'delete-'.$item['id'].'\').submit();"></i>
                                                                    </form>
                                                                </div>
                                
                                                                
                                                            </div>
                                                            <hr class="mt-2">


                                                           

                                                        <form method="post" class="me-4" action="php/update.php" id="update-'.$item['id'].'">

                                    <div class="row px-3">
                                        <div class="col-lg-6 col-md-12 mb-lg-0 mb-2">
                                            <label for="username-'.$counter.'" class="form-label">Username</label>
                                            <input id="username-'.$counter.'" type="text" class="form-control"
                                                value="'.$item['username'].'" contenteditable="true" name="username">
                                        </div>
                                        <div class="col-lg-6 col-md-12 mb-lg-0">
                                            <label for="password-'.$counter.'" class="form-label">Password</label>
                                            <div class="input-group mb-3">
                                                <input id="password-'.$counter.'" type="password" class="form-control"
                                                    value="'.$item["password"].'" contenteditable="true"
                                                    name="password">
                                                <span class="input-group-text"><i class="fas fa-eye"
                                                        style="cursor: pointer;" id="password-'.$counter.'-eye"
                                                        onclick="showPw(\'password-'.$counter.'\')"> </i></span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="upid" value="'.$item['id'].'" style="border: none;">
                                                <i class="fas fa-edit"
                                                    style="color: rgba(0, 0, 0, 0.685);box-shadow: none;font-size: 2em;"
                                                    title="Update"  onclick="document.getElementById(\'update-'.$item['id'].'\').submit();"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>';
                        $counter++;
                        }
                        ?>

                            </div>
                        </div>
                    </div>

                </section>
            </section>
            <!---->
        </div>
        <script>
            function showPw(id) {
                const field = document.getElementById(id);
                const eye = document.getElementById(`${id}-eye`);
                if (field.type == "text") {
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