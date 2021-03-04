<?php session_start();
function Redirect($url){
  header('Location: ' . $url);
  exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="indexe1.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="animated.css">
    <script type="text/javascript">
        window.onload = function() {
          var a = document.getElementById("mylink");
          a.onclick = function() {
            deconnexion();
            return true;
          }
        }
    </script>
    <title>Govoit</title>
  </head>
  <body onload="isAdmin();initMap();">

    <section class="intro">
      <div class="inner">
        <div class="content">
          <section class="title animated bounceInUp">
            <h1>Govoit</h1>
          </section>
          <section id="front" class="bouton">
            <a class="button"><?php echo " ".$_SESSION['Nom']." ".$_SESSION['Prenom']; ?></a>
            <a class="button"><?php echo "Note : ".$_SESSION['Avis']."/5"; ?></a>
            <a class="button" id="mylink" href="indexe.php">Deconnexion</a>
            <a id="car" class="button" href="vehicule.php">Renseignement d'un vehicule</a>
            <a id="trajet" class="button" href="creation.php">Nouveau Trajet</a>
          </section>
        </div>
      </div>
    </section>

      <div class="container">
        <div class="row">
          <div id="momo" class="col-sm-12 col-md-6">
            <form class="RechercheForm" action="#" method="post">
              <div class="form-group">
                <input id="Recherche_Depart" onclick="initAutocomplete()" class="form-control" placeholder="Votre départ" name="RechercheDepart" value="" autocomplete="off" type="text">
              </div>
              <div class="form-group">
                <input id="Recherche_Arrivee" onclick="initAutocomplete()" class="form-control" placeholder="Votre arrivée" name="RechercheArrivee" value="" autocomplete="off" type="text">
              </div>
              <div class="form-group">
                <input id="Date_Depart" class="form-control" placeholder="JJ/MM/AAAA" name="DateDep" type="date">
              </div>

              <div class="form-group">
                <input id="Recherche" class="btn btn-primary btn-block" value="Tracer le trajet" name="Recherche" type="button">
              </div>
              <div class="form-group">
                <input class="btn btn-primary btn-block" value="Recherche un trajet" name="submit" type="submit">
              </div>
              <div class="btn btn-danger" onclick="trajet/creation.php">
                <p class="TexteBoite">
                  Vous êtes conducteur?
                  <br>
                  <strong>Publiez un trajet</strong>
                </p>
              </div>
            </form>
          </div>

          <div id="toto" class="" class="col-sm-12 col-md-6">

          </div>

        </div>
      </div>
      <?php
      function deconnexion(){
        session_destroy();
      }
      if(isset($_POST['submit'])){
        $hostname='localhost';
        $username='root';
        $password='';
        try {
          $dbh = new PDO("mysql:host=$hostname;dbname=projet",$username,$password);
          $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $dbh->prepare("SELECT idTrajet,Prenom,Prix FROM trajet,membre
                                 WHERE DateDep = '".$_POST['DateDep']."'
                                 AND idConducteur=idMembre
                                 AND idTrajet IN (SELECT v1.idTrajet
                                                  FROM villeetape v1,villeetape v2
                                                  WHERE v2.idville IN (SELECT idVille
                                                                       FROM ville
                                                                       WHERE Nom='".$_POST['RechercheDepart']."')
                                                  AND v1.idville IN (SELECT idVille
                                                                     FROM ville
                                                                     WHERE Nom='".$_POST['RechercheArrivee']."')
                                                  AND v2.Ordre<v1.Ordre)
                                  ORDER BY Prix");
      $stmt->execute();
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
          if ($result) {
            $resColumn = $stmt->fetchAll();
            echo '<table class="table table-striped" style="width:100%">
            <tr>
              <th>Ville de Depart</th>
              <th>Ville Arrivee</th>
              <th>Date de Depart</th>
              <th>Conducteur</th>
              <th>Prix</th>
              <th>Inscription</th>
            </tr>';
            foreach ($resColumn as $r) {
               echo "<tr><td>".$_POST['RechercheDepart']."</td><td>".$_POST['RechercheArrivee']."</td><td>".$_POST['DateDep']."</td><td>".$r['Prenom']."</td><td>".$r['Prix'].'</td><td><form action="" method="post"><input type="submit" value="Inscription" name="submit"></input><input type="text" name="trajet" style="display:none;" value="'.$r['idTrajet'].'"></input></td></form></tr>';
            }
          }
          echo "</table>";
          $dbh = null;
        }
        catch(PDOException $e)
        {
          echo $e->getMessage();
        }
      }
        ?>
      <script>
      function isAdmin(){
        var estAdmin = <?php echo $_SESSION['Administrateur']; ?>;
        if(estAdmin){
          var w= document.getElementById('front');
          var a = document.createElement("a");
          a.href="membres.php";
          a.className = "button";
          a.id = "membre";
          var t = document.createTextNode("Les Membres");
          a.appendChild(t);
          w.appendChild(a);
        }
      }
      var directionsDisplay;
      var directionsService;
      var map;
      function initAutocomplete() {
        var DepartInput = document.getElementById('Recherche_Depart');
        var DestinationInput = document.getElementById('Recherche_Arrivee');
        var options = {
          types: ['(cities)'],
          componentRestrictions: {country: "fr"}
        };
        var DepartAutocomplete = new google.maps.places.Autocomplete(DepartInput, options);
        var DestinationAutocomplete = new google.maps.places.Autocomplete(DestinationInput, options);
      }
      function initMap(){
        document.getElementById('toto').className = "toto animated bounceInLeft";
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        var options = {
          zoom:6,
          center:{lat:48.858299,lng:2.294149},
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('toto'),options);
        directionsDisplay.setMap(map);
        document.getElementById('Recherche').addEventListener('click', function() {
              calculateAndDisplayRoute(directionsService, directionsDisplay);
            });
      }
      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
      var depart = document.getElementById('Recherche_Depart').value;
      var arrivee = document.getElementById('Recherche_Arrivee').value;
      var request = {
        origin: depart,
        destination: arrivee,
        travelMode: 'DRIVING'
      };
      directionsService.route(request, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(result);
        }
      });
      }
      </script>
      <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBFpQE-Hm_Y5BMDtElGTEyCHfqZJU1KUU&callback=initAutocomplete&libraries=places" type="text/javascript"></script>
  </body>
</html>
