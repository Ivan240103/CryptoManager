<!-- pagina di logout e terminazione sessione -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/pages.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Logout</title>
  </head>
  <body>
    <header>
      <h1>CryptoManager</h1>
    </header>
    <nav>
      <table>
        <thead>
          <th><span class="current">Logout</span></th>
        </thead>
      </table>
    </nav>
    <main>
      <?php
        session_start();
        //elimina i dati dell'utente
        $_SESSION = array();
        //termina la sessione
        session_destroy();
      ?>
      <div class="logoutInfo">
        <p>You have successfuly logged out</p>
        <a href="../index.php">Go back to home page</a>
      </div>
    </main>
  </body>
</html>
