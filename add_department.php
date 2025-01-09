<?php
require 'header.php';

require 'include/department_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Users Department</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#employee_form" data-bs-toggle="collapse" href="#">
      <span>Please add your Department</span>
      <button type="button" class="btn btn-primary">Add<i class="bi bi-plus ms-auto"></i></button>
    </a>
    <br>
    <ul id="employee_form" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <div class="card-body">
        <!-- Employee Form -->
        <form class="row g-3" id="departmentForm">
          <div class="col-md-12">
            <input type="text" name="departmentname" class="form-control" required>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form><!-- End Employee Form -->

      </div>
    </ul>
  </div><!-- End Components Nav -->

  <div class="pagetitle"></div><!-- End Page Title -->

  <div id="response"></div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Department List</h5>

            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>S No.</th>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Change Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM department";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // To check the result
                // print_r($result);

                $sn = 1;
                foreach ($result as $row) {
                  $bg = '';
                  $status = $row['status'];
                  if ($status == 1) {
                    $status = "Enable";
                    $bg = 'bg-success';
                  } else {
                    $status = "Disable";
                    $bg = 'bg-danger';
                  }
                ?>
                  <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $row['departmentname']; ?></td>
                    <td><span class="badge <?php echo $bg; ?>"><?php echo $status; ?></span></td>
                    <td>
                      <button type="button" class="btn btn-success" onclick="changestatus(<?php echo $row['departmentID']; ?>, <?php echo $row['status']; ?>)">Change</button>
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger" onclick="delEmployee(<?php echo $row['departmentID']; ?>)">
                        Delete
                      </button>
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

</main><!-- End #main -->

<?php
require 'footer.php';
?>

<script>
  $('#departmentForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // swal("Hello");

    // Append the action to the serialized data
    formData += '&action=addDepartment';

    // AJAX request
    $.ajax({
      url: 'include/department_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        swal({
          title: "Department Added Successfully!",
          icon: "success",
          button: "OK",
        }).then(() => {
          // Reload the page after the alert is closed
          window.location.reload();
        });
      },
      error: function() {
        // $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

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