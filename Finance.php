<?php 
// // some imports in here
$url = "finance" ;
require_once ('./back/php/check_login_status.php');
include_once('./back/php/g.manager/update_users.php');

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> finance Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
        <!-- message shower with  notyf-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
        <!-- Font Awesome CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


        <!-- user imprted css -->
        <link rel="stylesheet" href="./css/style1.css">
        <link rel="stylesheet" href="./css/userTable.css">
        <link rel="stylesheet" href="./css/table.css">
        <?php 
        include_once('./back/php/get_buget.php');
        ?>

    </head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar p-1">
                    <div class="position-sticky">
                        <div class="sidebar-header d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="Company Logo" class="rounded-circle me-2">
                            <span class="fs-3 text">SSTA</span>
                            <h6 class=" d-flex text"><?php echo "c:0".  $_SESSION['user']['code']?></h6>
                        </div>
                        <div class="sidebar-body">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#" data-bs-target="#viewStatus">
                                        <i class="bi bi-speedometer2 me-2"></i><span class="text">View Status</span>
                                    </a>
                                </li>

                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-target="#messages">
                                        <i class="bi bi-envelope me-2"></i><span class="text">approval</span>
                                    </a>
                                </li> -->
                                <li class="nav-item">
                                    <a class="nav-link" href="#" id="logoutLink"
                                        onclick="document.getElementById('logoutForm').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i><span class="text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="sidebar-footer d-flex align-items-center gap-lg-5">

                            <span
                                class="text"><?php echo $_SESSION['user']['fname'] ." " . $_SESSION['user']['lname']?></span>

                            <i data-toggle="modal" data-target="#loginModal" style="cursor: pointer;"
                                class="fa-solid fa-gear cursor-pointer"></i>

                        </div>
                    </div>
                    <div class="toggle-button" id="toggleButton">
                        <i class="bi bi-chevron-left"></i>
                    </div>
                </nav>

                <!-- Hidden form to handle logout -->
                <form id="logoutForm" action="./back/php/check_login_status.php" method="POST" style="display: none;">
                    <input type="hidden" name="logout" value="logout">
                </form>

                <!-- Main Content -->
                <main class="col-md-9 col-lg-10 px-md-4 main-content">
                    <div class="content" id="viewStatus">
                        <h2>View Status</h2>
                        <div id="table-container"></div>

                    </div>

                </main>
            </div>
        </div>
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">update</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="loginForm" action="" method="post">
                            <div class="form-group">
                                <rname for="username">Username:</label>
                                    <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


        <!-- sicript with notyf -->
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

        <!-- user add scripts -->
        <script src="./front/js/script1.js"></script>
        <script src="./front/js/jst.js"></script>

        <!-- Mammoth.js Library for DOCX -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>



        <script type="module">
        import NotyfService from "./front/js/message.shower.js";

        <?php if (isset($message)): ?>
        const message = JSON.parse('<?php echo json_encode($message)?>')
        NotyfService.showMessage(message.status, message.message);
        navigateToSlide(message.navigateToSlide || "viewStatus")
        <?php endif;
        $message = null;
        ?>
        </script>

    </body>

</html>