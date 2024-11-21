<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $department_name = $_POST['department_name'];

    $sql = "INSERT INTO departments (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department_name);

    if ($stmt->execute()) {
        echo "Department added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="add_department.php" method="POST">
    <label for="department_name">Department Name:</label>
    <input type="text" id="department_name" name="department_name" required>
    <button type="submit">Add Department</button>
</form>

    
</body>
</html>