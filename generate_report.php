<?php
include 'db_connection.php';
session_start();

// Default class and student selection
$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;
$student_id = isset($_POST['student_id']) ? $_POST['student_id'] : null;

// Fetch classes 
$sql_classes = "SELECT * FROM classes";
$result_classes = $conn->query($sql_classes);

// Fetch students based on selected class
$students = [];
if ($class_id) {
    $sql_students = "SELECT id, name, roll_number FROM students WHERE class_id = ?";
    $stmt_students = $conn->prepare($sql_students);
    $stmt_students->bind_param("i", $class_id);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    
    while ($row = $result_students->fetch_assoc()) {
        $students[] = $row;
    }
}

// Query to fetch student details and marks
$student_details = null;
$subject_marks = [];
if ($student_id) {
    // Comprehensive student performance query
    $sql_student_details = "
        SELECT 
            s.id AS student_id,
            s.name AS student_name,
            s.roll_number,
            c.name AS class_name,
            AVG(m.marks_obtained) AS average_marks,
            COUNT(CASE WHEN m.marks_obtained >= sub.pass_mark THEN 1 END) AS passed_subjects,
            COUNT(DISTINCT sub.id) AS total_subjects,
            MIN(m.marks_obtained) AS lowest_mark,
            MAX(m.marks_obtained) AS highest_mark
        FROM students s
        JOIN classes c ON s.class_id = c.id
        JOIN marks m ON s.id = m.student_id
        JOIN subjects sub ON m.subject_id = sub.id
        WHERE s.id = ?
        GROUP BY s.id, s.name, s.roll_number, c.name";
    
    $stmt_student_details = $conn->prepare($sql_student_details);
    $stmt_student_details->bind_param("i", $student_id);
    $stmt_student_details->execute();
    $result_student_details = $stmt_student_details->get_result();
    $student_details = $result_student_details->fetch_assoc();

    // Detailed subject marks
    $sql_subject_marks = "
        SELECT 
            sub.name AS subject_name, 
            m.marks_obtained, 
            sub.pass_mark, 
            sub.fail_mark,
            CASE
                WHEN m.marks_obtained >= sub.pass_mark THEN 'Pass'
                WHEN m.marks_obtained < sub.fail_mark THEN 'Fail'
                ELSE 'Pending'
            END AS result
        FROM marks m
        JOIN subjects sub ON m.subject_id = sub.id
        WHERE m.student_id = ?
        ORDER BY m.marks_obtained DESC";
    
    $stmt_subject_marks = $conn->prepare($sql_subject_marks);
    $stmt_subject_marks->bind_param("i", $student_id);
    $stmt_subject_marks->execute();
    $result_subject_marks = $stmt_subject_marks->get_result();
    
    while ($row = $result_subject_marks->fetch_assoc()) {
        $subject_marks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .report-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
        }
        .report-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .performance-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .pass-badge {
            background-color: #28a745;
            color: white;
        }
        .fail-badge {
            background-color: #dc3545;
            color: white;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .report-container {
                box-shadow: none;
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="report-container">
            <div class="report-header text-center">
                <h2>Student Performance Report</h2>
            </div>
            
            <!-- Selection Form -->
            <form method="POST" class="no-print">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Select Class:</label>
                        <select id="class_id" name="class_id" class="form-select" required>
                            <option value="" disabled selected>Select a Class</option>
                            <?php foreach ($result_classes as $row_class): ?>
                                <option value="<?= $row_class['id']; ?>" <?= ($row_class['id'] == $class_id) ? 'selected' : ''; ?>>
                                    <?= $row_class['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="student_id" class="form-label">Select Student:</label>
                        <select id="student_id" name="student_id" class="form-select" <?= $class_id ? '' : 'disabled'; ?>>
                            <option value="" disabled selected>
                                <?= $class_id ? 'Select a Student' : 'First Select a Class' ?>
                            </option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id']; ?>" <?= ($student['id'] == $student_id) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($student['name'] . ' (Roll: ' . $student['roll_number'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>

            <!-- Student Report Card -->
            <?php if ($student_details): ?>
                <div class="mt-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="mb-3">Personal Details</h4>
                            <p><strong>Name:</strong> <?= htmlspecialchars($student_details['student_name']); ?></p>
                            <p><strong>Roll Number:</strong> <?= htmlspecialchars($student_details['roll_number']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">Academic Details</h4>
                            <p><strong>Class:</strong> <?= htmlspecialchars($student_details['class_name']); ?></p>
                            <p><strong>Total Subjects:</strong> <?= $student_details['total_subjects']; ?></p>
                        </div>
                    </div>

                    <h4 class="mb-3">Subject Performance</h4>
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Pass Mark</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subject_marks as $subject): ?>
                                <tr>
                                    <td><?= htmlspecialchars($subject['subject_name']); ?></td>
                                    <td><?= htmlspecialchars($subject['marks_obtained']); ?></td>
                                    <td><?= htmlspecialchars($subject['pass_mark']); ?></td>
                                    <td>
                                        <span class="performance-badge <?= 
                                            $subject['result'] == 'Pass' ? 'pass-badge' : 
                                            ($subject['result'] == 'Fail' ? 'fail-badge' : 'badge bg-warning text-dark')
                                        ?>">
                                            <?= htmlspecialchars($subject['result']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Performance Summary</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Passed Subjects:</strong> 
                                        <?= $student_details['passed_subjects']; ?> / 
                                        <?= $student_details['total_subjects']; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Average Marks:</strong> 
                                        <?= number_format($student_details['average_marks'], 2); ?>%
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Marks Range:</strong> 
                                        <?= $student_details['lowest_mark']; ?> - 
                                        <?= $student_details['highest_mark']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-success no-print">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#class_id').change(function() {
            var classId = $(this).val();
            $.ajax({
                url: 'get_students.php',
                method: 'POST',
                data: { class_id: classId },
                success: function(response) {
                    $('#student_id').html(response).prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>