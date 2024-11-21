<?php
include 'db_connection.php';

$sql = "SELECT 
            students.name AS student_name, 
            subjects.name AS subject_name, 
            marks.marks_obtained, 
            marks.max_marks 
        FROM marks 
        JOIN students ON marks.student_id = students.id 
        JOIN subjects ON marks.subject_id = subjects.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Student</th>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Maximum Marks</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['student_name'] . "</td>
                <td>" . $row['subject_name'] . "</td>
                <td>" . $row['marks_obtained'] . "</td>
                <td>" . $row['max_marks'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No marks found.";
}

$conn->close();
?>
