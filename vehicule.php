<?php session_start(); ?>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Goconvoit</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

  <div id="main">
    <a href="indexe1.php"><h1>Govoit</h1></a>
    <div id="login">
      <h2>Votre Vehicule</h2>
      <hr/>
      <form action="" method="post">
        <label>Immatriculation :</label>
        <input type="text" name="Immatriculation" required="required" placeholder="Entrez l'immatriculation"/><br /><br />
        <label>Marque :</label>
        <input type="text" name="Marque" required="required" placeholder="Entrez la marque"/><br/><br />
        <label>Couleur :</label>
        <div>
          <select class="age" name="Couleur" required="required">
            <option value="" selected="selected"></option>
            <option value="Noir">Noir</option>
            <option value="Gris">Gris</option>
            <option value="Blanc">Blanc</option>
            <option value="Rouge">Rouge</option>
            <option value="Vert">Vert</option>
            <option value="Jaune">Jaune</option>
            <option value="Bleu">Bleu</option>
          </select>
        </div>
        <label>Nombre de Places :</label>
        <div>
          <select class="age" name="nbPlace" required="required">
            <option value="" selected="selected"></option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
          </select>
        </div>
        <input type="submit" value=" Submit " name="submit"/><br />
      </form>
    </div>
</div>
<?php
if(isset($_POST["submit"])){
  $hostname='localhost';
  $username='root';
  $password='';

  try {
    $dbh = new PDO("mysql:host=$hostname;dbname=projet",$username,$password);

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
    $sql = "INSERT INTO vehicule (Immatriculation, Marque, Couleur, nbPlace, idMembre)
    VALUES ('".$_POST["Immatriculation"]."','".$_POST["Marque"]."','".$_POST["Couleur"]."','".$_POST["nbPlace"]."','".$_SESSION["idMembre"]."')";
    if ($dbh->query($sql)) {
      echo "<script type= 'text/javascript'>alert('Nouveau vehicule ajouté.');</script>";
    }
    else{
      echo "<script type= 'text/javascript'>alert('Impossible d'ajouter le vehicule.');</script>";
    }
    array_push($_SESSION['Immatriculation'], $_POST['Immatriculation']);
    array_push($_SESSION['nbPlace'], $_POST['nbPlace']);
    $url = "indexe1.php";
    Redirect($url);
    $dbh = null;
  }
  catch(PDOException $e)
  {
    echo $e->getMessage();
  }

}
function Redirect($url){
  header('Location: ' . $url);
  exit();
}
?>
</body>
</html>
