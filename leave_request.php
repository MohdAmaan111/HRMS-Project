<?php
require 'header.php';
require 'include/leave_code.php';
?>

<main id="main" class="main">

  <!-- Modal -->
  <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assignModalLabel">Employee Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <!-- Employee Details Section -->
          <div class="mb-3">
            <h6>Employee Details</h6>
            <div class="p-3 border rounded">
              <p><strong>Employee ID:</strong> <span id="empID"></span></p>
              <p><strong>Employee Name:</strong> <span id="empName"></span></p>
              <p><strong>Leave Type:</strong> <span id="empLeaveType"></span></p>
              <p><strong>Reason:</strong> <span id="empReason"></span></p>
              <p><strong>Dates:</strong> <span id="empDates"></span></p>
            </div>
          </div>

          <!-- Leave Status Form -->
          <form id="leaveStatusForm">
            <div class="mb-3">
              <!-- hidden input for Leave id -->
              <input type="hidden" name="leaveID" class="form-control" id="leaveID" value="">
            </div>

            <div class="mb-3">
              <label for="leaveStatus" class="form-label">Leave Status</label>
              <select class="form-select" aria-label="Default select example" name="leaveStatus" id="leaveStatus" required>
                <option selected value="" disabled>-- Select --</option>
                <option value="Approved">Approve</option>
                <option value="Rejected">Reject</option>
              </select>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
          <!-- Leave Status Form End -->
        </div>
      </div>
    </div>
  </div>

  <div class="pagetitle">
    <h1>Leave Request</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div id="response"></div>

  <div class="pagetitle"></div><!-- End Page Title -->

  <section class="section">

    <!-- Leave application table will show only when current user request exist in table -->
    <div class="row">
      <div class="col-lg-12">

        <!-- Table with Leave Application History -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Leave Request History</h5>

            <table class="table datatable">
              <thead>
                <tr>
                  <th>S No.</th>
                  <th>Emp ID</th>
                  <th>Leave Type</th>
                  <th>From</th>
                  <th>To</th>
                  <th>Reason</th>
                  <th>Applied Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $sql = "SELECT * FROM leave_req";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $sn = 1;
                foreach ($result as $row) {
                  $bg = '';
                  if ($row['status'] === 'Approved') {
                    $bg = 'bg-success';
                  } elseif ($row['status'] === 'Rejected') {
                    $bg = 'bg-danger';
                  } else {
                    $bg = 'bg-warning';
                  }
                ?>
                  <tr>
                    <td><?php echo $sn++; ?></td>
                    <td><?php echo $row['employeeID']; ?></td>
                    <td><?php echo $row['leave_type']; ?></td>
                    <td><?php echo $row['fromDate']; ?></td>
                    <td><?php echo $row['toDate']; ?></td>
                    <td><?php echo $row['reason']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><span class="badge <?php echo $bg; ?>">
                        <?php echo $row['status']; ?>
                      </span>
                    </td>
                    <td class="row">
                      <button class="btn btn-info" type="button" onclick="viewEmp(<?php echo $row['leaveID']; ?>)">
                        View
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
  var viewEmp = function(id) {

    // AJAX request
    $.ajax({
      url: 'include/leave_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: {
        action: 'empDetail',
        id: id
      },

      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        var d = JSON.parse(response);

        if (d.success) {
          // Populate modal fields with employee details
          $('#leaveStatusForm #leaveID').val(d.data.leaveID);
          $('#empName').text(d.data.name);
          $('#empID').text(d.data.employeeID);
          $('#empLeaveType').text(d.data.leave_type);
          $('#empReason').text(d.data.reason);
          $('#empDates').text(d.data.fromDate + ' to ' + d.data.toDate);

          // Show the modal
          $('#assignModal').modal('show');
        } else {
          alert('Unable to fetch employee details. Please try again.');
        }
        $('#assignModal').modal('show');

      },
      error: function() {
        // $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  };

  $('#leaveStatusForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // Append the action to the serialized data
    formData += '&action=changeStatus';


    // AJAX request
    $.ajax({
      url: 'include/leave_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,
      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        var d = JSON.parse(response);

        if (d.statusCode == 200) {
          // SweetAlert success message
          swal({
            title: `Employee Status ${d.leaveStatus} Successfully!`,
            icon: "success",
            button: "OK",
          }).then(() => {
            // Reload the page after the alert is closed
            window.location.reload();
          });
        }
      },
      error: function() {
        // $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });
</script>