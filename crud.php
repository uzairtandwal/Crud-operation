<?php
// Database Connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "official";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 📝 Insert Record
if (isset($_POST['action']) && $_POST['action'] == "insert") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "INSERT INTO agee (name, email) VALUES ('$name', '$email')";
    $conn->query($sql);
    echo "Record inserted successfully!";
    exit();
}

// 🛠️ Update Record
if (isset($_POST['action']) && $_POST['action'] == "update") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $sql = "UPDATE agee SET name='$name', email='$email' WHERE id=$id";
    $conn->query($sql);
    echo "Record updated successfully!";
    exit();
}

// 🗑️ Delete Record
if (isset($_POST['action']) && $_POST['action'] == "delete") {
    $id = $_POST['id'];
    $sql = "DELETE FROM agee WHERE id=$id";
    $conn->query($sql);
    echo "Record deleted successfully!";
    exit();
}

// 📊 Fetch All Records
if (isset($_POST['action']) && $_POST['action'] == "fetch") {
    $result = $conn->query("SELECT * FROM agee");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}
?>
