<?php session_start();
function Redirect($url){
  header('Location: ' . $url);
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="aut.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
  <link href='https://fonts.googleapis.com/css?family=Passion+One' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>
  <title>Connexion</title>
</head>
<body>
  <div class="container">
    <div class="row main">
      <div class="panel-heading">
        <div class="panel-title text-center">
          <h1 class="title">Connexion</h1>
          <hr />
        </div>
      </div>
      <div class="main-login main-center">
        <form class="form-horizontal" method="post" action="#">
          <div class="form-group">
            <label for="email" class="cols-sm-2 control-label">Votre Email</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
                <input type="text" class="form-control" name="email" id="email"  placeholder="Entrer votre email"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="cols-sm-2 control-label">Password</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                <input type="password" class="form-control" name="password" id="password"  placeholder="Entrer votre Password"/>
              </div>
            </div>
          </div>

          <div class="form-group ">
            <input type="submit" class="btn btn-primary btn-lg btn-block login-button" name="submit" value="Connexion">
          </div>
          <div class="login-register">
            <a href="registers.php">Creer un Compte</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php
  if(isset($_POST["submit"])){
    $hostname='localhost';
    $username='root';
    $password='';
    try {
      $dbh = new PDO("mysql:host=$hostname;dbname=projet",$username,$password);
      $requete="SELECT count(*) from membre where AdrMail='".$_POST['email']."' and MotDP='".$_POST['password']."';";
      $sql="SELECT * from membre where AdrMail='".$_POST['email']."' and MotDP='".$_POST['password']."';";
      $result=$dbh->query($requete);
      if ($result->fetch()[0]>0) {

        foreach($dbh->query($sql) as $row){
          $_SESSION['idMembre']=$row['idMembre'];
          $_SESSION['AdrMail']=$row['AdrMail'];
          $_SESSION['Nom']=$row['Nom'];
          $_SESSION['Prenom']=$row['Prenom'];
          $_SESSION['Administrateur']=$row['Administrateur'];
          $_SESSION['Avis']=$row['Avis'];
          $_SESSION['NbAvis']=$row['NbAvis'];
        }
        //Vehicule
        $sql="SELECT Immatriculation,nbPlace from vehicule where IdMembre='".$_SESSION['idMembre']."';";
        foreach ($dbh->query($sql) as $row) {
            array_push($_SESSION['Immatriculation'], $row['Immatriculation']);
            array_push($_SESSION['nbPlace'], $row['nbPlace']);
        }
        $url="indexe1.php";
        Redirect($url);
      }
      else{
        echo "<script type= 'text/javascript'>alert('Connexion echoué.');</script>";
      }

      $dbh = null;
    }
    catch(PDOException $e)
    {
      echo $e->getMessage();
    }

  }
  ?>
</body>
</html>
