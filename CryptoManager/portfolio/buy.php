<!-- aggiunta o incremento asset -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/portfolio.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Portfolio - Buy</title>
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
          <th><a href="../private/profile.php">Profile</a></th>
        </thead>
      </table>
    </nav>
    <main>
      <h2>Buy</h2>
      <form class="operation" action="buy.php" method="post">
        <h3>Insert a new purchase</h3>
        <label for="symbol">Symbol </label>
        <input type="text" name="symbol" maxlength="5" placeholder="Like BTC, ETH..." required><br>
        <label for="quantity">Quantity </label>
        <input type="number" name="quantity" min="0" step=".0000000001" maxlength="20" required><br>
        <label for="price">Price (USD) </label>
        <input type="number" name="price" min="0" step=".0000000001" maxlength="20" required><br>
        <label for="date">Buy date </label>
        <input type="date" name="date" value="<?php echo date("Y-m-d"); ?>" required><br>
        <label for="time">Buy time </label>
        <input type="time" name="time" value="<?php echo date("H:i"); ?>" required><br>
        <div class="buttons">
          <button class="cancel" type="button" onclick="window.open('../private/portfolio.php', '_self')">Cancel</button>
          <button class="submit" type="submit">Insert</button>
        </div>
      </form>
      <?php
        session_start();
        if ($_POST) {
          //array che servirà per l'inserimento nella tabella operations
          $toInsert = array(
            'symbol' => strtoupper($_POST["symbol"]),
            'price' => $_POST["price"],
            'quantity' => $_POST["quantity"],
            'execDate' => $_POST["date"],
            "execTime" => $_POST["time"]
          );

          $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
          $q = "SELECT * FROM assets WHERE user = ".$_SESSION["idUser"];
          $q .= " AND symbol = '".$_POST["symbol"]."'";
          $res = mysqli_query($con, $q) or die("Query error");
          if (mysqli_num_rows($res) == 1) {
            //l'utente ha già il symbol in portafoglio
            $row = mysqli_fetch_array($res);
            //raccolta dati mancanti
            $toInsert["idAsset"] = $row["idAsset"];
            $toInsert["assetName"] = $row["assetName"];
            //aggiornamento della quantità
            $qIns = "UPDATE assets SET quantity = quantity + ".$toInsert["quantity"];
            $qIns .= " WHERE user = ".$_SESSION["idUser"]." AND symbol = '".$toInsert["symbol"]."'";
            $resIns = mysqli_query($con, $qIns) or die("Query error");
            if ($resIns == false) {
              echo "<p>Error while inserting new purchase</p>";
            }
          } else {
            //l'utente non possiede il symbol passato
            //request api
            //URL della richiesta per i dati sulle crypto
            $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/map';
            //parametri opzionali della richiesta
            $parameters = [
              'symbol' => $toInsert["symbol"] //dati solo sulla crypto richiesta
            ];
            //headers della richiesta
            $headers = [
              'Accepts: application/json',
              'X-CMC_PRO_API_KEY: 03b2eeae-439e-4bae-80c9-519f61f16cc8' //key di accettazione
            ];
            //inserimento dei parametri nella query
            $qs = http_build_query($parameters);
            //creazione dell'URL completo di richiesta
            $completeUrl = "{$url}?{$qs}";

            //crea una risorsa cURL
            $request = curl_init();
            //imposta le opzioni della risorsa cURL
            curl_setopt_array($request, array(
              CURLOPT_URL => $completeUrl, //URL della richiesta
              CURLOPT_HTTPHEADER => $headers, //headers della richiesta
              CURLOPT_RETURNTRANSFER => 1 //richiede la raw response invece del bool
            ));
            //invio della richiesta e salvataggio della risposta
            $response = curl_exec($request);
            //converte i dati da formato JSON ad un array associativo
            $decoded = (array) json_decode($response);
            //dati sulla crypto
            $info = (array) $decoded["data"][0];
            //raccolta dati mancanti
            $toInsert["idAsset"] = $info["id"];
            $toInsert["assetName"] = $info["name"];
            //aggiunta alla tabella assets
            $qIns = "INSERT INTO assets VALUES";
            $qIns .= "(".$toInsert["idAsset"].", ".$_SESSION["idUser"].", '".$toInsert["symbol"]."', '";
            $qIns .= $toInsert["assetName"]."', ".$toInsert["quantity"].")";
            $resIns = mysqli_query($con, $qIns) or die("Query error");
            if ($resIns == false) {
              echo "<p>Error while inserting new purchase</p>";
            }
          }
          //aggiunta alla lista dei buy
          $qBuy = "INSERT INTO operations VALUES";
          $qBuy .= "(null, 'buy', ".$toInsert["price"].", ".$toInsert["quantity"];
          $qBuy .= ", ".$toInsert["idAsset"].", '".$toInsert["symbol"]."', '";
          $qBuy .= $toInsert["assetName"]."', '".$toInsert["execDate"]."', '";
          $qBuy .= $toInsert["execTime"]."', ".$_SESSION["idUser"].")";
          $resBuy = mysqli_query($con, $qBuy) or die("Query error");
          if ($resBuy == false) {
            echo "<p>Error while inserting new purchase in list</p>";
          }
          mysqli_close($con);
        }
      ?>
      <!-- stampa dei precedenti acquisti -->
      <div class="operation">
        <h3>Previous purchases</h3>
        <table>
          <thead>
            <th>Symbol</th>
            <th>Name</th>
            <th>Order</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Datetime</th>
          </thead>
          <tbody>
            <?php
              $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
              //raccolta precedenti acquisti in ordine temporale
              $q = "SELECT * FROM operations WHERE user = ".$_SESSION["idUser"];
              $q .= " AND orderType = 'buy' ORDER BY execDate DESC, execTime DESC";
              $res = mysqli_query($con, $q) or die("Query error");
              while ($row = mysqli_fetch_array($res)) {
                echo "<tr><td>".$row["symbol"]."</td><td class='left'>".$row["assetName"]
                ."</td><td>".$row["orderType"]."</td><td>".$row["quantity"]."</td><td>";
                //arrotonda il prezzo
                if ($row["price"] < 1) {
                  //a 5 decimali se è minore di 1$
                  echo round($row["price"], 5)." $</td>";
                } else {
                  //a 2 decimali se è maggiore di 1$
                  echo round($row["price"], 2)." $</td>";
                }
                echo "<td>".$row["execDate"].", ".$row["execTime"]."</td></tr>";
              }
              mysqli_close($con);
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>
