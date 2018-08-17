<?php 
require_once dirname(__FILE__).'/gapi/src/google/autoload.php';  
 
define('CLIENT_ID', '367254810962-4forjpmel9119eu6od4n0mkv736o910l.apps.googleusercontent.com'); 
define('CLIENT_SECRET', 'siZSx1Sgx9o6Ei_83qd9qmRI'); 
define('CLIENT_REDIRECT_URL', 'http://mycity.com/import-complete.php');

$login_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' . 
urlencode('https://www.googleapis.com/auth/contacts.readonly') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online';


  

?>

<a href="<?= $login_url ?>">Login with Google</a>