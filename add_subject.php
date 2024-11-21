<?php
include 'db_connection.php';

// Start session for feedback message
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_name = $_POST['subject_name'];
    $class_id = $_POST['class_id'];
    $pass_mark = $_POST['pass_mark'];
    $fail_mark = $_POST['fail_mark'];

    // Prepare SQL query to insert the subject details including pass and fail marks
    $sql = "INSERT INTO subjects (name, class_id, pass_mark, fail_mark) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidd", $subject_name, $class_id, $pass_mark, $fail_mark);

    if ($stmt->execute()) {
        // Store success message in session and redirect to the same page
        $_SESSION['message'] = "Subject added successfully.";
        header("Location: add_subject.php");
        exit(); // Stop further execution to prevent form resubmission
    } else {
        // Store error message in session and redirect to the same page
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: add_subject.php");
        exit();
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
    <title>Add Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Subject</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); // Clear session message after display ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); // Clear session error message after display ?>
        <?php endif; ?>

        <form action="add_subject.php" method="POST">
            <!-- Subject Name -->
            <div class="mb-3">
                <label for="subject_name" class="form-label">Subject Name:</label>
                <input type="text" id="subject_name" name="subject_name" class="form-control" required>
            </div>

            <!-- Class Selection -->
            <div class="mb-3">
                <label for="class_id" class="form-label">Class:</label>
                <select id="class_id" name="class_id" class="form-select" required>
                    <option value="" disabled selected>Select a Class</option>
                    <?php
                    // Fetch classes from the database
                    $sql = "SELECT * FROM classes";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Pass Mark -->
            <div class="mb-3">
                <label for="pass_mark" class="form-label">Pass Mark:</label>
                <input type="number" id="pass_mark" name="pass_mark" class="form-control" step="0.01" required>
            </div>

            <!-- Fail Mark -->
            <div class="mb-3">
                <label for="fail_mark" class="form-label">Fail Mark:</label>
                <input type="number" id="fail_mark" name="fail_mark" class="form-control" step="0.01" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Add Subject</button>
        </form>
    </div>
</body>
</html>
