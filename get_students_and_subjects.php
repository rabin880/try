<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);

    // Fetch students in the selected class
    $sql_students = "SELECT id, name FROM students WHERE class_id = ?";
    $stmt = $conn->prepare($sql_students);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $students_result = $stmt->get_result();

    // Fetch subjects for the selected class
    $sql_subjects = "SELECT id, name FROM subjects WHERE class_id = ?";
    $stmt_subjects = $conn->prepare($sql_subjects);
    $stmt_subjects->bind_param("i", $class_id);
    $stmt_subjects->execute();
    $subjects_result = $stmt_subjects->get_result();

    if ($students_result->num_rows > 0 && $subjects_result->num_rows > 0) {
        // Start table
        echo '<form action="add_marks_process.php" method="POST">';
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Student Name</th>';

        // Display subject names as table headers
        while ($subject = $subjects_result->fetch_assoc()) {
            echo '<th>' . $subject['name'] . '</th>';
        }
        echo '</tr></thead><tbody>';

        // Display each student with input fields for marks
        while ($student = $students_result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $student['name'] . '</td>';

            // Loop through subjects and create input fields for marks
            $subjects_result->data_seek(0);  // Reset subject result set for each student
            while ($subject = $subjects_result->fetch_assoc()) {
                echo '<td><input type="number" name="marks[' . $student['id'] . '][' . $subject['id'] . ']" class="form-control" step="0.01" required></td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '<button type="submit" class="btn btn-primary">Submit Marks</button>';
        echo '</form>';
    } else {
        echo '<p>No students or subjects found for this class.</p>';
    }

    $stmt->close();
    $stmt_subjects->close();
    $conn->close();
}
?>
