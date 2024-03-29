<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("Location:home.php");
}
?>
<?php
include_once "php/head.php";
include_once "php/conn.php";
if (!isset($_GET['tag_name'])) {
    $_GET['tag_name'] = "";
}
?>


<div id="preview" class="preview">
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
                            height: 100vh;
                            background-attachment: fixed;
                        }

                        .tag-badge {
                            position: relative;
                        }

                        .tag-badge:hover {
                            background-color: red;
                            color: red;
                        }

                        .tag-badge:hover::before {
                            content: 'X';
                            position: absolute;
                            color: white;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            font-weight: bold;
                        }
                    </style>
                    <div>
                        <div class="container text-center">
                            <!-- Center elements -->

                            <div class="d-flex align-items-center justify-content-center row">
                                <div class="col-2">
                                    <a class="navbar-brand" href="home.php">
                                        <img src="https://raw.githubusercontent.com/ZXY-CC-3ag13/GitPass/main/src/img/brand.svg" class="w-100 rounded-4" alt="Logo" aria-controls="#picker-editor" style="height:3em;min-width: 2em;">
                                    </a>
                                </div>
                                <div class="col-8">
                                    <h1 class="display-3 fw-bold ls-tight my-3" style="color: hsl(218, 81%, 95%)">
                                        <span>Dashboard</span>
                                    </h1>
                                </div>
                                <div class="col-1 ml-1">
                                    <a href="profile.php?id=<?php echo $_SESSION['unique_id']; ?>"><button class="btn btn-primary mb-2 "><i class="fas fa-user"></i></button></a>
                                </div>
                                <div class="col-1">
                                    <a href="logout.php?id=<?php echo $_SESSION['unique_id']; ?>"><button class="btn btn-danger mb-2 "><i class="fas fa-sign-out-alt"></i></button></a>
                                </div>
                            </div>

                            <!-- Right elements -->
                        </div>




                        <div class="card mx-3" style="background-color: hsla(0, 0%, 100%, 0) !important;box-shadow: none !important;">
                            <div class="card-body justify-content-center">
                                <div class="text-center">
                                    <a href="new.php" class="mb-4"><button class="btn mb-2" style="color: rgba(255, 255, 255, 0.945);box-shadow: none;font-size: 2em;"><i class="fas fa-plus-circle"></i></button></a>
                                </div>

                                <!-- Tag selector -->
                                <!-- Get the distinct tags from social_accounts_tags table and make it radio box -->

                                <form method="GET" action="index.php">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-center">
                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">


                                                    <?php
                                                    $stmt = $conn->prepare("SELECT DISTINCT tag_name FROM social_account_tags");
                                                    $stmt->execute();
                                                    $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                                    $stmt->execute();

                                                    if ($_GET['tag_name'] == "") {
                                                        echo '<button type="submit" class="btn btn-primary" name="tag_name" value="">All</button>';
                                                    } else {
                                                        echo '<button type="submit" class="btn btn-outline-primary" name="tag_name" value="">All</button>';
                                                    }
                                                    foreach ($tags as $tag) {
                                                        $activeClass = isset($_GET['tag_name']) && $_GET['tag_name'] === $tag ? 'btn-primary' : 'btn-outline-primary';
                                                        echo '<button type="submit" class="btn ' . $activeClass . '" name="tag_name" value="' . $tag . '">' . $tag . '</button>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <?php
                                $counter = 1;
                                $userId = $_SESSION['unique_id'];
                                if (isset($_GET['tag_name']) && $_GET['tag_name'] != "") {
                                    $tag_name = $_GET['tag_name'];
                                    $sql =  "SELECT sa.site, sa.title, sa.username, AES_DECRYPT(sa.password, :encryptionKey) AS password, sam.favorite, sam.metadata_id, sa.account_id, sa.user_id FROM social_accounts sa LEFT JOIN social_account_metadata sam ON sa.account_id = sam.account_id WHERE sa.user_id = :userId AND sa.account_id IN (SELECT account_id FROM social_account_tags WHERE tag_name = :tag_name) ORDER BY sam.favorite DESC, sa.username;";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':userId', $userId);
                                    $stmt->bindParam(':tag_name', $tag_name);
                                    $stmt->bindParam(':encryptionKey', $_SESSION['password']);
                                    $stmt->execute();
                                    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } else {
                                    $sql =  "SELECT sa.site, sa.title, sa.username, AES_DECRYPT(sa.password, :encryptionKey) AS password, sam.favorite, sam.metadata_id, sa.account_id, sa.user_id FROM social_accounts sa LEFT JOIN social_account_metadata sam ON sa.account_id = sam.account_id WHERE sa.user_id = :userId ORDER BY sam.favorite DESC, sa.username;";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bindParam(':userId', $userId);
                                    $stmt->bindParam(':encryptionKey', $_SESSION['password']);
                                    $stmt->execute();
                                    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $account_id = $_SESSION['unique_id'];
                                $stmt = $conn->prepare("SELECT previous_username,previous_password, changed_at FROM password_history WHERE account_id = :account_id ORDER BY changed_at DESC");
                                $stmt->bindParam(':account_id', $account_id);
                                $stmt->execute();
                                $array1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (empty($array)) {
                                    echo '
                                    <div class="card mt-3" style="background-color: hsla(0, 0%, 100%, 0.9) !important; backdrop-filter: saturate(200%) blur(25px);">
                                    <div class="card-body">

                                    <div class="text-center"><h5>No Records<h5></div>

                                    </div>
                                    </div>';
                                } else {
                                    foreach ($array as $item) {
                                        // favorite starts
                                        echo '
                                                <div class="card mt-3" style="background-color: hsla(0, 0%, 100%, 0.9) !important; backdrop-filter: saturate(200%) blur(25px);">
                                                    <div class="card-body">
                                                        <div class="row mx-3">
                                                            <div class="col-5"><h5><a href="' . $item["site"] . '" target="_blank">' . $item["title"] . '</a></h5></div>
                                                            <div class="col-5 d-flex justify-content-start">
                                                            <form method="post" action="php/add_tags.php" class="me-4">
                                                            <input type="hidden" name="account_id" value="' . $item['account_id'] . '">
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control tag-input" name="tag_name" placeholder="Add tag...">
                                                                <button type="submit" class="btn btn-outline-secondary" title="Add Tag">+</button>
                                                            </div>
                                                        </form>';
                                        $stmt = $conn->prepare("SELECT tag_name FROM social_account_tags WHERE account_id = :account_id");
                                        $stmt->bindParam(':account_id', $item['account_id']);
                                        $stmt->execute();
                                        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

                                        if (!empty($tags)) {
                                            echo '<span class="text-muted mt-1 me-2">Tags:</span>';
                                            foreach ($tags as $tag) {
                                                echo '<form method="post" action="php/delete_tag.php" class="d-inline-block">';
                                                echo '<input type="hidden" name="account_id" value="' . $item['account_id'] . '">';
                                                echo '<input type="hidden" name="tag_name" value="' . htmlspecialchars($tag) . '">';
                                                echo '<button type="submit" class="btn btn-secondary tag-badge p-2 me-2">' . htmlspecialchars($tag) . '</button>';
                                                echo '</form>';
                                            }
                                        }

                                        echo '</div> <div class="col-2 d-flex justify-content-end">
                                                                <form method="post" class="me-4" action="php/favorite.php" id="favorite-' . $item['metadata_id'] . '">
                                                                    <input type="hidden" name="favid" value="' . $item['metadata_id'] . '">';
                                        if ($item["favorite"]) {
                                            echo '<i class="fas fa-star text-warning" style="cursor: pointer;" title="Remove from favorites" onclick="document.getElementById(\'favorite-' . $item['metadata_id'] . '\').submit();"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="cursor: pointer;" title="Add to favorites" onclick="document.getElementById(\'favorite-' . $item['metadata_id'] . '\').submit();"></i>';
                                        }
                                        // favorite ends

                                        echo '
        </form>
        <form method="post" action="php/delete.php" id="delete-' . $item['account_id'] . '">
            <input type="hidden" name="delid" value="' . $item['account_id'] . '">
            <i class="fas fa-trash-alt" style="cursor: pointer;" title="Delete" onclick="document.getElementById(\'delete-' . $item['account_id'] . '\').submit();"></i>
        </form>
                    </div>
                </div>
                <hr class="mt-2">
                
                <form method="post" class="me-4" action="php/update.php" id="update-' . $item['account_id'] . '">

                    <div class="row px-3">
                        <div class="col-lg-6 col-md-12 mb-lg-0 mb-2">
                            <label for="username-' . $counter . '" class="form-label">Username</label>
                            <input id="username-' . $counter . '" type="text" class="form-control"
                                value="' . $item['username'] . '" contenteditable="true" name="username" oninput="checkChanges' . $counter . '()">
                        </div>
                        <div class="col-lg-6 col-md-12 mb-lg-0">
                            <label for="password-' . $counter . '" class="form-label">Password</label>
                            <div class="input-group mb-3">
                                <input id="password-' . $counter . '" type="password" class="form-control"
                                    value="' . $item["password"] . '" contenteditable="true"
                                    name="password" oninput="checkChanges' . $counter . '()">
                                <span class="input-group-text"><i class="fas fa-eye"
                                        style="cursor: pointer;" id="password-' . $counter . '-eye"
                                        onclick="showPw(\'password-' . $counter . '\')"> </i></span>
                            </div>
                        </div>
                        
                            <div class="text-center" id="UpdateBox-' . $counter . '">
                            <button type="submit" name="upid" value="' . $item["account_id"] . '" style="border: none;background-color:#e8ecf400"><i class="fas fa-edit" style="color: rgba(0, 0, 0, 0.685);box-shadow: none;font-size: 2em;" title="Update"  onclick="document.getElementById(\'update-' . $item['account_id'] . '\').submit();"></i></button>
                            </div>
                    </div>
                </form>
            <form method="post" class="me-4" action="history.php" id="history-' . $item['account_id'] . '">
                <div class="text-center" id="HistoryBox">
                    <button type="submit" name="hisid" value="' . $item["account_id"] . '" style="border: none;background-color:#e8ecf400"><i class="fa-solid fa-clock-rotate-left" style="color: rgba(0, 0, 0, 0.685);box-shadow: none;font-size: 2em;" title="History"  onclick="document.getElementById(\'history-' . $item['account_id'] . '\').submit();"></i></button>
                </div>
            </form>
        </div>
    </div>
                                        <script>
                                    var originalUsername' . $counter . ' = "' . $item['username'] . '";
                                    var originalPassword' . $counter . ' = "' . $item['password'] . '";

                                    function checkChanges' . $counter . '() {
                                        var usernameField = document.getElementById("username-' . $counter . '");
                                        var passwordField = document.getElementById("password-' . $counter . '");

                                        if (usernameField.value === originalUsername' . $counter . ' && passwordField.value === originalPassword' . $counter . ') {
                                        document.getElementById("UpdateBox-' . $counter . '").style.display = "none";
                                        } else {
                                        document.getElementById("UpdateBox-' . $counter . '").style.display = "block";
                                        }
                                    }
                                    checkChanges' . $counter . '();
                                    </script>';
                                        $counter++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                </section>
            </section>
            <!---->
        </div>
        <script type="text/javascript" src="js/showPw.js"></script>
        <?php
        include_once "php/footer.php";
        ?>