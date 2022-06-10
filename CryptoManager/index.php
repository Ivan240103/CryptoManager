<!-- home page con i dati delle top cryptovalute -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="author" content="Ivan De Simone">
    <meta charset="utf-8">
    <link rel="icon" href="images/icon.jpg">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/home.css">
    <script type="text/javascript" src="js/script.js"></script>
    <title>Home</title>
  </head>
  <body>
    <header>
      <h1>CryptoManager</h1>
    </header>
    <nav>
      <table>
        <thead>
          <th><span class="current">Home</span></th>
          <th><a href="private/portfolio.php">Portfolio</a></th>
          <th><a href="private/profile.php">Profile</a></th>
        </thead>
      </table>
    </nav>
    <main>
      <!-- TODO: aggiungere market cap -->
      <!-- <p>Total crypto market cap: </p> -->
      <section class="top">
        <?php
          //utilizzo delle API di CoinMarketCap
          //URL della richiesta per i dati sulle crypto
          $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
          //parametri opzionali della richiesta
          $parameters = [
            'start' => '1', //numero della crypto di partenza
            'limit' => '20' //numero di crypto di cui richiede i dati
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

          //creazione di una risorsa cURL
          $request = curl_init();
          //impostazione opzioni della risorsa cURL
          curl_setopt_array($request, array(
            CURLOPT_URL => $completeUrl, //URL della richiesta
            CURLOPT_HTTPHEADER => $headers, //headers della richiesta
            CURLOPT_RETURNTRANSFER => 1 //richiede la raw response invece del bool
          ));
          //invio della richiesta e salvataggio della risposta
          $response = curl_exec($request);
          //conversione dati da formato JSON ad array associativo
          $decoded = (array) json_decode($response);
        ?>
        <!-- tabella con le top cryptovalute -->
        <table>
          <caption class="left">Top 20 cryptocurrencies at the moment</caption>
          <thead>
            <th id="rank">Rank</th>
            <th>Symbol</th>
            <th>Name</th>
            <th>Price</th>
            <th>24h %</th>
            <th>7d %</th>
          </thead>
          <tbody>
            <?php
              //stampa dei dati
              //ogni value è una crypto, key è il rank in classifica per market cap
              foreach ($decoded["data"] as $key => $value) {
                $crypto = (array) $value;
                $price = (array) $crypto["quote"];
                $usd = (array) $price["USD"];
                //variazione % delle ultime 24 ore arrotondata a due decimali
                $per24h = round($usd["percent_change_24h"], 2);
                //variazione % dell'ultima settimana arrotondata a due decimali
                $per7d = round($usd["percent_change_7d"], 2);
                echo "<tr><td id='rank'>".($key + 1)."</td><td>".$crypto["symbol"]
                  ."</td><td class='left'>".$crypto["name"]."</td><td>";
                //arrotonda il prezzo
                if ($usd["price"] < 1) {
                  //a 5 decimali se è minore di 1$
                  echo round($usd["price"], 5)." $</td>";
                } else {
                  //a 2 decimali se è maggiore di 1$
                  echo round($usd["price"], 2)." $</td>";
                }
                //controlla se la variazione giornaliera è positiva o negativa
                if ($per24h > 0) {
                  echo "<td class='over'>";
                } else if ($per24h < 0) {
                  echo "<td class='under'>";
                } else {
                  echo "<td>";
                }
                echo "$per24h %</td>";
                //controlla se la variazione settimanale è positiva o negativa
                if ($per7d > 0) {
                  echo "<td class='over'>";
                } else if ($per7d < 0) {
                  echo "<td class='under'>";
                } else {
                  echo "<td>";
                }
                echo "$per7d %</td></tr>";
              }

              //chiude la richiesta
              curl_close($request);
            ?>
          </tbody>
        </table>
      </section>
    </main>
  </body>
</html>
