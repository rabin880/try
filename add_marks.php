<?php
include 'db_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $marks_obtained = $_POST['marks_obtained'];
    $max_marks = $_POST['max_marks'];

    $sql = "INSERT INTO marks (student_id, subject_id, marks_obtained, max_marks) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iidd", $student_id, $subject_id, $marks_obtained, $max_marks);

    if ($stmt->execute()) {
        $message = "Marks added successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Add Marks</h2>

        <!-- Class Selection -->
        <div class="mb-3">
            <label for="class_id" class="form-label">Class:</label>
            <select id="class_id" name="class_id" class="form-select" required>
                <option value="" disabled selected>Select a class</option>
                <?php
                include 'db_connection.php';
                $sql = "SELECT id, name FROM classes";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <!-- Table for Students and Subjects (Populated via AJAX) -->
        <div id="marks_table_container"></div>

    </div>

    <!-- AJAX Script -->
    <script>
        $(document).ready(function () {
            $('#class_id').on('change', function () {
                const classId = $(this).val();
                if (classId) {
                    $.ajax({
                        url: 'get_students_and_subjects.php',
                        type: 'POST',
                        data: { class_id: classId },
                        success: function (data) {
                            $('#marks_table_container').html(data);
                        },
                        error: function () {
                            alert('Error loading students and subjects.');
                        }
                    });
                } else {
                    $('#marks_table_container').html('');
                }
            });
        });
    </script>

</body>
</html>

