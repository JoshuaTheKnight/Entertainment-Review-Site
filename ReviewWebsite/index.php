<h1>This is my PHP homepage!!</h1>


<?php

  $servername = "localhost";

  $username   = "root";

  $password   = "";

  $dbname     = "entertainment_reviews";


  // Create connection object

  $conn = new mysqli($servername, $username, $password, $dbname);


  // Check connection

  if ($conn->connect_error) {

    die("Connection failed: " . $conn->connect_error);

  }

  $sql = "SELECT user_id, username, email, date_joined FROM users";

  $result = $conn->query($sql);

  

  if ($result->num_rows > 0) {

    // output data of each row

    while($row = $result->fetch_assoc()) {

      echo "Name: " . $row["username"]. "<br>";

    }

  } else {

    echo "0 results";

  }

  $conn->close();
?>
