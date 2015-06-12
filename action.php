<?php

include('./httpful.phar');

$authorization_url='https://fcp.integ01.dev-franceconnect.fr/api/v1/authorize';
$token_url='https://fcp.integ01.dev-franceconnect.fr/api/v1/token';
$userinfo_url='https://fcp.integ01.dev-franceconnect.fr/api/v1/userinfo';
$logout_url='https://fcp.integ01.dev-franceconnect.fr/api/v1/logout';
$client_id='239403378b6864968661ce40e13b0b53';
$client_secret='9df1c84e42b4cef083a8780c68fecb34';
$callback_url='https://127.0.0.1/oidc_callback';

//$state_aleatoire='314159';
//$nonce_aleatoire='314159';
$state_aleatoire=openssl_random_pseudo_bytes(10);
$nonce_aleatoire=openssl_random_pseudo_bytes(10);

$data_url='https://datafranceconnect.opendatasoft.com/api/records/1.0/search?dataset=ban-departement-75&pretty_print=true';

// login
if (isset($_GET['login'])) {

$url=$authorization_url.'?response_type=code&client_id='.$client_id.'&redirect_uri='.$callback_url.'&scope=openid%20profile&state='.$state_aleatoire.'&nonce='.$nonce_aleatoire;

// Creation du cookie propre au fournisseur de service
setcookie('cookie_session', openssl_random_pseudo_bytes(10), 0, '/', '', True, True);

// Redirection vers l'authent france connect
header('Location: '.$url);
exit();
}

//callback
if (isset($_GET['code']) and isset($_GET['state'])) {

// Recuperation des parametres passes par France Connect dans le callback
$req_code=$_GET['code'];
$req_state=$_GET['state'];

// Appel endpoint token
$json='{"grant_type":"authorization_code","redirect_uri":"'.$callback_url.'","client_id":"'.$client_id.'","client_secret":"'.$client_secret.'","code":"'.$req_code.'"}';

$reponse_json = \Httpful\Request::post($token_url)->sendsJson()->body($json)->send();
$parsed_json = json_decode($reponse_json);
$access_token = $parsed_json->{"access_token"};
$token_type = $parsed_json->{"token_type"};
$expires_in = $parsed_json->{"expires_in"};
$id_token = $parsed_json->{"id_token"};

// Appel endpoint userinfo
$reponse_json = \Httpful\Request::get($userinfo_url.'?schema=openid')->addHeader('Authorization','Bearer '.$access_token)->send();

$parsed_json = json_decode($reponse_json);
$anniv = $parsed_json->{"birthdate"};
$prenom = $parsed_json->{"given_name"};
$nom = $parsed_json->{"family_name"};

echo '<!DOCTYPE html><html lang="fr"><head></head><body>';
echo '<div>';
echo 'Identite de l\'utilisateur connecte: '.$prenom.' '.strtoupper($nom).' ne le '.$anniv;
echo '</div>';
echo '<br>';
echo '<div><a href="/">Retour</a></div>';
echo '</body></html>';

//Appel endpoint fournisseur de donnees
$reponse_json = \Httpful\Request::get($data_url)->addHeader('Authorization','Bearer '.$access_token)->send();
echo $reponse_json; 

exit();
}

//logout
if (isset($_GET['logout'])) {

// Suppression du cookie propre au fournisseur de service
setcookie('cookie_session', '', time()-3600);

$url=$logout_url;
header('Location: '.$url);
exit();
}

?>
