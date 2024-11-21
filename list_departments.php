<?php
include 'db_connection.php';

$sql = "SELECT * FROM departments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Created At</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['id'] . "</td>
                <td>" . $row['name'] . "</td>
                <td>" . $row['created_at'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No departments found.";
}

$conn->close();
?>
