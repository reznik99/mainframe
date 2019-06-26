<?php
  //error reporting
  //ini_set('display_errors', 1); error_reporting(E_ALL);

  $mysql_con = mysqli_connect("localhost", "root", "mainframe451", "MainframeDB");
  if($mysql_con->connect_error) {
      die($mysql_con->connect_error);
  }

  // Select all comments but return only the ones that are new (passed as GET param)
  $query = "SELECT * FROM `COMMENTS`";
  $results = mysqli_query($mysql_con, $query);
  $num_rows = mysqli_num_rows($results);
  $prev = htmlspecialchars($_GET["prev"]);
  
  if($num_rows > $prev){
    $count = 0;
    while($row = mysqli_fetch_assoc($results)){
      if($count >= $prev){
        echo "<li class='comment'>";
        echo "<i class='name'>" . $row['name'] . "-></i>" . $row['comment'] . "<i class='date'>" . $row['datetime'] . "</i>";
        echo "</li>";
      }
      $count++;
    }
  }
?>
