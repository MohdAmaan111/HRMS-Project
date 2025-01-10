<?php
require 'header.php';
$roleErr = $inputErr = $emailErr = $mobileErr = $usernameErr = "";
$name = $employeeID = $role = $mobile = $email = $username = $password = "";
?>

<main id="main" class="main">

  <div class="pagetitle" id="target-paragraph">
    <h1>Employee Details</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="nav-item">

    <span>Register as an Employee</span>
    <button type="button" class="btn btn-primary" onclick="add()">Add<i class="bi bi-plus ms-auto"></i></button>
    <span style="color: red;"><?php echo $inputErr; ?></span>

    <div class="collapse" id="employee_form">
      <div class="card-body">
        <h5 class="card-title">Employee Form</h5>

        <!-- Employee Form -->
        <form class="row g-3" id="main_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <!-- hidden input for employee id -->
          <input type="hidden" name="employeeID" class="form-control" id="employeeID" value="<?php echo $employeeID; ?>">

          <div class="col-md-6">
            <label for="inputName" class="form-label">Your Name</label>
            <input type="text" name="name" class="form-control" id="inputName" value="<?php echo $name; ?>" required>
          </div>
          <div class="col-md-6">
            <label for="inputRole" class="form-label">Role</label>
            <select class="form-select" aria-label="Default select example" name="role" id="inputRole">
              <option selected value="">Select Role</option>
              <?php
              $sql = "SELECT * FROM role WHERE status!=:status";
              $stmt = $conn->prepare($sql);
              $param = array(
                ':status' => 0
              );
              $stmt->execute($param);
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($result as $row) {
              ?>
                <option value="<?= $row['roleID']; ?>"> <?= $row['rolename']; ?> </option>
              <?php } ?>
            </select>
            <div style="color: red;"><?php echo $roleErr; ?></div>
          </div>
          <div class="col-md-6">
            <label for="inputMobile" class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control" id="inputMobile" value="<?php echo $mobile; ?>" required>
            <div style="color: red;"><?php echo $mobileErr; ?></div>
          </div>
          <div class="col-md-6">
            <label for="inputEmail" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="inputEmail" value="<?php echo $email; ?>" required>
            <div style="color: red;"><?php echo $emailErr; ?></div>
          </div>
          <div class="col-md-6">
            <label for="inputUsername" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="inputUsername" value="<?php echo $username; ?>" required>
            <div style="color: red;"><?php echo $usernameErr; ?></div>
          </div>
          <div class="col-md-6">
            <label for="inputPassword" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="inputPassword" required>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="cancel()">Cancel</button>
          </div>
        </form><!-- End Employee Form -->
      </div>

    </div>
  </div><!-- End Components Nav -->

  <div class="pagetitle"></div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Employee Data</h5>

            <!-- Filter Form -->
            <form class="row g-3" id="filter_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

              <div class="row">

                <div class="col-md-3 custom-select">
                  <input type="text" class="custom-select-input form-select" placeholder="Select employee...">
                  <div class="custom-select-options">
                    <?php
                    $sql = "SELECT * FROM employee";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                    ?>
                      <div data-value="<?= $row['empID']; ?>"><?= $row['name']; ?></div>
                    <?php } ?>
                  </div>
                </div>

                <div class="col-md-3">
                  <select class="form-select" aria-label="Default select example" name="filter_role" id="filterRole">
                    <option selected value="" disabled>Select Role</option>
                    <?php
                    $sql = "SELECT * FROM role";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                    ?>
                      <option value="<?= $row['roleID']; ?>"> <?= $row['rolename']; ?> </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-3">
                  <select class="form-select" aria-label="Default select example" name="filter_dept" id="filterDept">
                    <option selected value="" disabled>Select Department</option>
                    <?php
                    $sql = "SELECT * FROM department";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                    ?>
                      <option value="<?= $row['departmentID']; ?>"> <?= $row['departmentname']; ?> </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <button type="submit" class="btn btn-dark">Filter</button>
                </div>
              </div>
            </form>
            <!-- Filter End -->

            <!-- Table with stripped rows -->
            <table class="table datatable" id="empTable">
              <thead>
                <tr>
                  <th><input type="checkbox" class="form-check-input"></th>
                  <th>S No.</th>
                  <th>Employee Name</th>
                  <th>Role</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM employee INNER JOIN role ON role.roleID=employee.role";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // To check the result
                // echo "<pre>";
                // print_r($result);
                // echo "</pre>";

                $sn = 1;
                foreach ($result as $row) {
                  $bg = 'table-primary';
                  if ($row['employee_status'] == '0') {
                    $bg = 'table-danger';
                  }
                ?>
                  <tr class="<?php echo $bg; ?>">
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['rolename']; ?></td>
                    <td><?php echo $row['mobile']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                      <button type="button" class="btn btn-success" onclick="changestatus(<?php echo $row['empID']; ?>, <?php echo $row['employee_status']; ?>)">Change</button>
                    </td>
                    <td class="row">
                      <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Action
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#target-paragraph" onclick="edit(<?php echo $row['empID']; ?>)">Edit</a></li>
                          <li><a class="dropdown-item" href="javascript:void(0)" onclick="delEmployee(<?php echo $row['empID']; ?>)">Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php }
                ?>
              </tbody>
            </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>
    </div>
  </section>
  <div id="response"></div>
