<?php
$url = "g_manager";
require_once ('./back/php/check_login_status.php'); 
include_once('./back/php/g.manager/admin_create_account.php'); ?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
        <!-- message shower with  notyf-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

        <!-- user imprted css -->
        <link rel="stylesheet" href="./css/style1.css">
        <link rel="stylesheet" href="./css/userTable.css">


    </head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar p-1">
                    <div class="position-sticky">
                        <div class="sidebar-header d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" alt="Company Logo" cl class="rounded-circle me-2">
                            <span class="fs-3 text">SSTA</span>
                            <h6 class=" d-flex text"><?php echo "c:".  $_SESSION['user']['code']?></h6>
                        </div>

                        <div class="sidebar-body">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#" data-bs-target="#viewStatus">
                                        <i class="bi bi-speedometer2 me-2"></i><span class="text">View Status</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-target="#report">
                                        <i class="bi bi-file-earmark-text me-2"></i><span class="text">Report</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-target="#Manage_account">
                                        <i class="bi bi-person-circle me-2"></i><span class="text">Manage
                                            Account</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-target="#User_list">
                                        <i class="bi bi-envelope me-2"></i><span class="text">User list</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-bs-target="#messages">
                                        <i class="bi bi-envelope me-2"></i><span class="text">Messages</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" id="logoutLink"
                                        onclick="document.getElementById('logoutForm').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i><span class="text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="sidebar-footer d-flex align-items-center">
                            <img src="https://via.placeholder.com/40" alt="User Avatar" class="rounded-circle me-2">
                            <span class="text">John Doe</span>
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
                        <p>This is the View Status content.</p>
                    </div>
                    <div class="content" id="messages" style="display: none;">
                        <h2>Messages</h2>
                        <p>This is the Messages content.</p>
                    </div>
                    <div class="content" id="report" style="display: none;">
                        <h2> view Report</h2>
                        <div class="container-fluid d-flex">
                            <div class="row flex-fill">
                                <div class="col-md-4 left-side p-3" id="buttonContainer">
                                    <!-- Buttons will be generated here by JavaScript -->
                                </div>
                                <div class="col-md-8 right-side p-3">
                                    <div id="viewer" class="viewer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="User_list" style="display: none;">
                        <h2>user list</h2>
                        <div class="container">
                            <div class="header">
                                <h1>User List</h1>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>role</th>
                                        <th>Firstname</th>
                                        <th>Lastname</th>
                                        <th>Username</th>
                                        <th>Phone number</th>
                                        <th>code</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- to get the the vlues of the regesterd users -->
                                    <?php include('./back/php/g.manager/admin_get_account.php')?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="content" id="Manage_account" style="display: none;">
                        <h2>Manage Account</h2>

                        <div class="reg">
                            <div class="container">
                                <h2>Register</h2>
                                <form action="./g_manager.php" method="POST" id="reg">
                                    <div class="form-group">
                                        <label for="role">Role:</label>
                                        <select id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="finance">Finance</option>
                                            <option value="b_manager">B. Manager</option>
                                            <option value="director">Director</option>
                                            <option value="g_manager">g_manager</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name">First Name:</label>
                                        <input type="text" id="first_name" name="first_name" pattern="[a-zA-Z]+"
                                            title="only insert characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="code">Code:</label>
                                        <input type="text" id="code" name="code" pattern="[0-9]+"
                                            title="code must be in numbers" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name:</label>
                                        <input type="text" id="last_name" name="last_name" pattern="[a-zA-Z]+"
                                            title="only insert characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number:</label>
                                        <input type="tel" id="phone_number" name="phone_number" pattern=".{10,13}"
                                            title="phone_number at lest contain 10 digits" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" pattern="[a-zA-Z]+"
                                            title="only insert characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" name="password" pattern=".{8,}"
                                            title="password must be greater than 8 characters" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password:</label>
                                        <input type="password" name="confirm_password" id="confirm_password"
                                            pattern=".{8,}" title="password must be greater than 8 characters" required>
                                    </div>
                                    <span id="password_message" class="alert alert-block"></span>
                                    <div class="form-group">
                                        <input type="submit" name="submit" value="Register">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <!-- sicript with notyf -->
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

        <!-- user add scripts -->
        <script src="./front/js/script1.js"></script>

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