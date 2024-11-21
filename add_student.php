<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_name = $_POST['student_name'];
    $roll_number = $_POST['roll_number'];
    $class_id = $_POST['class_id'];

    $sql = "INSERT INTO students (name, roll_number, class_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $student_name, $roll_number, $class_id);

    if ($stmt->execute()) {
        echo "Student added successfully.";
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
<form action="add_student.php" method="POST">
    <label for="student_name">Student Name:</label>
    <input type="text" id="student_name" name="student_name" required>

    <label for="roll_number">Roll Number:</label>
    <input type="text" id="roll_number" name="roll_number" required>

    <label for="class_id">Class:</label>
    <select id="class_id" name="class_id" required>
        <?php
        include 'db_connection.php';
        $sql = "SELECT * FROM classes";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        $conn->close();
        ?>
    </select>

    <button type="submit">Add Student</button>
</form>

    
</body>
</html>