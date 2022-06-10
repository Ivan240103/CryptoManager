<!-- visualizzazione allocazione e tabella asset -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/portfolio.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Portfolio</title>
  </head>
  <body>
    <header>
      <h1>CryptoManager</h1>
    </header>
    <nav>
      <table>
        <thead>
          <th><a href="../index.php">Home</a></th>
          <th><span class="current">Portfolio</span></th>
          <th><a href="profile.php">Profile</a></th>
        </thead>
      </table>
    </nav>
    <main>
      <?php
        session_start();
        //se le credenziali sono settate allora mostra il portfolio
        if (isset($_SESSION["idUser"])) {
      ?>
      <section class="order">
        <button type="button" class="buy" onclick="window.open('../portfolio/buy.php', '_self')">Buy</button>
        <button type="button" class="sell" onclick="window.open('../portfolio/sell.php', '_self')">Sell</button>
      </section>
      <section class="asset">
        <div class="assetTable">
          <table>
            <thead>
              <th>Symbol</th>
              <th>Name</th>
              <th>Quantity</th>
            </thead>
            <tbody>
              <?php
                //reperimento dati dal db e stampa
                $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
                $q = "SELECT * FROM assets WHERE user = ".$_SESSION["idUser"];
                $res = mysqli_query($con, $q) or die("Query error");
                while ($row = mysqli_fetch_array($res)) {
                  echo "<tr><td>".$row["symbol"]."</td><td>".$row["assetName"]
                  ."</td><td>".$row["quantity"]."</td></tr>";
                }
                // TODO: aggiungere prezzo medio di carico
                mysqli_close($con);
              ?>
            </tbody>
          </table>
        </div>
        <div class="assetGraph">
          <!-- TODO aggiungere grafico dell'allocazione -->
        </div>
      </section>
      <?php
        } else {
          //se non è autenticato mostra pagina di login
          echo "<div class='portDen'><p><a href='login.html'>Go to login page</a></p></div>";
        }
      ?>
    </main>
  </body>
</html>
