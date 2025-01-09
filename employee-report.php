<?php
require 'header.php';
require 'include/leave_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Employee Report</h1>
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
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Between Dates Report</h5>

            <!-- Employee Form -->
            <form class="row g-3" id="leave_form">
              <!-- hidden input for employee id -->
              <!-- <input type="hidden" name="employeeID" class="form-control" id="employeeID" value=""> -->

              <div class="col-md-6">
                <label for="fromDate" class="form-label">From</label>
                <input type="date" name="fromDate" class="form-control" id="fromDate" value="" required>
              </div>
              <div class="col-md-6">
                <label for="toDate" class="form-label">To</label>
                <input type="date" name="toDate" class="form-control" id="toDate" value="" required>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form><!-- End Leave Form -->
          </div>
        </div>

      </div>
    </div>

    <!-- Leave application table will show only when current user request exist in table -->
    <div class="row">
      <div class="col-lg-12">

        <?php
        $sql = "SELECT * FROM leave_req WHERE empID = {$_SESSION['userid']}";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // To check the result
        // print_r($result);
        if (!empty($result)) {
          echo '   
        <!-- Table with Leave Application History -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Leave Application History</h5>

            <table class="table datatable">
              <thead>
                <tr>
                  <th>S No.</th>
                  <th>Leave Type</th>
                  <th>From</th>
                  <th>To</th>
                  <th>Reason</th>
                  <th>Applied Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>';
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
            echo '
                  <tr>
                    <td>' . $sn++ . '</td>
                    <td>' . htmlspecialchars($row['leave_type']) . '</td>
                    <td>' . htmlspecialchars($row['fromDate']) . '</td>
                    <td>' . htmlspecialchars($row['toDate']) . '</td>
                    <td>' . htmlspecialchars($row['reason']) . '</td>
                    <td>' . htmlspecialchars($row['created_at']) . '</td>
                    <td><span class="badge ' . $bg . '">' . htmlspecialchars($row['status']) . '</span></td>
                    <td class="row">
                      <button class="btn btn-danger" type="button" onclick="delRequest(' . htmlspecialchars($row['leaveID']) . ')">
                        Cancel
                      </button>
                    </td>
                  </tr>';
          }
          echo '
              </tbody>
            </table>
            <!-- End Table with stripped rows -->
            ';
        }
        ?>

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
  $('#leave_form').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // swal(" Hello");

    // Append the action to the serialized data
    formData += '&action=leaveRequest';
    // $('#response').html('<p>' + formData + '</p>');

    // AJAX request
    $.ajax({
      url: 'include/leave_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        var d = JSON.parse(response);

        // Check the success flag from the response
        if (d.success) {
          swal({
            title: d.message,
            icon: "success",
            button: "OK",
          }).then(() => {
            // Reload the page after the alert is closed
            window.location.reload();
          });
        } else {
          swal({
            title: d.message,
            icon: "error",
            button: "OK",
          });
        }
      },
      error: function() {
        // $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

  var delRequest = function(id) {
    swal({
      title: "Are you sure?",
      text: "Do you really want to delete this request? This action cannot be undone.",
      icon: "warning",
      buttons: ["Cancel", "Delete"],
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        $.ajax({
          type: 'POST',
          url: 'include/leave_code.php',
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
                title: "Request Deleted Successfully!",
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
</script>