<?php
include 'db_connection.php';

// Start session for displaying messages
session_start();

// Default class selection
$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;

// Fetch classes to populate the class selection dropdown
$sql_classes = "SELECT * FROM classes";
$result_classes = $conn->query($sql_classes);

// Query to fetch student names, subject names, marks obtained, and pass/fail result for a specific class
if ($class_id) {
    $sql = "
        SELECT s.name AS student_name, sub.name AS subject_name, m.marks_obtained, sub.pass_mark, sub.fail_mark,
        CASE
            WHEN m.marks_obtained >= sub.pass_mark THEN 'Pass'
            WHEN m.marks_obtained < sub.fail_mark THEN 'Fail'
            ELSE 'Pending'
        END AS result
        FROM students s
        JOIN marks m ON s.id = m.student_id
        JOIN subjects sub ON m.subject_id = sub.id
        WHERE s.class_id = ? 
        ORDER BY s.name, sub.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch class name for displaying it in the report section (if class_id is selected)
$class_name = null;
if ($class_id) {
    $sql_class_name = "SELECT name FROM classes WHERE id = ?";
    $stmt_class_name = $conn->prepare($sql_class_name);
    $stmt_class_name->bind_param("i", $class_id);
    $stmt_class_name->execute();
    $stmt_class_name->bind_result($class_name);
    $stmt_class_name->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .table th, .table td {
            text-align: center;
        }
        .btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Generate Student Marks Report</h2>

        <!-- Class Selection Form -->
        <form method="POST" class="mb-4">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Select Class:</label>
                    <select id="class_id" name="class_id" class="form-select" required>
                        <option value="" disabled selected>Select a Class</option>
                        <?php while ($row_class = $result_classes->fetch_assoc()): ?>
                            <option value="<?= $row_class['id']; ?>" <?= ($row_class['id'] == $class_id) ? 'selected' : ''; ?>>
                                <?= $row_class['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                </div>
            </div>
        </form>

        <!-- Display Report if Class is Selected -->
        <?php if ($class_id && $result->num_rows > 0): ?>
            <h3 class="text-center">Report for Class: <?= htmlspecialchars($class_name); ?></h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Subject Name</th>
                        <th>Marks Obtained</th>
                        <th>Pass Mark</th>
                        <th>Fail Mark</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_name']); ?></td>
                            <td><?= htmlspecialchars($row['subject_name']); ?></td>
                            <td><?= htmlspecialchars($row['marks_obtained']); ?></td>
                            <td><?= htmlspecialchars($row['pass_mark']); ?></td>
                            <td><?= htmlspecialchars($row['fail_mark']); ?></td>
                            <td>
                                <strong class="<?= $row['result'] == 'Pass' ? 'text-success' : ($row['result'] == 'Fail' ? 'text-danger' : 'text-warning'); ?>">
                                    <?= htmlspecialchars($row['result']); ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($class_id): ?>
            <p class="text-center text-danger">No students found in this class.</p>
        <?php endif; ?>
    </div>

    <!-- Optional: Add Bootstrap JS for interactivity (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
