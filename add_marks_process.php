<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['marks'])) {
    // Loop through the submitted marks and insert into the database
    foreach ($_POST['marks'] as $student_id => $subjects) {
        foreach ($subjects as $subject_id => $marks_obtained) {
            $max_marks = 100;  // Set the maximum marks for simplicity, or fetch dynamically

            // Insert marks into the database
            $sql = "INSERT INTO marks (student_id, subject_id, marks_obtained, max_marks) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iidd", $student_id, $subject_id, $marks_obtained, $max_marks);

            if ($stmt->execute()) {
                echo "Marks for student $student_id in subject $subject_id added successfully.<br>";
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>
