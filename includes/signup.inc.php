<?php
// Here we check whether the user got to this page by clicking the proper signup button.
if (isset($_POST['signup-submit'])) {

  // We include the connection script so we can use it later.
  // We don't have to close the MySQLi connection since it is done automatically, but it is a good habit to do so anyways since this will immediately return resources to PHP and MySQL, which can improve performance.
  require 'dbh.inc.php';
  
  $username = $_POST['uid']; // We grab all the data which we passed from the signup form so we can use it later.
  $email = $_POST['mail'];
  $password = $_POST['pwd'];
  $passwordRepeat = $_POST['pwd-repeat'];
    
  // First we create the statement that searches our database table to check for any identical usernames.
  $sql = "SELECT username FROM users WHERE username=?;";

  
  $stmt = mysqli_stmt_init($conn);// We create a prepared statement.
  if (!mysqli_stmt_prepare($stmt, $sql)) {    // If there is an error we send the user back to the signup page.
    header("Location: ../signup.php?error=sqlerror");
    exit();
  }
  else {
    // Next we need to bind the type of parameters we expect to pass into the statement, and bind the data from the user.
      // In case you need to know, "s" means "string", "i" means "integer", "b" means "blob", "d" means "double".
      mysqli_stmt_bind_param($stmt, "s", $username);
      // Then we execute the prepared statement and send it to the database!
      mysqli_stmt_execute($stmt);
      // Then we store the result from the statement.
      mysqli_stmt_store_result($stmt);
      // Then we get the number of result we received from our statement. This tells us whether the username already exists or not!
      $resultCount = mysqli_stmt_num_rows($stmt);
      // Then we close the prepared statement!
      mysqli_stmt_close($stmt);
      // Here we check if the username exists.
      if ($resultCount > 0) {
        header("Location: ../signup.php?error=usertaken&mail=".$email);
        exit();
      }
      else {
        // If we got to this point, it means the user didn't make an error!
        // Next thing we do is to prepare the SQL statement that will insert the users info into the database. We HAVE to do this using prepared statements to make this process more secure. DON'T JUST SEND THE RAW DATA FROM THE USER DIRECTLY INTO THE DATABASE!
        // Prepared statements works by us sending SQL to the database first, and then later we fill in the placeholders (this is a placeholder -> ?) by sending the users data.
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?);";
        // Here we initialize a new statement using the connection from the dbh.inc.php file.
        $stmt = mysqli_stmt_init($conn);
        // Then we prepare our SQL statement AND check if there are any errors with it.
        if (!mysqli_stmt_prepare($stmt, $sql)) {
          // If there is an error we send the user back to the signup page.
          header("Location: ../signup.php?error=sqlerror");
          exit();
        }
        else {       

          // Before we send ANYTHING to the database we HAVE to hash the users password to make it un-readable in case anyone gets access to our database without permission!
          // The hashing method I am going to show here, is the LATEST version and will always will be since it updates automatically. DON'T use md5 or sha256 to hash, these are old and outdated!
          $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

          // Next we need to bind the type of parameters we expect to pass into the statement, and bind the data from the user.
          mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
          // Then we execute the prepared statement and send it to the database!
          // This means the user is now registered! :)
          mysqli_stmt_execute($stmt);
          // Lastly we send the user back to the signup page with a success message!
          header("Location: ../signup.php?signup=success");
          exit();

        }
      }    
  }
  // Then we close the prepared statement and the database connection!
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
else {
  // If the user tries to access this page an inproper way, we send them back to the signup page.
  header("Location: ../signup.php");
  exit();
}