</main><!-- End #main -->

<?php
require 'footer.php';
?>

<script>
  var add = function() {
    $('#main_form')[0].reset();
    $('#employee_form').collapse('show');
  }

  var cancel = function() {
    $('#employee_form').collapse('hide');
  }

  $('#filter_form').on('submit', function(e) {
    e.preventDefault();

    var formData = $(this).serialize();

    // alert(formData);

    // Append the action to the serialized data
    formData += '&action=filterdata';

    // AJAX request
    $.ajax({
      url: 'include/employee_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,
      dataType: 'json',

      success: function(response) {
        if (response.length > 0) {
          alert("working");
          // Clear the existing table content
          $('#empTable tbody').empty();

          // Populate the table with new data
          response.forEach(function(item) {
            var row = `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.rolename}</td>
                            <td>${item.mobile}</td>
                        </tr>
                    `;
            $('#empTable tbody').append(row);
          });
        }

        // Update the UI or provide feedback
        $('#response').html('<p>' + response + '</p>');
        // window.location.reload();
      },
      error: function() {
        $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

  $('#main_form').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // alert(formData);

    // Append the action to the serialized data
    formData += '&action=adddata';

    // AJAX request
    $.ajax({
      url: 'include/employee_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        $('#response').html('<p>' + response + '</p>');
        window.location.reload();
      },
      error: function() {
        $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

  var edit = function(id) {
    // To check the input id
    //alert('Employee id = ' + id);

    $.ajax({
      type: 'POST', // HTTP method for the request
      url: 'include/employee_code.php', // Defines URL of the server-side script to which the request is sent
      data: { // data being sent to the server in the request. It includes an object with two properties:
        action: 'getdata', // specifies the type of action to be performed
        id: id // sends an id parameter, where id is a variable likely defined earlier
      },
      cache: false, // Sets cache to false to prevent the browser from caching the response. This ensures that the latest data is fetched every time this AJAX request is made.

      success: function(response) {
        var d = JSON.parse(response);

        $('#employee_form #employeeID').val(d.id);
        $('#employee_form #inputName').val(d.name);
        $('#employee_form #inputRole').val(d.role);
        $('#employee_form #inputMobile').val(d.mobile);
        $('#employee_form #inputEmail').val(d.email);
        $('#employee_form #inputUsername').val(d.username);

        $('#employee_form').collapse('show');
      }
    });
  }

  var delEmployee = function(id) {
    swal({
      title: "Are you sure?",
      text: "Do you really want to delete this employee? This action cannot be undone.",
      icon: "warning",
      buttons: ["Cancel", "Delete"],
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        $.ajax({
          type: 'POST',
          url: 'include/employee_code.php',
          data: {
            action: 'delete',
            id: id
          },
          cache: false,
          success: function(response) {
            var d = JSON.parse(response);
            if (d.statusCode == 200) {
              // Sweet alert for success
              swal({
                title: "Employee Deleted Successfully!",
                icon: "success",
                button: "OK",
              }).then(() => {
                // Reload the page after the alert is closed
                window.location.reload();
              });
            }
          }
        });
      }
    });
  };

  var changestatus = function(id, status) {
    if (status == 1) {
      status = "enable";
    }
    if (status == 0) {
      status = "disable";
    }

    // Determine the new status and confirmation message based on the current status
    var newStatus = status === "enable" ? "disable" : "enable";
    var confirmationMessage = `Are you sure you want to ${newStatus} this item?`;

    swal({
      title: "Confirmation",
      text: confirmationMessage,
      icon: "warning",
      buttons: ["Cancel", `${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`],
      dangerMode: true,
    }).then((willChange) => {
      if (willChange) {
        $.ajax({
          type: 'POST',
          url: 'include/employee_code.php',
          data: {
            action: 'status',
            id: id
          },
          cache: false,
          success: function(response) {
            var d = JSON.parse(response);
            if (d.statusCode == 200) {
              // SweetAlert success message
              swal({
                title: `Status Changed to ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}!`,
                icon: "success",
                button: "OK",
              }).then(() => {
                // Reload the page after the alert is closed
                window.location.reload();
              });
            }
          }
        });
      }
    });
  }
</script>