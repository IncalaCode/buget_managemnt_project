<?php
include_once('./back/php/connect.php');
$sql = "SELECT * FROM employ";
$con = connect();
$query = $con->query($sql);

if ($query->num_rows > 0) {
    get_row($query);
} else {
    echo "<tr>
            <td colspan='5' class= 'text-center'>No user found</td>
          </tr>";
}

function get_row($query){
    while ($row = $query->fetch_assoc()) {
        echo 
        "<tr>
            <td>" . $row['id']. "</td>
             <td>" . $row['role']. "</td>
            <td>" . $row['fname'] . "</td>
            <td>" . $row['lname'] . "</td>
            <td>" . $row['username'] . "</td>
            <td>" . $row['phonenum'] . "</td>
            <td>" . $row['code'] . "</td>
        </tr>"; 
    }
}

?>