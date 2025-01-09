<?php
require 'header.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Log History</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Data</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">User Log Data</h5>

            <!-- Table with stripped rows -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th>S No.</th>
                  <th>User Name</th>
                  <th>IP Address</th>
                  <th>Log in Time</th>
                  <th>Log out Time</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM userid INNER JOIN userlog ON userid.id = userlog.userid";
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
                    <td><?php echo $row['ipaddress']; ?></td>
                    <td><?php echo $row['logintime']; ?></td>
                    <td><?php echo $row['logouttime']; ?></td>
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