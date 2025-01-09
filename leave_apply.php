<?php
require 'header.php';
require 'include/leave_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Leave Application</h1>
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
            <h5 class="card-title">Apply for Leave</h5>

            <!-- Employee Form -->
            <form class="row g-3" id="leave_form">

              <div class="col-md-12">
                <?php
                // Fetch total remaining days
                $sql = "SELECT SUM(remainingDays) AS totalLeave FROM employee_leaves WHERE empID = {$_SESSION['userid']}";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $totalLeave = $stmt->fetch(PDO::FETCH_ASSOC)['totalLeave'] ?? 0;
                ?>
                <label for="leave_type" class="form-label">Leave Type</label><span class="ms-2 badge bg-danger" id="totalLeave">Leave Left (<?= $totalLeave; ?>)</span>

                <select class="form-select" aria-label="Default select example" name="leave_type" id="leave_type">
                  <option selected value="">Select Leave</option>
                  <?php
                  // Show types of leaves from database
                  $sql = "SELECT * FROM leave_types";
                  $stmt = $conn->prepare($sql);
                  $stmt->execute();
                  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($result as $row) {
                  ?>
                    <?php
                    // Show remaining days in each leave type
                    $sql2 = "SELECT remainingDays FROM employee_leaves WHERE empID = {$_SESSION['userid']} AND leaveTypeID = {$row['leaveTypeID']}";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute();
                    $daysLeft = $stmt2->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <option value="<?= $row['leaveTypeID']; ?>"> <?= $row['leaveTypeName']; ?>
                      (Remaining Days : <span class="text-danger" id="totalLeave"><?= $daysLeft['remainingDays']; ?></span>)
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="fromDate" class="form-label">From</label>
                <input type="date" name="fromDate" class="form-control" id="fromDate" value="" required>
              </div>
              <div class="col-md-6">
                <label for="toDate" class="form-label">To</label>
                <input type="date" name="toDate" class="form-control" id="toDate" value="" required>
              </div>
              <div class="col-md-12">
                <label for="reason" class="col-md-4 col-lg-3 col-form-label">Reason</label>
                <textarea name="reason" class="form-control" id="reason" style="height: 100px"></textarea>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" onclick="cancel()">Cancel</button>
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
        $sql = "SELECT * FROM leave_req WHERE employeeID = {$_SESSION['userid']}";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // $sqli = "SELECT * FROM leave_req INNER JOIN leave_types ON leave_types.leaveTypeID=leave_req.leave_type";
        // $stm = $conn->prepare($sqli);
        // $stm->execute();
        // $leavaNameResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $leaveID = $row['leave_type'];
            $sql = "SELECT leaveTypeName FROM leave_types WHERE leaveTypeID = $leaveID";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $leaveName = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    <td>' . htmlspecialchars($leaveName['leaveTypeName']) . '</td>
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

    // swal("Hello");

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

        if (d.status){}
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
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        console.log(xhr.responseText);
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