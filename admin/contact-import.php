<?php
if(!isset($_SESSION))session_start();

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


$client->addScope('profile');
$client->addScope('https://www.googleapis.com/auth/contacts.readonly'); 


$googleImportUrl = $client -> createAuthUrl();
 




function curl($url, $post = "") 
{
	$curl = curl_init();
	$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
	curl_setopt($curl, CURLOPT_URL, $url);
	//The URL to fetch. This can also be set when initializing a session with curl_init().
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	//TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	//The number of seconds to wait while trying to connect.
	if ($post != "") {
		curl_setopt($curl, CURLOPT_POST, 5);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
	//The contents of the "User-Agent: " header to be used in a HTTP request.
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	//To follow any "Location: " header that the server sends as part of the HTTP header.
	curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
	//To automatically set the Referer: field in requests where it follows a Location: redirect.
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	//The maximum number of seconds to allow cURL functions to execute.
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	//To stop cURL from verifying the peer's certificate.
	$contents = curl_exec($curl);
	curl_close($curl);
	return $contents;
}

//google response with contact. We set a session and redirect back
if (isset($_GET['code'])) {
	$auth_code = $_GET["code"];
	$_SESSION['google_code'] = $auth_code;	
}



	if(isset($_SESSION['google_code'])) 
	{
		$auth_code = $_SESSION['google_code'];
		$max_results = 200;
    	$fields=array(
        'code'=>  urlencode($auth_code),
        'client_id'=>  urlencode($google_client_id),
        'client_secret'=>  urlencode($google_client_secret),
        'redirect_uri'=>  urlencode($google_redirect_uri),
        'grant_type'=>  urlencode('authorization_code')
    );
    $post = ''; 
    foreach($fields as $key=>$value)
    {
        $post .= $key.'='.$value.'&';
    }

    $post = rtrim($post,'&');
    $result = curl('https://accounts.google.com/o/oauth2/token',$post);
    $response =  json_decode($result);
    $accesstoken = $response->access_token;
    $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&alt=json&v=3.0&oauth_token='.$accesstoken;
    $xmlresponse =  curl($url);
    $contacts = json_decode($xmlresponse,true);
	
	$return = array();
	if (!empty($contacts['feed']['entry'])) {
		foreach($contacts['feed']['entry'] as $contact) {
           //retrieve Name and email address  
			$return[] = array (
				'name'=> $contact['title']['$t'],
				'email' => $contact['$email'][0]['address'],
			);
		}				
	}
	
	$google_contacts = $return;
	
	unset($_SESSION['google_code']);
	
}
 
?>


<a href="<?php echo $googleImportUrl; ?>"> Import google contacts </a>

 