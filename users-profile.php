<?php
require 'header.php';

require 'include/profile_code.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Profile</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">Profile</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <?php
  $sql = "SELECT * FROM personal_details WHERE profile_id={$_SESSION['userid']}";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $results = $stmt->fetch(PDO::FETCH_ASSOC);
  $image = $results['image'] ?? "default.jpg";
  $about = $results['about'] ?? "";
  $company = $results['company'] ?? "";
  $job = $results['job'] ?? "";
  $country = $results['country'] ?? "";
  $address = $results['address'] ?? "";
  $phone = $results['phone'] ?? "";
  $email = $results['email'] ?? "";
  $twitter = $results['twitter'] ?? "";
  $github = $results['github'] ?? "";
  $instagram = $results['instagram'] ?? "";
  $linkedin = $results['linkedin'] ?? "";
  ?>

  <section class="section profile">
    <div class="row">
      <div class="col-xl-4">

        <div class="card">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img src="upload/userProfile/<?php echo $image; ?>" alt="Profile" class="rounded-circle">
            <h2><?= $_SESSION['fullname']; ?></h2>
            <h3><?= $job; ?></h3>
            <div class="social-links mt-2">
              <a href="<?php echo $twitter; ?>" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
              <a href="<?php echo $github; ?>" class="github" target="_blank"><i class="bi bi-github"></i></a>
              <a href="<?php echo $instagram; ?>" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
              <a href="<?php echo $linkedin; ?>" class="linkedin" target="_blank"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
        </div>

      </div>

      <div class="col-xl-8">

        <div class="card">
          <div class="card-body pt-3">
            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered">

              <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
              </li>

            </ul>
            <div class="tab-content pt-2">

              <!-- About Profile Section -->
              <div class="tab-pane fade show active profile-overview" id="profile-overview">
                <h5 class="card-title">About</h5>
                <p class="small fst-italic"><?php echo $about; ?></p>

                <h5 class="card-title">Profile Details</h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label ">Full Name</div>
                  <div class="col-lg-9 col-md-8"><?php echo $_SESSION['fullname']; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Company</div>
                  <div class="col-lg-9 col-md-8"><?php echo $company; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Job</div>
                  <div class="col-lg-9 col-md-8"><?php echo $job; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Country</div>
                  <div class="col-lg-9 col-md-8"><?php echo $country; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Address</div>
                  <div class="col-lg-9 col-md-8"><?php echo $address; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Phone</div>
                  <div class="col-lg-9 col-md-8"><?php echo $phone; ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Email</div>
                  <div class="col-lg-9 col-md-8"><?php echo $email; ?></div>
                </div>

              </div>

              <!-- Profile Edit Section -->
              <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                <div id="response"></div>
                <!-- Edit Form -->
                <form id="profile_form" method="POST" enctype="multipart/form-data">

                  <div class="row mb-3">

                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                    <div class="col-md-8 col-lg-9">
                      <img src="upload/userProfile/<?= $image; ?>" alt="Profile" id="profilePreview">
                      <div class="pt-2">
                        <button type="button" class="btn btn-primary btn-sm" id="uploadButton">
                          <i class="bi bi-upload"></i>
                        </button>
                        <input type="file" name="image" accept="image/*" id="fileInput" style="display: none;">

                        <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $_SESSION['fullname']; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                    <div class="col-md-8 col-lg-9">
                      <textarea name="about" class="form-control" id="about" style="height: 100px"><?php echo $about; ?></textarea>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="company" class="col-md-4 col-lg-3 col-form-label">Company</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="company" type="text" class="form-control" id="company" value="<?php echo $company; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Job" class="col-md-4 col-lg-3 col-form-label">Job</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="job" type="text" class="form-control" id="Job" value="<?php echo $job; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="country" type="text" class="form-control" id="Country" value="<?php echo $country; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="address" type="text" class="form-control" id="Address" value="<?php echo $address; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="phone" type="text" class="form-control" id="Phone" value="<?php echo $phone; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="Email" value="<?php echo $email; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="twitter" type="text" class="form-control" id="Twitter" value="<?php echo $twitter; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="github" class="col-md-4 col-lg-3 col-form-label">Github Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="github" type="text" class="form-control" id="github" value="<?php echo $github; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="instagram" type="text" class="form-control" id="Instagram" value="<?php echo $instagram; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin Profile</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="linkedin" type="text" class="form-control" id="Linkedin" value="<?php echo $linkedin; ?>">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End Profile Edit Form -->

              </div>

              <!-- Profile Settings Section -->
              <div class="tab-pane fade pt-3" id="profile-settings">

                <!-- Settings Form -->
                <form>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                    <div class="col-md-8 col-lg-9">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="changesMade" checked>
                        <label class="form-check-label" for="changesMade">
                          Changes made to your account
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="newProducts" checked>
                        <label class="form-check-label" for="newProducts">
                          Information on new products and services
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proOffers">
                        <label class="form-check-label" for="proOffers">
                          Marketing and promo offers
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                        <label class="form-check-label" for="securityNotify">
                          Security alerts
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End settings Form -->

              </div>

              <!-- Change Password Section -->
              <div class="tab-pane fade pt-3" id="profile-change-password">
                <!-- Change Password Form -->
                <form id="profile_password" method="post">

                  <div class="row mb-3">
                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="password" type="password" class="form-control" id="currentPassword" required>
                      <div id="passwordError" style="color: red;"></div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="newpassword" type="password" class="form-control" id="newPassword" required>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="renewpassword" type="password" class="form-control" id="renewPassword" required>
                      <div id="newpasswordError" style="color: red;"></div>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                  </div>
                </form><!-- End Change Password Form -->

              </div>

            </div><!-- End Bordered Tabs -->

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
  $('#uploadButton').click(function() {
    $('#fileInput').click();
  });

  // Display selected image in the profile preview
  $("#fileInput").on("change", function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        $("#profilePreview").attr("src", e.target.result); // Update profile image
      };
      reader.readAsDataURL(file);
    }
  });

  $('#profile_password').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    $('#passwordError').text('');
    $('#newpasswordError').text('');

    // Serialize the form data
    var formData = $(this).serialize();
    // alert(formData);

    // Append the action to the serialized data
    formData += '&action=password';

    // AJAX request
    $.ajax({
      url: 'include/profile_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,
      success: function(response) {
        // Update the UI or provide feedback
        // $('#response').html('<p>' + response + '</p>');
        // window.location.reload();
        var d = JSON.parse(response);

        if (d.success) {
          // Show success message
          //alert(d.message);

          // Sweet alert code
          swal({
            title: "Password updated successfully!",
            icon: "success",
            button: "OK",
          }).then(() => {
            // Reload the page after the alert is closed
            window.location.reload();
          });
        } else {
          // Display errors
          if (d.errors.password) {
            // alert(d.errors.password);
            $('#passwordError').text(d.errors.password);
          } else if (d.errors.newpassword) {
            // alert(d.errors.newpassword);
            $('#newpasswordError').text(d.errors.newpassword);
          }
        }
      },
      error: function() {
        $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });

  $('#profile_form').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

    // Serialize the form data
    // var formData = $(this).serialize();
    var formData = new FormData(document.getElementById('profile_form'));
    // alert(formData);

    // Append the action to the serialized data
    // formData += '&action=profile';

    // Append custom data (e.g., 'action')
    formData.append('action', 'profile');

    // AJAX request
    $.ajax({
      url: 'include/profile_code.php', // The server-side script to handle the form data
      type: 'POST',
      data: formData,
      processData: false, // Prevent jQuery from converting FormData to a string
      contentType: false, // Ensure the correct Content-Type for file uploads
      success: function(response) {
        // Update the UI or provide feedback
        $('#response').html('<p>' + response + '</p>');
        swal({
          title: "Information updated successfully!",
          icon: "success",
          button: "OK",
        }).then(() => {
          // Reload the page after the alert is closed
          window.location.reload();
        });
      },
      error: function() {
        $('#response').html('<p>An error occurred. Please try again.</p>');
      }
    });
  });
</script>