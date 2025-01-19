<?php
require 'include/config.php';

// require 'include/leave_code.php';

if (!isset($_SESSION['fullname'])) {
  header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Amaan Project</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo2.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- jQuery File -->
  <script src="assets/jquery/jquery.min.js"></script>

</head>

<body>
  <!-- Header File -->
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo2.png" alt="">
        <span class="d-none d-lg-block">AmaanProject</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <!-- Notification Nav Start -->
        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" id="notificationDropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number" id="notificationCount"></span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" id="notificationList">

            <!-- Notifications will be dynamically loaded here -->
            <script>
              $(document).ready(function() {
                // Function to fetch notifications
                function fetchNotifications() {
                  $.ajax({
                    url: 'include/notification_code.php',
                    type: 'GET',
                    dataType: 'json', // response is already parsed into a JavaScript object when the dataType is set to json in the AJAX request
                    success: function(response) {

                      if (response.success) {
                        let notifications = response.notifications;
                        let notificationCount = notifications.length;
                        $('#notificationCount').text(notificationCount);
                        $('#notificationHeaderCount').text(notificationCount);

                        let notificationItems = '';
                        notifications.forEach(function(item) {
                          notificationItems += `
                            <a href="leave_request.php">
                              <li class="notification-item">
                                <i class="bi bi-info-circle text-warning"></i>
                                <div>
                                  <h4>${item.name}</h4>
                                  <p>${item.leave_type}: ${item.reason}</p>
                                  <p>${item.created_at}</p>
                                </div>
                              </li>
                            </a>
                            <li>
                              <hr class="dropdown-divider">
                            </li>
                          `;
                        });
                        $('#notificationList').html(`
                          <li class="dropdown-header">
                            You have ${notificationCount} new notifications
                          </li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          ${notificationItems}
                          <li class="dropdown-footer">
                            <a href="#">Show all notifications</a>
                          </li>
                        `);
                      }
                    },
                    error: function(xhr, status, error) { //Error Debugging: Update the error function to log more detailed errors:
                      console.error('AJAX Error:', status, error);
                      console.log(xhr.responseText);
                    }
                  });
                }

                // Fetch notifications on dropdown click
                $('#notificationDropdown').on('click', function() {
                  fetchNotifications();
                });

                // Initial fetch to update badge count on page load
                fetchNotifications();
              });
            </script>

            <!-- <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <i class="bi bi-info-circle text-primary"></i>
              <i class="bi bi-check-circle text-success"></i>
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li> -->

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <!-- Messages Nav Start -->
        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a><!-- End Messages Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li>

          </ul><!-- End Messages Dropdown Items -->

        </li><!-- End Messages Nav -->

        <!-- Profile Nav Start -->
        <li class="nav-item dropdown pe-3">

          <?php
          $sql = "SELECT * FROM personal_details WHERE profile_id={$_SESSION['userid']}";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $results = $stmt->fetch(PDO::FETCH_ASSOC);
          $image = $results['image'] ?? "default.jpg";
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="upload/userProfile/<?= $image ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?= $_SESSION['fullname']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['fullname']; ?></h6>
              <span>Web Designer</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.php">
                <i class="bi bi-question-circle"></i>
                <span>NeedHelp?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="./include/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->


  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Components</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="components-alerts.html">
              <i class="bi bi-circle"></i><span>Alerts</span>
            </a>
          </li>
          <li>
            <a href="components-accordion.html">
              <i class="bi bi-circle"></i><span>Accordion</span>
            </a>
          </li>
          <li>
            <a href="components-badges.html">
              <i class="bi bi-circle"></i><span>Badges</span>
            </a>
          </li>
          <li>
            <a href="components-breadcrumbs.html">
              <i class="bi bi-circle"></i><span>Breadcrumbs</span>
            </a>
          </li>
          <li>
            <a href="components-buttons.html">
              <i class="bi bi-circle"></i><span>Buttons</span>
            </a>
          </li>
          <li>
            <a href="components-cards.html">
              <i class="bi bi-circle"></i><span>Cards</span>
            </a>
          </li>
          <li>
            <a href="components-carousel.html">
              <i class="bi bi-circle"></i><span>Carousel</span>
            </a>
          </li>
          <li>
            <a href="components-list-group.html">
              <i class="bi bi-circle"></i><span>List group</span>
            </a>
          </li>
          <li>
            <a href="components-modal.html">
              <i class="bi bi-circle"></i><span>Modal</span>
            </a>
          </li>
          <li>
            <a href="components-tabs.html">
              <i class="bi bi-circle"></i><span>Tabs</span>
            </a>
          </li>
          <li>
            <a href="components-pagination.html">
              <i class="bi bi-circle"></i><span>Pagination</span>
            </a>
          </li>
          <li>
            <a href="components-progress.html">
              <i class="bi bi-circle"></i><span>Progress</span>
            </a>
          </li>
          <li>
            <a href="components-spinners.html">
              <i class="bi bi-circle"></i><span>Spinners</span>
            </a>
          </li>
          <li>
            <a href="components-tooltips.html">
              <i class="bi bi-circle"></i><span>Tooltips</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <!-- Add Organization Nav Start-->
      <?php
      // Check if the role is set in the session
      if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        // Show Add Role and Department only for admin
        echo '
          <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#organization-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-menu-button-wide"></i><span>Organization</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="organization-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
              <li class="nav-item">
                <a class="nav-link collapsed" href="employee_role.php">
                  <i class="bi bi-circle"></i>
                  <span>Add Role</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link collapsed" href="add_department.php">
                  <i class="bi bi-circle"></i>
                  <span>Add Department</span>
                </a>
              </li>
            </ul>
          </li><!-- End Components Nav -->
      ';
      }
      ?><!-- End Add Organization Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Forms</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="forms-elements.html">
              <i class="bi bi-circle"></i><span>Form Elements</span>
            </a>
          </li>
          <li>
            <a href="forms-layouts.html">
              <i class="bi bi-circle"></i><span>Form Layouts</span>
            </a>
          </li>
          <li>
            <a href="forms-editors.html">
              <i class="bi bi-circle"></i><span>Form Editors</span>
            </a>
          </li>
          <li>
            <a href="forms-validation.html">
              <i class="bi bi-circle"></i><span>Form Validation</span>
            </a>
          </li>
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#leaves-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Leaves</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="leaves-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

          <?php
          // Check if the role is set in the session
          if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            // Show Add Leave Type only for admin
            echo '
            <li class="nav-item">
              <a class="nav-link collapsed" href="add_leaveType.php">
                <i class="bi bi-circle"></i>
                <span>Add Leave Type</span>
              </a>
            </li>';
          }
          ?><!-- End Leave Type Nav -->

          <?php
          // Check if the role is set in the session
          if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
            // Show Leave Request only for admin
            echo '
            <li class="nav-item">
              <a class="nav-link collapsed" href="leave_request.php">
                <i class="bi bi-circle"></i>
                <span>Leave Request</span>
              </a>
            </li>';
          }
          ?><!-- End Leave Request History Nav -->

          <li class="nav-item">
            <a class="nav-link collapsed" href="leave_apply.php">
              <i class="bi bi-circle"></i>
              <span>Leave Application</span>
            </a>
          </li><!-- End Apply Leave Nav -->

        </ul>
      </li><!-- End Tables Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart"></i><span>Charts</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="charts-chartjs.html">
              <i class="bi bi-circle"></i><span>Chart.js</span>
            </a>
          </li>
          <li>
            <a href="charts-apexcharts.html">
              <i class="bi bi-circle"></i><span>ApexCharts</span>
            </a>
          </li>
          <li>
            <a href="charts-echarts.html">
              <i class="bi bi-circle"></i><span>ECharts</span>
            </a>
          </li>
        </ul>
      </li><!-- End Charts Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#icons-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gem"></i><span>Icons</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="icons-bootstrap.html">
              <i class="bi bi-circle"></i><span>Bootstrap Icons</span>
            </a>
          </li>
          <li>
            <a href="icons-remix.html">
              <i class="bi bi-circle"></i><span>Remix Icons</span>
            </a>
          </li>
          <li>
            <a href="icons-boxicons.html">
              <i class="bi bi-circle"></i><span>Boxicons</span>
            </a>
          </li>
        </ul>
      </li><!-- End Icons Nav -->

      <li class="nav-heading">Pages</li>

      <?php
      // Check if the role is set in the session
      if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        // Show Assign Department only for admin
        echo '
      <li class="nav-item">
        <a class="nav-link collapsed" href="assign_department.php">
          <i class="bi bi-person"></i>
          <span>Assign Department</span>
        </a>
      </li>';
      }
      ?><!-- End Assign Department Nav -->

      <?php
      // Check if the role is set in the session
      if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        // Show Employee Details only for admin
        echo '
        <li class="nav-item">
          <a class="nav-link collapsed" href="employee_registraion.php">
            <i class="bi bi-person"></i>
            <span>Employee Details</span>
          </a>
        </li>';
      }
      ?><!-- End Employee Register Nav -->

      <?php
      // Check if the role is set in the session
      if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        // Show User Log only for admin
        echo '
      <li class="nav-item">
        <a class="nav-link collapsed" href="userlog-table.php">
          <i class="bi bi-person"></i>
          <span>User Log</span>
        </a>
      </li>';
      }
      ?><!-- End User Log Nav -->

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li> -->
      <!-- End Profile Page Nav -->

      <?php
      // Check if the role is set in the session
      if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
        // Show User Log only for admin
        echo '
        <li class="nav-item">
          <a class="nav-link collapsed" href="employee-report.php">
            <i class="bi bi-person"></i>
            <span>Report</span>
          </a>
        </li><!-- End Report Page Nav -->';
      }
      ?><!-- End User Log Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-faq.php">
          <i class="bi bi-question-circle"></i>
          <span>F.A.Q</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.php">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="register.php">
          <i class="bi bi-card-list"></i>
          <span>Register</span>
        </a>
      </li><!-- End Register Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="login.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li><!-- End Login Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-error-404.html">
          <i class="bi bi-dash-circle"></i>
          <span>Error 404</span>
        </a>
      </li><!-- End Error 404 Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-blank.php">
          <i class="bi bi-file-earmark"></i>
          <span>Blank</span>
        </a>
      </li><!-- End Blank Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->