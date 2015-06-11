<!DOCTYPE html>

<html lang="fr">

<head>
<title>Fournisseur de service</title>
</head>

<body>

<?php
//var_dump($_SERVER);
//var_dump($_REQUEST);

echo '<div>';

if (isset($_COOKIE['cookie_session'])) {

echo '<h1>Session active.</h1>';

} else {

echo '<h1>Pas de session en cours.</h1>';
echo '</div>';

echo '<br>';

echo '<div>';
echo '<a href="/action.php?login">Connexion via France Connect</a>';
echo '</div>';

}

?>

<br>

<div>
<a href="/action.php?logout">Deconnexion</a>
</div>

</body>

</html>

<?php exit(); ?>
