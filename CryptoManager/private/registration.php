<!-- esecuzione della registrazione utente -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/pages.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Signed up</title>
  </head>
  <body>
    <header>
      <h1>CryptoManager</h1>
    </header>
    <nav>
      <table>
        <thead>
          <th><a href="javascript:history.go(-1)">< Back</a></th>
          <th><span class="current">Registration</span></th>
        </thead>
      </table>
    </nav>
    <main>
      <div class="regInfo">
        <?php
          $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
          //prende tutti gli username dal db
          $q = "SELECT username FROM users";
          $res = mysqli_query($con, $q) or die("Query error");
          //variabile per segnare se l'username è già presente
          $found = false;
          while ($row = mysqli_fetch_array($res)) {
            //controlla se l'username è già stato usato
            if ($_POST["user"] == $row["username"]) {
              $found = true;
              break;
            }
          }
          //segnala il risultato
          if ($found) {
            echo "<p>Username not available</p>";
            echo "<p><a href='signup.html'>Return to registration page</a></p>";
          } else {
            //inserisce il nuovo utente nel db
            $encryptPsw = openssl_encrypt($_POST["psw"], "AES-128-CTR",
              "ChiaveSegreta", 0, "1234567891011121");
            $q = "INSERT INTO users VALUES";
            $q .= "(null, '".$_POST["name"]."', '".$_POST["surname"]."', '"
              .$_POST["email"]."', '".$_POST["user"]."', '$encryptPsw')";
            $res = mysqli_query($con, $q) or die("Query error");
            //controlla il risultato dell'inserimento
            if ($res != false) {
              echo "<p>Registration successful</p>";
              echo "<p><a href='login.html'>Go to login page</a><p>";
            } else {
              echo "<p>Subscription error</p>";
              echo "<p><a href='signup.html'>Return to registration page</a></p>";
            }
          }
          mysqli_close($con);
        ?>
      </div>
    </main>
  </body>
</html>
