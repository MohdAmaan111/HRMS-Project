<?php
require 'header.php';

require 'include/department_code.php';
?>


<!-- Modal -->
<div class="modal" id="editLeaveTypeModal" tabindex="-1" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Leave Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateLeaveType">
          <div class="row w-100">
            <div class="col-6">
              <label for="inputUsername" class="form-label">Leave Type</label>
            </div>
            <div class="col-6">
              <label for="inputUsername" class="form-label">Default Days</label>
            </div>
          </div>
          <div class="row mb-3 w-100">
            <?php
            $sql = "SELECT * FROM leave_types";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
              echo '
              <div class="col-6 mb-2">
                <input type="text" name="leaveTypeName[]" class="form-control" id="leaveTypeName" value="' . $row['leaveTypeName'] . '" required>
              </div>
              <div class="col-6 mb-2">
                <input type="text" name="numberOfDays[]" class="form-control" id="numberOfDays" value="' . $row['defaultDays'] . '" required>
              </div>
              ';
            }
            ?>

          </div>

          <hr>

          <div class="row mb-3">
            <div class="col-6 pe-1 text-end">Total =</div>
            <div class="col-6 ps-0" id="totalDays"></div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Leave Type</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="nav-item">
    <span>Add Leave Types</span>
    <button type="button" class="btn btn-primary" id="addBtn">Add <i class="bi bi-plus ms-auto"></i></button>
    <button type="button" class="btn btn-danger" id="editBtn">Edit <i class=" bi bi-pencil-square
 ms-auto"></i></button>
    <br>
    <ul id="employee_form" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <div class="card-body">

        <!-- Leave Form -->
        <form class="row g-3" id="leaveTypeForm">
          <!-- hidden input for employee id -->
          <input type="hidden" name="leaveID" class="form-control" id="leaveID">

          <div class="col-md-6">
            <label for="leavetype" class="form-label">Leave Type</label>
            <input type="text" name="leavetype" class="form-control" id="leavetype" required>
          </div>
          <div class="col-md-6">
            <label for="leavedays" class="form-label">Number of Days</label>
            <input type="text" name="leavedays" class="form-control" id="leavedays" required>
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
                  <th>Leave Type</th>
                  <th>No. of Days</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM leave_types";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // To check the result
                // print_r($result);

                $sn = 1;
                foreach ($result as $row) {
                ?>
                  <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $row['leaveTypeName']; ?></td>
                    <td><?php echo $row['defaultDays']; ?></td>
                    <td>
                      <button type="button" class="btn btn-danger" onclick="deleteLeaveType(<?php echo $row['leaveTypeID']; ?>)">
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
  $('#updateLeaveType').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // swal("Hello");

    // Append the action to the serialized data
    formData += '&action=updateLeaveType';

    // AJAX request
    $.ajax({
      url: 'include/leave_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        $('#response').html('<p>' + response + '</p>');

        // swal({
        //   title: "Leave Type Added Successfully!",
        //   icon: "success",
        //   button: "OK",
        // }).then(() => {
        //   // Reload the page after the alert is closed
        //   window.location.reload();
        // });
      },
      error: function() {
        $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

  // Function to calculate the total days
  function calculateTotal() {
    let total = 0;
    // Select all input fields with the class "numberOfDays"
    document.querySelectorAll('#numberOfDays').forEach(input => {
      // Parse the value of each input as a number and add it to the total
      total += parseFloat(input.value) || 0;
    });
    // Update the totalDays span with the calculated total
    document.getElementById('totalDays').textContent = total;
  }

  // Add event listeners to update the total in real-time
  document.querySelectorAll('#numberOfDays').forEach(input => {
    input.addEventListener('input', calculateTotal);
  });

  // Initial calculation on page load
  calculateTotal();

  // Handle the click event on the Add button
  $('#addBtn').click(function(e) {
    e.preventDefault(); // Prevent default link behavior
    const target = $('#employee_form');
    if (!target.hasClass('show')) {
      target.collapse('show'); // Show the form if it is not visible
    } else target.collapse('hide');
  });

  $('#editBtn').click(function() {
    $('#editLeaveTypeModal').modal('show');
  });

  $('#leaveTypeForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // swal("Hello");

    // Append the action to the serialized data
    formData += '&action=addLeaveType';

    // AJAX request
    $.ajax({
      url: 'include/leave_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        swal({
          title: "Leave Type Added Successfully!",
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

  var deleteLeaveType = function(id) {

    swal({
      title: "Confirmation",
      text: "Do you really want to delete this leave type? This action cannot be undone.",
      icon: "warning",
      buttons: ["Cancel", "Delete"],
      dangerMode: true,
    }).then((willChange) => {
      if (willChange) {
        $.ajax({
          type: 'POST',
          url: 'include/leave_code.php',
          data: {
            action: 'deleteLeaveType',
            id: id
          },
          cache: false,
          success: function(response) {
            var d = JSON.parse(response);
            if (d.statusCode == 200) {
              // SweetAlert success message
              swal({
                title: "Leave Type Deleted Successfully!",
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