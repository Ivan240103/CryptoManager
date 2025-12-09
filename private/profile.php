<!-- pagina del profilo utente con i dati -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/pages.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Profile</title>
    <?php
      //ritorna false se l'accesso viene negato, l'id dell'utente altrimenti
      function access($u, $p) {
        //valore da returnare
        $tr = false;
        //password inserita criptata
        $encryptPsw = openssl_encrypt($p, "AES-128-CTR",
          "ChiaveSegreta", 0, "1234567891011121");

        $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
        //prende gli utenti dal db
        $q = "SELECT idUser, username, password FROM users";
        $res = mysqli_query($con, $q) or die("Query error");
        while ($row = mysqli_fetch_array($res)) {
          if ($u == $row["username"] && $encryptPsw == $row["password"]) {
            //l'utente è autenticato
            $tr = $row["idUser"];
            break;
          }
        }
        mysqli_close($con);
        return $tr;
      }
    ?>
  </head>
  <body>
    <header>
      <h1>CryptoManager</h1>
    </header>
    <nav>
      <table>
        <thead>
          <th><a href="../index.php">Home</a></th>
          <th><a href="portfolio.php">Portfolio</a></th>
          <th><span class="current">Profile</span></th>
        </thead>
      </table>
    </nav>
    <main>
      <?php
        session_start();
        //se le credenziali sono settate allora mostra il profilo
        if (isset($_SESSION["idUser"])) {
          $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
          //recupera i dati dell'utente
          $q = "SELECT username, name, surname, email FROM users WHERE idUser = '".$_SESSION["idUser"]."'";
          $res = mysqli_query($con, $q) or die("Query error");
          $row = mysqli_fetch_array($res);
          //stampa i dati
          echo "<section class='personal'>";
          echo "<h4>Welcome to your profile, ".$row["username"]."!</h4>";
          echo "<p>Name: ".$row["name"]."</p>";
          echo "<p>Surname: ".$row["surname"]."</p>";
          echo "<p>Email: ".$row["email"]."</p></section>";
        ?>
        <button class='logout' type='button' onclick="window.open('logout.php', '_self')">Logout</button>
        <?php
          mysqli_close($con);
        } else {
          /*se non sono settate verifico se si è arrivati con il post, nel caso
          bisogna verificare le credenziali, altrimenti rimanda alla pagina di
          login*/
          if ($_POST) {
            //controllo credenziali
            $id = access($_POST["user"], $_POST["psw"]);
            if ($id != false) {
              //utente autorizzato, imposta la sessione utente
              $_SESSION["idUser"] = $id;
              //riapre la pagina per accedere con la sessione
              echo "<script>window.open('profile.php', '_self')</script>";
            } else {
              //utente non autorizzato
              echo "<div class='deniedInfo'><p>Access denied</p>";
              echo "<p><a href='login.html'>Go to login page</a></p><div>";
            }
          } else {
            //sessione non impostata e non sta cercando di fare login
            echo "<div class='deniedInfo'><p><a href='login.html'>Go to login page</a></p></div>";
          }
        }
      ?>
    </main>
  </body>
</html>
