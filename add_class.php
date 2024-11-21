<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = $_POST['class_name'];
    $department_id = $_POST['department_id'];

    $sql = "INSERT INTO classes (name, department_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $class_name, $department_id);

    if ($stmt->execute()) {
        echo "Class added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<form action="add_class.php" method="POST">
    <label for="class_name">Class Name:</label>
    <input type="text" id="class_name" name="class_name" required>
    
    <label for="department_id">Department:</label>
    <select id="department_id" name="department_id" required>
        <?php
        include 'db_connection.php';
        $sql = "SELECT * FROM departments";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        $conn->close();
        ?>
    </select>

    <button type="submit">Add Class</button>
</form>
