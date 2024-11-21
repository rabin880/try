<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }
        .content {
            margin-top: 60px;
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center mt-3">Result System</h4>
        <a href="add_department.php">Add Department</a>
        <a href="add_class.php">Add Class</a>
        <a href="add_subject.php">Add Subject</a>
        <a href="add_student.php">Add Student</a>
        <a href="add_marks.php">Add Marks</a>
        <a href="list_marks.php">View Marks</a>
        <a href="generate_report.php">Generate Results</a>
    </div>

    <!-- Header -->
    <div class="header">
        <h5>Result Management System</h5>
        <div>
            <a href="#" class="text-white">Profile</a> |
            <a href="#" class="text-white">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Welcome to the Result Management System</h1>
        <p>Select an option from the sidebar to manage departments, classes, subjects, students, marks, or results.</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
