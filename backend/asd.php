<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคิว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">จัดการคิว</h1>
        
        <?php
        require '../vendor/autoload.php';
        use Firebase\JWT\JWT;
        use Firebase\JWT\Key;

        $key = '1a3LM3W966D6QTJ5BJb9opunkUcw_d09NCOIJb9QZTsrneqOICoMoeYUDcd_NfaQyR787PAH98Vhue5g938jdkiyIZyJICytKlbjNBtebaHljIR6-zf3A2h3uy6pCtUFl1UhXWnV6madujY4_3SyUViRwBUOP-UudUL4wnJnKYUGDKsiZePPzBGrF4_gxJMRwF9lIWyUCHSh-PRGfvT7s1mu4-5ByYlFvGDQraP4ZiG5bC1TAKO_CnPyd1hrpdzBzNW4SfjqGKmz7IvLAHmRD-2AMQHpTU-hN2vwoA-iQxwQhfnqjM0nnwtZ0urE6HjKl6GWQW-KLnhtfw5n_84IRQ';

        if(isset($_COOKIE['token'])){
            $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
        } else {
            header('location:index.php');
            exit();
        }

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

        $sql = "SELECT id, name, title, time, status FROM queue";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead class='table-light'><tr><th>ID</th><th>Name</th><th>Title</th><th>Time</th><th>Status</th><th>Actions</th></tr></thead>";
            echo "<tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["title"] . "</td>";
                echo "<td>" . $row["time"] . "</td>";
                echo "<td>" . $row["status"] . "</td>";
                echo "<td class='d-flex gap-2'>";
                

                echo "<form action='edit_queue.php' method='POST' class='d-inline-flex align-items-center'>";
                echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                echo "<input type='text' name='name' value='" . $row["name"] . "' class='form-control me-2' required>";
                echo "<input type='text' name='title' value='" . $row["title"] . "' class='form-control me-2' required>";
                echo "<select name='status' class='form-select me-2' required>";
                echo "<option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                echo "<option value='Accepted'" . ($row['status'] == 'Accepted' ? ' selected' : '') . ">Accepted</option>";
                echo "<option value='Success'" . ($row['status'] == 'Success' ? ' selected' : '') . ">Success</option>";
                echo "</select>";
                echo "<button type='submit' class='btn btn-warning'>Edit</button>";
                echo "</form>";

                echo "<form action='delete_queue.php' method='POST' class='d-inline-flex'>";
                echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                echo "<button type='submit' class='btn btn-danger'>Delete</button>";
                echo "</form>";
                echo "</td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-center'>ตอนนี้ยังไม่มีคิว</p>";
        }

        $conn->close();
        ?>
        
        <form action="clear_queue.php" method="POST" class="mt-3">
            <button type="submit" class="btn btn-danger">Clear Queue</button>
        </form>
        
        <form action="logout.php" method="POST" class="mt-3">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
        
        <div class="mt-3">
            <a href="http://localhost/phpmyadmin" class="btn btn-primary">Go to Database</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
