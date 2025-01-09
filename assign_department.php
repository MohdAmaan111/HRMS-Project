<?php
require 'header.php';
require 'include/department_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Assign Department</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->


  <!---------- Modal ---------->
  <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assignModalLabel">Assign Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="departmentForm">
            <div class="mb-3">
              <!-- hidden input for employee id -->
              <input type="hidden" name="employeeID" class="form-control" id="employeeID" value="<?php echo $employeeID; ?>">
            </div>

            <div class="mb-3">
              <label for="department" class="form-label">Department</label>
              <select class="form-select" aria-label="Default select example" name="department" id="department">
                <option selected value="">Select Department</option>
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

            <div class="mb-3">
              <label for="dessignation" class="form-label">Dessignation</label>
              <select class="form-select" aria-label="Default select example" name="dessignation" id="dessignation">
                <option selected value="">Select Dessignation</option>
                <option value="Manager">Manager</option>
                <option value="Supervisor">Supervisor</option>
                <option value="Developer">Developer</option>
                <option value="Trainer">Trainer</option>
              </select>
            </div>

            <div class="mb-3">

              <label for="salary" class="form-label">Salary</label>
              <input type="text" class="form-control" name="salary" id="salary" placeholder="Enter your salary">
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!---------- Modal End ---------->

  <div id="response"></div>

  <div class="pagetitle"></div><!-- End Page Title -->

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
                  <th>Department</th>
                  <th>Dessignation</th>
                  <th>Salary</th>
                  <th>Assign</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM employee LEFT JOIN department ON department.departmentID=employee.department";
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
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['departmentname']; ?></td>
                    <td><?php echo $row['dessignation']; ?></td>
                    <td><?php echo $row['salary']; ?></td>
                    <td class="row">
                      <button class="btn btn-warning" type="button"
                        onclick="employeeID(this)" data-id="<?php echo $row['empID']; ?>" data-dept="<?php echo $row['department']; ?>" data-desig="<?php echo $row['dessignation']; ?>" data-salary="<?php echo $row['salary']; ?>">
                        Assign
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
  var employeeID = function(element) {
    id = $(element).attr('data-id');
    depID = $(element).attr('data-dept');
    desgn = $(element).attr('data-desig');
    salary = $(element).attr('data-salary');

    $('#departmentForm #employeeID').val(id);
    $('#departmentForm #department').val(depID);
    $('#departmentForm #dessignation').val(desgn);
    $('#departmentForm #salary').val(salary);
    $('#assignModal').modal('show');
  }
  
  $('#departmentForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    var formData = $(this).serialize();

    // Append the action to the serialized data
    formData += '&action=updateDepartment';

    // AJAX request
    $.ajax({
      url: 'include/department_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,

      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');

        swal({
          title: "Department Updated Successfully!",
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
</script>