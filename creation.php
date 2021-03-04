<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="creation.css" rel="stylesheet">
  <link href="animated.css" rel="stylesheet">
  <title>Car</title>
</head>
<body onload="initMap()">
  <div class="container">
    <div class="row">
      <div id="creation" class="col-md-12 col-md-4">
        <form class="CreationForm well" method="post" action="#">
          <div class="form-group">
            <label class="control-label depart">Depart</label>
            <input id="Creation_Depart" onclick="initAutocomplete();" class="form-control" placeholder="Votre départ" name="CreationDepart" value="" autocomplete="off" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Adresse de Depart</label>
            <input id="Adresse_Depart" class="form-control" placeholder="L'adresse de depart" name="AdresseDepart" value="" autocomplete="off" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Destination</label>
            <input id="Creation_Arrivee" onclick="initAutocomplete();" class="form-control" placeholder="Votre arrivée" name="CreationArrivee" value="" autocomplete="off" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Adresse d'Arrivée</label>
            <input id="Adresse_Arrivee" class="form-control" placeholder="L'adresse d'arrivée" name="AdresseArrivee" value="" autocomplete="off" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Nombre de Ville Etape</label>
            <select id="Nb_Etape" class="form-control" name="NombreEtape" onclick="newCity(this.value);">
            <option value="00" selected="selected"></option>
            <option value="1">01</option>
            <option value="2">02</option>
            <option value="3">03</option>
            <option value="4">04</option>
            <option value="5">05</option>
            <option value="6">06</option>
            <option value="7">07</option>
            <option value="8">08</option>
            <option value="9">09</option>
            <option value="10">10</option>
          </select>
            <label class="control-label" id="etape" name="etape">Ville Etape dans l'ordre</label>
          </div>
          <div class="form-group">
            <input id="submit" class="btn btn-primary btn-block" value="Tracer votre trajet" name="Affichage" type="button">
          </div>
          <div class="form-group">
            <label class="control-label">Prix</label>
            <input id="Prix" class="form-control" placeholder="Le prix du trajet" name="Prix" value="" autocomplete="off" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Date de Depart</label>
            <input id="Date_Depart" class="form-control" name="DateDep" type="date">
          </div>
          <input style="display:none;" id="distance" value="" name="distance" type="text">
          <div class="form-group">
            <label>
              Heures
              <select id="Heure_Depart" class="form-control" name="HeureDep">
              <option value="" selected="selected"></option>
              <option value="0">00</option>
              <option value="1">01</option>
              <option value="2">02</option>
              <option value="3">03</option>
              <option value="4">04</option>
              <option value="5">05</option>
              <option value="6">06</option>
              <option value="7">07</option>
              <option value="8">08</option>
              <option value="9">09</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
            </select>
            </label>
          </div>
          <div class="form-group">
            <label>
              Minutes
              <select id="Minute_Depart" class="form-control" name="MinuteDep">
              <option value="" selected="selected"></option>
              <option value="0">00</option>
              <option value="5">05</option>
              <option value="2">10</option>
              <option value="3">15</option>
              <option value="4">20</option>
              <option value="5">25</option>
              <option value="6">30</option>
              <option value="7">35</option>
              <option value="8">40</option>
              <option value="9">45</option>
              <option value="10">50</option>
              <option value="11">55</option>
            </select>
            </label>
          </div>
          <div class="form-group">
            <input id="Creation" class="btn btn-primary btn-block" value="Creer un trajet" name="Creation" type="submit">
          </div>
        </form>
      </div>

      <div class="col-md-12 col-md-8">
        <div id="toto" class="form-group" style="margin-left:0;width:110%;height:610px;margin-top:40px;">
        </div>
      </div>

    </div>
  </div>
  <?php
  if(isset($_POST["Creation"])){
    $hostname='localhost';
    $username='root';
    $password='';
    try {
      $dbh = new PDO("mysql:host=$hostname;dbname=projet",$username,$password);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
      //verifie si la ville existe dans la bdd sinon on l'ajoute
      $requete1="SELECT count(*) from ville where Nom='".$_POST['CreationDepart']."';";
      $requete2="SELECT count(*) from ville where Nom='".$_POST['CreationArrivee']."';";
      $result1=$dbh->query($requete1);
      $result2=$dbh->query($requete2);
      if ($result1->fetch()[0]==0) {
        $sql1 = "INSERT INTO ville (Nom)
        VALUES ('".$_POST["CreationDepart"]."')";
        $result1=$dbh->query($sql1);
      }
      if ($result2->fetch()[0]==0) {
        $sql2 = "INSERT INTO ville (Nom)
        VALUES ('".$_POST["CreationArrivee"]."')";
        $result1=$dbh->query($sql2);
      }
      //on ajoute le trajet
      $stmt = $dbh->prepare("INSERT INTO trajet (DateDep, Prix, Distance, idConducteur, AdresseRdv, AdresseArr)
      VALUES ('".$_POST["DateDep"]."','".$_POST["Prix"]."','".$_POST["distance"]."','".$_SESSION["idMembre"]."','".$_POST["AdresseDepart"]."','".$_POST["AdresseArrivee"]."')");
      $stmt->execute();
      $dbh = null;
    }
    catch(PDOException $e)
    {
      echo $e->getMessage();
    }
  }
  ?>
  <script>
  var nbEtape=0;
  var geocoder;
  var map;
  var directionsDisplay;
  var directionsService;
  function initAutocomplete2(data){
    var options = {
      types: ['(cities)'],
      componentRestrictions: {country: "fr"}
    };
    var j='Ville_Etape';
    var id;
    for(var i=0;i<data;i++){
      id = j.concat(i);
      var VilleEtape = document.getElementById(id);
      var EtapeAutocomplete = new google.maps.places.Autocomplete(VilleEtape, options);
    }
  }
  function initAutocomplete() {
    var DepartInput = document.getElementById('Creation_Depart');
    var DestinationInput = document.getElementById('Creation_Arrivee');
    var options = {
      types: ['(cities)'],
      componentRestrictions: {country: "fr"}
    };
    var DepartAutocomplete = new google.maps.places.Autocomplete(DepartInput, options);
    var DestinationAutocomplete = new google.maps.places.Autocomplete(DestinationInput, options);
    geocoder = new google.maps.Geocoder();
  }
  var marker;
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
  document.getElementById('submit').addEventListener('click', function() {
          calculateAndDisplayRoute(directionsService, directionsDisplay);
        });

}
  function calculateAndDisplayRoute(directionsService, directionsDisplay) {
  var depart = document.getElementById('Creation_Depart').value;
  var j = "Ville_Etape";
  var id;
  var waypts=[];
  for(var i=0;i<nbEtape;i++){
    id = j.concat(i);
    waypts.push({
      location: document.getElementById(id).value,
      stopover: true
    });
  }
  var arrivee = document.getElementById('Creation_Arrivee').value;
  var request = {
    origin: depart,
    destination: arrivee,
    waypoints: waypts,
    optimizeWaypoints: true,
    travelMode: 'DRIVING'
  };
  var distance = 0;
  directionsService.route(request, function(result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
      for(var i = 0;i<=nbEtape;i++){
        distance += result.routes[0].legs[i].distance.value;
      }
      document.getElementById('distance').value = distance;
    }
  });
  }

  function newCity(data){
    var w= document.getElementById('etape');
    var j = "Ville_Etape";
    for(var i=0;i<data;i++){
      var input = document.createElement("input");
      input.type = "text";
      var id = j.concat(i);
      input.id = id;
      input.className = "form-control";
      input.value = "";
      w.appendChild(input);
    }
    initAutocomplete2(data);
    nbEtape=data;
  }
  </script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBFpQE-Hm_Y5BMDtElGTEyCHfqZJU1KUU&callback=initMap&libraries=places,geometry" type="text/javascript"></script>
</body>
</html>
