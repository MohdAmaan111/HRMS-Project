<?php
include_once('include/header.php');
//   ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Tender
      <span>
        <button class="btn btn-primary" type="button" onclick="add()">
          Add Tender
        </button>
      </span>
    </h1>
  </div><!-- End Page Title -->

  <section class="section">

    <div class="collapse" id="addtender">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Add / Edit Tender</h5>
          <form class="row g-3" action="include/model/tender.php" method="post" id="tender-form">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="tid" id="tid">

            <!-- Tender ID -->
            <div class="col-12 col-md-4">
              <label for="tenderid" class="form-label">Tender ID <span class="text-danger fw-5">*</span></label>
              <input type="text" class="form-control" name="tenderid" id="tenderid" required>
            </div>

            <!-- Published By -->
            <div class="col-12 col-md-4">
              <label for="publishedby" class="form-label">Published By <span class="text-danger fw-5">*</span></label>
              <input type="text" class="form-control" name="publishedby" id="publishedby" required>
            </div>

            <!-- Tender Brief -->
            <div class="col-12 col-md-4">
              <label for="brief" class="form-label">Tender Brief <span class="text-danger fw-5">*</span></label>
              <input type="text" class="form-control" name="brief" id="brief" required>
            </div>

            <!-- Location -->
            <div class="col-12 col-md-4">
              <label for="location" class="form-label">Location <span class="text-danger fw-5">*</span></label>
              <input type="text" class="form-control" name="location" id="location" required>
            </div>

            <!-- Submission Mode -->
            <div class="col-12 col-md-4">
              <label for="nmode" class="form-label">Submission Mode <span class="text-danger fw-5">*</span></label>
              <select class="form-select" name="mode" id="mode" required>
                <option selected disabled>Select Mode</option>
                <option value="Online">Online</option>
                <option value="Hardcopy">Hardcopy</option>
                <option value="E-Tender">E-Tender</option>
                <option value="GEM Portal">GEM Portal</option>
              </select>
            </div>

            <!-- Last Date -->
            <div class="col-12 col-md-4">
              <label for="lastdate" class="form-label">Last Date <span class="text-danger fw-5">*</span></label>
              <input type="date" class="form-control" name="lastdate" id="lastdate" required>
            </div>

            <!-- Submit and Reset buttons -->
            <div class="col-12 text-right">
              <input type="submit" class="btn btn-primary" name="submit" value="Submit">
              <input type="reset" class="btn btn-secondary" value="Reset">
            </div>
          </form>

        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Tender List</h5>

        <div class="table-responsive">
          <table class="table datatable ">
            <thead>
              <tr>
                <th>SN</th>
                <th>Logo</th>
                <th>Tender ID</th>
                <th>Published By</th>
                <th>Tender Brief</th>
                <th>Location</th>
                <th>Mode</th>
                <th>Last Date</th>
                <th>PDF</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM tender ORDER BY id DESC";
              $stmt = $conn->prepare($sql);
              $stmt->execute();
              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

              $n = 1;
              foreach ($result as $row) {
                $bg = 'table-primary';
                if (!empty($row['image']))
                  $img = 'assets/img/logo/' . $row['image'];
                else
                  $img = 'assets/img/logo/skillbulletin.png';
                if ($row['status'] == '0')
                  $bg = 'table-danger';
              ?>
                <tr class="<?php echo $bg; ?>">
                  <td><?= $n++; ?></td>
                  <td><img src="<?= $img ?>" alt="skillbulletin" class="w-50"></td>
                  <td><?= $row['tenderid']  ?></td>
                  <td><?= $row['publishedby']  ?></td>
                  <td><?= $row['brief']  ?></td>
                  <td><?= $row['location']  ?></td>
                  <td><?= $row['mode']  ?></td>
                  <td><?= $row['lastdate']  ?></td>
                  <td>
                    <?php
                    if (!empty($row['pdf']))
                      echo '<a href="assets/img/pdf/' . $row['pdf'] . '" download="' . $row['pdf'] . '">
                      <img src="assets/img/pdf/demor.png" alt="skillbulletin" class="w-50"></a>';
                    ?>
                  </td>
                  <td>

                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-sm btn-primary" title="Edit" onclick="edit(<?php echo $row['id']; ?>)">
                        <i class="bi bi-pen"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-secondary" title="Logo" onclick="uploadlogo(<?= $row['id']; ?>)">
                        <i class="bi bi-upload"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-warning" title="Summary Pdf" onclick="uploadpdf(<?= $row['id']; ?>)">
                        <i class="bi bi-cloud-upload"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-warning" title="Detail Pdf" onclick="uploaddpdf(<?= $row['id']; ?>)">
                        <i class="bi bi-cloud-upload"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-success" title="Status" onclick="changestatus(<?= $row['id']; ?>)">
                        <i class="bi bi-eye"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="del(<?= $row['id']; ?>)">
                        <i class="ri-delete-bin-2-line"></i>
                      </button>
                    </div>

                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <!-- End Table with stripped rows -->

      </div>
    </div>
  </section>

</main><!-- End #main -->
<!-- Image Modal -->
<div class="modal fade" id="addimages" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Upload Logo </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="save_forms" method="post" enctype="multipart/form-data">
          <input type="hidden" class="form-control" name="action" value="upload">
          <input type="hidden" class="form-control" id="lid" name="lid">
          <div class="form-group mb-2">
            <input type="file" class="form-control" name="image" id="image" placeholder="Item Image">
          </div>
          <button type="button" class="btn btn-primary waves-effect waves-light" onclick="uploadImage()">Upload Image</button>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- pdf Modal -->
<div class="modal fade" id="addpdf" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Upload PDF </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="save_pdf" method="post" enctype="multipart/form-data">
          <input type="hidden" class="form-control" name="action" value="uploadpdf">
          <input type="hidden" class="form-control" id="did" name="did">
          <div class="form-group mb-2">
            <input type="file" class="form-control" name="image" id="image" placeholder="Item Image">
          </div>
          <button type="button" class="btn btn-primary waves-effect waves-light" onclick="uploaddoc()">Upload PDF</button>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- details pdf-->
<div class="modal fade" id="adddetailpdf" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload PDF </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="saved_pdf" method="post" enctype="multipart/form-data">
          <input type="hidden" class="form-control" name="action" value="uploaddpdf">
          <input type="hidden" class="form-control" id="dtid" name="dtid">
          <div class="form-group mb-2">
            <input type="file" class="form-control" name="image" id="image" placeholder="Item Image">
          </div>
          <button type="button" class="btn btn-primary waves-effect waves-light" onclick="uploadddoc()">Upload PDF</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php
include_once('include/footer.php');
?>

<script>
  var add = function() {
    $('#tender-form')[0].reset();
    $('#addtender').collapse('toggle');
  }
  var edit = function(id) {
    $.ajax({
      type: 'POST',
      url: 'include/model/tender.php',
      data: {
        action: 'getdata',
        id: id
      },
      cache: false,
      success: function(response) {
        var d = JSON.parse(response);
        $('#addtender #tid').val(d.id);
        $('#addtender #tenderid').val(d.tenderid);
        $('#addtender #publishedby').val(d.publishedby);
        $('#addtender #brief').val(d.brief);
        $('#addtender #location').val(d.location);
        $('#addtender #mode').val(d.mode);
        $('#addtender #lastdate').val(d.lastdate);
        $('#addtender').collapse('toggle');
      }
    });
  }

  var uploadlogo = function(id) {

    $('#lid').val(id);
    $('#addimages').modal('show');
  }
  var uploadImage = function() {
    var formData = new FormData(document.getElementById('save_forms'));
    $.ajax({
      type: 'POST',
      url: 'include/model/tender.php', // Specify the PHP script that handles the image upload
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        var d = JSON.parse(response);
        if (d.statusCode == 200) {
          alert('Image Upload Successfully');
          $('#uploadStatus').html(response);
          $('#save_forms').find('input').val('');
          $('#addimages').modal('hide');
        } else
          alert('Image Not Upload');
        window.location.reload();
      },
      error: function(error) {
        console.log(error);
      }
    });
  }

  var uploadpdf = function(id) {
    $('#did').val(id);
    $('#addpdf').modal('show');
  }

  var uploaddoc = function() {
    var formData = new FormData(document.getElementById('save_pdf'));
    $.ajax({
      type: 'POST',
      url: 'include/model/tender.php', // Specify the PHP script that handles the image upload
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        var d = JSON.parse(response);
        if (d.statusCode == 200) {
          alert('PDF uploaded successfully!'); // Alert for success
          $('#uploadStatus').html(response);
          $('#save_pdf').find('input').val('');
          $('#addpdf').modal('hide');
        } else {
          alert('PDF not uploaded.'); // Alert for failure
        }
        window.location.reload();
      },
      error: function(error) {
        console.log(error);
        alert('An error occurred while uploading the PDF.'); // Alert for AJAX error
      }
    });
  }

  var uploaddpdf = function(id) {
    $('#dtid').val(id);
    $('#adddetailpdf').modal('show');
  }

  var uploadddoc = function() {
    var formData = new FormData(document.getElementById('save_pdf'));
    $.ajax({
      type: 'POST',
      url: 'include/model/tender.php', // Specify the PHP script that handles the image upload
      data: formData,
      contentType: false,
      processData: false,
      success: function(response) {
        var d = JSON.parse(response);
        if (d.statusCode == 200) {
          alert('PDF uploaded successfully!'); // Alert for success
          $('#uploadStatus').html(response);
          $('#saved_pdf').find('input').val('');
          $('#adddetailpdf').modal('hide');
        } else {
          alert('PDF not uploaded.'); // Alert for failure
        }
        window.location.reload();
      },
      error: function(error) {
        console.log(error);
        alert('An error occurred while uploading the PDF.'); // Alert for AJAX error
      }
    });
  }


  var changestatus = function(id) {
    if (confirm("Are you sure you want to change status ?")) {
      $.ajax({
        type: 'POST',
        url: 'include/model/tender.php',
        data: {
          action: 'status',
          id: id
        },
        cache: false,
        success: function(response) {
          var d = JSON.parse(response);
          if (d.statusCode == 200) {
            alert('Tender Status Change Successfully');
            window.location.reload();
          }
        }
      });
    }
  }

  var del = function(id) {
    if (confirm("Are you sure you want to delete ?")) {
      $.ajax({
        type: 'POST',
        url: 'include/model/tender.php',
        data: {
          action: 'delete',
          id: id
        },
        cache: false,
        success: function(response) {
          var d = JSON.parse(response);
          if (d.statusCode == 200) {
            alert('Tender Deleted Successfully');
            window.location.reload();
          }
        }
      });
    }
  }
</script>