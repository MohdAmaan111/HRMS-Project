<?php
require 'header.php';
require 'include/role_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Users Role</h1>
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
      <span>Please select your Role</span>
      <button type="button" class="btn btn-primary">Add<i class="bi bi-plus ms-auto"></i></button>
    </a>
    <br>
    <ul id="employee_form" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <div class="card-body">
        <!-- Employee Form -->
        <form class="row g-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="col-md-12">
            <input type="text" name="rolename" class="form-control" required>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form><!-- End Employee Form -->

      </div>
    </ul>
  </div><!-- End Components Nav -->

  <div class="pagetitle"></div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Roles</h5>

            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>S No.</th>
                  <th>Name</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM role";
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
                    <td><?php echo $row['rolename']; ?></td>
                    <td><span class="badge <?php echo $bg; ?>"><?php echo $status; ?></span></td>
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