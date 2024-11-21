<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);

    $sql = "SELECT id, name FROM students WHERE class_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
    } else {
        echo "<option value='' disabled>No students available</option>";
    }

    $stmt->close();
    $conn->close();
}
?>
