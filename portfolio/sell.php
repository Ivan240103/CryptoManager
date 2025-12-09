<!-- rimozione o decremento asset -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="../images/icon.jpg">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/portfolio.css">
    <script type="text/javascript" src="../js/script.js"></script>
    <title>Portfolio - Sell</title>
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
      <?php
        session_start();
        if ($_POST) {
          //array che servirà per l'inserimento nella tabella operations
          $toInsert = array(
            'idAsset' => $_POST["asset"],
            'price' => $_POST["price"],
            'quantity' => $_POST["quantity"],
            'execDate' => $_POST["date"],
            "execTime" => $_POST["time"]
          );

          $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
          //decrementa la quantità nella tabella assets
          $qIns = "UPDATE assets SET quantity = quantity - ".$toInsert["quantity"];
          $qIns .= " WHERE user = ".$_SESSION["idUser"]." AND idAsset = ".$toInsert["idAsset"];
          $resIns = mysqli_query($con, $qIns) or die("Query error");
          if ($resIns == false) {
            echo "<p>Error while inserting new sale</p>";
          }
          //seleziona l'asset appena modificato
          $qCheck = "SELECT * FROM assets WHERE user = ".$_SESSION["idUser"]." AND idAsset = ".$toInsert["idAsset"];
          $resCheck = mysqli_query($con, $qCheck) or die("Query error");
          $rowCheck = mysqli_fetch_array($resCheck);
          //raccolta dati mancanti
          $toInsert["symbol"] = $rowCheck["symbol"];
          $toInsert["assetName"] = $rowCheck["assetName"];
          //controlla se la quantità è diventata minore/uguale di 0
          if ($rowCheck["quantity"] <= 0) {
            //se lo è elimina l'asset dal db
            $qDel = "DELETE FROM assets WHERE user = ".$_SESSION["idUser"]." AND idAsset = ".$toInsert["idAsset"];
            $resDel = mysqli_query($con, $qDel) or die("Query error");
            if ($resDel == false) {
              echo "<p>Error while deleting asset at zero</p>";
            }
          }
          mysqli_close($con);
        }
      ?>
      <h2>Sell</h2>
      <form class="operation" action="sell.php" method="post">
        <h3>Insert a new sale</h3>
        <label for="asset">Asset sold</label>
        <select name="asset">
          <?php
            $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
            $q = "SELECT idAsset, symbol, assetName FROM assets WHERE user = ".$_SESSION["idUser"];
            $res = mysqli_query($con, $q) or die("Query error");
            while ($row = mysqli_fetch_array($res)) {
              echo "<option value='".$row["idAsset"]."'>".$row["symbol"]." - "
                .$row["assetName"]."</option>";
            }
            mysqli_close($con);
          ?>
        </select><br>
        <label for="quantity">Quantity </label>
        <input type="number" name="quantity" min="0" step=".0000000001" maxlength="20" required><br>
        <label for="price">Price (USD) </label>
        <input type="number" name="price" min="0" step=".0000000001" maxlength="20" required><br>
        <label for="date">Sell date </label>
        <input type="date" name="date" value="<?php echo date("Y-m-d"); ?>" required><br>
        <label for="time">Sell time </label>
        <input type="time" name="time" value="<?php echo date("H:i"); ?>" required><br>
        <div class="buttons">
          <button class="cancel" type="button" onclick="window.open('../private/portfolio.php', '_self')">Cancel</button>
          <button class="submit" type="submit">Insert</button>
        </div>
      </form>
      <?php
        //aggiunta vendita alla tabella
        if ($_POST) {
          $con = mysqli_connect("127.0.0.1", "root", "", "PortfolioDB") or die(mysqli_connect_error());
          $qSell = "INSERT INTO operations VALUES";
          $qSell .= "(null, 'sell', ".$toInsert["price"].", ".$toInsert["quantity"];
          $qSell .= ", ".$toInsert["idAsset"].", '".$toInsert["symbol"]."', '";
          $qSell .= $toInsert["assetName"]."', '".$toInsert["execDate"]."', '";
          $qSell   .= $toInsert["execTime"]."', ".$_SESSION["idUser"].")";
          $resSell = mysqli_query($con, $qSell) or die("Query error");
          if ($resSell == false) {
            echo "<p>Error while inserting new sale in list</p>";
          }
          mysqli_close($con);
        }
      ?>
      <!-- stampa delle precedenti vendite -->
      <div class="operation">
        <h3>Previous sales</h3>
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
            //seleziona le precedenti vendite in ordine temporale
            $q = "SELECT * FROM operations WHERE user = ".$_SESSION["idUser"];
            $q .= " AND orderType = 'sell' ORDER BY execDate DESC, execTime DESC";
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
