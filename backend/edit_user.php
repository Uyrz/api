<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rank'] != 1) {
    header("Location: login.php");
    exit();
}

include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the form submission for editing user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['username'];
    $user_password = $_POST['password'];
    $user_name = $_POST['name'];
    $user_birthdate = $_POST['birthdate'];
    $user_phone = $_POST['phone'];
    $user_address = $_POST['address'];
    $new_email = $_POST['email'];

    // Hash the password before storing it if it's not empty
    if (!empty($user_password)) {
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
    } else {
        // Fetch current password if not updating
        $result = $conn->query("SELECT password FROM users WHERE id = $user_id");
        $current_password = $result->fetch_assoc()['password'];
        $hashed_password = $current_password;
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, name = ?, birthdate = ?, phone = ?, address = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $new_username, $hashed_password, $user_name, $user_birthdate, $user_phone, $user_address, $new_email, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch the list of users
$result = $conn->query("SELECT id, username, password, birthdate, phone, address, email FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AdminLTE 3 | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    .edit-form {
        display: none;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Login as: 123u</a>
        </div>
      </div>

      <form action="index.php" method="POST" class="mt-3">
        <button type="submit" class="btn btn-primary">Back to Home</button>
      </form>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
       
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!-- Breadcrumbs -->
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- Main content -->
    <div class="container">
        <h2>Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="showEditForm(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['password']); ?>', '<?php echo htmlspecialchars($row['birthdate']); ?>', '<?php echo htmlspecialchars($row['phone']); ?>', '<?php echo htmlspecialchars($row['address']); ?>', '<?php echo htmlspecialchars($row['email']); ?>')">Edit</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Edit Form -->
        <div class="edit-form" id="editForm">
            <h3>Edit User</h3>
            <form action="" method="POST" id="editUserForm">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" name="username" id="editUsername" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editPassword">Password</label>
                    <input type="password" name="password" id="editPassword" class="form-control">
                </div>
                <div class="form-group">
                    <label for="editName">Name</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editBirthdate">Birthdate</label>
                    <input type="date" name="birthdate" id="editBirthdate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editPhone">Phone</label>
                    <input type="tel" name="phone" id="editPhone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editAddress">Address</label>
                    <input type="text" name="address" id="editAddress" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" name="email" id="editEmail" class="form-control" required>
                </div>
                <button type="submit" name="update_user" class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-secondary" onclick="hideEditForm()">Cancel</button>
            </form>
        </div>
    </div>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script>
function showEditForm(id, username, password, birthdate, phone, address, email) {
    document.getElementById('editForm').style.display = 'block';
    document.getElementById('editUserId').value = id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editPassword').value = ''; // Clear password field
    document.getElementById('editName').value = name;
    document.getElementById('editBirthdate').value = birthdate;
    document.getElementById('editPhone').value = phone;
    document.getElementById('editAddress').value = address;
    document.getElementById('editEmail').value = email;
}

function hideEditForm() {
    document.getElementById('editForm').style.display = 'none';
}
</script>
</body>
</html>
<?php
$conn->close();
?>
