<?php
session_start(); 
 
//include google api library
require_once 'gcapi/vendor/autoload.php';  

$google_client_id = '367254810962-4forjpmel9119eu6od4n0mkv736o910l.apps.googleusercontent.com';
$google_client_secret = 'siZSx1Sgx9o6Ei_83qd9qmRI';
$google_redirect_uri = 'http://mycity.com/import-complete.php';

//setup new google client
$client = new Google_Client();
$client -> setApplicationName('MyCity Referrer');
$client -> setClientid($google_client_id);
$client -> setClientSecret($google_client_secret);
$client -> setRedirectUri($google_redirect_uri);

 
$client->addScope('https://www.googleapis.com/auth/contacts'); 
$client->addScope('https://www.googleapis.com/auth/contacts.readonly'); 

 

if (isset($_GET['oauth'])) 
{ 
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} 
else if (isset($_GET['code'])) 
{
   
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/import-complete.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL ));  

} else  if (isset($_SESSION['access_token']) && $_SESSION['access_token']) 
{
  // You have an access token; use it to call the People API
  $client->setAccessToken($_SESSION['access_token']);
  $people_service = new Google_Service_People($client);
    
   
   $results = $people_service->people_connections->listPeopleConnections('people/me', 
    array('pageSize' =>'10','personFields' => 'emailAddresses') );

       

}
else
{
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/?oauth';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}



?>

<div id="fb-root"></div>

<section id="contact">

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 50px">

                <h4>Contact Import Complete</h4>
 

            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">

            </div>

        </div>

    </div>

</section>

<?php include("footer.php") ?>

