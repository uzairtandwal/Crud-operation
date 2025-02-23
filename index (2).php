<?php
// Database connection
$host = "localhost";  // Change to your host
$username = "root";   // Your database username
$password = "";       // Your database password
$dbname = "official";  // Your database name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert record
if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "INSERT INTO records (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch records
if (isset($_POST['action']) && $_POST['action'] == 'fetch') {
    $sql = "SELECT * FROM records";
    $result = $conn->query($sql);
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    echo json_encode($records);
    exit(); // To prevent further processing
}

// Update record
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "UPDATE records SET name='$name', email='$email' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Delete record
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM records WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX CRUD System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        h2 { color: #4CAF50; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        td:nth-child(2) { background-color: #e3f2fd; color: #1e88e5; } 
        td:nth-child(3) { background-color: #f3e5f5; color: #9c27b0; } 
        .form-container { margin: 20px 0; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .form-container input, .form-container button { margin: 5px; padding: 10px; font-size: 16px; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #45a049; }
        .edit-btn, .delete-btn { padding: 5px 10px; text-decoration: none; cursor: pointer; border-radius: 5px; }
        .edit-btn { background-color: #ffc107; color: #fff; }
        .edit-btn:hover { background-color: #ffb300; }
        .delete-btn { background-color: #dc3545; color: #fff; }
        .delete-btn:hover { background-color: #c82333; }
        #updateForm { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        #updateForm h3 { color: #333; }
        #updateForm input, #updateForm button { width: 100%; margin: 10px 0; }
    </style>
</head>
<body>

    <h2>🌟 AJAX CRUD System 🌟</h2>

    <div class="form-container">
        <h3>➕ Add New Record</h3>
        <form id="insertForm">
            <input type="text" id="name" placeholder="Name" required>
            <input type="email" id="email" placeholder="Email" required>
            <button type="submit">Add</button>
        </form>
    </div>

    <h3>📋 All Records</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="recordTable">
            <!-- Records will be dynamically inserted here -->
        </tbody>
    </table>

    <div id="updateForm" class="form-container" style="display: none;">
        <h3>✏️ Update Record</h3>
        <input type="hidden" id="editId">
        <input type="text" id="editName" required>
        <input type="email" id="editEmail" required>
        <button id="updateBtn">Update</button>
    </div>

    <script>
        function fetchRecords() {
            $.post("index.php", { action: "fetch" }, function (data) {
                let rows = "";
                try {
                    let records = JSON.parse(data);  // Parse the JSON response
                    records.forEach(function (record) {
                        rows += `
                            <tr>
                                <td>${record.id}</td>
                                <td>${record.name}</td>
                                <td>${record.email}</td>
                                <td>
                                    <button class="edit-btn" onclick="editRecord(${record.id}, '${record.name}', '${record.email}')">✏️ Edit</button>
                                    <button class="delete-btn" onclick="deleteRecord(${record.id})">🗑️ Delete</button>
                                </td>
                            </tr>`;
                    });
                } catch (error) {
                    console.log('Error parsing data: ', error);
                }
                $("#recordTable").html(rows);
            });
        }

        $("#insertForm").submit(function (e) {
            e.preventDefault();
            const name = $("#name").val();
            const email = $("#email").val();
            $.post("index.php", { action: "insert", name: name, email: email }, function (response) {
                alert(response);
                fetchRecords();
                $("#insertForm")[0].reset();
            });
        });

        function editRecord(id, name, email) {
            $("#editId").val(id);
            $("#editName").val(name);
            $("#editEmail").val(email);
            $("#updateForm").show();
        }

        $("#updateBtn").click(function () {
            const id = $("#editId").val();
            const name = $("#editName").val();
            const email = $("#editEmail").val();
            $.post("index.php", { action: "update", id: id, name: name, email: email }, function (response) {
                alert(response);
                fetchRecords();
                $("#updateForm").hide();
            });
        });

        function deleteRecord(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                $.post("index.php", { action: "delete", id: id }, function (response) {
                    alert(response);
                    fetchRecords();
                });
            }
        }

        $(document).ready(function () {
            fetchRecords();
        });
    </script>

</body>
</html>
