<?php
if (!isset($_SESSION)) session_start();
header("Access-Control-Allow-Origin: *");
date_default_timezone_set('America/Los_Angeles');
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

//include_once dirname(__FILE__).DIRECTORY_SEPARATOR . 'mailer' .DIRECTORY_SEPARATOR   . "PHPMailerAutoload.php";
require(dirname(__FILE__) . "/vendor/PHPMailer/src/PHPMailer.php");
require(dirname(__FILE__) . "/vendor/PHPMailer/src/SMTP.php");
require(dirname(__FILE__) . "/vendor/PHPMailer/src/Exception.php");


if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test") {
    $config = [
        'settings' => [
            'displayErrorDetails' => true,
            'dbdetails' =>
                [
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => '',
                    'db' => 'mycity29_maindb'
                ],
        ],
    ];
} else {
    $config = [
        'settings' =>
            [
                'displayErrorDetails' => true,
                'dbdetails' => [
                    'host' => 'localhost',
                    'user' => 'mycity29_root',
                    'password' => 'zBi6h49~',
                    'db' => 'mycity29_maindb'
                ],
            ],
    ];
}

$app = new \Slim\App($config);

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

function getPDO($app)
{
    $settings = $app->get('settings')['dbdetails'];
    $pdo = new PDO("mysql:host=" . $settings['host'] . ";dbname=" . $settings['db'] . "", $settings['user'],
        $settings['password']);
    $pdo->exec("set names utf8");

    return $pdo;
}

function memberrating($a, $b)
{
    if ($a['rating'] == $b['rating']) return 0;
    return ($a['rating'] > $b['rating']) ? -1 : 1;
}

function memberrank($a, $b)
{
    if ($a['rank'] == $b['rank']) return 0;
    return ($a['rank'] > $b['rank']) ? -1 : 1;
}


function findneighbours($appobj, $zip, $radius)
{
    try {
        $pdo = getPDO($appobj);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_city_geolocation where zip='$zip'   ";
        $zipcodeList = array();
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $latlong = $rst->fetchAll(PDO::FETCH_ASSOC)[0];

            $lat = $latlong['latitude'];
            $lon = $latlong['longitude'];

            $sql = 'select distinct(zip) from mc_city_geolocation  ' .
                ' where (3958*3.1415926*sqrt((latitude-' . $lat . ')*(longitude-' . $lat . ') + ' .
                'cos(latitude/57.29578)*cos(' . $lat . '/57.29578)*(longitude-' . $lon . ')*(longitude-' . $lon . '))/180) <= ' . $radius . ';';
            $result = $pdo->query($sql);

            $rszip = $result->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rszip as $row) {
                array_push($zipcodeList, $row['zip']);
            }
        }
    } catch (PDOException $e) {

    }
    return $zipcodeList;
}

$app->post('/member/updatestatus/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $idcsv = $allPostPutVars['ids'];
    $role = $allPostPutVars['role'];

    if ($role == 'admin') {
        if ($idcsv != '') {
            $ids = explode(',', $idcsv);
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($ids as $id) {
                $sql_query = "select  user_status  from mc_user where id = '$id'";

                $rst = $pdo->query($sql_query);
                if ($rst->rowCount() > 0) {
                    $user_status = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['user_status'];
                    $sts = $user_status == '0' ? 1 : 0;
                    $pdo->query("update mc_user set  user_status  = '$sts' WHERE  id  = '$id'");

                }
            }

        }
    }
});

//downgrade or upgrade status
$app->post('/member/statusupdate/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $idcsv = $allPostPutVars['ids'];
    $role = $allPostPutVars['role'];
    $state = $allPostPutVars['state'];
    if ($role == 'admin') {
        if ($idcsv != '') {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("update mc_user set user_status = '$state' WHERE  id  in ( $idcsv  )");
        }
    }
});

$app->get('/nearbyzipcodes/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $city = "91423";
    $neighbours = findneighbours($this, $city, '30');

    $response->getBody()->write(json_encode($neighbours));
    return $response;
});

//user authentication code
$app->post('/login/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $email = $allPostPutVars['email'];
    $password = md5($allPostPutVars['password']);
    $rememberme = $allPostPutVars['rememberme'];
    //$email =  'referrals@mycity.com';
    //$password =  '52cac95b81d273d0c1433481c498373b';


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  mc_user where   user_email='$email'  and user_pass = '$password'  ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $sp = $rst->fetchAll(PDO::FETCH_OBJ);
            $token = $sp[0]->id . strtotime(date('Y-m-d H:i:s'));
            if ($rememberme == 1) {
                $retoken = md5($sp[0]->id . $_SERVER['REMOTE_ADDR'] . date('d-m-Y H:i:s'));
                $profile = array('id' => $sp[0]->id, 'role' => $sp[0]->user_role, 'package' => $sp[0]->user_pkg,
                    'phone' => $sp[0]->user_phone, 'name' => $sp[0]->username, 'email' => $sp[0]->user_email, 'image' => $sp[0]->image,
                    'status' => $sp[0]->user_status, 'profile' => $sp[0]->publicprofile, 'visibility' => $sp[0]->profileisvisible,
                    'signupmode' => $sp[0]->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'), 'retoken' => $retoken);
                $pdo->query("insert into mc_login_log (userid,logintime, token, remembertoken ) values ('" . $sp[0]->id . "', NOW(), '" . md5($token) . "', '$retoken' )");
            } else {
                $profile = array('id' => $sp[0]->id, 'role' => $sp[0]->user_role, 'package' => $sp[0]->user_pkg,
                    'phone' => $sp[0]->user_phone, 'name' => $sp[0]->username, 'email' => $sp[0]->user_email, 'image' => $sp[0]->image,
                    'status' => $sp[0]->user_status, 'profile' => $sp[0]->publicprofile, 'visibility' => $sp[0]->profileisvisible,
                    'signupmode' => $sp[0]->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'), 'retoken' => '0');
                $pdo->query("insert into mc_login_log (userid,logintime, token  ) values ('" . $sp[0]->id . "', NOW(), '" . md5($token) . "'  )");
            }

            $profile['login_log_id'] = $pdo->lastInsertId();

            $usergrouprs = $pdo->query(" select * from user_details where user_id = '" . $sp[0]->id . "' ");

            if ($usergrouprs->rowCount() > 0) {
                $gcrowra = $usergrouprs->fetchAll(PDO::FETCH_ASSOC);
                if ($gcrowra[0]['groups'] != '') {
                    $profile['grps'] = $gcrowra[0]['groups'];
                    $profile['mzip'] = $gcrowra[0]['zip'];
                    $grouparr = explode(',', $gcrowra[0]['groups']);
                    $grouparr = array_filter($grouparr);
                    sort($grouparr);
                    $linkgroups = (array_unique($grouparr));
                    $groupids = implode(', ', $linkgroups);

                    $grounamesrs = $pdo->query("select group_concat( grp_name) as grp_names from groups where id in 
						(" . $groupids . " ) ");
                    $groupnames = $grounamesrs->fetchAll(PDO::FETCH_ASSOC)[0]['grp_names'];
                } else {
                    $profile['groupnames'] = 'Not Specified';
                }
                //$profile['groupnames'] = $groupnames ;
            } else {
                $profile['groupnames'] = 'Not Specified';
            }

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: noreply@mycity.com';
            $msg1 = "<!DOCTYPE html><html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
					<title>Thanks for registration</title>
					<style type='text/css'>
						body {margin: 10px 0; padding: 0 10px; background: #f3f3f3; font-size: 14px;}
							table {border-collapse: collapse;}
							td{font-family: arial, sans-serif; color: #333333;}
							@media only screen and (max-width: 480px) {
								body,table,td,p,a,li,blockquote {
								-webkit-text-size-adjust:none !important;
							}
							table {width: 100% !important;}
							.responsive-image img 
							{
								height: auto !important;
								max-width: 100% !important;
								width: 100% !important;
							}
						}
					</style>
				</head>
				<body>
						<table border='0' cellpadding='0' cellspacing='0' width='100%'>
							<tr>
								<td>
									<table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF' style='color: #333333'>
										<tr>
											<td bgcolor='#333333' style='font-size: 30px; color:#fff; padding: 0 10px;border-bottom:10px solid #78b0d1;' height='100' align='center'>
												<a href='http://www.mycity.com' target='_blank'>
													<img src='http://www.mycity.com/images/logo.png' width='100' alt='www.mycity.com' />
												</a>
											</td>
										</tr>
										<tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>User Login</span></td></tr>
										<tr>
											<td style='padding: 10px 10px 30px 10px;'>
												<div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
												<br />
												<div>A user is logged-in on www.mycity.com</div>
												<br />
												<div>Username: " . $sp[0]->username . "</div>
												<br />
												<div>Email: " . $sp[0]->user_email . "</div>
												<br />
											</td>
										</tr>
										<tr>
										<td style='padding: 0 10px 10px 10px;'>If you are not concerned person, ignore this email please.</td>
										</tr>
										<tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
										<tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
										<tr>
											<td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
												<table border='0' cellpadding='0' cellspacing='0' width='100%'>
													<tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
													<tr>
														<td style='padding: 0 10px; color: #cccccc;' align='center'>
															Copyright &copy; " . date('Y') . " | All Rights Reserved.
														</td>
													</tr>
													<tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
				</body>
				</html>";
            sendemail('bob@mycity.com', 'User signup on MyCity', $msg1, $msg1);
        } else {
            $profile = array('id' => '0');
        }
    } catch (PDOException $e) {
        $profile = array('id' => '0', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($profile));
    return $response;
});


//user switching authentication code
$app->post('/user/switch/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  mc_user where id='$id'";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $sp = $rst->fetchAll(PDO::FETCH_OBJ);

            if ($id == 1) {
                $token = $sp[0]->id . strtotime(date('Y-m-d H:i:s'));
                $retoken = md5($sp[0]->id . $_SERVER['REMOTE_ADDR'] . date('d-m-Y H:i:s'));
                $profile = array('id' => $sp[0]->id, 'role' => $sp[0]->user_role, 'package' => $sp[0]->user_pkg,
                    'phone' => $sp[0]->user_phone, 'name' => $sp[0]->username, 'email' => $sp[0]->user_email, 'image' => $sp[0]->image,
                    'status' => $sp[0]->user_status, 'profile' => $sp[0]->publicprofile, 'visibility' => $sp[0]->profileisvisible,
                    'signupmode' => $sp[0]->signup_type, 'token' => $_SESSION['logintoken'],
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'));

                $userloginlog = $pdo->query("select * from  mc_login_log where  token ='" . $_SESSION['logintoken'] . "'");
                if ($userloginlog->rowCount() > 0) {
                    $loginlogrow = $userloginlog->fetchAll(PDO::FETCH_ASSOC);
                    $profile['login_log_id'] = $loginlogrow[0]["id"];
                    $profile['retoken'] = $loginlogrow[0]["remembertoken"];
                }

                $_SESSION['switchtoken'] = '0'; //switch to admin
            } else {
                $token = $sp[0]->id . strtotime(date('Y-m-d H:i:s'));
                $retoken = md5($sp[0]->id . $_SERVER['REMOTE_ADDR'] . date('d-m-Y H:i:s'));
                $profile = array('id' => $sp[0]->id, 'role' => $sp[0]->user_role, 'package' => $sp[0]->user_pkg,
                    'phone' => $sp[0]->user_phone, 'name' => $sp[0]->username, 'email' => $sp[0]->user_email, 'image' => $sp[0]->image,
                    'status' => '1', 'profile' => $sp[0]->publicprofile, 'visibility' => $sp[0]->profileisvisible,
                    'signupmode' => $sp[0]->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'), 'retoken' => $retoken);

                $pdo->query("insert into mc_login_log (userid,logintime, token  ) values ('" . $sp[0]->id . "', NOW(), '" . md5($token) . "'  )");
                $profile['login_log_id'] = $pdo->lastInsertId();
                $_SESSION['switchtoken'] = '1';  //switch to normal user
            }

            $usergrouprs = $pdo->query(" select groups from user_details where user_id = '" . $sp[0]->id . "' ");
            if ($usergrouprs->rowCount() > 0) {
                $gcrowra = $usergrouprs->fetchAll(PDO::FETCH_ASSOC);
                if ($gcrowra[0]['groups'] != '') {
                    $grouparr = explode(',', $gcrowra[0]['groups']);
                    $grouparr = array_filter($grouparr);
                    sort($grouparr);
                    $linkgroups = (array_unique($grouparr));
                    $groupids = implode(', ', $linkgroups);
                    $grounamesrs = $pdo->query("select group_concat( grp_name) as grp_names from groups where id in 
						(" . $groupids . " ) ");
                    $groupnames = $grounamesrs->fetchAll(PDO::FETCH_ASSOC)[0]['grp_names'];
                } else {
                    $profile['groupnames'] = 'Not Specified';
                }
            } else {
                $profile['groupnames'] = 'Not Specified';
            }
        } else {
            $profile = array('id' => '0');
        }
    } catch (PDOException $e) {
        $profile = array('id' => '0', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($profile));
    return $response;
});


$app->post('/remembermecheck/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $remembertoken = $allPostPutVars['remembertoken'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_login_log where remembertoken = '" . $remembertoken . "'";
        $rs = $pdo->query($sql_query);
        if ($rs->rowCount() > 0) {
            $rsp = $rs->fetchAll(PDO::FETCH_ASSOC);

            $sql_query = "select * from  mc_user where  id = '" . $rsp[0]['userid'] . "'  ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $sp = $rst->fetchAll(PDO::FETCH_OBJ);
                $token = $sp[0]->id . strtotime(date('Y-m-d H:i:s'));
                $profile = array('id' => $sp[0]->id, 'role' => $sp[0]->user_role, 'package' => $sp[0]->user_pkg,
                    'phone' => $sp[0]->user_phone, 'name' => $sp[0]->username, 'email' => $sp[0]->user_email, 'image' => $sp[0]->image,
                    'status' => $sp[0]->user_status, 'profile' => $sp[0]->publicprofile, 'visibility' => $sp[0]->profileisvisible,
                    'signupmode' => $sp[0]->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'));


                $usergrouprs = $pdo->query(" select groups from user_details where user_id = '" . $sp[0]->id . "' ");

                if ($usergrouprs->rowCount() > 0) {
                    $gcrowra = $usergrouprs->fetchAll(PDO::FETCH_ASSOC);
                    if ($gcrowra[0]['groups'] != '') {
                        $grouparr = explode(',', $gcrowra[0]['groups']);
                        $grouparr = array_filter($grouparr);
                        sort($grouparr);
                        $linkgroups = (array_unique($grouparr));
                        $groupids = implode(', ', $linkgroups);

                        $grounamesrs = $pdo->query("select group_concat( grp_name) as grp_names from groups where id in 
							(" . $groupids . " ) ");
                        $groupnames = $grounamesrs->fetchAll(PDO::FETCH_ASSOC)[0]['grp_names'];
                    } else {
                        $profile['groupnames'] = 'Not Specified';
                    }
                    //$profile['groupnames'] = $groupnames ;
                } else {
                    $profile['groupnames'] = 'Not Specified';
                }
            } else {
                $profile = array('id' => '0');
            }
        }
    } catch (PDOException $e) {
        $profile = array('id' => '0', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($profile));
    return $response;
});


//read a single/all vocations
$app->post('/vocations/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  vocations order by  voc_name ";
        else
            $sql_query = "select * from  vocations where id='$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No vocation found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all vocations
$app->post('/lifestyle/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  lifestyles order by  ls_name ";
        else
            $sql_query = "select * from  lifestyles where id='$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = array('error' => '0', 'errmsg' => 'Lifestyle information retrieved!', 'results' => $rst->fetchAll(PDO::FETCH_ASSOC));
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No lifestyle information found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Code to add/update trigger
$app->post('/lifestyles/add/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $role = $allPostPutVars['role'];
    $lifestyle = $allPostPutVars['lifestyle'];
    $currlifestyleid = $allPostPutVars['currlifestyle'];


    if ($role != 'admin') :
        $log = array('error' => '100', 'errmsg' => "Not enough privilege to add tag!");
    else:
        try {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($currlifestyleid) && $currlifestyleid != 0) {
                $pdo->query("update  lifestyles  set  ls_name  = '$lifestyle' where  id  = '$currlifestyleid'");
                $log = array('error' => '0', 'errmsg' => 'Lifestyle updated successfully!');
            } else {
                $check = $pdo->query("select id from lifestyles where ls_name  = '$lifestyle'");
                if ($check->rowCount() > 0) {
                    $log = array('error' => '10', 'errmsg' => 'Lifestyle already exists!');
                } else {
                    $pdo->query("insert into lifestyles  ( ls_name ) values  ('$lifestyle')");
                    $log = array('error' => '0', 'errmsg' => 'Lifestyle saved successfully!');
                }
            }

        } catch (PDOException $e) {
            $log = array('error' => '1', 'errmsg' => 'Something went wrong. Please retry!');
        }
    endif;

    $response->getBody()->write(json_encode($log));
    return $response;

});

//read a single/all groups
$app->post('/groups/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0) {
            $sql_query = "select * from  groups order by  grp_name ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $pdoresult = $rst->fetchAll(PDO::FETCH_ASSOC);
                for ($i = 0; $i < $rst->rowCount(); $i++) {
                    $jsonresult[] = ["id" => $pdoresult[$i]['id'], "grp_name" => $pdoresult[$i]['grp_name']];
                }
            }
        } else {
            $sql_query = "select * from user_details where user_id = '$id'";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $pdoresult = $rst->fetchAll(PDO::FETCH_ASSOC);
                $groups = explode(",", $pdoresult[0]['groups']);

                $where_group = "(";
                for ($i = 0; $i < sizeof($groups); $i++) {
                    $where_group .= $groups[$i];
                    if ($i < sizeof($groups) - 1) {
                        $where_group .= ",";
                    }
                }
                $where_group .= ")";
                $rst = $pdo->query("select * from groups where id in " . $where_group . "  order by grp_name");

                if ($rst->rowCount() > 0) {
                    $pdoresult = $rst->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < $rst->rowCount(); $i++) {
                        $jsonresult[] = ["id" => $pdoresult[$i]['id'], "grp_name" => $pdoresult[$i]['grp_name']];
                    }
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'No matching group!');
                }
            } else
                $jsonresult = array('error' => '10', 'errmsg' => 'No matching group!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read a single/all city
$app->post('/cities/', function (Request $request, Response $response) {
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select distinct client_location from user_people order by client_location ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $rst->rowCount(); $i++) {
                $jsonresult[] = ["name" => $resultset[$i]['client_location']];
            }
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No city information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read questions
$app->post('/questions/', function (Request $request, Response $response) {
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select   id as a , question  as b ,  question_type  as c from questions";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);
            $jsonresult = array('error' => '0', 'errmsg' => 'Questions are retrieved!', 'results' => $resultset);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No question found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read questions
$app->post('/savequestions/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $_user_role = $allPostPutVars['role'];
    $allQues = $allPostPutVars['allQues']; #array

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_user_role == 'admin') {
            foreach ($allQues as $allQue) {
                $ques_id = $allQue['data_id'];
                $value = $allQue['data_value'];
                $q_type = $allQue['q_type'];
                if ($value == '') continue;
                if ($ques_id > 0) {
                    $stmt = $pdo->prepare("update questions set question = ? , question_type=? WHERE id = ? ");
                    $stmt->execute(array($value, $q_type, $ques_id));
                } elseif ($ques_id == 0) {
                    $stmt = $pdo->prepare("INSERT INTO questions (question, question_type) VALUES (?,?)");
                    $stmt->execute(array($value, $q_type));
                }
            }
            $jsonresult = array('error' => '0', 'errmsg' => "All questions updated successfully!");
        } else
            $jsonresult = array('error' => '10', 'errmsg' => sizeof($allQues) . 'You don\'t have priviledge to update questions!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => sizeof($allQues) . $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//global function to delete any record
$app->post('/delete/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $_user_role = $allPostPutVars['role'];
    $id = $allPostPutVars['id'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_user_role == 'admin') {
            if ($id != 0) {
                $pdo->query("delete from questions where id = '$id'");
                $jsonresult = array('error' => '0', 'errmsg' => "Selected question deleted successfully!");
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "No question selected for deletion!");
            }
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => sizeof($allQues) . 'You don\'t have priviledge to update questions!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => sizeof($allQues) . $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all city
$app->post('/tags/', function (Request $request, Response $response) {
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_tags order by tagname  ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $rst->rowCount(); $i++) {

                $jsonresult[] = ["id" => $resultset[$i]['id'], "tagname" => $resultset[$i]['tagname']];
            }
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No city information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read a single/all help
$app->post('/helps/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  helps   ";
        else
            $sql_query = "select * from  helps where id='$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);

        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No help information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all helpbuttons
$app->post('/helpbuttons/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  helpsbuttons  order by id ";
        else
            $sql_query = "select * from  helpsbuttons where id='$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No help information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read a single/all testimonials
$app->post('/testimonials/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  mc_testimonial  ";
        else
            $sql_query = "select * from  mc_testimonial where id='$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No testimonial found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//update testimonial sort order
$app->post('/testimonials/updateposition/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $pos = $allPostPutVars['pos'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "update mc_testimonial set printorder=? where id=?";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($pos, $id));
        $jsonresult = array('error' => '0', 'errmsg' => 'Testimonial updated!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Testimonial could not be updated!');
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all testimonials
$app->post('/emailtemplates/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "select * from  mc_mail_templates where status='0' order by templatename ";
        else
            $sql_query = "select * from  mc_mail_templates where id='$id'";

        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $results = $rst->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $rst->rowCount(); $i++) {
                if ($results[$i]['mailtype'] == 0) {
                    $mailtype = 'Trigger Mail';
                } else if ($results[$i]['mailtype'] == 1) {
                    $mailtype = 'Introduction Mail';
                } else if ($results[$i]['mailtype'] == 2) {
                    $mailtype = 'LinkedIn Invitation';
                } else if ($results[$i]['mailtype'] == 3) {
                    $mailtype = 'Testimonial Videos';
                } else if ($results[$i]['mailtype'] == 5) {
                    $mailtype = 'Unfinished Signup';
                }

                $jsonresult[] = ["id" => $results[$i]['id'], "mailtype" => $mailtype,
                    "template" => $results[$i]['templatename'],
                    "subject" => $results[$i]['subject'],
                    "mailbody" => htmlspecialchars($results[$i]['mailbody'])];
            }
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No testimonial found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all testimonials
$app->post('/getemailtemplatebytype/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $mailtype = $allPostPutVars['mailtype'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  mc_mail_templates where mailtype='$mailtype'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $results = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $jsonresult[] = ["id" => $results['id'],
                "template" => $results['templatename'],
                "subject" => $results['subject'],
                "mailbody" => htmlspecialchars($results['mailbody'])];
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No testimonial found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single/all triggers
$app->post('/triggers/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from my_triggers where user_id='$id' order by trigger_question ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No trigger information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//read a single/all city
$app->get('/packages/', function (Request $request, Response $response) {
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from packages ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No city information found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Code to update profile photo
$app->post('/members/updatephoto/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];

    if (isset($_SESSION['tempmemprofile'])) {
        $path = $_SESSION['tempmemprofile'];
        try {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql_query = "update mc_user set image=? where id = ? ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($path, $id));
            $log = array('error' => '0', 'errmsg' => 'Member profile photo updated!');
        } catch (PDOException $e) {
            $log = array('error' => '1', 'errmsg' => 'Member profile photo could not be updated!');
        }
    } else {
        $log = array('error' => '1', 'errmsg' => 'Member profile photo could not be updated!');
    }


    $response->getBody()->write(json_encode($log));
    return $response;
});

//read a single post
$app->post('/member/getprofile/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $memberid = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  mc_user where id= '$memberid'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single post
$app->get('/member/profile/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  mc_user where id= '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read a single post
$app->get('/member/details/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from  user_details where user_id= '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read a single post
$app->post('/member/completeprofile/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['userid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select u.*,  user_id ,  city , zip ,  current_company , 
		 linkedin_profile , country , groups , target_clients , target_referral_partners , 
		 vocations ,  about_your_self ,  upd_public_private ,  upd_reminder_email ,   lcid 
		 from mc_user as u inner join user_details as d on u.id=d.user_id where u.id= '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = array('error' => '0', 'errmsg' => 'Member found!', 'results' => $rst->fetchAll(PDO::FETCH_ASSOC));
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all triggers
$app->post('/member/grouprequest/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $state = $allPostPutVars['state'];

    $goto = $allPostPutVars['goto'];
    $pagesize = $allPostPutVars['pagesize'];

    if ($pagesize <= 0) $pagesize = 10;
    $start = ($goto - 1) * $pagesize;


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select a.id as a, user_id  as b, current_company  as c, linkedin_profile  as d, city  as e, zip  as f, 
		country  as g, groups  as g, target_clients  as i, target_referral_partners  as j, vocations  as k, about_your_self  as l, 
		upd_public_private  as m, upd_reminder_email  as n, a.createdOn  as o, lcid  as p ,
		b.id as q , b.user_email as r, b.user_pass  as s, b.username as t, b.user_role as u, b.user_pkg  as v, b.user_phone as w, 
		b.image as x, b.createdOn as y, b.user_status as z, b.group_status as aa, b.resPWToken as ab, b.resPWExp as ac, 
		b.publicprofile as ad, b.profileisvisible as ae, b.signup_type as af from user_details as a inner join mc_user as b on b.id = a.user_id where b.group_status  = '$state' limit $start, $pagesize";


        $sql_query_count = "select count(*) as reccnt from user_details as a inner join mc_user as b on b.id = a.user_id where b.group_status  = '$state' ";


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < sizeof($jsonresult); $i++) {
                $groups = $jsonresult[$i]['g'];
                if (isset($groups) && $groups != '') {
                    $rs_groupnames = $pdo->query("select GROUP_CONCAT(grp_name) as grpnames from  groups where id in (" . $groups . " )");

                    if ($rs_groupnames->rowCount() > 0) {
                        $groupnamesrow = $rs_groupnames->fetchAll(PDO::FETCH_ASSOC);
                        $jsonresult[$i]['g'] = $groupnamesrow[0]['grpnames'];
                    }
                }
            }
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'New members requesting to join in a group found!', 'results' => $jsonresult);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No new member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get all members
$app->post('/member/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    //$memberid = $request->getAttribute('memberid');
    //$groupid = $request->getAttribute('groupid');
    $userid = $allPostPutVars['userid'];
    $groupid = $allPostPutVars['groupid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id 
			where (FIND_IN_SET('$groupid', groups)) AND  b.id != '1' and b.id!='$userid' and user_pkg='Gold'";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Search and get member profile
$app->post('/member/getbyid/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['profileid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select *,  0 AS group_names from user_details as a inner join mc_user as b on b.id = a.user_id 
		where user_id='$id' ";


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);

            if ($jsonresult[0]['groups'] != '') {
                $sql_query_group = "select group_concat(grp_name) as group_names FROM groups where id in  (" . $jsonresult[0]['groups'] . ") ";

                $groupresult = $pdo->query($sql_query_group);
                if ($groupresult->rowCount() > 0)
                    $groupnames = $groupresult->fetchAll(PDO::FETCH_ASSOC)[0]['group_names'];
                $jsonresult[0]['group_names'] = str_replace(",", ", ", $groupnames);
            } else {
                $jsonresult[0]['group_names'] = 'Not specified';
            }
        } else {
            $sql_query = "select  id ,  user_email ,  user_pass ,  username, user_role, user_pkg, user_phone, " .
                " image, createdOn, user_status, group_status, resPWToken, resPWExp, publicprofile, " .
                " profileisvisible, tags, signup_type, busi_name, user_type, busi_location_street, busi_location, " .
                " busi_type, busi_hours, busi_website , " .
                "  0 AS current_company, 0 AS linkedin_profile,0 AS street, 'No Specified' AS city, 0 AS zip, 0 AS country, 0 AS groups, " .
                " 'No Specified' AS target_clients,  'No Specified' AS target_referral_partners, 'No Specified' AS vocations, 0 AS about_your_self, " .
                " 0 AS upd_public_private, 0 AS upd_reminder_email,  0 AS lcid,  0 AS group_names " .
                " from  mc_user  where id='$id' ";

            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
                if ($jsonresult[0]['groups'] != '') {
                    $sql_query_group = "select group_concat(grp_name) as group_names FROM groups where id in  (" . $jsonresult[0]['groups'] . ") ";

                    $groupresult = $pdo->query($sql_query_group);
                    if ($groupresult->rowCount() > 0)
                        $groupnames = $groupresult->fetchAll(PDO::FETCH_ASSOC)[0]['group_names'];
                    $jsonresult[0]['group_names'] = str_replace(",", ", ", $groupnames);
                } else {
                    $jsonresult[0]['group_names'] = 'Not specified';
                }
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Search member by name as key
$app->get('/member/getbyname/{name}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $name = $request->getAttribute('name');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM mc_user where username like '%$name%' and user_role='user'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

//Search member by name as key
$app->get('/member/getpartners/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM user_people where user_id = '$id'";

        //$groups = explode(",", $user['groups']);
        //$query = "SELECT * FROM mc_user a LEFT JOIN user_details b on a.id = b.user_id WHERE a.username LIKE '%" . $nameSrch . "%'";
        //$notAdmin = " AND a.id != 1";
        //$whereGroup = "(FIND_IN_SET('".implode("', b.groups) OR FIND_IN_SET('", $groups)."', b.groups))";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//Search member by name as key
$app->post('/member/getbyvocations/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $vocations = $allPostPutVars['vocations'];
    $role = $allPostPutVars['userrole'];

    $professionlist = explode(",", $vocations);
    $where_group = " ( ";
    for ($i = 0; $i < sizeof($professionlist); $i++) {
        $where_group .= " find_in_set (  '" . $professionlist[$i] . "' , client_profession ) ";
        if ($i < sizeof($professionlist) - 1) {
            $where_group .= " OR ";
        }
    }
    $where_group .= " ) ";

    if ($role == 'admin')
        $sql_query = "SELECT * FROM user_people where $where_group ORDER by client_name";
    else
        $sql_query = "SELECT * FROM user_people where user_id = '$userid' AND $where_group ORDER by client_name";


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query($sql_query);
        $jsonresult = array();

        if ($rst->rowCount() > 0) {
            foreach ($rst as $row) {
                $jsonresult[] = array('id' => $row['id'], 'username' => $row['client_name']);
            }

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');


    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//Search member by name as key
$app->post('/member/getmyknows/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];


    $sql_query = "SELECT * FROM user_people where user_id = '$userid'  ORDER by client_name";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query($sql_query);
        $jsonresult = array();

        if ($rst->rowCount() > 0) {
            foreach ($rst as $row) {
                $jsonresult[] = array('id' => $row['id'], 'username' => $row['client_name']);
            }
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Update phone number of a know
$app->post('/member/know/updatephone/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $phone = $allPostPutVars['phone'];
    $knowid = $allPostPutVars['knowid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "update user_people set client_phone=?, check_fields='100000' where id=?";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($phone, $knowid));

        if ($stmt->rowCount()) {
            $jsonresult = array('error' => '0', 'errmsg' => 'Phone number updated!');
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'Phone number could not be updated!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Update tag  of a know
$app->post('/member/know/updatetags/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $tags = $allPostPutVars['tag'];
    $knowid = $allPostPutVars['knowid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = 'update user_people set tags=? where id=?';
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($tags, $knowid));

        if ($stmt->rowCount()) {
            $jsonresult = array('error' => '0', 'errmsg' => 'Tags updated!');
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'Tags could not be updated!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/know/updatevocation/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $vocs = $allPostPutVars['vocs'];
    $knowid = $allPostPutVars['knowid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = 'update user_people set client_profession=? where id=?';
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($vocs, $knowid));

        if ($stmt->rowCount()) {
            $jsonresult = array('error' => '0', 'errmsg' => 'Vocations updated!');
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'Vocations could not be updated!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/know/updatecityzip/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $city = $allPostPutVars['city'];
    $zip = $allPostPutVars['zip'];
    $knowid = $allPostPutVars['knowid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = 'update user_people set client_zip=?, client_location=? where id=?';
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($zip, $city, $knowid));

        if ($stmt->rowCount()) {
            $jsonresult = array('error' => '0', 'errmsg' => 'City and Zip updated!');
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'City and Zip could not be updated!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->get('/member/getlimit/{id}/', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select ref_limit from  packages where package_title = (select user_pkg from  mc_user where  id = '$id')";
        $rst = $pdo->query($sql_query);
        $jsonresult = array();

        if ($rst->rowCount() > 0) {
            $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);
            $jsonresult["ref_limit"] = $resultset[0]['ref_limit'];
            $sql_query = "select count(*) as knowcount from user_people where user_id = '$id'";
            $rst = $pdo->query($sql_query);
            $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);
            $jsonresult["know_count"] = $resultset[0]['knowcount'];
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//Search member by name as key
$app->post('/member/searchbyname/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $name = $allPostPutVars['name'];
    $useremail = $allPostPutVars['email'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $results = $pdo->query("select * from user_details as a inner join mc_user as b on b.id = a.user_id 
		where username like '%" . $name . "%'  AND  b.id != 1");

        if ($results->rowCount() > 0) {
            $users = array();
            $members = $results->fetchAll(PDO::FETCH_ASSOC);
            foreach ($members as $row) {
                $refcnt = 0;
                $revrefcnt = 0;
                $target_clients = explode(",", $row["target_clients"]);
                $target_referral_partners = explode(",", $row["target_referral_partners"]);
                $user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png";
                $str = "abcdefghijklmnopqrstuvwxyz";
                $rand = substr(str_shuffle($str), 0, 3);

                $partnerinreferrals = $pdo->query(" select * from  user_people  where  client_email='" . $row["user_email"] . "'");
                if ($partnerinreferrals->rowCount() > 0) {
                    $refpartcount = $pdo->query("select count(*) as refcnt  from  referralsuggestions 
						where knowenteredby='$user_id' and emailstatus='1' and 
						knowreferedto IN (SELECT id FROM `user_people` where client_email='" . $row["user_email"] . "' )")->fetchAll(PDO::FETCH_ASSOC);
                    $refcnt = $refpartcount[0]['refcnt'];
                }

                //Counting referrals sent back
                $reversesender = $pdo->query("select id from  mc_user  where user_email='" . $row["user_email"] . "'");

                if ($reversesender->rowCount() > 0) {
                    $reversesenderid = $reversesender->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
                    $revrefpartcount = $pdo->query("select count(*) as refcnt  from  referralsuggestions 
				where knowenteredby='$reversesenderid' and emailstatus='1' and 
				knowreferedto IN (SELECT id FROM `user_people` where client_email='$useremail' )")->fetchAll(PDO::FETCH_ASSOC);
                    $revrefcnt = $revrefpartcount[0]['refcnt'];
                }

                $html .= '<div class="panel panel-default"><div class="panel-body"> <div class="row"> <div class="col-sm-2">' .
                    "<img src='" . $user_picutre . "' alt='" . $row["username"] . "' class='img-rounded' height='120' width='120'>" .
                    '</div><div class="col-sm-4"><p><strong>Name:</strong> ' . $row["username"] . '</p>' .
                    '<p><strong>Email:</strong> ' . $row["user_email"] . '</p><p><strong>Phone:</strong> ' . $row["user_phone"] . '</p>';

                $html .= '</div>
			<div class="col-sm-3">
				<div class="hero-widget well well-sm">
					<div class="icon">
						 <i class="fa fa-user fa-3x green"></i>
					</div>
					<div class="text">
						<var>' . $refcnt . '</var>
						<label class="text-muted">Referrals Sent</label>
					</div>
					<div class="options">
						<button class="btn btn-primary btn-md"><i class="fa fa-search"></i> View Referrals</button>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="hero-widget well well-sm">
					<div class="icon">
						 <i class="fa fa-user fa-3x orange"></i>
					</div>
					<div class="text">
						<var>' . $revrefcnt . '</var>
						<label class="text-muted">Referrals Received</label>
					</div>
					<div class="options">
						<button class="btn btn-primary btn-md"><i class="fa fa-search"></i> View Referrals</button>
					</div>
				</div>
			</div>
	 
			</div></div></div>';
            }
            $jsonresult = array('error' => '0', 'errmsg' => $results->rowCount() . ' member(s) found!', 'results' => $html);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all members
$app->post('/member/autosuggest/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $vocations = $allPostPutVars['vocations'];

    $name = $allPostPutVars['name'];
    $city = $allPostPutVars['city'];

    if ($name !== NULL && $name != '') {
        $name_where = " and username like '%$name%' ";

    }

    if ($city !== NULL && $city != '' && $city != 'null') {

        $city_where = ' and (';
        $citylist = explode(',', $city);

        for ($i = 0; $i < sizeof($citylist); $i++) {
            $city_where .= " city = '" . $citylist[$i] . "' ";
            if ($i < sizeof($citylist) - 1) {
                $city_where .= ' or ';
            }
        }
        $city_where .= ' ) ';

    }

    if ($vocations !== NULL && $vocations != '' && $vocations != 'null') {
        $voc_where = ' and (';
        $vocationlist = explode(',', $vocations);

        for ($i = 0; $i < sizeof($vocationlist); $i++) {
            $voc_where .= " find_in_set ( '" . $vocationlist[$i] . "' , vocations) ";
            if ($i < sizeof($vocationlist) - 1) {
                $voc_where .= ' OR ';
            }
        }
        $voc_where .= ' )';
    }

    $ds = DIRECTORY_SEPARATOR;
    $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select groups from user_details where user_id='$userid' ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $groupslist = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['groups'];
            $groupids = explode(',', $groupslist);
            $jsonresult = array('error' => '10', 'errmsg' => 'Group IDs found!', 'groupids' => $groupslist);

            $where = '';
            for ($i = 0; $i < sizeof($groupids); $i++) {
                $where .= "FIND_IN_SET ( '" . $groupids[$i] . "', groups) ";
                if ($i < sizeof($groupids) - 1) {
                    $where .= ' OR ';
                }
            }
            if ($where != '') {
                $sql_query_count = "select count(*) as reccnt from mc_user as u inner join user_details as d on u.id=d.user_id 
				where u.id !='$userid' and u.id !='1' and (" . $where . ")" . $voc_where . $name_where . $city_where;
                $rst_count = $pdo->query($sql_query_count);
                $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                $pages = ceil($result_count[0]['reccnt'] / 10);

                if ($goto == 0) {
                    $goto = mt_rand(1, $pages);
                    $pagesize = 11;
                } else {
                    $pagesize = 10;
                }
                $start = ($goto - 1) * $pagesize;
                $sql_query = "select u.id as id, user_email ,  username ,  user_phone ,  publicprofile ,  profileisvisible , image,  current_company ,  linkedin_profile ,  city ,  zip ,  country ,  groups ,  target_clients ,  target_referral_partners ,  vocations ,  about_your_self  from mc_user as u inner join user_details as d on u.id=d.user_id 
				where u.id !='$userid' and u.id !='1'  and  (" . $where . ") " . $voc_where . $name_where . $city_where;
                $rst = $pdo->query($sql_query);

                $allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);
                for ($i = 0; $i < $rst->rowCount(); $i++) {
                    $sp = $allmembers[$i][id];
                    $query_connect_check = " select * from mc_member_connections where  status='1' and (  ( firstpartner='$userid' and secondpartner='$sp')  or ( firstpartner='$sp' and secondpartner='$userid') ) ";

                    $rstconcheck = $pdo->query($query_connect_check);
                    if ($rstconcheck->rowCount() > 0) {
                        $allmembers[$i][isconnected] = '1';
                    }

                    if ($allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null') {
                        $user_picture = (file_exists($imagepath . $allmembers[$i]['image']) ? $allmembers[$i]['image'] : "no-photo.png");
                        $allmembers[$i]['image'] = $user_picture;
                    } else {
                        $allmembers[$i]['image'] = "no-photo.png";
                    }
                }

                usort($allmembers, function ($a, $b) {
                    return $b['isconnected'] - $a['isconnected'];
                });
                $final = array();
                for ($i = 0; $i < 10; $i++) {
                    $final[] = $allmembers[$start + $i];
                }

                $jsonresult = array('error' => '0', 'path' => $user_picture, 'pages' => $pages, 'errmsg' => 'Members you may be interested to contact found!', 'results' => $final);
            } else
                $jsonresult = array('error' => '10', 'errmsg' => 'No member suggestion found!');
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member suggestion found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please try again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

//get all recently connected members
$app->post('/member/recentconnections/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $start = ($goto - 1) * 10;


    $ds = DIRECTORY_SEPARATOR;
    $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "(select firstpartner as mid from  mc_member_connections where secondpartner='$userid' and status='1'  order by approvedon desc ) " .
            " union " .
            " ( select secondpartner as mid from  mc_member_connections where firstpartner='$userid'  and status='1' order by approvedon desc )";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $mids = $rst->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($rst->rowCount() / 10);
            if ($goto == 0) {
                $goto = mt_rand(1, $pages);
                $pagesize = 11;
            } else {
                $pagesize = 10;
            }

            $midlist = '';
            foreach ($mids as $item) {
                $midlist .= $item['mid'] . ",";
            }
            $midlist .= "0";

            $start = ($goto - 1) * $pagesize;
            $sql_query = "select u.id as id, user_email ,  username ,  user_phone ,  publicprofile ,  profileisvisible , image,  current_company ,  linkedin_profile ,  city ,  zip ,  country ,  groups ,  target_clients ,  target_referral_partners ,  vocations ,  about_your_self, 0 as isconnected, 0 as rating  from mc_user as u inner join user_details as d on u.id=d.user_id 
			where u.id !='$userid' and u.id !='1'  and u.id in  (" . $midlist . ") order by find_in_set( u.id, '" . $midlist . "' ) ";

            $rst = $pdo->query($sql_query);
            $allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $rst->rowCount(); $i++) {

                $allmembers[$i][isconnected] = '1';

                if ($allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null') {
                    $user_picture = (file_exists($imagepath . $allmembers[$i]['image']) ? $allmembers[$i]['image'] : "no-photo.png");
                    $allmembers[$i]['image'] = $user_picture;
                } else {
                    $allmembers[$i]['image'] = "no-photo.png";
                }
            }

            $final = array();
            for ($i = 0; $i < 10 && $i < $rst->rowCount(); $i++) {
                $final[] = $allmembers[$start + $i];
            }

            $jsonresult = array('error' => '0', 'path' => $user_picture, 'pages' => $pages, 'errmsg' => "Member fetched successfully.", 'results' => $final);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member suggestion found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please try again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


$app->post('/member/saveminimal/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $name = $allPostPutVars['bcname'];
    $email = $allPostPutVars['bcemail'];
    $company = $allPostPutVars['bcorg'];
    $phone = $allPostPutVars['bcphone'];
    $website = $allPostPutVars['bcweb'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "  SELECT * FROM mc_user WHERE user_email='$email'   ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = array('error' => '10', 'errmsg' => 'Member already already registered.');

        } else {
            $insQstmnt = "insert into  mc_user (user_email, username, user_phone , user_pkg, user_role) VALUES ( ?,?,? ,'Basic','user')";
            $stmt = $pdo->prepare($insQstmnt);
            $stmt->execute(array($email, $name, $phone));
            $memid = $pdo->lastInsertId();

            $insdetails = "insert into  user_details ( user_id, current_company, createdOn) VALUES ( ?, ?, NOW() )";
            $stmt = $pdo->prepare($insdetails);
            $stmt->execute(array($memid, $company));

            $jsonresult = array('error' => '0', 'userid' => $memid, 'errmsg' => 'Member registered successfully!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/searchnearest/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $keyword = $allPostPutVars['name'];
    $userid = $allPostPutVars['userid'];
    $city = $allPostPutVars['city'];
    $iszip = $allPostPutVars['iszip'];
    $vocation = $allPostPutVars['vocation'];
    $page = $allPostPutVars['page'];
    $memberids = $allPostPutVars['searched_members'];
    $utype = $allPostPutVars['utype'];

    $where_member_ids = array();
    $pagesize = 10;
    $start = ($page - 1) * $pagesize;

    if ($city == "") {
        $where_city = '';
        $knowwhere_city = '';
    } else {
        if ($iszip == 1) {
            $neighbours = findneighbours($this, $city, '30');
            if (sizeof($neighbours) > 0) {
                $neighbourzips = implode(",", $neighbours);
                $where_city = "   zip in  (  " . $neighbourzips . "  )  and ";
                $knowwhere_city = "   client_zip in  (  " . $neighbourzips . "  )  and ";
            } else {
                $where_city = "  zip ='" . $city . "' and ";
                $knowwhere_city = " client_zip ='" . $city . "' and ";
            }
        } else {
            $cities = explode(",", $city);
            $where_city = " ( FIND_IN_SET('" . implode("',  cities ) OR FIND_IN_SET('", $cities) . "',  city ) )  and";

            $knowwhere_city = " ( FIND_IN_SET('" . implode("',  client_location ) OR FIND_IN_SET('", $cities) . "',  client_location ) )  and";
        }
    }
    $where_vocations = '';
    if ($vocation != '') {
        $keys = explode(",", $vocation);
        $where_vocations = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) ";

        $knowwhere_vocations = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) ";

    } else if ($keyword != '') {
        $keys = explode(",", $keyword);
        $where_vocations = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) ";

        $knowwhere_vocations = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) ";

    }

    if ($keyword == '' && $city == '' && $vocation == '') {
        $jsonresult = array('error' => '10', 'errmsg' => 'Search parameter missing!');
        $response->getBody()->write(json_encode($jsonresult));
        return $response;
    }

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($utype == 1) {
            $sql_query_ids = " select a.id as ui from mc_user as a inner join  user_details  as b on a.id=b.user_id  where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations )   ";

            $rst = $pdo->query($sql_query_ids);
            if ($rst->rowCount() > 0) {
                $member_ids = $rst->fetchAll(PDO::FETCH_ASSOC);
                $ids = array();
                foreach ($member_ids as $row) {
                    $where_member_ids[] = $row["ui"];
                }
            }


            $sql_query = " select a.id as ui, 0 knid  , user_email as a, username  as b, user_role  as c, user_pkg  as d, user_phone  as e,  image  as f,  busi_name  as g,  user_type  as h, " .
                " busi_location_street  as i,  busi_location  as j,  busi_type  as k,  busi_hours  as l, busi_website  as m, current_company  as n, linkedin_profile  as o, " .
                " street  as p, city  as q, zip  as r, country  as s,  groups  as t,  target_clients  as u, target_referral_partners  as v, vocations  as w, about_your_self  as x , " .
                " 0 as isconnected, r.rating as rating from mc_user as a
            left join (select user_id, sum(ranking)/count(DISTINCT(rated_by)) as rating from mc_user_rating  group by user_id) as r on a.id = r.user_id
            inner join  user_details  as b on a.id=b.user_id  
            " .
                " where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations )  
            order by rating desc, username  limit $start, $pagesize ";

            $sql_query_count = "select count(*) as reccnt from mc_user as a inner join  user_details  as b on a.id=b.user_id  " .
                " where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations ) ";

            $rst = $pdo->query($sql_query);
            $membercount = $rst->rowCount();

            $memberids = array();
            if ($membercount > 0) {

                $members = $rst->fetchAll(PDO::FETCH_ASSOC);

                for ($i = 0; $i < $rst->rowCount(); $i++) {
                    $sp = $members[$i]['ui'];
                    $memberids[] = $sp;
                    $query_connect_check = " select * from mc_member_connections where  status='1' and (  ( firstpartner='$userid' and secondpartner='$sp')  or ( firstpartner='$sp' and secondpartner='$userid') ) ";

                    $rstconcheck = $pdo->query($query_connect_check);
                    if ($rstconcheck->rowCount() > 0) {
                        $members[$i]['isconnected'] = '1';
                    }
                }
                //loggin search keyword
                $rst_count = $pdo->query($sql_query_count);
                $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                $pages = ceil($result_count[0]['reccnt'] / 10);
                $jsonresult = array('pages' => $pages, 'result' => $members, 'msg1' => 'Member fetched successfully!');
            }
            $memberids = implode(",", $memberids);
        }

        //loading knows
        $knows = [];
        if ($memberids != '') {
            $wherememberid = " user_id  in ( " . $memberids . ") and ";
            $membername = " and find_in_set('$keyword', client_profession) ";

            //fetching knows
            $sql_query = "select r.rating, k.user_id  as ui, r.rating as rating, k.id as knid , u.username as un, k.client_email as a  ,  k.client_name as b , 
                    'na' c, 'na' d,  k.client_phone as e, 'na' f, 
                    'na' g, 'na' h, 'na' i, 'na' j,  'na' k, 'na' l,  'na' m, 'na' n, 
                    'na' o, 'na'  p,  k.client_location as q,  k.client_zip as r, 'na'  s, 
                    'na' t, 'na'  u, 'na'  v, k.client_profession as w, 'na'  x ,  0 as isconnected  
                    from  user_people as k
                    left join (select user_id as r_user_id, sum(ranking) as rating from user_rating  group by user_id) as r on k.id = r_user_id
                    inner join mc_user as u on k.user_id=u.id  where $wherememberid " .
                " (find_in_set('Rated 25', k.tags) or  find_in_set('Rated 6 Need to Contact', k.tags) ) order by rating desc limit $start, $pagesize ";

            $sql_query_count = "select count(*) as reccnt from  user_people  where $wherememberid " .
                " (find_in_set('Rated 25', tags) or  find_in_set('Rated 6 Need to Contact', tags) ) ";


            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult['know_pages'] = $pages;
            $rst = $pdo->query($sql_query);

            if ($rst->rowCount() > 0) {
                $knows = $rst->fetchAll(PDO::FETCH_ASSOC);
            }

        } else {
            $wherememberid = "";
            $jsonresult = array('pages' => '0', 'result' => '', 'msg1' => 'No matching members found!');

            $rsnameorvoc = $pdo->query("SELECT * FROM `groups` where islisted='1' and grp_name='$keyword' ORDER BY `grp_name` ");

            if ($rsnameorvoc->rowCount() == 0) {
                $membername = " and client_name like '%$keyword%' ";
            } else {
                $membername = " and find_in_set('$keyword', client_profession) ";
            }
        }
        if ($keyword == '') {
            $membername = " ";
        }


        $know_result = array();
        if (sizeof($knows) > 0) {
            for ($i = 0; $i < $rst->rowCount() && $i < $pagesize; $i++) {
                $know_result[] = $knows[$i];
            }

            $jsonresult['knows'] = $know_result;
            $jsonresult['msg2'] = 'Matching knows found!';
        } else {
            $jsonresult['knows'] = '';
            $jsonresult['msg2'] = 'Matching knows not found!';
        }

        $jsonresult['errmsg'] = 'Member fetched successfully!';
        $jsonresult['error'] = '0';

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/request/connection/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $receipentid = $allPostPutVars['partnerid'];
    $user_id = $allPostPutVars['user_id'];
    $username = $allPostPutVars['uname'];
    $useremail = $allPostPutVars['useremail'];

    $sender_vocation = '';
    $requestid = 0;
    $token = md5($receipentid);
    $tokenlength = strlen($receipentid);
    $token = $receipentid . $token;

    $subject = "New Connection Request Request via MyCity.com";
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rslt = $pdo->query("select username, vocations from 
		mc_user as a inner join user_details as b on a.id= b.user_id where a.id='$user_id' ");
        if ($rslt->rowCount() > 0):
            $user_row = $rslt->fetchAll(PDO::FETCH_ASSOC)[0];
            $sender_vocation = $user_row['vocations'];
            $username = $user_row['username'];
        endif;


        $results = $pdo->query("select * from mc_member_connections where  (firstpartner='$user_id' and secondpartner='$receipentid') and  request_type='1'  ");

        if ($results->rowCount() > 0) {
            $jsonresult = array('error' => '10', 'errmsg' => "A request for connection exists!");
        } else {
            $stmt = $pdo->prepare("insert into mc_member_connections (firstpartner, secondpartner,request_type, requestdate ) values (?,?, '1', NOW() )  ");
            $stmt->execute(array($user_id, $receipentid));
            $requestid = $pdo->lastInsertId();

            $rslt = $pdo->query("select * from mc_user where id='$receipentid' ");
            if ($rslt->rowCount() > 0):

                $row = $rslt->fetchAll(PDO::FETCH_ASSOC)[0];
                $receipentmail = $row["user_email"];
                $receipentname = $row["username"];

                //notification email
                //email headings
                $headers = "From: bob@mycity.com\r\n";
                $headers .= "Reply-To: bob@mycity.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                $ds = DIRECTORY_SEPARATOR;
                $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
                $mailbody = "";

                if (file_exists($path . "templates/black_template_01.txt")) {
                    $template_part = file_get_contents($path . "templates/black_template_01.txt");
                }


                if (file_exists($path . "templates/connection_request.txt")) {
                    $mail_body = file_get_contents($path . "templates/connection_request.txt");
                    $mail_body = str_replace("{tokenid}", $token, $mail_body);
                    $mail_body = str_replace("{tokenlength}", $tokenlength, $mail_body);
                    $mail_body = str_replace("{year}", date('Y'), $mail_body);
                    $mail_body = str_replace("{sender}", strip_tags($username), $mail_body);
                    $mail_body = str_replace("{vocation}", $sender_vocation, $mail_body);
                    $mail_body = str_replace("{receipent}", $receipentname, $mail_body);
                }

                $mailbody = str_replace("{mail_body}", $mail_body, $template_part);
                sendemail($receipentmail, $subject, $mailbody, $mailbody);
                //insert into mail log
                $stmt = $pdo->prepare("insert into mc_mailbox (sender, receipent, subject , emailbody , emailstatus , email_type , senton) VALUES (?,?, ?, ?, '0', '10', NOW() )");
                $stmt->execute(array($useremail, $receipentmail, $subject, $mail_body));
                $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => "Email sent!");

            endif;
            $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => "Member connection request placed successfully!", 'requestid' => $requestid);
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry sending email!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get all connection requests
$app->post('/member/connections/getall/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $rstatus = $allPostPutVars['rstatus'];
    $direction = $allPostPutVars['dir'];
    $status_where = '';
    if ($rstatus == -1) {
        $status_where = " and status in  (0, 1) ";
    } else {
        $status_where = " and status ='" . $rstatus . "'";
    }

    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($direction == 0) {
            $sql_query = "select b.*,  c.current_company,  c.city, c.zip, c.country, a.id as mdid, a.requestdate, a.status 
			from mc_member_connections as a inner join mc_user as b on a.firstpartner = b.id 
			inner join user_details as c  on c.user_id=b.id 
			where  secondpartner  ='$userid' and request_type='1'  $status_where order by status desc, b.username  limit $start, $pagesize ";

            $sql_query_count = "select count(*) as reccnt from mc_member_connections as a inner join mc_user as b on a.firstpartner = b.id 
			inner join user_details as c  on c.user_id=b.id 
			where secondpartner  ='$userid' and request_type='1' $status_where";
        } else {
            $sql_query = "select b.*,  c.current_company,  c.city, c.zip, c.country, a.id as mdid, a.requestdate, a.status 
			from mc_member_connections as a inner join mc_user as b on a.secondpartner = b.id 
			inner join user_details as c  on c.user_id=b.id 
			where  firstpartner  ='$userid' and request_type='1'  $status_where order by status desc, b.username  limit $start, $pagesize ";

            $sql_query_count = "select count(*) as reccnt from mc_member_connections as a inner join mc_user as b on a.secondpartner = b.id 
			inner join user_details as c  on c.user_id=b.id 
			where firstpartner  ='$userid' and request_type='1' $status_where";
        }


        $ds = DIRECTORY_SEPARATOR;
        $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";
        $rst = $pdo->query($sql_query);
        $allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            if ($allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null') {
                $user_picture = (file_exists($imagepath . $allmembers[$i]['image']) ? $allmembers[$i]['image'] : "no-photo.png");
                $allmembers[$i]['image'] = $user_picture;
            } else {
                $allmembers[$i]['image'] = "no-photo.png";
            }
        }
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => $sql_query, 'results' => $allmembers);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $sql_query);
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all rated knows
$app->post('/member/knows/getrated/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $ranking = $allPostPutVars['ranking'];
    $goto = $allPostPutVars['page'];

    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select a.*, b.* from user_people as a inner join " .
            " (select user_id, sum(ranking) as rate from user_rating group by user_id) as b on a.id=b.user_id " .
            " where b.rate = '$ranking' order by a.client_name asc limit $start, $pagesize";

        $rst = $pdo->query($sql_query);
        $allknows = $rst->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = " select count(*) as reccnt from user_people as a inner join " .
            " (select user_id, sum(ranking) as rate from user_rating group by user_id) as b on a.id=b.user_id " .
            " where b.rate = '$ranking' order by a.client_name asc ";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);

        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => "Top rated knows fetched successfully", 'results' => $allknows);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Top rated knows could not be fetched. Please retry later!', 'errDetail' => $e);
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/updateforcepass/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $email = $allPostPutVars['email'];
    $password = $allPostPutVars['password'];
    $password = md5($password);
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query("select * from mc_user where user_email = '$email'  ");
        if ($rst->rowCount() > 0) {
            $stmt = $pdo->prepare("update mc_user set user_pass = ? where user_email = ? ");
            $stmt->execute(array($password, $email));
            $jsonresult = array('error' => '0', 'errmsg' => "Member password updated successfully!");
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => "No matching user found!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});
//get all rated knows
$app->post('/member/knows/getratedbyemail/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $ranking = $allPostPutVars['ranking'];
    $memberemail = $allPostPutVars['memberemail'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select a.*, b.* from user_people as a inner join " .
            " (select user_id, sum(ranking) as rate from user_rating group by user_id) as b on a.id=b.user_id " .
            " where b.rate = '$ranking'  and a.client_email='$memberemail'";

        $rst = $pdo->query($sql_query);
        $allknows = $rst->fetchAll(PDO::FETCH_ASSOC);


        $jsonresult = array('error' => '0', 'errmsg' => "Top rated knows fetched successfully", 'results' => $allknows);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Top rated knows could not be fetched. Please retry later!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get profile claim email template
$app->post('/member/getclaimprofileemail/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $name = $allPostPutVars['name'];
    $knowid = $allPostPutVars['knowid'];

    $token = md5($knowid);
    $tokenlength = strlen($knowid);
    $token = $knowid . $token;


    $mailbody = '';
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($name !== NULL || $name != '') {
            $ds = DIRECTORY_SEPARATOR;
            $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
            if (file_exists($path . "templates/claim_your_profile.txt")) {
                $mailbody = file_get_contents($path . "templates/claim_your_profile.txt");
                $mailbody = str_replace("{receipent}", $name, $mailbody);
                $mailbody = str_replace("{tokenid}", $token, $mailbody);
                $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
                $mailbody = str_replace("{tokenlengthhash}", md5($tokenlength), $mailbody);


                $jsonresult = array('error' => '0', 'errmsg' => "Email template loaded.", 'mailbody' => $mailbody);

                // insert profile invite email
                $rst = $pdo->query("select count(*) as ecount from mc_claimprofile_invite where user_id='$knowid'");
                if ($rst->rowCount() > 0) {
                    if ($rst->fetchAll(PDO::FETCH_ASSOC)[0]['ecount'] == 0) {
                        $stmt = $pdo->prepare("insert into mc_claimprofile_invite (user_id, invitedate ) VALUES (? , NOW() )");
                        $stmt->execute(array($knowid));
                    }
                }
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Profile claim email template missing. Please create it first.", 'mailbody' => 'Email template missing.');
            }
        } else {
            $jsonresult = array('error' => '100', 'errmsg' => "Something went wrong. Please retry again!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/claimprofileemail/send/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $to = $allPostPutVars['to'];
    $subject = "Claim your MyCity.com Profile. People are waiting to connect with you.";
    $id = $allPostPutVars['id'];
    $mid = $allPostPutVars['mid'];
    $from = "bob@mycity.com";
    $token = md5($id);
    $tokenlength = strlen($id);
    $token = $id . $token;

    try {
        $ds = DIRECTORY_SEPARATOR;
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds;

        if (file_exists($path . "templates/black_template_01.txt")) {
            $template_part = file_get_contents($path . "templates/black_template_01.txt");

            if (file_exists($path . "templates/claim_profile_invite_by_member.txt")) {
                $mailbody = file_get_contents($path . "templates/claim_profile_invite_by_member.txt");
                $mailbody = str_replace("{receipent}", $name, $mailbody);
                $mailbody = str_replace("{tokenid}", $token, $mailbody);
                $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
                $mailbody = str_replace("{tokenlengthhash}", md5($tokenlength), $mailbody);

                $template_part = str_replace("{mail_body}", $mailbody, $template_part);
                sendemail($to, $from, $subject, $template_part, $template_part);

                // insert profile invite email
                $pdo = getPDO($this);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $rst = $pdo->query("select count(*) as ecount from mc_claimprofile_invite where user_id='$id'");
                if ($rst->rowCount() > 0) {
                    if ($rst->fetchAll(PDO::FETCH_ASSOC)[0]['ecount'] == 0) {
                        $stmt = $pdo->prepare("insert into mc_claimprofile_invite (user_id, member_id, invitedate ) VALUES (? ,?, NOW() )");
                        $stmt->execute(array($id, $mid));
                    }
                }
                //update profile claim
                $sql_query = "update user_people  set isinvited='1' where id= ?";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($id));

                $jsonresult = array('error' => '0', 'errmsg' => "Connection request sent!",
                    'mailbody' => $template_part);
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Email template missing. Please consult admin for assistance.", 'mailbody' => 'Email template missing.');
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong while sending invite. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get profile claim email template
$app->post('/member/claimprofile/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $knowid = $allPostPutVars['i'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query("select * from mc_claimprofile_invite as a inner join user_people as b on b.id=a.user_id where a.user_id = '$knowid'  ");
        if ($rst->rowCount() > 0) {
            $row = $rst->fetchAll(PDO::FETCH_ASSOC);

            if ($row[0]['isaccepted'] == 0) {

                //now copy part of the know's record to mc_user
                $memberrst = $pdo->query("select count(*) as emcount from mc_user  where user_email= '" . $row[0]['client_email'] . "'  ");
                if ($memberrst->rowCount() > 0) {
                    $emrow = $memberrst->fetchAll(PDO::FETCH_ASSOC);


                    if ($emrow[0]['emcount'] == 0) {
                        $stmt = $pdo->prepare("update mc_claimprofile_invite set isaccepted='1', claimedon=NOW() where user_id = ? ");
                        $stmt->execute(array($knowid));
                        $password = mt_rand(0, 10000);
                        $stmt = $pdo->prepare("insert into mc_user (user_email, user_pass,username, user_phone, user_role, user_pkg, signup_type,  user_status, createdOn ) values (?,?,?, ?, 'user', 'Basic' , '10' , '1',NOW() )  ");
                        $stmt->execute(array(
                            $row[0]['client_email'], md5($password),
                            $row[0]['client_name'], $row[0]['client_phone']));
                        $newuserid = $pdo->lastInsertId();
                        $jsonresult = array('error' => '0', 'userid' => $newuserid, 'errmsg' => "Profile claimed successfully.");
                    } else {
                        $jsonresult = array('error' => '0', 'userid' => $newuserid, 'errmsg' => "A member with the same email already exists. Please login instead!");
                    }
                }
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => $emcount . "You already have claimed your profile.");
            }
        } else {
            $jsonresult = array('error' => '100', 'errmsg' => "Seems like you don't have any invitation to claim profile. You can signup below.");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//update privacy settings
$app->post('/member/privacy/update/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    $privacyset = $allPostPutVars['privacyset'];
    $mid = 19;
    $privacyset = 1;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query("select * from mc_user where id = '$mid'  ");
        if ($rst->rowCount() > 0) {
            $stmt = $pdo->prepare("update mc_user set privacyoption = ? where id = ? ");
            $stmt->execute(array($privacyset, $mid));
            $jsonresult = array('error' => '0', 'errmsg' => "Privacy settings updated.");
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => "No matching user found!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get profile claim email template
$app->post('/member/getclaimprofileinvitebymember/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $name = $allPostPutVars['name'];
    $knowid = $allPostPutVars['knowid'];
    $mid = $allPostPutVars['mid'];
    $token = md5($knowid);
    $tokenlength = strlen($knowid);
    $token = $knowid . $token;


    $mailbody = '';
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($name !== NULL || $name != '') {
            $ds = DIRECTORY_SEPARATOR;
            $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
            if (file_exists($path . "templates/claim_profile_invite_by_member.txt")) {
                $mailbody = file_get_contents($path . "templates/claim_profile_invite_by_member.txt");
                $mailbody = str_replace("{receipent}", $name, $mailbody);
                $mailbody = str_replace("{tokenid}", $token, $mailbody);
                $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
                $mailbody = str_replace("{tokenlengthhash}", md5($tokenlength), $mailbody);

                $jsonresult = array('error' => '0', 'errmsg' => "Email template loaded.", 'mailbody' => $mailbody);

                // insert profile invite email
                $rst = $pdo->query("select count(*) as ecount from mc_claimprofile_invite where user_id='$knowid'");
                if ($rst->rowCount() > 0) {
                    if ($rst->fetchAll(PDO::FETCH_ASSOC)[0]['ecount'] == 0) {
                        $stmt = $pdo->prepare("insert into mc_claimprofile_invite (user_id,member_id, invitedate ) VALUES (? ,?, NOW() )");
                        $stmt->execute(array($knowid, $mid));
                    }
                }
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Profile claim email template missing. Please create it first.", 'mailbody' => 'Email template missing.');
            }
        } else {
            $jsonresult = array('error' => '100', 'errmsg' => "Something went wrong. Please retry again!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/claimprofile/getemails/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $page = $allPostPutVars['page'];
    $pagesize = 10;
    $start = ($page - 1) * $pagesize;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select a.id as logid, a.member_id as searchby, a.invitedate, a.isaccepted, b.*, 'n/a' membername from mc_claimprofile_invite as a inner join user_people as b on b.id=a.user_id where  a.member_id <> '-1' and a.member_id <> '0' order by a.id desc limit $start, $pagesize ";
        $sql_query_count = "select count(*) as reccnt  from mc_claimprofile_invite as a inner join user_people as b on b.id=a.user_id where  a.member_id <> '-1' and a.member_id <> '0'   ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $profileclaimlogs = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);

            $i = 0;
            foreach ($profileclaimlogs as $row) {
                $rate_q = $pdo->query("select username from mc_user where id = '" . $row['searchby'] . "'");
                if ($rate_q->rowCount() > 0)
                    $profileclaimlogs[$i]['membername'] = $rate_q->fetchAll(PDO::FETCH_ASSOC)[0]['username'];
                $i++;
            }


            $jsonresult = array('error' => '0', 'pages_1' => $pages, 'errmsg' => 'Search log found!', 'result_1' => $profileclaimlogs);

        }

        $sql_query = "select * from mc_claimprofile_invite as a inner join user_people as b on b.id=a.user_id where   a.member_id  = '-1'  order by a.id desc limit $start, $pagesize ";
        $sql_query_count = "select count(*) as reccnt  from mc_claimprofile_invite as a inner join user_people as b on b.id=a.user_id where  a.member_id = '-1'  ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $profileclaimlogs = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);

            $jsonresult['pages_2'] = $pages;
            $jsonresult['errmsg'] = 'Search log found!';
            $jsonresult['result_2'] = $profileclaimlogs;
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all performance data
$app->post('/mailbox/count/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $email = $allPostPutVars['email'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select count(*) as totalreceived from mc_mailbox where receipent= '$email' and emailstatus='0' ";
        $rst = $pdo->query($sql_query);

        $jsonresult[] = array(
            'error1' => '0',
            'errmsg' => 'Received email count fetched!',
            'count' => $rst->fetchall(PDO::FETCH_ASSOC)[0]['totalreceived']);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read all performance data
$app->post('/member/connection/received/count/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select count(*) as totalreceived from mc_member_connections where  secondpartner= '$userid' and request_type='1' ";
        $rst = $pdo->query($sql_query);

        $jsonresult[] = array('error1' => '0', 'errmsg' => 'Reminders fetched successfully!',
            'count' => $rst->fetchall(PDO::FETCH_ASSOC)[0]['totalreceived']);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all performance data
$app->post('/member/rating/getquestions/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $memid = $allPostPutVars['memid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select *, 1 as ranking from mc_questions where user_type='1' order by id";
        $rst = $pdo->query($sql_query);
        $allquestions = $rst->fetchAll(PDO::FETCH_ASSOC);

        $i = 0;
        foreach ($allquestions as $item) {
            $question = $item['id'];
            $query = "select ranking from mc_user_rating where user_id='$memid' and rated_by='$userid' and question_id='$question'";
            $raters = $pdo->query($query);
            if ($raters->rowCount() > 0) {
                $allquestions[$i]['ranking'] = $raters->fetchAll(PDO::FETCH_ASSOC)[0]['ranking'];
            }
            $i++;
        }
        $jsonresult = array('error' => '0', 'errmsg' => 'Rating questions fetched successfully!', 'results' => $allquestions);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all performance data
$app->post('/member/rating/saveratings/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $memid = $allPostPutVars['memid'];
    $ques1 = $allPostPutVars['ques1'];
    $rate1 = $allPostPutVars['rate1'];
    $ques2 = $allPostPutVars['ques2'];
    $rate2 = $allPostPutVars['rate2'];
    $ques3 = $allPostPutVars['ques3'];
    $rate3 = $allPostPutVars['rate3'];
    $ques4 = $allPostPutVars['ques4'];
    $rate4 = $allPostPutVars['rate4'];
    $ques5 = $allPostPutVars['ques5'];
    $rate5 = $allPostPutVars['rate5'];


    $data[] = array('user_id' => $memid, 'rated_by' => $userid, 'question_id' => $ques1, 'ranking' => $rate1);
    $data[] = array('user_id' => $memid, 'rated_by' => $userid, 'question_id' => $ques2, 'ranking' => $rate2);
    $data[] = array('user_id' => $memid, 'rated_by' => $userid, 'question_id' => $ques3, 'ranking' => $rate3);
    $data[] = array('user_id' => $memid, 'rated_by' => $userid, 'question_id' => $ques4, 'ranking' => $rate4);
    $data[] = array('user_id' => $memid, 'rated_by' => $userid, 'question_id' => $ques5, 'ranking' => $rate5);

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("delete from mc_user_rating where user_id= ? and rated_by =? ");
        $stmt->execute(array($memid, $userid));

        for ($i = 0; $i < 5; $i++) {
            $stmt = $pdo->prepare("insert into mc_user_rating (user_id, rated_by,question_id, ranking ) values (?,?, ?,? )   ");
            $stmt->execute(array($data[$i]['user_id'], $data[$i]['rated_by'], $data[$i]['question_id'], $data[$i]['ranking']));
        }
        $jsonresult = array('error' => '0', 'errmsg' => 'Rating saved successfully!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/directmail/send/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $receipentid = $allPostPutVars['id'];
    $mailbody = $allPostPutVars['mailbody'];
    $originalbody = $mailbody;
    $user_id = $allPostPutVars['user_id'];
    $_username = $allPostPutVars['username'];
    $subject = $allPostPutVars['subject'];
    $senderemail = $allPostPutVars['senderemail'];
    $senderphone = $allPostPutVars['senderphone'];

    //email headings
    $headers = "From: " . $senderemail . "\r\n";
    $headers .= "Reply-To: " . $senderemail . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $token = md5($receipentid);
    $tokenlength = strlen($receipentid);
    $token = $receipentid . $token;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $results = $pdo->query("select * from mc_user where id='$receipentid' ");

        if ($results->rowCount() > 0) {
            $mailreceipent = $results->fetchAll(PDO::FETCH_ASSOC);
            $receipentmail = $mailreceipent[0]['user_email'];
            $receipentname = $mailreceipent[0]['username'];
            if ($receipentmail !== NULL || $receipentmail != '') {
                $ds = DIRECTORY_SEPARATOR;
                $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
                if (file_exists($path . "templates/directemail.txt")) {
                    $filecontent = file_get_contents($path . "templates/directemail.txt");
                    $filecontent = str_replace("{receipent_name}", $receipentname, $filecontent);
                    $filecontent = str_replace("{mail_body}", $mailbody, $filecontent);
                    $filecontent = str_replace("{sender_name}", $_username, $filecontent);
                    $filecontent = str_replace("{sender_email}", $senderemail, $filecontent);
                    $filecontent = str_replace("{sender_phone}", $senderphone, $filecontent);
                    $filecontent = str_replace("{tokenid}", $token, $filecontent);
                    $filecontent = str_replace("{tokenlength}", $tokenlength, $filecontent);
                    $filecontent = str_replace("{year}", date('Y'), $filecontent);
                    $mailbody = $filecontent;
                }

                sendemail($receipentmail, $subject, $mailbody, $mailbody);

                //insert into mail log
                $stmt = $pdo->prepare("INSERT INTO mc_mailbox (sender, receipent, subject , emailbody , emailstatus , email_type , senton) VALUES (?,?, ?, ?, '0', '0', NOW() )");
                $stmt->execute(array($senderemail, $receipentmail, $subject, $originalbody));
                $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => "Email sent!");
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Something went wrong. Please retry sending email!");
            }
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => "Something went wrong. Please retry sending email!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/directmail/request/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $receipentid = $allPostPutVars['partnerid'];
    $user_id = $allPostPutVars['user_id'];
    $username = $allPostPutVars['uname'];
    $usermail = $allPostPutVars['usermail'];
    $requestid = 0;

    $token = md5($receipentid);
    $tokenlength = strlen($receipentid);
    $token = $receipentid . $token;


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $results = $pdo->query("select * from mc_member_connections where  (firstpartner='$user_id' and secondpartner='$receipentid')  ");

        if ($results->rowCount() > 0) {
            //$stmt= $pdo->prepare("update mc_member_connections set approvedon=NOW(), status= ? where  firstpartner=? and secondpartner=? ");
            //$stmt->execute(array( $status, $user_id, $receipentid ) );
            //$requestid = $pdo->lastInsertId();
            $jsonresult = array('error' => '10', 'errmsg' => "A request for direct email communication exists!");
        } else {
            $stmt = $pdo->prepare("insert into mc_member_connections (firstpartner, secondpartner,requestdate ) values (?,?, NOW() )  ");
            $stmt->execute(array($user_id, $receipentid));
            $requestid = $pdo->lastInsertId();

            $rslt = $pdo->query("select * from mc_user where id='$receipentid' ");
            if ($rslt->rowCount() > 0):

                $row = $rslt->fetchAll(PDO::FETCH_ASSOC)[0];
                $receipentmail = $row["user_email"];
                $receipentname = $row["username"];

                //notification email
                //email headings
                $headers = "From: Referrals@mycity.com\r\n";
                $headers .= "Reply-To: Referrals@mycity.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                $ds = DIRECTORY_SEPARATOR;
                $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
                $mailbody = "";
                if (file_exists($path . "templates/directemailrequest.txt")) {
                    $filecontent = file_get_contents($path . "templates/directemailrequest.txt");
                    $filecontent = str_replace("{request_origin_name}", $receipentname, $filecontent);
                    $filecontent = str_replace("{request_receipent_name}", $username, $filecontent);
                    $filecontent = str_replace("{tokenid}", $token, $filecontent);
                    $filecontent = str_replace("{tokenlength}", $tokenlength, $filecontent);
                    $filecontent = str_replace("{year}", date('Y'), $filecontent);

                    $mailbody = $filecontent;
                }

                $subject = "New Direct Email Communication Request via MyCity.com";
                sendemail($receipentmail, $subject, $mailbody, $mailbody);

                //insert into mail log
                $stmt = $pdo->prepare("insert into  mc_mailbox (sender, receipent, subject , emailbody , emailstatus , email_type , senton) VALUES (?,?, ?, ?, '0', '10', NOW() )");
                $stmt->execute(array($usermail, $receipentmail, $subject, $mailbody));

            endif;
            $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => "Direct email communication request successfully made!", 'requestid' => $requestid);
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry sending email!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/directmail/checkstatus/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $receipentid = $allPostPutVars['partnerid'];
    $user_id = $allPostPutVars['user_id'];
    $requestid = 0;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $results = $pdo->query("select * from mc_member_connections where  (firstpartner='$user_id' and secondpartner='$receipentid') or (firstpartner='$receipentid' and secondpartner='$user_id') ");

        if ($results->rowCount() > 0) {
            $status = $results->fetchAll(PDO::FETCH_ASSOC)[0]['status'];
            $jsonresult = array('error' => '0', 'errmsg' => "A request for direct email communication exists!", 'status' => $status);
        } else {
            $jsonresult = array('error' => '0', 'errmsg' => "No direct email communication request found!", 'status' => 0);
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry sending email!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/directmail/request/update/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $receipentid = $allPostPutVars['partnerid'];
    $partneremail = $allPostPutVars['partneremail'];
    $user_id = $allPostPutVars['user_id'];
    $status = $allPostPutVars['status'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($receipentid > 0) {
            $stmt = $pdo->prepare("update mc_member_connections set approvedon=NOW(), status= ? where 
			(firstpartner=? and secondpartner=?) or (firstpartner=? and secondpartner=?) ");
            $stmt->execute(array($status, $receipentid, $user_id, $user_id, $receipentid));
            $requestid = $pdo->lastInsertId();
        } else {
            $sql_query = "select id from mc_user where user_email='$partneremail' and user_status='1'";

            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $partnerid = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
                $stmt = $pdo->prepare("update mc_member_connections set approvedon=NOW(), status= ? where 
				(firstpartner=? and secondpartner=?) or (firstpartner=? and secondpartner=?) ");
                $stmt->execute(array($status, $partnerid, $user_id, $user_id, $partnerid));
                $requestid = $pdo->lastInsertId();
            }
        }

        $jsonresult = array('error' => '0', 'errmsg' => "Direct email communication updated!");
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry sending email!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all direct mail requests
$app->post('/directmail/getrequests/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $dir = $allPostPutVars['dir'];
    $rstatus = $allPostPutVars['rstatus'];

    $status_where = '';
    if ($rstatus == -1) {
        $status_where = " and status in  (0, 1) ";
    } else {
        $status_where = " and status ='" . $rstatus . "'";
    }
    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "( select b.*,  c.current_company,  c.city, c.zip, c.country, a.id as mdid, a.firstpartner, a.secondpartner,  a.requestdate, a.status from mc_member_connections as a inner join mc_user as b " .
            " on a.secondpartner = b.id inner join user_details as c  on c.user_id=b.id where firstpartner ='$userid' $status_where ) union 
	( select b.*,  c.current_company,  c.city, c.zip, c.country, a.id as mdid, a.firstpartner, a.secondpartner,  a.requestdate, a.status from mc_member_connections as a inner join mc_user as b " .
            " on a.firstpartner  = b.id inner join user_details as c  on c.user_id=b.id where secondpartner ='$userid' $status_where )  limit $start, $pagesize ";

        $sql_query_count = "select count(*) as reccnt from mc_member_connections as a inner join mc_user as b on a.secondpartner = b.id 
		inner join user_details as c  on c.user_id=b.id 
		where  firstpartner  ='$userid' " . $status_where;


        $ds = DIRECTORY_SEPARATOR;
        $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";
        $rst = $pdo->query($sql_query);
        $allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            if ($allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null') {
                $user_picture = (file_exists($imagepath . $allmembers[$i]['image']) ? $allmembers[$i]['image'] : "no-photo.png");
                $allmembers[$i]['image'] = $user_picture;
            } else {
                $allmembers[$i]['image'] = "no-photo.png";
            }
        }
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Direct email contact requests!', 'results' => $allmembers);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all direct mail requests
$app->post('/directmail/getincomingrequests/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $rstatus = $allPostPutVars['rstatus'];

    $status_where = '';
    if ($rstatus == -1) {
        $status_where = " and status in  (0, 1) ";
    } else {
        $status_where = " and status ='" . $rstatus . "'";
    }

    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql_query = "select b.*,  c.current_company,  c.city, c.zip, c.country, a.id as mdid, a.requestdate, a.status 
		from mc_member_connections as a inner join mc_user as b on a.firstpartner = b.id 
		inner join user_details as c  on c.user_id=b.id 
		where  secondpartner  ='$userid' $status_where limit $start, $pagesize ";

        $sql_query_count = "select count(*) as reccnt from mc_member_connections as a inner join mc_user as b on a.firstpartner = b.id 
		inner join user_details as c  on c.user_id=b.id 
		where secondpartner  ='$userid' $status_where";


        $ds = DIRECTORY_SEPARATOR;
        $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";
        $rst = $pdo->query($sql_query);
        $allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            if ($allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null') {
                $user_picture = (file_exists($imagepath . $allmembers[$i]['image']) ? $allmembers[$i]['image'] : "no-photo.png");
                $allmembers[$i]['image'] = $user_picture;
            } else {
                $allmembers[$i]['image'] = "no-photo.png";
            }
        }
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Direct email contact requests!', 'results' => $allmembers);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->get('/mails/load/{id}/', function (Request $request, Response $response) {

});


$app->post('/mails/sendintroducemail/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $suggestemail = $allPostPutVars['suggestemail'];
    $suggestname = $allPostPutVars['suggestname'];
    $suggestid = $allPostPutVars['suggestid'];
    $email = $allPostPutVars['to'];
    $profession = $allPostPutVars['profession'];
    $phone = $allPostPutVars['phone'];
    $mailogid = $allPostPutVars['mailogid'];
    $receipentname = $allPostPutVars['receipentname'];
    $receipentprof = $allPostPutVars['receipentprof'];
    $receipentphone = $allPostPutVars['receipentphone'];
    $clientid = $allPostPutVars['clientid'];
    $cc1 = $allPostPutVars['cc1'];
    $ccname1 = $allPostPutVars['ccname1'];
    $templateid = $allPostPutVars['templateid'];

    $user_id = $allPostPutVars['user_id'];
    $_username = $allPostPutVars['username'];
    $emailbody = $allPostPutVars['emailbody'];


    $ds = DIRECTORY_SEPARATOR;
    $apppath = '';
    $path = $_SERVER['DOCUMENT_ROOT'] . $ds;


    //email headings
    $subject = 'Introduction/Referral from ' . $_username;
    $headers = "From: referrals@mycity.com\r\n";
    $headers .= "Reply-To: referrals@mycity.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $results = $pdo->query("SELECT * FROM referralsuggestions where id='$mailogid' ");

        if ($results->rowCount() > 0) {
            $trow = $results->fetchAll(PDO::FETCH_ASSOC);
            if ($trow[0]['emailstatus'] == 1) {
                $jsonresult = array('error' => '10', 'errmsg' => "Introduction email already sent!");
                $response->getBody()->write(json_encode($jsonresult));
                return $response;
            }
        }

        //preparing notifier email for partner
        $notifiermail = "";
        if (file_exists($path . "templates/introductionmailnotifier01.txt")) {
            $filecontent = file_get_contents($path . "templates/introductionmailnotifier01.txt");
            $filecontent = str_replace("{username}", $_username, $filecontent);
            $filecontent = str_replace("{partner}", $suggestname, $filecontent);
            $filecontent = str_replace("{profession}", $profession, $filecontent);
            $filecontent = str_replace("{suggestemail}", $suggestemail, $filecontent);
            $filecontent = str_replace("{phone}", $phone, $filecontent);
            $filecontent = str_replace("{receipentname}", $receipentname, $filecontent);
            $filecontent = str_replace("{receipentprof}", $receipentprof, $filecontent);
            $filecontent = str_replace("{email}", $email, $filecontent);
            $filecontent = str_replace("{receipentphone}", $receipentphone, $filecontent);
            $notifiermail = $filecontent;
        }

        //preparing notifier email for sender
        $sendernotifierhtml = "";
        if (file_exists($path . "templates/introductionmailnotifier02.txt")) {
            $filecontent = file_get_contents($path . "templates/introductionmailnotifier02.txt");
            $filecontent = str_replace("{username}", $_username, $filecontent);
            $filecontent = str_replace("{partner}", $suggestname, $filecontent);
            $filecontent = str_replace("{receipentname}", $receipentname, $filecontent);
            $filecontent = str_replace("{receipentprof}", $receipentprof, $filecontent);
            $filecontent = str_replace("{to}", $to, $filecontent);
            $filecontent = str_replace("{receipentphone}", $receipentphone, $filecontent);
            $filecontent = str_replace("{body}", $mailbody, $filecontent);
            $sendernotifierhtml = $filecontent;
        }

        //sending introduction email: reading template body
        $html = "";

        if ($emailbody != "") {
            if (file_exists($path . "templates/introductionmail01.txt")) {
                $filecontent = file_get_contents($path . "templates/introductionmail01.txt");
                $html = str_replace("{body}", $emailbody, $filecontent);
            }
        } else {
            $mailtemplates = $pdo->query("select * from mc_mail_templates where id='$templateid'");
            if ($mailtemplates->rowCount() > 0) {
                $mailtemplate = $mailtemplates->fetchAll(PDO::FETCH_ASSOC)[0];
                $mailbody = $mailtemplate['mailbody'];
                $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
                $mailbody = str_replace("{user}", $_username, $mailbody);
                $mailbody = str_replace("{rated_by}", $ccname1, $mailbody);
                $mailbody = str_replace("{introducee}", $suggestname, $mailbody);
                $mailbody = str_replace("{introducee_profession}", $profession, $mailbody);
                $mailbody = str_replace("{introducee_email}", $suggestemail, $mailbody);
                $mailbody = str_replace("{introducee_phone}", $phone, $mailbody);
                if (file_exists($path . "templates/introductionmail01.txt")) {
                    $filecontent = file_get_contents($path . "templates/introductionmail01.txt");
                    $html = str_replace("{body}", $mailbody, $filecontent);
                }
            }
        }

        $to = $email;
        //mail towards the new connect
        $mailexistcheck = $pdo->query("SELECT COUNT(*) as ecnt FROM mailbox where sender='$user_id' AND receipent='$clientid' AND suggestedconnectid='$suggestid' ");
        $ecnt = $mailexistcheck->fetchAll(PDO::FETCH_ASSOC)[0]['ecnt'];
        $ins_query = "INSERT INTO mailbox (sender, receipent, subject, mailbody, senton, suggestedconnectid) values ( ?,?,? , ? ,  NOW(),  ?)";
        $stmt = $pdo->prepare($ins_query);
        $stmt->execute(array($user_id, $clientid, $subject, $mailbody, $suggestid));
        //mark as mail sent
        $upd_query = "UPDATE referralsuggestions SET emaillog=? , emailstatus=  '1',  senton=NOW()  WHERE id = ? ";
        $stmt = $pdo->prepare($upd_query);
        $stmt->execute(array($mailbody, $mailogid));
        $mailstatus = sendreferralmail($to, $subject, $html, $html);
        //mail towards partner
        sendemail($cc1, 'Referral suggestion for one of your connection sent', $notifiermail, $notifiermail);
        if ($mailstatus == 1) {
            $jsonresult = array('error' => '0', 'errmsg' => "Introduction email sent successfully!");
            //notifier to sender
            sendemail($suggestemail, $subject, $sendernotifierhtml, $sendernotifierhtml);
        } else {
            $jsonresult = array('error' => '1', 'errmsg' => "Mail sending has failed!");
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get member name by id
$app->post('/contact/getname/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $contactid = $allPostPutVars['contactid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT  id, client_name  FROM user_people  WHERE id  = '$contactid' ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = array('error' => '0', 'contactname' => $rst->fetchAll(PDO::FETCH_ASSOC)[0]['client_name']);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No contact name found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get member name by ids (comman separated values)
$app->post('/contact/getbyids/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $cid = $allPostPutVars['cid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select  p.id as a ,  p.user_id as b, p.client_name as c, p.client_profession as d,  p.client_lifestyle as e, 
		p.client_phone as f, p.client_email as g, p.client_location as h, p.client_zip as i, p.client_note as j, p.user_group as k, 
		p.entrydate as l, p.updatedate as m, p.company as n, p.isimport as o, p.lcid as p ,p.tags as q ,  u.user_email as r, 
		u.username as s, u.user_phone as t from  user_people as p inner join mc_user as u on u.id=p.user_id where p.id  in ($cid)  ";


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = array('error' => '10', 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));;
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No contact name found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read mail templates
$app->post('/mailtemplates/read/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $fileid = $allPostPutVars['templateid'];
    $receipentname = $allPostPutVars['receipentname'];
    $receipentemail = $allPostPutVars['to'];
    $ccname1 = $allPostPutVars['ccname1'];
    $suggestname = $allPostPutVars['suggestname'];
    $profession = $allPostPutVars['profession'];
    $suggestemail = $allPostPutVars['suggestemail'];
    $phone = $allPostPutVars['phone'];
    $clientid = $allPostPutVars['clientid'];
    $_username = $allPostPutVars['username'];
    $_mremail = $allPostPutVars['mremail'];
    $_userphone = $allPostPutVars['muserphone'];

    $suggestid = $allPostPutVars['suggestid'];


    try {

        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        if ($suggestid > 0) {
            $sql_query = " select * from  mc_user  where id = (select user_id from  user_people where id='$suggestid')  ";
            $rst_suggest = $pdo->query($sql_query);
            if ($rst_suggest->rowCount() > 0) {
                $row = $rst_suggest->fetchAll(PDO::FETCH_ASSOC)[0];
                $_username = $row['username'];
                $_mremail = $row['user_email'];
                $_userphone = $row['user_phone'];
            }
        }


        $sql_query = " select * from  mc_user  where id = (select user_id from  user_people where id='$clientid')  ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $ratedby = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['username'];

            $ds = DIRECTORY_SEPARATOR;
            $apppath = '';
            $path = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds;
            if ($_username == $ratedby) {
                if (file_exists($path . "templates/mailsamemember" . $fileid . ".txt")) {
                    $mailbody = file_get_contents($path . "templates/mailsamemember" . $fileid . ".txt");
                    $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
                    $mailbody = str_replace("{rated_by}", $ratedby, $mailbody);
                    $mailbody = str_replace("{introducee}", $suggestname, $mailbody);
                    $mailbody = str_replace("{introducee_profession}", $profession, $mailbody);
                    $mailbody = str_replace("{introducee_email}", $suggestemail, $mailbody);
                    $mailbody = str_replace("{introducee_phone}", $phone, $mailbody);
                    $mailbody = str_replace("{user}", $_username, $mailbody);
                    $mailbody = str_replace("{user_email}", $_mremail, $mailbody);
                    $mailbody = str_replace("{sender_phone}", $_userphone, $mailbody);
                    $jsonresult = array('error' => '0', 'templatebody' => $mailbody);
                }
            } else {
                if (file_exists($path . "templates/mail" . $fileid . ".txt")) {
                    $mailbody = file_get_contents($path . "templates/mail" . $fileid . ".txt");
                    $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
                    $mailbody = str_replace("{user}", $_username, $mailbody);
                    $mailbody = str_replace("{rated_by}", $ratedby, $mailbody);
                    $mailbody = str_replace("{introducee}", $suggestname, $mailbody);
                    $mailbody = str_replace("{introducee_profession}", $profession, $mailbody);
                    $mailbody = str_replace("{introducee_email}", $suggestemail, $mailbody);
                    $mailbody = str_replace("{introducee_phone}", $phone, $mailbody);
                    $mailbody = str_replace("{user}", $_username, $mailbody);
                    $mailbody = str_replace("{user_email}", $_mremail, $mailbody);
                    $mailbody = str_replace("{sender_phone}", $_userphone, $mailbody);
                    $jsonresult = array('error' => '0', 'templatebody' => $mailbody);
                }
            }

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No email template found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read introduction/referral template
$app->post('/referralmail/read/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $fileid = $allPostPutVars['templateid'];
    $receipentname = $allPostPutVars['receipentname'];
    $receipentemail = $allPostPutVars['to'];
    $ccname1 = $allPostPutVars['ccname1'];
    $suggestname = $allPostPutVars['suggestname'];
    $profession = $allPostPutVars['profession'];
    $suggestemail = $allPostPutVars['suggestemail'];
    $phone = $allPostPutVars['phone'];
    $clientid = $allPostPutVars['clientid'];
    $_username = $allPostPutVars['username'];
    $_useremail = $allPostPutVars['useremail'];
    $_userphone = $allPostPutVars['userphone'];
    $suggestid = $allPostPutVars['suggestid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from  mc_user  where id = (select user_id from  user_people where id='$suggestid')  ";

        $sql_query = "  select * from  user_people where id='$suggestid' ";

        $ref_rst = $pdo->query($sql_query);

        if ($ref_rst->rowCount() > 0) {
            $ref_det = $ref_rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $sql_query = " select * from  mc_user  where id = '" . $ref_det['user_id'] . "' ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $ratedby = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['username'];
                $ds = DIRECTORY_SEPARATOR;
                $apppath = '';
                $path = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds;
                if (file_exists($path . "templates/introduction_mail_referal.txt")) {
                    $mailbody = file_get_contents($path . "templates/introduction_mail_referal.txt");
                    $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
                    $mailbody = str_replace("{user}", $_username, $mailbody);
                    $mailbody = str_replace("{user_email}", $_useremail, $mailbody);
                    $mailbody = str_replace("{sender_phone}", $_userphone, $mailbody);
                    $mailbody = str_replace("{rated_by}", $ratedby, $mailbody);
                    $mailbody = str_replace("{introducee}", $suggestname, $mailbody);
                    $mailbody = str_replace("{introducee_profession}", $profession, $mailbody);
                    $mailbody = str_replace("{introducee_email}", $suggestemail, $mailbody);
                    $mailbody = str_replace("{introducee_phone}", $phone, $mailbody);
                    $mailbody = str_replace("{introducee_zip}", $ref_det['client_zip'], $mailbody);
                    $jsonresult = array('error' => '0', 'templatebody' => $mailbody);
                } else
                    $jsonresult = array('error' => '10', 'errmsg' => 'No email template found!');
            } else
                $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No referral data found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/mailtemplates/rated6/', function (Request $request, Response $response) {


    $allPostPutVars = $request->getParsedBody();
    $fileid = $allPostPutVars['templateid'];
    $receipentname = $allPostPutVars['receipentname'];
    $receipentemail = $allPostPutVars['to'];
    $knowid = $allPostPutVars['knowid'];
    $partnerid = $allPostPutVars['partnerid'];
    $useremail = $allPostPutVars['useremail'];
    $_username = $allPostPutVars['username'];
    $target_voc = '';

    $sender = '';
    $receipent = '';
    $url = '';
    $sender_city = '';
    $vocations = '';

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //get know details
        $sql_query = "select client_name from  user_people where id='$knowid'  ";
        $rst_know = $pdo->query($sql_query);
        if ($rst_know->rowCount() > 0) {
            $know_row = $rst_know->fetchAll(PDO::FETCH_OBJ);
            $knowname = $know_row[0]->client_name;
            $knowname_arr = explode('.', $knowname);
            $knowname = implode(' ', array_filter($knowname_arr));
            $knowname_arr = explode(',', $knowname);
            $knowname = implode(' ', array_filter($knowname_arr));
            $knowname_arr = explode(' ', $knowname);
            $knowname = implode('-', array_filter($knowname_arr));
        }


        //get partner details
        $sql_query = "select a.id, username, user_shortcode, b.city , b.vocations
		from mc_user as a inner join user_details as b 
		on a.id=b.user_id 
		where a.id='$partnerid'  ";
        $rst_user = $pdo->query($sql_query);
        if ($rst_user->rowCount() > 0) {
            $user_row = $rst_user->fetchAll(PDO::FETCH_OBJ);
            $partnername = $user_row[0]->username;
            $sender = $partnername;

            $sender_city = $user_row[0]->city;
            $vocations = $user_row[0]->vocations;

            $partername_arr = explode('.', $partnername);
            $partnername = implode(' ', array_filter($partername_arr));
            $partername_arr = explode(',', $partnername);
            $partnername = implode(' ', array_filter($partername_arr));
            $partername_arr = explode(' ', $partnername);
            $partnername = implode('-', array_filter($partername_arr));

        }
        $hash = md5($knowid . $partnerid);

        //saving invite log
        $invitelogid = 0;
        $sql_query = "select count(*) as rcnt from  mc_invite_know_log 
		where hash_id =  '$hash'";

        $rst_log = $pdo->query($sql_query);
        if ($rst_log->rowCount() > 0) {
            $member_row = $rst_log->fetchAll(PDO::FETCH_OBJ);
            if ($member_row[0]->rcnt == 0) {
                $sql_query = "select count(*) as ecnt from mc_invite_know_log  where partner_id='$partnerid' and know_name='$knowname'";
                $ecnt_rs = $pdo->query($sql_query);
                $ecnt_row = $ecnt_rs->fetchAll(PDO::FETCH_OBJ);
                $suffix = $ecnt_row[0]->ecnt;
                if ($suffix > 0) {
                    $knowname = $knowname . "-" . ($suffix + 1);
                }

                //inserting into log
                $sql_query = "insert into mc_invite_know_log  
				(know_id, know_name, partner_id, partner_name, hash_id, send_date) 
				values  
				( '$knowid', '$knowname', '$partnerid', '$partnername', '$hash',   NOW() ) ";
                $pdo->query($sql_query);
                $invitelogid = $pdo->lastInsertId();
            }

            $url = "https://mycity.com/profile/invite/" . $partnername . "/" . $knowname;

        }

        //check member rating
        $member_rank = 0;
        $sql_query = "select avg(ranking) as rank from  mc_user_rating  where user_id='$partnerid'  ";
        $rst_ranking = $pdo->query($sql_query);
        if ($rst_ranking->rowCount() > 0) {
            $rank_row = $rst_ranking->fetchAll(PDO::FETCH_OBJ);
            $member_rank = ceil($rank_row[0]->rank);
        }


        //sending email
        $sql_query = "select * from  user_answers  where user_id='$knowid'  ";
        $rst_answer = $pdo->query($sql_query);
        if ($rst_answer->rowCount() > 0) {
            $row = $rst_answer->fetchAll(PDO::FETCH_OBJ);
            $target_voc = implode(',', array_filter(explode(',', $row[0]->answer)));
        }
        $hashcode = md5($knowid . $partnerid);
        $ds = DIRECTORY_SEPARATOR;
        $apppath = '';
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds;
        if (file_exists($path . "templates/mail" . $fileid . ".txt")) {
            $mailbody = file_get_contents($path . "templates/mail" . $fileid . ".txt");
            $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
            $mailbody = str_replace("{vocation}", $vocations, $mailbody);
            $mailbody = str_replace("{member}", $partnername, $mailbody);
            $mailbody = str_replace("{know}", $knowname, $mailbody);

            $mailbody = str_replace("{sender}", $sender, $mailbody);
            $mailbody = str_replace("{receipent}", $receipent, $mailbody);
            $mailbody = str_replace("{landing_url}", $url, $mailbody);
            $mailbody = str_replace("{profile}", "https://mycity.com/profile/" . $partnerid . "?fil=" . $hash, $mailbody);
            $mailbody = str_replace("{sender_location}", $sender_city, $mailbody);

            if ($member_rank > 0) {
                $ranking_html = "<p>" . $sender . " is rated: " . $member_rank . " stars.<p>";

                $mailbody = str_replace("{rating_info}", $ranking_html, $mailbody);
            } else {
                $mailbody = str_replace("{rating_info}", " ", $mailbody);
            }
            $jsonresult = array('error' => '0', 'templatebody' => $mailbody, 'errmsg' => 'Mail template successfully fetched.');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());

    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;


});


//read mail template and send introduction email for wizard
$app->post('/email/introduction/send/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $fileid = $allPostPutVars['templateid'];
    $suggestemail = $allPostPutVars['suggestemail'];
    $suggestname = $allPostPutVars['suggestname'];
    $suggestid = $allPostPutVars['suggestid'];
    $email = $allPostPutVars['to'];
    $profession = $allPostPutVars['profession'];
    $phone = $allPostPutVars['phone'];
    $receipentname = $allPostPutVars['receipentname'];
    $receipentprof = $allPostPutVars['receipentprof'];
    $receipentphone = $allPostPutVars['receipentphone'];
    $clientid = $allPostPutVars['clientid'];
    $mailogid = $allPostPutVars['mailogid'];
    $cc1 = $allPostPutVars['cc1'];
    $ccname1 = $allPostPutVars['ccname1'];
    $userid = $allPostPutVars['userid'];
    $_username = $allPostPutVars['username'];
    $_useremail = $allPostPutVars['mremail'];
    $emailbody = $allPostPutVars['emailbody'];

    $ds = DIRECTORY_SEPARATOR;
    $apppath = '';
    $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
    $html = '';
    //check if a refferalsuggestion entry exists. If not make an entry
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from referralsuggestions where id='$mailogid'";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            if ($rst->fetchAll(PDO::FETCH_ASSOC)[0]['emailstatus'] == 1) {
                $jsonresult = array('error' => '0', 'errmsg' => "An email was already sent earlier!");
            }
        } else {

            $sql_query = "select * from  mc_user  where id = (select user_id from  user_people where id='$clientid') ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $rs_rows = $rst->fetchAll(PDO::FETCH_ASSOC);
                $ratedby = $rs_rows[0]['username'];


                $refcounter = $pdo->query("select count(*) as refcnt from  referralsuggestions 
				where   knowtorefer='$suggestid' and knowreferedto='$clientid' and knowenteredby='$userid'")->fetchAll(PDO::FETCH_ASSOC);
                if ($refcounter[0]['refcnt'] == 0) {
                    $refcounter = $pdo->query("select user_id from  user_people  where id='$suggestid'");
                    if ($refcounter->rowCount() > 0) {
                        $pdo->query("insert into referralsuggestions 
							( partnerid, knowtorefer,knowreferedto, entrydate,  knowenteredby, senton, emailstatus) 
								VALUES ('" . $refcounter->fetchAll(PDO::FETCH_ASSOC)[0]['user_id'] . "', '$suggestid', 
								'$clientid' ,  NOW() ,  '$userid', NOW(), '1'  ) ");
                    }
                }


                if ($_username == $ratedby) {
                    if (file_exists($path . "templates/mailsamemember" . $fileid . ".txt")) {
                        $mailbody = file_get_contents($path . "templates/mailsamemember" . $fileid . ".txt");
                        $mailbody = str_replace("{receipent}", $receipentname, $mailbody);
                        $mailbody = str_replace("{rated_by}", $ratedby, $mailbody);
                        $mailbody = str_replace("{introducee}", $suggestname, $mailbody);
                        $mailbody = str_replace("{introducee_profession}", $profession, $mailbody);
                        $mailbody = str_replace("{introducee_email}", $suggestemail, $mailbody);
                        $mailbody = str_replace("{introducee_phone}", $phone, $mailbody);
                        $emailbody = $html = $mailbody;
                    }
                } else {
                    $html = $emailbody;
                }

                if (file_exists($path . "templates/introductionmail01.txt")) {
                    $filecontent = file_get_contents($path . "templates/introductionmail01.txt");
                    $html = str_replace("{body}", $html, $filecontent);
                }

                //sending mail
                $to = $email;
                $subject = 'Introduction/Referral from ' . $_username;
                $headers = "From: referrals@mycity.com\r\n";
                $headers .= "Reply-To: referrals@mycity.com\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                //mail towards the new connect
                $mailexistcheck = $pdo->query("select COUNT(*) as ecnt from mailbox where sender='$userid' AND receipent='$clientid' AND suggestedconnectid='$suggestid' ");
                $ecnt = $mailexistcheck->fetchAll(PDO::FETCH_ASSOC)[0]['ecnt'];
                //if( $ecnt )
                // if( $ecnt == 0)

                $stmt = $pdo->prepare("insert into mailbox 
				(sender, receipent, subject, mailbody, senton, suggestedconnectid) VALUES 
				(?,?,?,?,NOW(), ? )");
                $stmt->execute(array($userid, $clientid, $subject, $html, $suggestid));


                $stmt = $pdo->prepare("insert into mc_mailbox 
				(sender,sender_id, receipent, subject , emailbody , emailstatus , email_type , senton, receipent_id ) VALUES 
				(?,?, ?, ?, ?,  '0', '0', NOW() , ?)");

                $stmt->execute(array($_useremail, $userid, $email, $subject, $emailbody, $clientid));

                //mark as mail sent
                //$link->query("UPDATE referralsuggestions SET emaillog='" .  $link->real_escape_string($html) . "' , emailstatus='1',  senton=NOW()  WHERE id='$mailogid' ");
                // if(  mail($to, $subject, $html, $headers) == TRUE  )
                //activate when mail code is working

                $mailstatus = sendreferralmail($to, $subject, $html, $html, $_user_email, $_username, $cc1, $ccname1);

                //preparing notifier email for partner
                $notifiermail = "";
                if (file_exists($path . "templates/introductionmailnotifier01.txt")) {
                    $filecontent = file_get_contents($path . "templates/introductionmailnotifier01.txt");
                    $filecontent = str_replace("{username}", $_username, $filecontent);
                    $filecontent = str_replace("{partner}", $suggestname, $filecontent);
                    $filecontent = str_replace("{profession}", $profession, $filecontent);
                    $filecontent = str_replace("{suggestemail}", $suggestemail, $filecontent);
                    $filecontent = str_replace("{phone}", $phone, $filecontent);
                    $filecontent = str_replace("{receipentname}", $receipentname, $filecontent);
                    $filecontent = str_replace("{receipentprof}", $receipentprof, $filecontent);
                    $filecontent = str_replace("{email}", $email, $filecontent);
                    $filecontent = str_replace("{receipentphone}", $receipentphone, $filecontent);
                    $notifiermail = $filecontent;
                }

                //activate when mail code is working
                $mailstatus = sendemail($cc1, 'Referral suggestion for one of your connection sent', $notifiermail, $notifiermail);

                if ($mailstatus == 1) {
                    //send another email back to sender
                    $sendernotifierhtml = "";
                    if (file_exists($path . "templates/introductionmailnotifier02.txt")) {
                        $filecontent = file_get_contents($path . "templates/introductionmailnotifier02.txt");
                        $filecontent = str_replace("{username}", $_username, $filecontent);
                        $filecontent = str_replace("{partner}", $suggestname, $filecontent);
                        $filecontent = str_replace("{receipentname}", $receipentname, $filecontent);
                        $filecontent = str_replace("{receipentprof}", $receipentprof, $filecontent);
                        $filecontent = str_replace("{to}", $to, $filecontent);
                        $filecontent = str_replace("{receipentphone}", $receipentphone, $filecontent);
                        $filecontent = str_replace("{body}", $mailbody, $filecontent);
                        $sendernotifierhtml = $filecontent;
                    }

                    //activate when mail code is working
                    sendemail($sendernotifierhtml, $subject, $html, $html);
                }
            }

        }
        $jsonresult = array('error' => '0', 'errmsg' => "Introduction email sent!");
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get search log by vocations
$app->post('/logs/vocationsearch/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];
    $pagesize = $allPostPutVars['pagesize'];


    if ($pagesize <= 0) $pagesize = 10;
    $start = ($goto - 1) * $pagesize;


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select vsl.*,mu.username from  vocation_search_logs as vsl inner join mc_user as mu 
		on vsl.user_id=mu.id order by created_at desc limit $start,10";

        $sql_query_count = " select count(*) as reccnt from  vocation_search_logs as vsl inner join mc_user as mu 
		on vsl.user_id=mu.id  ";


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $results = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Search log found!', 'result' => $results);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No search log found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get search log by vocations
$app->post('/logs/homesearch/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];
    $pagesize = $allPostPutVars['pagesize'];

    if ($pagesize <= 0) $pagesize = 10;
    $start = ($goto - 1) * $pagesize;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from  home_search_log order by created_at desc limit $start,10 ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//searching a know using phone or name
$app->post('/knows/search/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $key = $allPostPutVars['key'];
    $goto = $allPostPutVars['goto'];
    $name = '%' . $key . '%';
    $phone = $key;
    $location = $allPostPutVars['location'];
    $voclist = $allPostPutVars['vocations'];        //get all cities 	$allcities = array();	//$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vTMQEU2B-XHhfdJvRbMMvWoAAFJYOvDcVLS6N39T_NUKPqAu76bg6GQUlUUxSCw9k2prtQhHkRtSdcN/pub?output=csv";		$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vTPil2UDQRQolqgdVjn8oYQ4mKtZYtnU5EaVCv5N8rsAMhH9pJMBQmI_Kn9-nRzoUi0cCZaWxCYMUud/pub?gid=0&single=true&output=csv";			if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";	$handle =   fopen($spreadsheet_url, "r");	if ( $handle  !== FALSE) 	{		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 		{			$allcities[] = $data;		} 		fclose($handle);	}
    if (isset($voclist) && $voclist != '' && $voclist != 'null') {
        $vocations = explode(',', $voclist);

        if (!empty($vocations)) {

            $searchVoc = " AND  client_profession  IN (";
            $vocationswhere = "";

            foreach ($vocations as $item) {
                $vocationswhere .= "'$item',";
            }

            $vocationswhere = rtrim($vocationswhere, ",");
            $searchVoc .= $vocationswhere . ")";
        }
    }


    $tagslist = $allPostPutVars['tags'];
    if (isset($tagslist) && $tagslist != '' && $tagslist != 'null') {
        $searchtags = explode(',', $tagslist);

        if (!empty($searchtags)) {

            $searchTag = " ";

            for ($i = 0; $i < sizeof($searchtags); $i++) {
                $searchTag .= " FIND_IN_SET ( '" . $searchtags[$i] . "' , p.tags) ";

                if ($i < sizeof($searchtags) - 1) {
                    $searchTag .= " OR ";
                }
            }

            $searchTag = " AND ( " . $searchTag . ")";
        }
    }

    $lifestylelist = $allPostPutVars['lifestyle'];
    if (isset($lifestylelist) && $lifestylelist != '' && $lifestylelist != 'null') {
        $searchlifestyles = explode(',', $lifestylelist);

        if (!empty($searchlifestyles)) {

            $searchLifeStyle = " ";

            for ($i = 0; $i < sizeof($searchlifestyles); $i++) {
                $searchLifeStyle .= " p.client_lifestyle =  '" . $searchlifestyles[$i] . "'";

                if ($i < sizeof($searchlifestyles) - 1) {
                    $searchLifeStyle .= " OR ";
                }
            }

            $searchLifeStyle = " AND ( " . $searchLifeStyle . ")";
        }
    }


    $size = 10;
    $start = ($goto - 1) * $size;

    $location_where = '';
    if ($location != '') {
        $location_where = " and p.client_location='$location'";
    }
    $zip = $allPostPutVars['zip'];
    $zip_where = '';
    if ($zip != '') {
        $zip_where = " and p.client_zip='$zip'";
    }


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select   u.id , u.user_email , u.username , u.user_role , u.user_pkg ,  u.user_phone  , p.tags, 
		 user_id, client_name, client_email, client_profession, client_phone , p.client_zip, p.client_location , p.id as knowid, 0 as ranking from user_people as p inner join mc_user as u on p.user_id=u.id 
		where ( p.client_name like '$name' or p.client_phone like '$phone%' ) " . $location_where . $zip_where . $searchVoc . $searchTag . $searchLifeStyle . " LIMIT $start,$size ";

        $sql_query_count = " select count(*) as reccnt from user_people as p inner join mc_user as u on p.user_id=u.id 
	   where  ( p.client_name like '$name' or p.client_phone= '$phone' ) " . $location_where . $zip_where . $searchVoc . $searchTag . $searchLifeStyle;

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $i = 0;
            foreach ($result as $row) {
                $rate_q = $pdo->query("select SUM(ranking) as user_ranking from user_rating where user_id = '" . $row['knowid'] . "'");
                if ($rate_q->rowCount() > 0) $result[$i]['ranking'] = $rate_q->fetchAll(PDO::FETCH_ASSOC)[0]['user_ranking'];
                if ($row['client_zip'] == '' && $row['client_location'] != '' && sizeof($allcities) > 0) {
                    for ($ci = 0; $ci < sizeof($allcities); $ci++) {
                        if ($allcities[$ci][0] == $row['client_location']) {
                            $result[$i]['client_zip'] = $allcities[$ci][2];
                            $pdo->query("update user_people set client_zip='" . $allcities[$ci][2] . "' where id = '" . $row['knowid'] . "'");
                            break;
                        }
                    }
                }
                $i++;
            }

            $jsonresult = array('error' => '0', 'errmsg' => "Search completed!",
                'pages' => $pages, 'results' => $result);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => "No matching know information found!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'An error occured!');
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//saving a contact
$app->post('/knows/add/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $user_id = $allPostPutVars['user_id'];
    $client_name = $allPostPutVars['client_name'];
    $client_pro = $allPostPutVars['client_pro'];
    $client_ph = $allPostPutVars['client_ph'];
    $client_email = $allPostPutVars['client_email'];
    $client_location = $allPostPutVars['client_location'];
    $client_zip = $allPostPutVars['client_zip'];
    $client_note = $allPostPutVars['client_note'];
    $user_grp = $allPostPutVars['user_grp'];
    $client_lifestyle = $allPostPutVars['client_lifestyle'];
    $client_tags = $allPostPutVars['client_tags'];
    $ques_rate = $allPostPutVars['ques_rate'];
    $ques_text = $allPostPutVars['user_ques_text'];
    $ques = $allPostPutVars['ques'];

    $alltags = explode(",", $client_tags);

    if (in_array("Rated 25", $alltags)) {
        $table = 'user_people_rated';
    } else {
        $table = 'user_people';
    }


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //reset sortorder field
        $pdo->query("update user_people set showfirst='0' where user_id = '$user_id' and showfirst='1' ");
        if ($id == 0) {
            $sql_query = "INSERT  INTO user_people (user_id, client_name, client_profession, client_phone,
			client_email, client_location, client_zip, client_note, user_group, entrydate, client_lifestyle, tags, showfirst) 
			VALUES ( ? , ? , ? , '$client_ph', '$client_email', '$client_location', '$client_zip', 
			'$client_note', '$user_grp', NOW(), '$client_lifestyle', '$client_tags' , '1')";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($user_id, $client_name, $client_pro));
            $knowid = $pdo->lastInsertId();
            $totalrank = 0;
            for ($i = 0; $i < count($ques_rate); $i++) {
                $totalrank += $ques_rate[$i];
                $pdo->query("INSERT INTO user_rating (user_id, question_id, ranking) VALUES ('$knowid', '" . $ques[$i] . "', '" . $ques_rate[$i] . "')");
            }
            for ($i = 0; $i < count($ques_text); $i++) {
                $q_id = $ques_text[$i][id];
                $answer = $ques_text[$i][answer];
                if ($answer) {

                    $pdo->query("INSERT INTO user_answers (user_id, question_id,  answer) values('$knowid', '" . $q_id . "', '" . $answer . "')");
                }
            }
            $sql_query = "update user_people set total_rank='$totalrank'  where id='$knowid' ";
            $pdo->query($sql_query);
            $jsonresult = array('error' => '0', 'errmsg' => 'Know information saved successfully!', 'knowid' => $knowid, 'action' => 'i', 'query' => $sql_query);
        } else {
            $pdo->query("update user_people set client_name = '$client_name', 
			client_profession= '$client_pro', client_phone= '$client_ph', client_email = '$client_email', 
			client_location = '$client_location',client_zip = '$client_zip', 
			client_note = '$client_note', user_group = '$user_grp',  
			client_lifestyle='$client_lifestyle', updatedate=NOW() ,tags =  '$client_tags', refgenerated='10' , showfirst='1' , check_fields='100000'  WHERE id = '$id'");
            $pdo->query("DELETE FROM user_rating WHERE user_id = '$id'");
            $pdo->query("DELETE FROM user_answers WHERE user_id = '$id'");

            $totalrank = 0;
            for ($i = 0; $i < count($ques_rate); $i++) {
                $totalrank += $ques_rate[$i];
                $pdo->query("INSERT INTO user_rating (user_id, question_id, ranking) VALUES ('$id', '" . $ques[$i] . "', '" . $ques_rate[$i] . "')");
            }
            for ($i = 0; $i < count($ques_text); $i++) {
                $q_id = $ques_text[$i][id];
                $answer = $ques_text[$i][answer];
                if ($answer) {
                    $pdo->query("INSERT INTO user_answers (user_id, question_id,  answer) values('$id', '" . $q_id . "', '" . $answer . "')");
                }
            }
            $jsonresult = array('error' => '0', 'errmsg' => 'Know information updated successfully!', 'action' => 'u', 'query' => $sql_query);
            //save/log  changes in lifestyle
            if ($client_lifestyle != '') {
                $pdo->query("INSERT INTO mc_know_update_log (know_id, change_area,  entrydate) values('$id', 'Lifestyle', NOW()  )");
            }
            $sql_query = "update user_people set total_rank='$totalrank' where id='$id' ";
            $pdo->query($sql_query);

            if (in_array("3 Touch", $alltags)) {
                $sql_query = "select clients_selected from mc_program_client where client_id='$user_id' and program_id='1'";

                $rst = $pdo->query($sql_query);
                $rscount = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
                $data = array();
                if ($rscount['clients_selected'] != '') {
                    $data = json_decode($rscount['clients_selected'], TRUE);
                }
                $data[(sizeof($data) + 1)] = $id;
                $sql_query = "update mc_program_client set clients_selected=? where client_id= ?  and program_id= ? ";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array(json_encode($data), $user_id, 1));
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage(), 'query' => $sql_query);
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//saving a contact
$app->post('/knows/rating/save/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $ques_rate = $allPostPutVars['ques_rate'];
    $ques = $allPostPutVars['ques'];
    $id = $allPostPutVars['id'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "INSERT  INTO user_people (user_id, client_name, client_profession, client_phone, client_email, client_location, client_zip, client_note, user_group, entrydate, client_lifestyle) 
		VALUES ('$user_id', '$client_name', '$client_pro', '$client_ph', '$client_email', '$client_location', '$client_zip', 
		'$client_note', '$user_grp', NOW(), '$client_lifestyle' )";

        for ($i = 0; $i < count($ques_rate); $i++) {
            $sql_query = "insert into user_rating (user_id, question_id, ranking) VALUES  (?,?,?)";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($id, $ques[$i], $ques_rate[$i]));
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'Know rating saved successfully!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//know mapping
$app->post('/knows/mapping/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['uid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userrst = $pdo->query("SELECT * FROM user_details where user_id = '$userid'");
        $user = $userrst->fetchAll(PDO::FETCH_ASSOC)[0];
        $groups = explode(",", $user['groups']);

        $sql_query = "select p.*, a.answer from user_people_rated as p inner join user_answers as a on p.id=a.user_id where p.user_id='$userid' and refgenerated in (0, 10)  and a.answer <> 'null' order by  p.id desc , p.updatedate desc  ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            //work only for the first row
            $allnewknows = $rst->fetchAll(PDO::FETCH_ASSOC);
            $actualrefgenerate = 0;
            for ($reccnt = 0; $reccnt < $rst->rowCount(); $reccnt++) {
                $newknows = $allnewknows[$reccnt];
                $knowprofessions = explode(",", $newknows['client_profession']);
                $interestedprofessions = $newknows['answer'];
                $newknowid = $newknows['id'];
                $sourcezip = $newknows['client_zip']; //zip code of the new know
                //mark referral suggestion
                $pdo->query("update  user_people  set refgenerated='1' where id='$newknowid'");
                if ($sourcezip == '') {
                    continue;
                }

                //second making main query
                $professionlist = explode(",", $interestedprofessions);
                $where_group = " ( ";
                for ($i = 0; $i < sizeof($professionlist); $i++) {
                    $where_group .= " find_in_set ( '" . $professionlist[$i] . "' , client_profession  ) ";
                    if ($i < sizeof($professionlist) - 1) {
                        $where_group .= " OR ";
                    }
                }
                $where_group .= " ) ";
                //first getting subquery for retrieving partners
                $where_in_set = " (  ";
                for ($i = 0; $i < sizeof($groups); $i++) {
                    $groupid = $groups[$i];
                    $where_in_set .= " FIND_IN_SET('$groupid', groups) ";
                    if ($i < sizeof($groups) - 1) {
                        $where_in_set .= " OR ";
                    }
                }

                $where_in_set .= " ) ";
                $qryInner = " SELECT a.user_id FROM user_details as a inner join mc_user as b on b.id = a.user_id 
			WHERE $where_in_set  AND  b.id != '1' and user_pkg='Gold'  ";

                $mainQry = "SELECT p.*,  SUM(r.ranking) as rank 
			FROM user_people_rated as p INNER JOIN user_rating as r on p.id=r.user_id 
			WHERE p.user_id IN  ( $qryInner )  AND " . $where_group . " GROUP BY p.id ORDER BY client_name";
                $matchingknowrst = $pdo->query($mainQry);
                if ($matchingknowrst->rowCount() > 0) {
                    $matchingknows = $matchingknowrst->fetchAll(PDO::FETCH_ASSOC);
                    $memberrank = usort($matchingknows, memberrank);

                    $actualrefgenerate++;
                    $pos = 1;
                    foreach ($matchingknows as $row) {
                        $id = $row['id'];
                        $user_ranking = $row['rank'];
                        $targetknowprofession = explode(",", $row['client_profession']);
                        $matchingprofession = array_intersect($knowprofessions, $targetknowprofession);

                        if ($user_ranking < 20) {
                            break;
                        }
                        // Count how many times each value exists
                        $matchingprofessioncount = array_count_values($matchingprofession);
                        $tmp = array_filter($matchingprofessioncount);

                        $rsrating = $pdo->query("select count(*) as rowcnt from user_rating where user_id='$newknowid' ");
                        $rslocation = $pdo->query("select client_location from user_people_rated where id='$newknowid' ");

                        if ($rslocation->rowCount() > 0) {
                            $clientlocationfield = $rslocation->fetchAll(PDO::FETCH_ASSOC)[0]['client_location'];
                        } else {
                            $clientlocationfield = '';
                        }

                        if ($clientlocationfield !== NULL &&
                            $clientlocationfield != '' &&
                            $rsrating->fetchAll(PDO::FETCH_ASSOC)[0]['rowcnt'] > 0
                        ) {
                            if ($user_ranking >= 20 && empty($tmp)) {
                                $targetzip = $row['client_zip'];
                                if ($targetzip == '') {
                                    continue;
                                }

                                if ($targetzip != "") {
                                    $existingrefresult = $pdo->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
								WHERE partnerid='" . $row['user_id'] . "' AND 
								knowtorefer='" . $row['id'] . "' AND 
								knowreferedto='$newknowid' AND knowenteredby='$userid' ");
                                    $existingrefcnt = $existingrefresult->fetchAll(PDO::FETCH_ASSOC)[0]['rcnt'];

                                    if ($existingrefcnt > 0) {
                                        $pdo->query("delete from referralsuggestions 
									where  partnerid='" . $row['user_id'] . "' and 
									knowtorefer='" . $row['id'] . "' and knowreferedto='$newknowid' AND knowenteredby='$userid' ");
                                    }

                                    if ($row['user_id'] != $userid) {

                                        //calculate distance
                                        if ($targetzip == $sourcezip) {
                                            $refqry = "INSERT INTO referralsuggestions 
										( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby,
											 sourcezip, targetzip, ranking, distance, distancecalculated) 
											 VALUES ('" . $row['user_id'] . "', '" . $row['id'] . "',
											 '$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,
											 '$targetzip' , '$user_ranking', '0', '1' )";
                                        } else if ($targetzip != '' && $sourcezip != '') {
                                            $zipqry = "select * from  mc_city_geolocation where zip in (" . $targetzip . ", " . $sourcezip . " ) ";
                                            $rsgeolocs = $pdo->query($zipqry);

                                            if ($rsgeolocs->rowCount() == 2) {
                                                $geolocs = $rsgeolocs->fetchAll(PDO::FETCH_ASSOC);
                                                $latitude1 = $geolocs[0]['latitude'];
                                                $longitude1 = $geolocs[0]['longitude'];
                                                $latitude2 = $geolocs[1]['latitude'];
                                                $longitude2 = $geolocs[1]['longitude'];

                                                $theta = $longitude1 - $longitude2;
                                                $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                                                $distance = acos($distance);
                                                $distance = rad2deg($distance);
                                                $distance = $distance * 60 * 1.1515;
                                                switch ($unit) {
                                                    case 'Mi':
                                                        break;
                                                    case 'Km' :
                                                        $distance = $distance * 1.609344;
                                                }

                                                $distance = (round($distance, 2));
                                                $refqry = "INSERT INTO referralsuggestions 
												( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby, 
												sourcezip, targetzip, ranking, distance, distancecalculated) 
												VALUES ('" . $row['user_id'] . "', '" . $row['id'] . "', 
												'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , 
												'$targetzip' , '$user_ranking', '$distance', '1' )";
                                            } else {
                                                $refqry = "INSERT INTO referralsuggestions 
												( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) VALUES 
												('" . $row['user_id'] . "', '" . $row['id'] . "', 
												'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )";
                                            }
                                        }
                                        $pdo->query($refqry);
                                    }
                                }
                            }
                        }

                    }
                }
                if ($actualrefgenerate >= 20) break; //break loop after generating maps for 10 knows
            }
        }

        $jsonresult = array('error' => '0', 'qry' => $mainQry, 'errmsg' => "Automatic referral complete!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'qry' => $mainQry, 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//know mapping
$app->post('/knows/selectivemapping/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['uid'];
    $knowid = $allPostPutVars['knowid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userrst = $pdo->query("SELECT * FROM user_details where user_id = '$userid'");
        $user = $userrst->fetchAll(PDO::FETCH_ASSOC)[0];
        $groups = explode(",", $user['groups']);

        $sql_query = "select p.*, a.answer from user_people as p inner join user_answers as a on p.id=a.user_id where p.user_id='$userid' and  p.id='$knowid' ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            //work only for the first row
            $newknows = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $knowprofessions = explode(",", $newknows['client_profession']);

            $interestedprofessions = $newknows['answer'];
            $newknowid = $newknows['id'];
            $sourcezip = $newknows['client_zip']; //zip code of the new know

            //second making main query
            $professionlist = explode(",", $interestedprofessions);
            $where_group = " ( ";
            for ($i = 0; $i < sizeof($professionlist); $i++) {
                $where_group .= " find_in_set ( '" . $professionlist[$i] . "' , client_profession  ) ";
                if ($i < sizeof($professionlist) - 1) {
                    $where_group .= " OR ";
                }
            }

            $where_group .= " ) ";
            //first getting subquery for retrieving partners
            $where_in_set = " (  ";
            for ($i = 0; $i < sizeof($groups); $i++) {
                $groupid = $groups[$i];
                $where_in_set .= " FIND_IN_SET('$groupid', groups) ";
                if ($i < sizeof($groups) - 1) {
                    $where_in_set .= " OR ";
                }
            }
            $where_in_set .= " ) ";

            $qryInner = " SELECT a.user_id FROM user_details as a inner join mc_user as b on b.id = a.user_id 
			WHERE $where_in_set  AND  b.id != '1' and user_pkg='Gold'  ";

            $mainQry = "SELECT p.*,  SUM(r.ranking) as rank 
			FROM user_people as p INNER JOIN user_rating as r on p.id=r.user_id 
			WHERE p.user_id IN  ( $qryInner )  AND " . $where_group . " GROUP BY p.id ORDER BY client_name";
            $matchingknowrst = $pdo->query($mainQry);
            if ($matchingknowrst->rowCount() > 0) {
                $matchingknows = $matchingknowrst->fetchAll(PDO::FETCH_ASSOC);
                foreach ($matchingknows as $row) {
                    $id = $row['id'];
                    $user_ranking = $row['rank'];
                    $targetknowprofession = explode(",", $row['client_profession']);
                    $matchingprofession = array_intersect($knowprofessions, $targetknowprofession);

                    // Count how many times each value exists
                    $matchingprofessioncount = array_count_values($matchingprofession);
                    $tmp = array_filter($matchingprofessioncount);

                    $rsrating = $pdo->query("select count(*) as rowcnt from user_rating where user_id='$newknowid' ");
                    $rslocation = $pdo->query("select client_location from user_people where id='$newknowid' ");

                    if ($rslocation->rowCount() > 0) {
                        $clientlocationfield = $rslocation->fetchAll(PDO::FETCH_ASSOC)[0]['client_location'];
                    } else {
                        $clientlocationfield = '';
                    }

                    if ($clientlocationfield !== NULL &&
                        $clientlocationfield != '' &&
                        $rsrating->fetchAll(PDO::FETCH_ASSOC)[0]['rowcnt'] > 0
                    ) {
                        if ($user_ranking > 20 && empty($tmp)) {
                            $targetzip = $row['client_zip'];
                            if ($targetzip != "") {
                                $existingrefresult = $pdo->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
								WHERE partnerid='" . $row['user_id'] . "' AND 
								knowtorefer='" . $row['id'] . "' AND 
								knowreferedto='$newknowid' AND knowenteredby='$userid' ");
                                $existingrefcnt = $existingrefresult->fetchAll(PDO::FETCH_ASSOC)[0]['rcnt'];

                                if ($existingrefcnt > 0) {
                                    $pdo->query("delete from referralsuggestions 
									where  partnerid='" . $row['user_id'] . "' and 
									knowtorefer='" . $row['id'] . "' and knowreferedto='$newknowid' AND knowenteredby='$userid' ");
                                }


                                if ($row['user_id'] != $userid) {
                                    //calculate distance
                                    if ($targetzip == $sourcezip) {
                                        $refqry = "INSERT INTO referralsuggestions 
											 ( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby,
											 sourcezip, targetzip, ranking, distance, distancecalculated) 
											 VALUES ('" . $row['user_id'] . "', '" . $row['id'] . "',
											 '$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,
											 '$targetzip' , '$user_ranking', '0', '1' )";
                                    } else {
                                        $zipqry = "select * from  mc_city_geolocation where zip in (" . $targetzip . ", " . $sourcezip . " ) ";
                                        $rsgeolocs = $pdo->query($zipqry);

                                        if ($rsgeolocs->rowCount() == 2) {
                                            $geolocs = $rsgeolocs->fetchAll(PDO::FETCH_ASSOC);
                                            $latitude1 = $geolocs[0]['latitude'];
                                            $longitude1 = $geolocs[0]['longitude'];
                                            $latitude2 = $geolocs[1]['latitude'];
                                            $longitude2 = $geolocs[1]['longitude'];

                                            $theta = $longitude1 - $longitude2;
                                            $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                                            $distance = acos($distance);
                                            $distance = rad2deg($distance);
                                            $distance = $distance * 60 * 1.1515;
                                            switch ($unit) {
                                                case 'Mi':
                                                    break;
                                                case 'Km' :
                                                    $distance = $distance * 1.609344;
                                            }

                                            $distance = (round($distance, 2));

                                            $refqry = "INSERT INTO referralsuggestions 
												 ( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby,
												 sourcezip, targetzip, ranking, distance, distancecalculated) 
												 VALUES ('" . $row['user_id'] . "', '" . $row['id'] . "',
												 '$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,
												 '$targetzip' , '$user_ranking', '$distance', '1' )";
                                        } else {
                                            $refqry = "INSERT INTO referralsuggestions 
												( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) VALUES 
												('" . $row['user_id'] . "', '" . $row['id'] . "', 
												'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )";
                                        }
                                    }
                                    $pdo->query($refqry);
                                }

                            }
                        }
                    }
                }
            }
            //mark referral suggestion
            $pdo->query("update  user_people  set refgenerated='1' where id='$newknowid'");
        }
        $jsonresult = array('error' => '0', 'qry' => $refqry, 'errmsg' => "Automatic referral complete!");
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//inbox grid loading
$app->post('/mails/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['page'];

    $start = ($goto - 1) * 10;
    $size = 10;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select id as a ,  name as b, email as c,  phone as d,  message as e,  senton as f,  isread as g, 
		 isdeleted as h,  company as i from    contacts order by senton desc limit $start, $size";

        $sql_query_count = "select count(*) as reccnt from    contacts order by senton desc ";
        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        if ($rst->rowCount() > 1) {
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!',
                'pages' => $pages, 'result' => $results);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');
        }

    } catch (PDOException $e) {

        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single email
$app->get('/mails/getreferences/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $mailid = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM contacts WHERE id='$mailid'  ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 1)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No matching record found!');

    } catch (PDOException $e) {

        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read a single email
$app->get('/mail/read/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $mailid = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM contacts WHERE id='$mailid'  ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 1)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No matching record found!');
    } catch (PDOException $e) {

        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//check if feedback email has already been sent
$app->get('/mail/feedbackstatus/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $mailid = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select feedbackmailsent from mailbox where id='$mailid'  ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() == 1)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No matching record found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//check if reference email has already been sent
$app->get('/mail/referenceinbox/{userid}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $userid = $request->getAttribute('userid');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select m.*, p.user_id, p.client_name, p.client_profession, p.client_phone, p.client_email, 
		p.client_location, p.client_zip, p.client_note FROM mailbox as m inner join user_people as p on m.sender=p.id 
		where m.receipent='$userid' order by senton desc";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() == 1)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'All caught up! No new email!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//check if reference email has already been sent
$app->get('/mail/reference/{mailid}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $mailid = $request->getAttribute('mailid');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM mailbox  WHERE id='$mailid'  ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() == 1)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

/*
mailbox code
*/


//check if reference email has already been sent
$app->post('/mail/outbox/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];
    $triggermail = $allPostPutVars['triggermail'];
    $sendermail = $allPostPutVars['useremail'];


    $start = ($goto - 1) * 10;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($userid != 1) {
            if ($triggermail == 1) {
                $sql_query = "SELECT  m.id as m_a, m.sender as m_b,  m.receipent as m_c, m.subject as m_d , 
				m.mailbody as m_e, m.senton as m_f,  m.isread as m_g, m.isdeleted as m_h,m.isreply as m_i,  
				m.repliedmailid as m_j,  m.suggestedconnectid as m_k,  m.feedbackmailsent as m_l,m.email_type as m_m, 
				m.reminder_status as m_n , p.user_id as ud, p.client_name as cn, p.client_profession as cp , 
				p.client_phone as cph, 
				p.client_email as ce, p.client_location as cl, p.client_zip as cz, 
				p.client_note as cnote 
				FROM mailbox as m inner join user_people as p on m.receipent=p.id 
				WHERE m.sender='$userid' and m.isdeleted='0'  and m.suggestedconnectid = '-1' 
				ORDER BY senton DESC LIMIT $start,10";

                $sql_query_count = "select count(*) as reccnt 
				from mailbox as m inner join user_people as p on m.receipent=p.id 
				where m.sender='$userid' and m.isdeleted='0' and m.suggestedconnectid = '-1' ";

                $rst = $pdo->query($sql_query);


                if ($rst->rowCount() > 1) {
                    $rst_count = $pdo->query($sql_query_count);
                    $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                    $pages = ceil($result_count[0]['reccnt'] / 10);
                    $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!',
                        'pages' => $pages, 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');
                }
            } else if ($triggermail == 2) //linkedin invite
            {
                $sql_query = "SELECT  m.id as m_a, m.sender as m_b,  m.receipent as m_c, m.subject as m_d , 
				m.mailbody as m_e, m.senton as m_f,  m.isread as m_g, m.isdeleted as m_h,m.isreply as m_i,  
				m.repliedmailid as m_j,  m.suggestedconnectid as m_k,  m.feedbackmailsent as m_l,m.email_type as m_m, 
				m.reminder_status as m_n , u.username,p.user_id as ud, p.client_name as cn, p.client_profession as cp , 
				p.client_phone as cph, p.client_email as ce, p.client_location as cl, p.client_zip as cz, 
				p.client_note as cnote  FROM mailbox as m inner join user_people as p on m.receipent=p.id  
				INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$userid' and m.suggestedconnectid = '-1' and 
				m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0' 
				ORDER BY senton DESC LIMIT $start,10";

                $sql_query_count = "select count(*) as reccnt FROM mailbox as m inner join user_people as p on m.receipent=p.id  
                INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$userid' and m.suggestedconnectid = '-1' and m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0' ";

                $rst = $pdo->query($sql_query);
                if ($rst->rowCount() > 1) {
                    $rst_count = $pdo->query($sql_query_count);
                    $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                    $pages = ceil($result_count[0]['reccnt'] / 10);
                    $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!',
                        'pages' => $pages, 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');
                }
            } else if ($triggermail == 0) {
                $sql_query = "SELECT  m.id as m_a, m.sender as m_b,  m.receipent as m_c, m.subject as m_d , 
				m.mailbody as m_e, m.senton as m_f,  m.isread as m_g, m.isdeleted as m_h,m.isreply as m_i,  
				m.repliedmailid as m_j,  m.suggestedconnectid as m_k,  m.feedbackmailsent as m_l,m.email_type as m_m, 
				m.reminder_status as m_n ,  p.user_id as ud, p.client_name as cn, p.client_profession as cp, p.client_phone  as cph,  
				p.client_email as ce, p.client_location as cl, p.client_zip as cz, p.client_note  as cnote
				FROM mailbox as m inner join user_people as p on m.receipent=p.id 
				WHERE m.sender='$userid' and m.isdeleted='0'  and m.suggestedconnectid <> '-1' 
				ORDER BY senton DESC LIMIT $start,10";

                $sql_query_count = "select count(*) as reccnt 
				from mailbox as m inner join user_people as p on m.receipent=p.id 
				where m.sender='$userid' and m.isdeleted='0' and m.suggestedconnectid <> '-1'  ";

                $rst = $pdo->query($sql_query);
                if ($rst->rowCount() > 1) {
                    $rst_count = $pdo->query($sql_query_count);
                    $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                    $pages = ceil($result_count[0]['reccnt'] / 10);
                    $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!',
                        'pages' => $pages, 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');
                }
            } else if ($triggermail == 3) {
                $sender = '';

                $sql_query = "select a.subject as a, a.emailbody as b, a.senton as c, a.email_type as d, a.emailstatus as e, " .
                    " a.id as id, b.username as f, b.user_pkg as g, b.user_phone as h, b.user_email as i  from  mc_mailbox as a " .
                    " inner join mc_user as b on a.receipent=b.user_email where sender='$sendermail' and email_type='0' order by a.senton desc limit $start,10";

                $sql_query_count = "select count(*) as reccnt  from  mc_mailbox as a inner join mc_user as b on a.receipent=b.user_email where sender='$sendermail'  and email_type='0'  ";

                $rst = $pdo->query($sql_query);
                if ($rst->rowCount() > 0) {
                    $rst_count = $pdo->query($sql_query_count);
                    $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
                    $pages = ceil($result_count[0]['reccnt'] / 10);
                    $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!',
                        'pages' => $pages, 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => $sql_query . 'No email found!');
                }
            }

        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//check if reference email has already been sent
$app->post('/mail/inbox/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $receipent = $allPostPutVars['receipent'];
    $mailtype = $allPostPutVars['mailtype']; // 0 for direct email 10 for connection request
    $goto = $allPostPutVars['page'];


    $start = ($goto - 1) * 10;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sender = '';
        $sql_query = "select a.subject as a, a.emailbody as b, a.senton as c, a.email_type as d, a.emailstatus as e, " .
            " a.id as id, a.sender as s, b.id as partnerid, b.username as f, b.user_pkg as g, b.user_phone as h, b.user_email as i  from  mc_mailbox as a " .
            " inner join mc_user as b on a.sender=b.user_email where receipent='$receipent' and email_type='$mailtype' and b.user_status='1' order by a.senton desc limit $start,10";

        $sql_query_count = "select count(*) as reccnt  from  mc_mailbox as a inner join mc_user as b on a.sender=b.user_email where  receipent='$receipent' and email_type='$mailtype' and b.user_status='1' ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!', 'pages' => $pages, 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No email found!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read blog posts
$app->get('/blog/read/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $postid = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($postid == 0)
            $sql_query = "select * from blogs";
        else
            $sql_query = "select * from blogs where id='" . $postid . "'";

        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No blog post found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read blog posts
$app->get('/blog/post/{id}/{role}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $postid = $request->getAttribute('id');
    $role = $request->getAttribute('role');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($role == 'admin') {
            if ($postid == 0) {
                $sql_query = "SELECT * FROM  blog_details  ";
            } else
                $sql_query = "SELECT * FROM  blog_details  WHERE  id  = '$postid'";

            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0)
                $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
            else
                $jsonresult = array('error' => '10', 'errmsg' => 'No blog post found!');
        } else {
            $jsonresult = array('error' => '900', 'errmsg' => 'You don\'t have access right!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read all faqs
$app->get('/faqs/{id}/{role}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    $role = $request->getAttribute('role');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($id == 0)
            $sql_query = "SELECT * FROM helps ORDER BY position asc";
        else
            $sql_query = "SELECT * FROM helps   WHERE  id  = '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No FAQ entry found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all triggers
$app->get('/triggers/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($id == 0)
            $sql_query = "select * from my_triggers";
        else
            $sql_query = "select * from my_triggers where id  = '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No trigger question found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all post or a single post
$app->get('/posts/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = " SELECT * FROM blog_posts order by post_date desc ";
        else
            $sql_query = " SELECT * FROM blog_posts where id='$id' ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

//Search member by name as key
$app->get('/post/save/{role}/{savepost}', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $role = $request->getAttribute('role');
    $savepost = $request->getAttribute('savepost');

    $allPostPutVars = $request->getParsedBody();
    $parent_id = $allPostPutVars['parent_id'];
    $subject = $allPostPutVars['subject'];
    $message = $allPostPutVars['message'];

    try {
        if ($role == 'admin') {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($savepost == 2) {
                $postid = $_POST['postid'];
                $title = $link->real_escape_string($_POST['title']);
                $post = $link->real_escape_string($_POST['content']);
                $id = $link->real_escape_string($_POST['content']);
                $link->query("UPDATE blog_posts SET  post_title= '$title', post_content = '$post' WHERE id='$postid' ");
            } else if ($savepost == 3) {
                //save status
                $status = $_POST['status'];
                $postid = $_POST['postid'];
                $link->query("UPDATE blog_posts SET  post_status= '$status'  WHERE id='$postid' ");
            } else if ($savepost == 1) {
                $title = $link->real_escape_string($_POST['title']);
                $post = $link->real_escape_string($_POST['content']);
                $link->query("INSERT INTO blog_posts ( post_author, post_date, post_title, post_content ) 
				VALUES ('admin', NOW(), '$title', '$post')");
            }


            $sql_query = "insert into sv_parent_enquiry (subject, enquirybody, parent_id, enquirydate  ) 
				values ( ?, ?, ?, NOW() ) ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($subject, $message, $parent_id));
            $icount = $pdo->lastInsertId();
            $log = array('err' => '0', 'errmsg' => '', 'sucmsg' => 'Your enquiry has been placed successfully!', 'regid' => $icount);
        }
    } catch (PDOException $e) {
        $log = array('err' => '1', 'id' => '0', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($log));
    return $response;
});


//get all post or a single post by status
$app->get('/posts/getbystatus/{status}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $status = $request->getAttribute('status');
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = " SELECT * FROM  blog_posts  WHERE post_status='$status' ORDER By ID desc ";


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

//read a single post
$app->get('/posts/getbyid/{id}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT * FROM `blog_posts` WHERE `id` = '$id'";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read all vocations
$app->get('/vocations/get/{id}/{vocation}/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$loadinbox = $allPostPutVars['loadinbox'];
    $id = $request->getAttribute('id');
    $vocation = $request->getAttribute('vocation');

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id == 0)
            $sql_query = "SELECT * FROM vocations where voc_name like '%$vocation%' ORDER BY voc_name";
        else
            $sql_query = "SELECT * FROM vocations where  id = '$id' ";

        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0)
            $jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
        else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//read all vocations
$app->post('/contacts/getimportedknows/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];
    $userid = $allPostPutVars['userid'];
    $size = $allPostPutVars['size'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $start = ($goto - 1) * $size;

        if ($userid == 1) {
            $sql_query = "SELECT  id as a,  user_id as b, client_name as c,  client_profession as d , 
			client_phone as e,  client_email as f,  client_location as g, 
			 client_zip as h , client_note  as i,  user_group as j, entrydate as k,  updatedate as l, 
			 company  as l, isimport as m, lcid as n, tags as o  ,client_lifestyle as  p
			 FROM user_people WHERE isimport ='1'  ORDER BY client_name ASC LIMIT $start,$size";
            $sql_query_count = "SELECT count(*) as cnt FROM user_people WHERE isimport ='1'  ";
        } else {
            $sql_query = "SELECT id as a,  user_id as b, client_name as c,  client_profession as d ,  
			 client_phone as e,  client_email as f,  client_location as g, 
			client_zip as h , client_note  as i,  user_group as j, entrydate as k,  updatedate as l, 
			company  as l, isimport as m, lcid as n, tags as o ,client_lifestyle as  p  FROM user_people WHERE isimport ='1' and user_id = '$userid' ORDER BY client_name ASC LIMIT $start,$size";
            $sql_query_count = "SELECT count(*) as cnt FROM user_people  WHERE isimport ='1' and user_id = '$userid' ";
        }
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $jsonresult = array('error' => '0', 'errmsg' => 'Contacts are retrieved!', 'numrows' => $result_count[0]["cnt"], 'result' => $result);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get all partners
$app->post('/get/partners/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $size = $allPostPutVars['size'];
    $userid = $allPostPutVars['userid'];
    $vocation = $allPostPutVars['vocation'];
    $group = $allPostPutVars['group'];
    $goto = $allPostPutVars['goto'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " SELECT *, 0 as rate FROM user_people WHERE user_id = '$userid' AND client_profession='$vocation'
		AND  FIND_IN_SET('$group',  user_group ) > 0  ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < $rst->rowCount(); $i++) {
                $grouplist = $result[$i][user_group];
                if ($grouplist != '') {
                    $grpQry = "select group_concat(grp_name) as groupnames from groups where id in ($grouplist)";
                    $rstGroups = $pdo->query($grpQry);
                    if ($rstGroups->rowCount() > 0) {
                        $groupnames = $rstGroups->fetchAll(PDO::FETCH_ASSOC);
                        $result[$i][user_group] = $groupnames[0]['groupnames'];
                    }
                }

                //get ratings
                $knowid = $result[$i][id];
                $rateQry = "select sum(ranking) user_ranking from user_rating where  user_id  = '$knowid'";
                $rstRate = $pdo->query($rateQry);
                if ($rstRate->rowCount() > 0) {
                    $knowrate = $rstRate->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i][rate] = $knowrate[0]['user_ranking'];
                }

            }
            $jsonresult = array('error' => '0', 'errmsg' => 'Partners information retrieved!', 'result' => $result);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => "No partner information found!");
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $grpQry);
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//fetch all partners
$app->post('/partners/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from mc_reminder where  enteredby= '$userid'";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $jsonresult[] = array('error1' => '0', 'errmsg' => 'Reminders fetched successfully!',
                'resultset1' => $rst->fetchall(PDO::FETCH_ASSOC));
        } else {
            $jsonresult[] = array('error1' => '10', 'errmsg' => 'No reminder found!');
        }

        $sql_query = " select * from mc_reminder where  assignedto= '$userid'";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult[] = array('error2' => '0', 'errmsg' => 'Reminders fetched successfully!',
                'resultset2' => $rst->fetchall(PDO::FETCH_ASSOC));
        } else {
            $jsonresult[] = array('error2' => '100', 'errmsg' => 'No reminder found!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read all faqs
$app->get('/get/faqs/', function (Request $request, Response $response) {
    //$allPostPutVars = $request->getParsedBody();
    //$goto = $allPostPutVars['goto'];
    //$userid = $allPostPutVars['userid'];
    //$size = $allPostPutVars['size'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$start = ($goto-1) * $size ;

        $sql_query = " select * from helps order by position asc";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            $jsonresult = array('error' => '0', 'errmsg' => 'Help guides are added!', 'result' => $result);

        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No help/guide information added!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all faqs
$app->post('/faqs/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $title = $allPostPutVars['title'];
    $helpbody = $allPostPutVars['helpbody'];
    $faqid = $allPostPutVars['faqid'];
    $userrole = $allPostPutVars['role'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($userrole == 'admin') {
            if ($faqid == 0) {
                //insert
                $sql_query = "INSERT INTO helps (helptitle, helptext, publish) VALUES ('$title','$helpbody', '1')";
            } else {
                //update
                $sql_query = "UPDATE helps SET helptitle ='$title' , helptext = '$helpbody' WHERE id= '$faqid' ";
            }
            $rst = $pdo->query($sql_query);
            $jsonresult = array('error' => '0', 'errmsg' => 'Help/Faq saved successfully!');

        } else {
            $jsonresult = array('error' => '100', 'errmsg' => 'User priviledge not sufficient!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//read all faqs
$app->post('/groups/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $groupname = $allPostPutVars['groupname'];
    $userrole = $allPostPutVars['role'];
    $groupid = $allPostPutVars['id'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($userrole == 'admin') {
            if ($groupid == 0) {
                //insert
                $sql_query = "insert into groups ( grp_name, islisted ) values ('$groupname', '1') ";

                $check = $pdo->query("select id from  groups  where grp_name = '$groupname'");
                if ($check->rowCount() > 0) {
                    $jsonresult = array('error' => '10', 'errmsg' => 'Group with name "' . $groupname . '" already exists!');
                } else {
                    $rst = $pdo->query($sql_query);
                    $jsonresult = array('error' => '0', 'errmsg' => 'Group saved successfully!');
                }
            } else {
                //update
                $sql_query = "update groups set grp_name  = '$groupname' where  id  = '$groupid'";
                $rst = $pdo->query($sql_query);
                $jsonresult = array('error' => '0', 'errmsg' => 'Group updated successfully!');
            }
        } else {
            $jsonresult = array('error' => '100', 'errmsg' => 'User priviledge not sufficient!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/tools/getpagecount/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $sql_query = $allPostPutVars['query'];

    $pages = 0;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            $rowcount = $result[0]['count(*)'];
            if ($rowcount > 10)
                $pages = ceil($rowcount / 10);

            $jsonresult = array('error' => '0', 'page' => $pages);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No member found!', 'page' => $pages);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//Code to add/update trigger
$app->post('/trigger/add/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $triggerName = $allPostPutVars['triggername'];
    $triggerid = $allPostPutVars['triggerid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($triggerid) && $triggerid > 0) {
            $sql_query = "update  my_triggers  set trigger_question  = ?  where  id  =  ?";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($triggerName, $triggerid));
            $log = array('error' => '0', 'errmsg' => 'Trigger question updated!');
        } else {
            $sql_query = "select id from  my_triggers where trigger_question  = ? and user_id = ? ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($triggerName, $userid));
            $rsttriggers = $stmt->fetchAll();
            if (sizeof($rsttriggers) > 0) {

                $log = array('error' => '10', 'errmsg' => 'Trigger question already exists!');
            } else {
                $sql_query = "insert into my_triggers  ( trigger_question, user_id ,  entry_date, status) VALUES ( ? , ?,  NOW(), '0'  )";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($triggerName, $userid));
                $log = array('error' => '0', 'errmsg' => 'Trigger question save!');
            }
        }

    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($log));
    return $response;

});

//read all vocations
$app->post('/scanzipdistance/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    //$goto = $allPostPutVars['goto'];
    $userid = $allPostPutVars['userid'];
    //$size = $allPostPutVars['size'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $start = ($goto - 1) * $size;

        $sql_query = "select  * from referralsuggestions  where isdeleted = '0' and knowenteredby  = '$userid' 
		 and sourcezip <>'' and targetzip <> '' and distance = '0' and distancecalculated='0' order by id desc LIMIT 0, 50 ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < $rst->rowCount(); $i++) {
                //$jsonresult[$i] = ['id'=> $result[$i]['id'], 'source'=>$result[$i]['sourcezip'], 'target'=> $result[$i]['targetzip']  ];

                $source = $result[$i]['sourcezip'];
                $target = $result[$i]['targetzip'];
                $refid = $result[$i]['id'];

                $sql_query_distance = " select max(distance) as distance from 
				(  select distinct distance from referralsuggestions 
				where ( sourcezip='$source' and targetzip='$target') or ( sourcezip='$target' and targetzip='$source' )  )  as d ";

                $rstdistance = $pdo->query($sql_query_distance);
                $distance = 0;
                if ($rstdistance->rowCount() > 0) {
                    $resultdistance = $rstdistance->fetchAll(PDO::FETCH_ASSOC);
                    $distance = $resultdistance[0]['distance'];
                    $jsonresult[$i] = ['distance' => $distance];
                    if ($distance > 0 && $distance <= 30) {
                        $pdo->query(" update referralsuggestions set distance='$distance' where  id = '$refid' ");

                    } else if ($distance > 30) {
                        $pdo->query(" update referralsuggestions SET distance='$distance' , isdeleted='1'  WHERE  id = '$refid' ");
                    }

                }
            }
        } else
            $jsonresult = array('error' => '10', 'errmsg' => 'No zip code entries found!', 'result' => $result);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Code to add/update trigger
$app->post('/tools/deleterow/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $rowid = $allPostPutVars['trn'];
    $table = $allPostPutVars['tn'];

    switch ($table) {
        case "trig":
            $sql_query = "delete from my_triggers where id =' $rowid' ";
            break;
        case "contact":
            $sql_query = "delete from contacts where id =' $rowid' ";
            break;
        case "ebox":
            $sql_query = "delete from mc_mailbox where id =' $rowid' ";
            break;
        case "ctrack":
            $sql_query = "delete from mc_client_tracking where id ='$rowid' ";
            break;
        case "m_c_q":
            $sql_query = "delete from mc_client_question where id ='$rowid' ";
            break;
        case "m_p_q":
            $sql_query = "delete from mc_program_questions where id ='$rowid' ";
            break;
    }


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec($sql_query);
        $log = array('error' => '0', 'errmsg' => 'Record removed successfully!');
    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($log));
    return $response;
});


//search all members
$app->post('/member/search/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];
    $vocations = $allPostPutVars['vocSrch'];
    $location = $allPostPutVars['locSrch'];
    $role = $allPostPutVars['role'];
    $userid = $allPostPutVars['userid'];
    $nameSrch = $allPostPutVars['nameSrch'];
    $mgroups = $allPostPutVars['groups'];
    $mzip = $allPostPutVars['mzip'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$start = ($goto-1) * $size ;
        if ($role != 'admin') {
            $pdo->query("insert into  vocation_search_logs (  vocation ,  location ,  user_id ,  created_at ) values  
			('" . implode(",", $vocations) . "','" . $location . "'," . $userid . ",'" . date("Y-m-d H:i:s") . "')");
        }

        $userPkgQ = $pdo->query("select * from  mc_user where id='$userid' ");

        if ($userPkgQ->rowCount() > 0) {
            $userPkgFet = $userPkgQ->fetchAll(PDO::FETCH_ASSOC);
            $user_pkg = $userPkgFet[0]['user_pkg'];


            $pkgInfo = (object)array();
            $pkgInfoQ = $pdo->query("select * from packages where package_title='$user_pkg' ");
            if ($pkgInfoQ->rowCount() > 0) {
                $pkgInfoArr = $pkgInfoQ->fetchAll(PDO::FETCH_ASSOC);
                $pkgInfo->price = $pkgInfoArr[0]['package_price'];
                $pkgInfo->limit = $pkgInfoArr[0]['package_limit'];
                $pkgshareLimit = $pkgInfoArr[0]['share_limit'];
                $pkgInfo->refLimit = $pkgInfoArr[0]['ref_limit'];
                $pkgInfo->connLimit = $pkgInfoArr[0]['conn_limit'];
                $pkgInfo->shareLimit = $pkgshareLimit == 0 ? 'unlimitedPkg' : $pkgshareLimit;
            }

            // Fetch user sent messages and user IDs
            $userSentMsgCount = 0;
            $userSentMsgTarg = array();
            $userMsgQ = $pdo->query("select * from user_messages where sender_id='$userid' ");
            if ($userMsgQ->rowCount() > 0) {
                $userSentMsgCount = $userMsgQ->rowCount();
                $userMsgQRslt = $userMsgQ->fetchAll(PDO::FETCH_ASSOC);
                foreach ($userMsgQRslt as $row) {

                    if (!in_array($row["user_id"], $userSentMsgTarg)) {
                        array_push($userSentMsgTarg, $row['user_id']);
                    }
                }
            }

            if ($mgroups == '') {
                $userGrps = $pdo->query("select groups from user_details where  user_id = '$userid'");
                $fetGrps = $userGrps->fetchAll(PDO::FETCH_ASSOC)[0];
                $grps = explode(',', $fetGrps['groups']);
            } else {
                $grps = explode(',', $mgroups);
            }

            $searchVoc = "";

            if (!empty($vocations)) {
                $searchVoc = " AND `client_profession` IN (";
                $vocationswhere = "";

                foreach ($vocations as $item) {
                    $vocationswhere .= "'$item',";
                }

                $vocationswhere = rtrim($vocationswhere, ",");
                $searchVoc .= $vocationswhere . ")";
            }

            $whereClause = "(FIND_IN_SET('" . implode("', `user_group`) OR FIND_IN_SET('", $grps) . "', `user_group`))" . $searchVoc;

            if ($_user_role == 'admin') {
                // $whereClause = "`client_location` LIKE '%$locSrch%' AND `client_profession` = '$vocSrch'";
                $whereClause = "`client_zip` = '$location' " . $searchVoc;
            }

            if ($vocations == "") {
                $userVocationQ = $pdo->query("SELECT target_clients FROM user_details WHERE user_id='$userid' ");
                $fetVoc = $userVocationQ->fetchAll(PDO::FETCH_ASSOC)[0];
                $expVoc = explode(",", $fetVoc['target_clients']);

                $vocations = '';
                foreach ($expVoc as $item) {
                    $vocations = "`client_profession` = '" . $item . "' OR ";
                }
                $vocations = "(" . rtrim($vocations, " OR ") . ")";

                if ($role != 'admin') {
                    $whereClause = "(FIND_IN_SET('" . implode("', `user_group`) OR FIND_IN_SET('", $grps) . "', `user_group`)) AND `client_location` LIKE '%$location%' AND $vocations";
                } else {
                    $whereClause = "`client_location` LIKE '%$locSrch%'";
                }
            }

            $qStment = "select * from user_people where " . $whereClause;
            $q = $pdo->query($qStment);
            if ($q->rowCount() > 0) {
                $admID = array();
                $results = array();
                $admins = [];
                $resultset = $q->fetchAll(PDO::FETCH_ASSOC);

                foreach ($resultset as $row) {
                    $people = $row['id'];
                    $rate_q = $pdo->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$people'");
                    $rate_row = $rate_q->fetchAll(PDO::FETCH_ASSOC)[0];
                    $user_ranking = $rate_row['user_ranking'];
                    $data = [];
                    if (!in_array($row['user_id'], $admins)) {
                        $admins[] = $row['user_id'];
                        $data[] = array('userID' => $row['id'], 'rank' => $user_ranking, 'voc' => $row['client_profession']);
                        $admID[$row['user_id']] = $data;
                    } else {
                        array_push($admID[$row['user_id']], array('userID' => $row['id'], 'rank' => $user_ranking, 'voc' => $row['client_profession']));
                    }
                }
                $html = '';
                $i = 0;
                $username = $user_email = $user_phone = '';
                $slCnt = $pkgInfo->shareLimit;
                $searchResultCnt = count($admID);
                foreach ($admID as $key) {
                    $distance = 0;
                    $q1 = $pdo->query("select a.*, b.street, b.city, b.zip from mc_user as a inner join user_details as b on a.id = b.user_id where a.id = '$admins[$i]'");
                    $row1 = $q1->fetchAll(PDO::FETCH_ASSOC)[0];
                    $username = $row1['username'];
                    $user_email = $row1['user_email'];
                    $user_phone = $row1['user_phone'];
                    $zip = $row1['zip'];
                    if ($zip == '') {
                        break;
                    }
                    $zipqry = " select * from  mc_city_geolocation where zip in (" . $location . ", " . $zip . " ) ";
                    $rsgeolocs = $pdo->query($zipqry);
                    if ($rsgeolocs->rowCount() == 2) {
                        $geolocs = $rsgeolocs->fetchAll(PDO::FETCH_ASSOC);
                        $latitude1 = $geolocs[0]['latitude'];
                        $longitude1 = $geolocs[0]['longitude'];
                        $latitude2 = $geolocs[1]['latitude'];
                        $longitude2 = $geolocs[1]['longitude'];
                        $theta = $longitude1 - $longitude2;
                        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                        $distance = acos($distance);
                        $distance = rad2deg($distance);
                        $distance = $distance * 60 * 1.1515;
                        switch ($unit) {
                            case 'Mi':
                                break;
                            case 'Km' :
                                $distance = $distance * 1.609344;
                        }
                        $distance = (round($distance, 2));
                    }
                    if ($distance <= 30) {
                        $html .= "<tr><td>$username</td><td>$user_email</td><td>$user_phone</td><td>$zip</td><td>$distance</td><td>";
                        foreach ($key as $item) {
                            $rank = $item['rank'];
                            $voc = $item['voc'];
                            $html .= "Knows a " . $voc . " person with rating " . $rank . "<br>";
                        }

                        $lvMsgBtn = "";
                        if ($slCnt === 'unlimitedPkg') {
                            $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i></button>";
                        } elseif (in_array($admins[$i], $userSentMsgTarg)) {
                            $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i>.</button>";
                            $slCnt -= 1;
                            $userSentMsgCount -= 1;
                        } elseif ($userSentMsgCount < $slCnt) {
                            $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i></button>";
                            $slCnt -= 1;
                        } else {
                            $lvMsgBtn = "";
                        }
                        //$testVar = "pkgLimit: $pkgInfo->shareLimit<br>userSentMsgCount: $userSentMsgCount<br>userSentMsgTarg: ".implode(',',$userSentMsgTarg)."<br>";
                        $html .= "</td><td>$lvMsgBtn</td></tr> ";

                    }
                    $i++;
                }
                $jsonresult = array('error' => '0', 'errmsg' => $qStment . $i . ' knows found!', 'results' => $html);
            } else {
                $jsonresult = array('error' => '1', 'errmsg' => $qStment . "No matching know found!");
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//search all members
$app->post('/member/business/search/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];
    $vocations = $allPostPutVars['vocation'];
    $city = $allPostPutVars['city'];
    $userid = $allPostPutVars['userid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $start = ($goto - 1) * 20;
        $qry = " select a.*, current_company,  linkedin_profile ,  city , zip ,  country , groups ,  target_clients ,  target_referral_partners ,  vocations ,  about_your_self , upd_public_private ,  upd_reminder_email ,  lcid  , 0 as rate from  mc_user as a inner join user_details as b on a.id=b.user_id where a.user_type='1' and   find_in_set(  ?, a.busi_type ) and a.busi_location LIKE ?   ";

        $stmt = $pdo->prepare($qry);
        $stmt->execute(array($vocations, "%" . $city . "%"));

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $i = 0;
        foreach ($results as $row) {
            $email = $row['user_email'];
            $username = $row['username'];

            $stmt2 = $pdo->prepare("select sum(ranking) as srank from user_rating where user_id in (select id from user_people where client_email= ? and client_name=  ?) group by user_id order by srank desc");
            $stmt2->execute(array($email, $username));
            $ratings = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            if (sizeof($ratings) > 0)
                $results[$i]['rate'] = $ratings[0]['srank'];

            $i++;
        }
        //logging business search
        $stmt = $pdo->prepare("insert into mc_business_search_log (user_id, city, vocation, created_at ) VALUES (?,?,?, NOW() )");
        $stmt->execute(array($userid, $city, $vocations));

        $jsonresult = array('error' => '0', 'errmsg' => 'Business information search completed!', 'results' => $results);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Business search log
$app->post('/business/search/logs/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['goto'];

    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select a.*, b.username, b.user_email, b.image from  mc_business_search_log as a inner join mc_user as b on a.user_id=b.id order by username limit $start, $pagesize ";

        $sql_query_count = "select count(*) as reccnt from  mc_business_search_log as a inner join mc_user as b on a.user_id=b.id order by username  ";


        $ds = DIRECTORY_SEPARATOR;
        $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";
        $rst = $pdo->query($sql_query);
        $logs = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            if ($logs[$i]['image'] !== NULL && $logs[$i]['image'] != '' && $logs[$i]['image'] != 'null') {
                $user_picture = (file_exists($imagepath . $logs[$i]['image']) ? $logs[$i]['image'] : "no-photo.png");
                $logs[$i]['image'] = $user_picture;
            } else {
                $logs[$i]['image'] = "no-photo.png";
            }
        }
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Business search log generated!', 'results' => $logs);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Code to add/update note
$app->post('/notes/add/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $userid = $allPostPutVars['userid'];
    $notes = $allPostPutVars['notes'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($id) && $id != 0) {
            $stmt = $pdo->prepare("update  mc_statements  set  note  = ? where  id  = ? ");
            $stmt->execute(array($notes, $id));
            $log = array('error' => '0', 'errmsg' => 'Statement updated successfully!');
        } else {
            $stmt = $pdo->prepare("insert into mc_statements  (   user_id ,  note ,  enteredon ,  status ) values  ( ?,?, NOW(), '1')");
            $stmt->execute(array($userid, $notes));
            $log = array('error' => '0', 'errmsg' => 'Statement saved successfully!');
        }
    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => 'Something went wrong. Please retry!');
    }

    $response->getBody()->write(json_encode($log));
    return $response;

});


//Code to add/update note
$app->post('/notes/getall/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['goto'];

    $pagesize = 1;
    $start = ($goto - 1) * $pagesize;


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select * from mc_statements   order by id desc limit $start, $pagesize ";

        $sql_query_count = "select count(*) as reccnt  from mc_statements ";


        $rst = $pdo->query($sql_query);
        $notes = $rst->fetchAll(PDO::FETCH_ASSOC);
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Notes retrieved!', 'results' => $notes);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Something went wrong. Please retry!');
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//Code to add/update trigger
$app->post('/tags/add/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $role = $allPostPutVars['role'];
    $tagname = $allPostPutVars['tagname'];
    $currtag = $allPostPutVars['currTagVal'];


    if ($role != 'admin') :

        $log = array('error' => '100', 'errmsg' => "Not enough privilege to add tag!");

    else:
        try {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($currtag) && $currtag != 0) {
                $pdo->query("update  mc_tags  set  tagname  = '$tagname' where  id  = '$currtag'");
                $log = array('error' => '0', 'errmsg' => 'Tag saved successfully!');
            } else {
                $check = $pdo->query("select id from mc_tags where tagname  = '$tagname'");
                if ($check->rowCount() > 0) {
                    $log = array('error' => '10', 'errmsg' => 'Tag already exists!');
                } else {
                    $pdo->query("insert into mc_tags  ( tagname ) values  ('$tagname')");
                    $log = array('error' => '0', 'errmsg' => 'Tag saved successfully!');
                }
            }

        } catch (PDOException $e) {
            $log = array('error' => '1', 'errmsg' => 'Something went wrong. Please retry!');
        }
    endif;

    $response->getBody()->write(json_encode($log));
    return $response;

});


//Code to add/update trigger
$app->post('/tags/getall/', function (Request $request, Response $response, $args) {

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rs = $pdo->query("select * from mc_tags");
        if ($rs->rowCount() > 0) {

            $log = array('error' => '0', 'errmsg' => 'Tags retrieved!', 'results' => $rs->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $log = array('error' => '10', 'errmsg' => 'No tag has been configured yet!');
        }
    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => 'Something went wrong. Please retry!');
    }
    $response->getBody()->write(json_encode($log));
    return $response;
});


//read all performance data
$app->post('/performance/get/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];

    $previous_week = strtotime("-1 week +1 day");
    $lstart_week = strtotime("last sunday midnight", $previous_week);
    $lend_week = strtotime("next saturday", $start_week);
    $last_week_start = date("Y-m-d", $lstart_week);
    $last_week_end = date("Y-m-d", $lend_week);

    $start_week = strtotime("last sunday midnight", $previous_week);
    $last_week_start = date("Y-m-d", $start_week);
    $last_week_end = date('Y-m-d', strtotime($last_week_start . ' +7 day'));

    $d = strtotime("today");
    $start_week = strtotime("last sunday midnight", $d);
    $current_week_start = date("Y-m-d", $start_week);
    $current_week_end = date('Y-m-d', strtotime($current_week_start . ' +6 day'));

    $jsonresult['start_week'] = date('Y-m-d', $start_week);
    $jsonresult['current_week_end'] = date('Y-m-d', strtotime($current_week_start . ' +6 day'));

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select count(*) as refcount from  referralsuggestions  where partnerid='$userid'";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $lastweekrefsrs = $pdo->query("select count(*) as refcount from referralsuggestions  
			where partnerid='$userid'  and (	entrydate >= '$last_week_start' and 	entrydate <='$last_week_end' ) ");
            $currentweekrefsrs = $pdo->query("select count(*) as refcount from referralsuggestions  
			where partnerid='$user_id'  and (	entrydate >= '$current_week_start' and 	entrydate <='$current_week_end' ) ");

            $lastweekrefsra = $lastweekrefsrs->fetchAll(PDO::FETCH_ASSOC);
            $currentweekrefsra = $currentweekrefsrs->fetchAll(PDO::FETCH_ASSOC);

            $lastweekrefcnt = $lastweekrefsra[0]['refcount'];
            $currentweekcnt = $currentweekrefsra[0]['refcount'];

            if ($lastweekrefcnt > 0)
                $cweekgrowthpc = round(((($currentweekcnt - $lastweekrefcnt) / $lastweekrefcnt) * 100), 2);
            else
                $cweekgrowthpc = 0;


            $jsonresult['lastweekrefcnt'] = $lastweekrefcnt;
            $jsonresult['currentweekcnt'] = $currentweekcnt;
            $jsonresult['currentweekgrowthpc'] = $cweekgrowthpc;


            $lastweekrefsmailrs = $pdo->query("select count(*) as refcount from referralsuggestions 
			where partnerid='$userid' and emailstatus='1'  and (	entrydate >= '$last_week_start' and 	entrydate <='$last_week_end' ) ");
            $currentweekrefsmailrs = $pdo->query("select count(*) as refcount from referralsuggestions 
			where partnerid='$userid' and emailstatus='1' and (	entrydate >= '$current_week_start' and 	entrydate <='$current_week_end' ) ");

            $lastweekrefsmailra = $lastweekrefsmailrs->fetchAll(PDO::FETCH_ASSOC);
            $currentweekrefsmailra = $currentweekrefsmailrs->fetchAll(PDO::FETCH_ASSOC);


            $lastweekrefsmailcnt = $lastweekrefsmailra[0]['refcount'];
            $currentweekrefsmailcnt = $currentweekrefsmailra[0]['refcount'];

            if ($lastweekrefsmailcnt > 0)
                $cweekemailgrowthpc = round(((($currentweekrefsmailcnt - $lastweekrefsmailcnt) / $lastweekrefsmailcnt) * 100), 2);
            else
                $cweekemailgrowthpc = 0;


            $jsonresult['lastweekrefsmailcnt'] = $lastweekrefsmailcnt;
            $jsonresult['currentweekrefsmailcnt'] = $currentweekrefsmailcnt;
            $jsonresult['cweekemailgrowthpc'] = $cweekemailgrowthpc;

            //group referral counts

            $groupcounter = $pdo->query("select group_concat(user_group) as user_groups from user_people where user_id='$userid' 
			and user_group <> ''");

            if ($groupcounter->rowCount() > 0) {
                $gcrowra = $groupcounter->fetchAll(PDO::FETCH_ASSOC);

                if ($gcrowra[0]['user_groups'] != '') {
                    $grouparr = explode(',', $gcrowra[0]['user_groups']);
                    $grouparr = array_filter($grouparr);
                    sort($grouparr);
                    $linkgroups = (array_unique($grouparr));
                    $groupids = implode(', ', $linkgroups);


                    $groupnamers = $pdo->query("select group_concat(grp_name) as groupnames from  groups where id in (" . $groupids . ")");

                    $jsonresult['groupcount'] = sizeof($linkgroups);
                    $jsonresult['groupnames'] = $groupnamers->fetchAll(PDO::FETCH_ASSOC)[0]['groupnames'];

                    /* Trigger Mail Logic */
                    $triggermails = $pdo->query("select count(*) as mailcnt from mailbox where sender='$userid' ");
                    $triggermailscount = 0;
                    if ($triggermails->rowCount() > 0) {
                        $triggermailscount = $triggermails->fetchAll(PDO::FETCH_ASSOC)[0]['mailcnt'];
                    }

                    $jsonresult['triggermailscount'] = $triggermailscount;
                } else {
                    $jsonresult['triggermailscount'] = 0;
                }
            }
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No performance report could be generated!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//read all performance data
$app->post('/reminders/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from mc_reminder where  enteredby= '$userid'  order by entrydate desc";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $jsonresult[] = array('error1' => '0', 'errmsg' => 'Reminders fetched successfully!',
                'resultset1' => $rst->fetchall(PDO::FETCH_ASSOC));
        } else {
            $jsonresult[] = array('error1' => '10', 'errmsg' => 'No reminder found!');
        }

        $sql_query = " select * from mc_reminder where  assignedto= '$userid'";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $jsonresult[] = array('error2' => '0', 'errmsg' => 'Reminders fetched successfully!',
                'resultset2' => $rst->fetchall(PDO::FETCH_ASSOC));
        } else {
            $jsonresult[] = array('error2' => '100', 'errmsg' => 'No reminder found!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Save reminder
$app->post('/reminder/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $remid = $allPostPutVars['remid'];
    $title = $allPostPutVars['title'];
    $type = $allPostPutVars['type'];
    $text = $allPostPutVars['text'];
    $assignedto = $allPostPutVars['assignedto'];
    $reminderdate = $allPostPutVars['reminderdate'];
    $hr = $allPostPutVars['hr'];
    $min = $allPostPutVars['min'];
    $hrformat = $allPostPutVars['hrformat'];
    $err = 0;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dateparts = explode('-', $reminderdate);
        if (sizeof($dateparts) == 3) {
            $reminderdatetime = date('Y-m-d H:i:s', strtotime($dateparts[2] . "-" . $dateparts[1] . "-" . $dateparts[0] .
                " " . $hr . ":" . $min . " " . $hrformat));
        } else {
            $err = 1;
            $errlog = array('err' => '1', 'msg' => 'Invalid date provided!');
        }

        if (!isset($title) || $title == "") {
            $err = 1;
            $errlog = array('err' => '1', 'msg' => 'Missing reminder title!');
        }

        if (!isset($text) || $text == "") {
            $err = 1;
            $errlog = array('err' => '1', 'msg' => 'Missing reminder body!');
        }

        if (!isset($type) || $type == "") {
            $err = 1;
            $errlog = array('err' => '1', 'msg' => 'You need to specify reminder type!');
        }
        if ($err == 0) {
            if ($remid == 0)
                $sql_query = "insert into mc_reminder 
				(type,subject,reminderbody,assignedto, emailreminderon, entrydate , enteredby) 
				VALUES ('$type','$title','$text',  $assignedto,  '$reminderdatetime'  , NOW() , '$userid')";
            else
                $sql_query = "update mc_reminder 
				set type='$type', subject= '$title',reminderbody= '$text',assignedto='$assignedto', 
				emailreminderon= '$reminderdatetime', lastupdate= NOW()  , isalerted='0'  where id= '$remid' ";

            $pdo->query($sql_query);
            $insID = $pdo->lastInsertId();
            $jsonresult = array('error' => '0', 'errmsg' => 'Reminder saved successfully!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Save reminder read
$app->post('/reminder/markread/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $remid = $allPostPutVars['remid'];
    $isread = $allPostPutVars['isread'];

    if ($isread == 1) return;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dateparts = explode('-', $reminderdate);


        if ($remid > 0)
            $sql_query = "update mc_reminder set isread='1' where id= '$remid' ";

        $pdo->query($sql_query);
        $insID = $pdo->lastInsertId();
        $jsonresult = array('error' => '0', 'errmsg' => 'Reminder marked as read!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//Save reminder
$app->post('/reminder/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['page'];

    $size = 10;
    $start = ($goto - 1) * $size;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select   id  as a,  type as b,  subject  as c, reminderbody  as d,  assignedto  as e, 
		 emailreminderon  as f,  entrydate  as g,  lastupdate  as h, enteredby  as i  from mc_reminder where enteredby='$userid' order by entrydate limit $start, $size";
        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt   from mc_reminder where enteredby='$userid'  ";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / $size);

        $jsonresult = array('error' => '1', 'result' => $results, 'pages' => $pages);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//get member reminders
$app->post('/reminders/getall/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $dates = $allPostPutVars['dates'];
    $datee = $allPostPutVars['datee'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "(select a.*, b.user_email, b.username from mc_reminder as a inner join mc_user as b on a.enteredby=b.id  " .
            " where  enteredby<> '1' and (emailreminderon between '$dates' and '$datee') and  isalerted <> '1'  order by a.emailreminderon ) " .
            " union " .
            "( select a.*, b.user_email, b.username from mc_reminder as a inner join mc_user as b  on a.assignedto=b.id " .
            " where  enteredby  = '1' and (emailreminderon between '$dates' and '$datee') and  isalerted <> '1' order by a.emailreminderon ) ";
        $rst = $pdo->query($sql_query);
        $jsonresult = array('error' => '1', 'result' => $rst->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/reminder/markalerted/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $remid = $allPostPutVars['remid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dateparts = explode('-', $reminderdate);


        if ($remid > 0)
            $sql_query = "update mc_reminder set isalerted='1' where id= '$remid' ";

        $pdo->query($sql_query);
        $insID = $pdo->lastInsertId();
        $jsonresult = array('error' => '0', 'errmsg' => 'Reminder email sent!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get unfinished signups
$app->post('/signups/new/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $interval = $allPostPutVars['interval'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select id, user_email, username from  mc_user where createdOn  >= DATE_ADD(CURDATE(), INTERVAL - " . $interval . " DAY) ";

        $rst = $pdo->query($sql_query);

        $jsonresult = array('error' => '0', 'errmsg' => '2 days old signups fetched!',
            'results' => $rst->fetchAll(PDO::FETCH_ASSOC));

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please try again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//get unfinished signups
$app->post('/signups/incomplete/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $from = $allPostPutVars['from'];
    $to = $allPostPutVars['to'];
    $goto = $allPostPutVars['goto'];
    $size = 10;
    $start = ($goto - 1) * $size;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select id, user_email, date(createdOn) as createdon from  mc_user 
		where (user_pass is null or user_pass='') or (username is null or username ='') or (user_phone is null or user_phone='') 
		order by id desc limit $start, $size";

        $sql_query_count = "select  count(*) as reccnt from  mc_user  where (user_pass is null or user_pass='') or (username is null or username ='') or (user_phone is null or user_phone='')";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
            $unfinishedsignup = $rst->fetchAll(PDO::FETCH_ASSOC);

            $pages = ceil($result_count[0]['reccnt'] / 10);
            $jsonresult = array('error' => '0', 'errmsg' => 'Unfinished signups fetched!',
                'pages' => $pages, 'results' => $unfinishedsignup);
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please try again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//search sent email
$app->post('/signups/incomplete/reinvite/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $fileno = $allPostPutVars['fileno'];
    $receipent = $allPostPutVars['receipent'];

    try {
        $ds = DIRECTORY_SEPARATOR;
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
        if (file_exists($path . "templates/unfinishsignuptemplate01.txt")) {

            // Create a stream
            $mailbody = file_get_contents($path . "templates/unfinishsignuptemplate01.txt");
            $jsonresult = array('error' => '0', 'errmsg' => $mailbody);

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: noreply@mycity.com';
            sendemail($receipent, "Mycity.com Signup not completed", $mailbody, $mailbody);
            sendemail("bob@mycity.com", "Mycity.com Signup not completed", $mailbody, $mailbody);
            $jsonresult = array('error' => '0', 'errmsg' => 'Email sent!', 'message' => $mailbody);

        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


/* imported knows */
//check if reference email has already been sent
$app->post('/knows/showallimported/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $goto = $allPostPutVars['goto'];
    $key = $allPostPutVars['key'];


    $start = ($goto - 1) * 10;


    if ($key != '')
        $and_where_clause = " and client_name like '%$key%'";
    else
        $and_where_clause = "";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = " select id as a, user_id as b, client_name as c,  client_profession as d,  
		client_lifestyle as e,  client_phone as f,  client_email as g, client_location as h, 
		client_zip as i, client_note as j,  user_group as k,  entrydate as l,  updatedate as m, 
		company as n, isimport as o, lcid as p,  tags as q  from user_people 
		where  user_id='$userid' and isimport='1' $and_where_clause order by id desc LIMIT $start,10 ";

        $sql_query_count = "SELECT count(*) as recnt  FROM user_people 
		WHERE  user_id = '$userid' and isimport='1'  $and_where_clause";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);

            $allknows = $rst->fetchAll(PDO::FETCH_ASSOC);

            $pages = ceil($result_count[0]['recnt'] / 10);
            $jsonresult = array('error' => '0', 'errmsg' => 'Imported knows/contacts are retrieved!',
                'pages' => $pages, 'result' => $allknows);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No imported know/contact found!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//search sent email
$app->post('/email/send/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $fback_name = $allPostPutVars['name'];
    $fback_email = $allPostPutVars['email'];
    $fback_comment = $allPostPutVars['comment'];

    try {
        $ds = DIRECTORY_SEPARATOR;
        $apppath = '';
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds;
        if (file_exists($path . "templates/generic_email.txt")) {

            // Create a stream
            $mailbody = file_get_contents($path . "templates/generic_email.txt");
            $jsonresult = array('error' => '0', 'errmsg' => $mailbody);

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: noreply@mycity.com';
            $msg2 = "";
            //mail("bob@mycity.com", "Mycity.com Feedback submitted", $msg2, $headers);
            sendemail("bob@mycity.com", "Mycity.com Feedback submitted", $msg2, $msg2);
            echo "success";
            $jsonresult = array('error' => '0', 'errmsg' => 'Help guides are added!', 'result' => $result);

        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//search sent email
$app->post('/leavemessage/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $email = $allPostPutVars['sender_email'];
    $name = $allPostPutVars['sender_name'];
    $msg = $allPostPutVars['leaveMsg'];
    $send_to = $allPostPutVars['send_to'];
    $user_id = $allPostPutVars['user_id'];


    $token = md5($send_to);
    $tokenlength = strlen($send_to);
    $token = $send_to . $token;


    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: noreply@mycity.com";

    $ds = DIRECTORY_SEPARATOR;
    $apppath = '';
    $path = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds;

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "SELECT user_email, username FROM mc_user WHERE id = '$send_to' ";
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $recipient = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $rec_name = $recipient['username'];
            $rec_email = $recipient['user_email'];

            if (file_exists($path . "templates/leavemessage01.txt")) {
                // Create a stream
                $mailbody = file_get_contents($path . "templates/leavemessage01.txt");
                $mailbody = str_replace("{user}", $name, $mailbody);
                $mailbody = str_replace("{email}", $email, $mailbody);
                $mailbody = str_replace("{mailbody}", nl2br($msg), $mailbody);
                $mailbody = str_replace("{tokenid}", $token, $mailbody);
                $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
                $mailbody = str_replace("{year}", date('Y'), $mailbody);
                sendemail($rec_email, 'Message: My City', $mailbody, $mailbody);


                $mailbody = file_get_contents($path . "templates/leavemessage01.txt");
                $mailbody = str_replace("{user}", $name, $mailbody);
                $mailbody = str_replace("{email}", $email, $mailbody);
                $mailbody = str_replace("{mailbody}", nl2br($msg), $mailbody);
                sendemail('bob@mycity.com', 'New message on www.mycity.com', $mailbody, $mailbody);

                $stmt = $pdo->prepare("insert into user_messages (user_id, sender_id, sender_email, sender_name, message) VALUES (?,?,?,?,?)");

                $stmt->execute(array($send_to, $user_id, $email, $name, htmlentities($msg)));

                $jsonresult = array('error' => '0', 'errmsg' => 'Email sent successfully!', 'mailbody' => $mailbody);
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => 'Email template not found!');
            }
        } else {
            $jsonresult = array('error' => '11', 'errmsg' => 'No user information found!');

        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//search sent email
$app->post('/mail/sent/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $to = $allPostPutVars['to'];
    $subject = $allPostPutVars['subject'];
    $body = $allPostPutVars['body'];
    $cc = $allPostPutVars['cc'];

    $from = "bob@mycity.com";

    try {
        if ($cc != "") {
            $headers = "From: bob@mycity.com\r\n" . "Cc: " . $cc . "\n";
        } else {
            $headers = "From: bob@mycity.com\n";
        }
        $headers .= "Reply-To: " . $from . "\n";
        $headers .= "Return-Path: " . $from . "\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "mail.mycity.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Priority = 1;
        $mail->AddCustomHeader("X-MSMail-Priority: High");
        $mail->Username = "noreply@mycity.com";
        $mail->Password = "rfq2707";
        $mail->SetFrom("bob@mycity.com", 'Bob Friedenthal');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);

        if (!$mail->Send()) {
            $jsonresult = array('error' => '1', 'errmsg' => 'Email could not be sent!');
        } else {
            $jsonresult = array('error' => '0', 'errmsg' => 'Email sent successfully!');
        }

    } catch (PDOException $e) {
        return 0;
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//search sent email
function sendmail($to, $from, $subject, $body, $altbody)
{

    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Return-Path: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "mail.mycity.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Priority = 1;
    $mail->AddCustomHeader("X-MSMail-Priority: High");
    $mail->Username = "noreply@mycity.com";
    $mail->Password = "rfq2707";
    $mail->SetFrom("bob@mycity.com", 'Bob Friedenthal');
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);

    if (!$mail->Send()) {
        return 0;
    } else {
        return 1;
    }


}


//search sent email
function sendmailusersigned($to, $from, $subject, $body, $altbody)
{
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "Return-Path: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->Mailer = "smtp";
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = "referralsmycity@gmail.com";
    $mail->Password = "Rfq#2707";
    $mail->SetFrom("referralsmycity@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);


    if (!$mail->Send()) {
        return 0;
    } else {
        return 1;
    }
}


//search sent email
$app->get('/email/test/', function (Request $request, Response $response) {
    $body = '<p>Hello Bob Friedenthal,</p>
	<p> is a referral partner of Bob Friedenthal. He gave a high rating of Bob Friedenthal on the following questions:<br/> 
	<ol style="margin-left: 16px;">
	<li>Do you want to grow your business?</li>
	<li>Are you willing to network?</li>
	<li>Are you willing to give referrals?</li>
	<li>What is your expertise in your field?</li>
	</ol>
	</p>
	<p>
	They also mentioned that you are interested in meeting someone in the following vocation.
	</p><p>Hello Bob Friedenthal,</p>
	<p> is a referral partner of Bob Friedenthal. He gave a high rating of Bob Friedenthal on the following questions:<br/> 
	<ol style="margin-left: 16px;">
	<li>Do you want to grow your business?</li>
	<li>Are you willing to network?</li>
	<li>Are you willing to give referrals?</li>
	<li>What is your expertise in your field?</li>
	</ol>
	</p>
	<p>
	They also mentioned that you are interested in meeting someone in the following vocation.
	</p>';

    try {
        $jsonresult = array('error' => '0', 'errmsg' =>
            sendreferralmail('xanayaima@hotmail.com', 'Testing email', $body, $body));

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//search sent email
function sendreferralmail($to, $subject, $body, $altbody, $cc = 'referrals@mycity.com', $ccname = 'Referral MyCity', $cc1 = '', $ccname1 = '')
{
    $from = "referralsmycity@gmail.com";
    if ($cc1 != "") {
        $to .= "," . $cc1;
    }

    if ($cc != "") {
        $headers = "From: " . $from . "\r\n" . "Cc: " . $cc . "\n";
    } else {
        $headers = "From: " . $from . "\n";
    }

    $headers .= "Reply-To: " . $from . "\n";
    $headers .= "Return-Path: " . $from . "\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->Mailer = "smtp";
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = "referralsmycity@gmail.com";
    $mail->Password = "Rfq#2707";
    $mail->SetFrom($from);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);

    if (!$mail->Send()) {
        return 0;
    } else {
        return 1;
    }
}


//search sent email
function sendemail($to, $subject, $body, $altbody, $cc = 'referrals@mycity.com', $ccname = 'Referral MyCity', $cc1 = '', $ccname1 = '')
{
    $from = "referralsmycity@gmail.com";
    if ($cc1 != "") {
        $to .= "," . $cc1;
    }
    if ($cc != "") {
        $headers = "From: referrals@mycity.com\r\n" . "Cc: " . $cc . "\r\n";
    } else {
        $headers = "From: referrals@mycity.com" . "\r\n";
    }
    $headers .= "Reply-To: referrals@mycity.com" . "\r\n";
    $headers .= "Return-Path: referrals@mycity.com" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->Mailer = "smtp";
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = "referralsmycity@gmail.com";
    $mail->Password = "Rfq#2707";
    $mail->SetFrom($from);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);


    if (!$mail->Send()) {
        return 0;
    } else {
        return 1;
    }

}

//save member photo
$app->post('/member/savephoto/', function (Request $request, Response $response, $args) {

    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $photo = mysql_escape_string($allPostPutVars['photo']);
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "update sv_teacher set photo='$photo' where id='$id'";
        $pdo->query($sql_query);
        $qlog = array('err' => '0', 'photo' => 'Photo uploaded successfully!');
    } catch (PDOException $e) {
        $qlog = array('err' => '1', 'photo' => 'Photo upload failed!');
    }
    $response->getBody()->write(json_encode($qlog));
    return $response;
});


//read questions
$app->get('/resetrating/{start}/{end}/', function (Request $request, Response $response) {
    $start = $request->getAttribute('start');
    $end = $request->getAttribute('end');
    $jsonresult = array('error' => '0', 'errmsg' => "Updated successfully!");
    /*
	try
	{
		$pdo =getPDO($this);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		for( $i= $start; $i<= $end; $i++)
		{
			$pdo->query("INSERT INTO user_rating ( user_id , question_id , ranking ) VALUES ('". $i . "','1','5')");
			$pdo->query("INSERT INTO user_rating ( user_id , question_id , ranking ) VALUES ('". $i . "','2','5')");
			$pdo->query("INSERT INTO user_rating ( user_id , question_id , ranking ) VALUES ('". $i . "','3','5')");
			$pdo->query("INSERT INTO user_rating ( user_id , question_id , ranking ) VALUES ('". $i . "','4','5')");
			$pdo->query("INSERT INTO user_rating ( user_id , question_id , ranking ) VALUES ('". $i . "','5','5')");
		}
		$jsonresult = array('error' =>  '0' ,  'errmsg' =>  "Updated successfully!" );

	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' =>   $e->getMessage() );
	}
	*/

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//read questions
$app->get('/mappingfixation/{start}/', function (Request $request, Response $response) {

    $start = $request->getAttribute('start') * 50;
    echo("  SELECT r.*, u.user_email, u.username, u.user_phone, u.image, u.user_status FROM referralsuggestions as r inner join mc_user as u ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto WHERE emailstatus='0' AND r.isdeleted <> '1' AND r.isdeleted <> '2' AND knowenteredby = '19' and markrem='0' and up.client_email <> u.user_email and user_status='1' and (r.distance >= 0 and r.distance < '30') ORDER BY r.id DESC LIMIT  $start, 100  ");


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rs = $pdo->query("    SELECT r.*, u.user_email, u.username, u.user_phone, u.image, u.user_status FROM referralsuggestions as r inner join mc_user as u ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto WHERE emailstatus='0' AND r.isdeleted <> '1' AND r.isdeleted <> '2' AND knowenteredby = '19' and markrem='0' and up.client_email <> u.user_email and user_status='1' and (r.distance >= 0 and r.distance < '30') ORDER BY r.id DESC LIMIT $start, 50 ");

        $knowids = $rs->fetchAll(PDO::FETCH_ASSOC);

        echo "Total records " . $rs->rowCount() . "<br/>";

        $i = 0;
        foreach ($knowids as $row) {

            $ratingrs = $pdo->query("select sum(ranking) as allrate from user_rating  where user_id='" . $row['knowreferedto'] . "'");

            if ($ratingrs->rowCount() > 0) {
                $allrate = $ratingrs->fetchAll(PDO::FETCH_ASSOC)[0]['allrate'];

                if ($allrate === NULL || $allrate == '') {
                    $i++;
                    $pdo->query("update referralsuggestions set markrem='1' where   id='" . $row['id'] . "'");
                    echo("update referralsuggestions set markrem='1' where   id='" . $row['id'] . "'");
                }

            }

        }
        $jsonresult = array('error' => '0', 'errmsg' => $i . " page fix complete successfully!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//remove unwanted referral suggestions
$app->post('/referralsuggestion/remove/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $introids = $allPostPutVars['introids'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query(" delete from referralsuggestions where id in (" . $introids . ")   ");

        $jsonresult = array('error' => '0', 'errmsg' => 'Referral suggestions removed successfully!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//fetch zip without distance values
$app->post('/managedistance/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rst = $pdo->query(" select * from referralsuggestions where distancecalculated='0' and  sourcezip<> '' and targetzip <>'' and sourcezip <> targetzip  limit 0, 10 ");


        $result = $rst->fetchAll(PDO::FETCH_ASSOC);
        $jsonresult = array('error' => '0', 'errmsg' => 'Zip codes details fetched!', 'results' => $result);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//save zip distance
$app->post('/zipdistance/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $distance = $allPostPutVars['distance'];
    $refsugid = $allPostPutVars['refsugid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query(" update  referralsuggestions set distancecalculated='1', distance='$distance'  where id ='$refsugid' ");
        $jsonresult = array('error' => '0', 'errmsg' => 'Distance saved successfully!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//common connections count
$app->post('/members/commonconnects/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['uid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userrst = $pdo->query("SELECT * FROM user_details where user_id = '$userid'");
        $user = $userrst->fetchAll(PDO::FETCH_ASSOC)[0];
        $groups = explode(",", $user['groups']);

        //first getting subquery for retrieving partners
        $where_in_set = " (  ";
        for ($i = 0; $i < sizeof($groups); $i++) {
            $groupid = $groups[$i];
            $where_in_set .= " FIND_IN_SET('$groupid', groups) ";
            if ($i < sizeof($groups) - 1) {
                $where_in_set .= " OR ";
            }
        }
        $where_in_set .= " ) ";


        $getpartnerquery = " SELECT a.user_id, b.username FROM user_details as a inner join mc_user as b on b.id = a.user_id 
		WHERE $where_in_set  AND  b.id != '1' and b.id <> '$userid' and user_pkg='Gold'  ";


        $rst = $pdo->query($getpartnerquery);
        if ($rst->rowCount() > 0) {
            $partnerids = $rst->fetchAll(PDO::FETCH_ASSOC);

            $matchingconnects = array();
            foreach ($partnerids as $partner) {
                $pid = $partner['user_id'];
                $username = $partner['username'];
                $getmcquery = "select count(*) as matchingcount from  user_people  where  user_id='$userid' and client_email in (select distinct client_email from  user_people  where  user_id='$pid')";

                $rst = $pdo->query($getmcquery);
                if ($rst->rowCount() > 0) {
                    foreach ($rst as $row) {
                        $matchingconnects[] = array('id' => $pid, 'username' => $username, 'matchingconnects' => $row['matchingcount']);
                    }
                }
            }
        }
        $jsonresult = array('error' => '0', 'errmsg' => "Matching connections fetched", 'results' => $matchingconnects);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//common connections list
$app->post('/members/commonconnects/getall/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['uid'];
    $partnerid = $allPostPutVars['partnerid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $getmcquery = "select * from  user_people  where  user_id='$userid' and client_email in (select distinct client_email from  user_people  where  user_id='$partnerid')";
        $rst = $pdo->query($getmcquery);
        if ($rst->rowCount() > 0)
            $jsonresult = array('error' => '0', 'errmsg' => "Matching connections fetched.", 'results' => $rst->fetchAll(PDO::FETCH_ASSOC));
        else
            $jsonresult = array('error' => '10', 'errmsg' => "No matching connections found!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//members get all members
$app->post('/members/getprofiles/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $goto1 = $allPostPutVars['page1'];
    $pagesize = 10;
    $start1 = ($goto1 - 1) * $pagesize;

    $goto2 = $allPostPutVars['page2'];
    $start2 = ($goto2 - 1) * $pagesize;

    $goto3 = $allPostPutVars['page3'];
    $start3 = ($goto3 - 1) * $pagesize;

    $keyword = $allPostPutVars['client'];
    if ($keyword != '')
        $where_name = " and username like  '$keyword%' ";
    else
        $where_name = " ";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		 user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,   publicprofile as k, 
		 profileisvisible as l,  tags as m ,  signup_type as n,  busi_name as o, user_type as p, 
		 busi_location_street as q,  busi_location as r,  busi_type as t,  busi_hours as u,  busi_website as v 
		 from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name order by username limit $start1, $pagesize ");
        $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page1count = ceil($result_count[0]['reccnt'] / 10);


        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		 user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,   publicprofile as k, 
		 profileisvisible as l,  tags as m ,  signup_type as n,  busi_name as o, user_type as p, 
		 busi_location_street as q,  busi_location as r,  busi_type as t,  busi_hours as u,  busi_website as v 
		 from mc_user where username <> ''  and id<>'1' and  user_status='10' $where_name order by username limit $start2, $pagesize ");
        $membersold = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='10' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page2count = ceil($result_count[0]['reccnt'] / 10);


        $member_ex = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		 user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,   publicprofile as k, 
		 profileisvisible as l,  tags as m ,  signup_type as n,  busi_name as o, user_type as p, 
		 busi_location_street as q,  busi_location as r,  busi_type as t,  busi_hours as u,  busi_website as v 
		 from mc_user where username <> ''  and id<>'1' and  user_status='100' $where_name order by username limit $start3, $pagesize ");
        $memberex = $member_ex->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='100' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page3count = ceil($result_count[0]['reccnt'] / 10);


        $jsonresult = array('error' => '0', 'errmsg' => 'Matching members are found!',
            'results' => $members, 'results_old' => $membersold, 'results_ex' => $memberex, 'page1' => $page1count, 'page2' => $page2count,
            'page3' => $page3count);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//common connections count
$app->get('/members/getall/{keyword}/', function (Request $request, Response $response, $args) {
    $keyword = $request->getAttribute('keyword');
    $users = array();
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query("select id, username from mc_user where username like   '$keyword%'");

        if ($member_rs->rowCount() > 0) {
            $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);
            $matchingconnects = array();
            foreach ($members as $members) {
                $users[] = array('code' => $members['id'], 'name' => $members['username']);
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('code' => '0', 'name' => 'No Match');
    }
    $response->getBody()->write(json_encode($users));
    return $response;
});


$app->post('/keyword/log/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $keyword = $allPostPutVars['keyword'];
    $city = $allPostPutVars['cityzip'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "insert into  mc_global_search_log (user_id, keyword, city_zip) values (?,?, ?) ";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($id, $keyword, $city));
        $jsonresult = array('error' => '0', 'errmsg' => 'Search log save!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Search log could not be save!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/recentupdatedknows/', function (Request $request, Response $response) {

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_know_update_log as a inner join user_people as b on a.know_id=b.id order by a.entrydate desc limit  20";
        $rst = $pdo->query($sql_query);
        $jsonresult = array('error' => '0', 'errmsg' => 'Search log save!', 'results' => $rst->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Search log could not be save!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/knows/getprofile/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from user_people where id = '$id' ";
        $rst = $pdo->query($sql_query);
        $profile = $rst->fetchAll(PDO::FETCH_ASSOC);

        $group = $profile[0]['user_group'];
        $sql_query = " select * from groups where id = '$group' ";
        $rst = $pdo->query($sql_query);
        $groupname = $rst->fetchAll(PDO::FETCH_ASSOC);

        $sql_query = "  select question, ranking from user_rating as a inner join questions as b on a.question_id = b.id where  user_id = '$id'  ";
        $rst = $pdo->query($sql_query);
        $ratings = $rst->fetchAll(PDO::FETCH_ASSOC);


        $sql_query = " select * from groups where id = '$group' ";
        $rst = $pdo->query($sql_query);
        $groupname = $rst->fetchAll(PDO::FETCH_ASSOC);


        $jsonresult = array('error' => '0', 'errmsg' => 'Know profile fetched!',
            'profile' => $profile, 'group' => $groupname, 'ratings' => $ratings

        );
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Know profile could not be fetched!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/diretmails/readmail/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['mailid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select * from mc_mailbox where id = '$id' ";
        $rst = $pdo->query($sql_query);
        $mails = $rst->fetchAll(PDO::FETCH_ASSOC);
        $jsonresult = array('error' => '0', 'errmsg' => 'Direct emails fetched!', 'results' => $mails);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Direct emails could not be fetched!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/commonvocations/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $knowid = $allPostPutVars['kid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query_vocations = "select client_profession from user_people where id = '$knowid' ";
        $rst = $pdo->query($sql_query_vocations);

        if ($rst->rowCount() > 0) {
            $vocations = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['client_profession'];
            $vocationlist = explode(',', $vocations);

            $vocation_where = ' where (';

            for ($i = 0; $i < sizeof($vocationlist); $i++) {
                $vocation_where .= 'find_in_set( "' . $vocationlist[$i] . '" , member_voc ) ';

                if ($i < sizeof($vocationlist) - 1) {
                    $vocation_where .= " or ";
                }
            }
            $vocation_where .= ')';
        }

        $sql_query = " select * from mc_common_vocation   " . $vocation_where;
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $target_vocations = $rst->fetchAll(PDO::FETCH_ASSOC);
            $targetvocs = '';
            for ($i = 0; $i < $rst->rowCount(); $i++) {
                $targetvocs .= $target_vocations[$i]['know_common_voc'];
                if ($i < $rst->rowCount() - 1) {
                    $targetvocs .= ",";
                }
            }
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'Common vocations fetched!', 'common_vocs' => $targetvocs, 'results' => $target_vocations);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Common vocations could not fetched!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/commonvocations/fill/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $source_voc = $allPostPutVars['source_voc'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $source_arr = array_filter(explode(',', $source_voc));
        $where_com_voc = " where (FIND_IN_SET('" . implode("', member_voc ) OR FIND_IN_SET('", $source_arr) . "', member_voc ))";

        $sql_query = " select * from mc_common_vocation " . $where_com_voc;
        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $target_vocations = $rst->fetchAll(PDO::FETCH_ASSOC);
            $targetvocs = '';
            for ($i = 0; $i < $rst->rowCount(); $i++) {
                $targetvocs .= $target_vocations[$i]['know_common_voc'];
                if ($i < $rst->rowCount() - 1) {
                    $targetvocs .= ",";
                }
            }
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'Common vocations could not fetched!', 'common_vocs' => $targetvocs, 'results' => $target_vocations);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Common vocations could not fetched!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//update vocations in knows
$app->post('/knows/update/vocations/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $knowid = $allPostPutVars['knowid'];
    $vocations = $allPostPutVars['vocations'];
    $comvocs = '';
    for ($i = 0; $i < sizeof($vocations) - 1; $i++) {
        $comvocs .= $vocations[$i] . ",";
    }
    $comvocs .= $vocations[$i];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select * from user_answers where user_id='$knowid' and question_id='9' ";

        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $ansrow = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['answer'];
            $sql_query = "update user_answers set answer = concat(answer , ? )  where user_id = ? and question_id='9' ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array("," . $comvocs, $knowid));
        } else {
            $sql_query = "insert into user_answers  (user_id, question_id, answer	) values (?,'9', ?) ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($knowid, $comvocs));
        }

        $log = array('error' => '0', 'errmsg' => $sql_query . 'Common Vocations saved!');
    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => $comvocs . $e->getMessage());
    }
    $response->getBody()->write(json_encode($log));
    return $response;
});


//calculate nearby zip codes
$app->post('/geolocation/nearbyzipcodes/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $zip = $allPostPutVars['zip'];
    $radius = $allPostPutVars['radius'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_city_geolocation where zip='$zip'   ";

        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $latlong = $rst->fetchAll(PDO::FETCH_ASSOC)[0];

            $lat = $latlong['latitude'];
            $lon = $latlong['longitude'];

            $sql = 'select distinct(zip) from mc_city_geolocation  ' .
                ' where (3958*3.1415926*sqrt((latitude-' . $lat . ')*(longitude-' . $lat . ') + ' .
                'cos(latitude/57.29578)*cos(' . $lat . '/57.29578)*(longitude-' . $lon . ')*(longitude-' . $lon . '))/180) <= ' . $radius . ';';
            $result = $pdo->query($sql);

            $zipcodeList = array();
            $rszip = $result->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rszip as $row) {
                array_push($zipcodeList, $row['zip']);
            }
        }

        $log = array('error' => '0', 'results' => $zipcodeList, 'errmsg' => 'Nearby zip codes fetched!');
    } catch (PDOException $e) {
        $log = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($log));
    return $response;
});


//Trending search log
$app->post('/trending/search/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['gotopage'];

    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select a.keyword, a.city_zip, b.username, b.user_email, b.image from  mc_global_search_log as a inner join mc_user as b on a.user_id=b.id  where b.id <> '1' order by a.id desc limit $start, $pagesize ";

        $sql_query_count = "select count(*) as reccnt from  mc_global_search_log as a inner join mc_user as b on a.user_id=b.id where b.id <> '1'    ";


        $ds = DIRECTORY_SEPARATOR;
        $imagepath = $_SERVER['DOCUMENT_ROOT'] . $ds . "images/";
        $rst = $pdo->query($sql_query);
        $logs = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            if ($logs[$i]['image'] !== NULL && $logs[$i]['image'] != '' && $logs[$i]['image'] != 'null') {
                $user_picture = (file_exists($imagepath . $logs[$i]['image']) ? $logs[$i]['image'] : "no-photo.png");
                $logs[$i]['image'] = $user_picture;
            } else {
                $logs[$i]['image'] = "no-photo.png";
            }
        }

        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages, 'errmsg' => 'Trending search logs generated!', 'results' => $logs);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/cities/getzipcodes/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['page'];
    $name = $allPostPutVars['city'];

    $name_where = " where city like '$name%' ";
    $pagesize = 10;
    if ($goto == '' || $goto == 0) $goto = 1;
    $start = ($goto - 1) * $pagesize;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_city_geolocation $name_where order by city limit $start, $pagesize";
        $sql_query_count = "select count(*) as reccnt from mc_city_geolocation $name_where order by city ";

        $rst = $pdo->query($sql_query);
        $cities = $rst->fetchAll(PDO::FETCH_ASSOC);
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'pages' => $pages,
            'errmsg' => 'City and zip codes fetched', 'results' => $cities);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/cities/zipupdate/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $lat = $allPostPutVars['lat_'];
    $long = $allPostPutVars['long_'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "update mc_city_geolocation set latitude=?, longitude= ? where id= ?";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($lat, $long, $id));

        $jsonresult = array('error' => '0', 'errmsg' => 'Latitude or longitude  for city updated!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/cities/requestlisting/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $cityname = $allPostPutVars['cityname'];
    $userid = $allPostPutVars['mid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select islisted from  groups where grp_name='$cityname' ";

        $rst = $pdo->query($sql_query);
        $result = $rst->fetchAll(PDO::FETCH_ASSOC);
        if ($rst->rowCount() > 0) {
            if ($result[0]['islisted'] == 0)
                $jsonresult = array('error' => '10', 'errmsg' => 'There is a pending city name listing request!');
            else
                $jsonresult = array('error' => '10', 'errmsg' => 'The city name is already listed!');
        } else {
            $sql_query = "insert into groups (grp_name, request_by) values ( ?, ? ) ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($cityname, $userid));
            $jsonresult = array('error' => '0', 'errmsg' => 'City name requested for listing!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'City name listing request failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/cities/getrequestlisting/', function (Request $request, Response $response) {

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select cg.id as i, cg.grp_name as g, u.username as n from  groups as cg " .
            " inner join mc_user as u on cg.request_by=u.id where cg.islisted='0' order by grp_name  ";

        $rst = $pdo->query($sql_query);
        $result = $rst->fetchAll(PDO::FETCH_ASSOC);
        if ($rst->rowCount() == 0) {
            $jsonresult = array('error' => '10', 'errmsg' => 'No new city listing request!');
        } else {
            $jsonresult = array('error' => '0', 'errmsg' => 'City listing requests are fetched!');
            $jsonresult['result'] = $result;
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'City name listing request failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/cities/updatelisting/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $act = $allPostPutVars['act'];
    $i = $allPostPutVars['i'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($act == 'r') {
            $sql_query = "delete from groups where id =   ?  ";
            $jsonresult['errmsg'] = 'City name removed from listing!';
        } else if ($act == 'a') {
            $sql_query = "update groups set islisted ='1' where id =   ?  ";
            $jsonresult['errmsg'] = 'City name listed successfully!';
        }

        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($i));
        $jsonresult = array('error' => '0');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'City name listing update failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/emailsprogram/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $heading = $allPostPutVars['heading'];
    $body = $allPostPutVars['body'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($id > 0) {
            $sql_query = "update mc_email_program set mail_heading=?, email_body=? where id=?";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($heading, $body, $id));
        } else {
            $sql_query = "insert into mc_email_program (mail_heading, email_body ) values (?,? )";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($heading, $body));
        }
        $jsonresult = array('error' => '0', 'errmsg' => 'Email saved!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Email could not be saved!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/emailsprogram/fetch/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $page = $allPostPutVars['page'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select id,  mail_heading as a, email_body as b from mc_email_program order by id desc";
        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        $jsonresult = array('error' => '0', 'errmsg' => 'Email retrieved!', 'results' => $results);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Email retreival failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/asignemail/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $em_client = $allPostPutVars['em_client'];
    $aemschedule = $allPostPutVars['aemschedule'];
    $radioemtemp = $allPostPutVars['radioemtemp'];
    $hr = $allPostPutVars['hr'];
    $min = $allPostPutVars['min'];
    $period = $allPostPutVars['period'];


    $aemschedule .= $hr . ":" . $min . ":00" . $period;

    $aemschedule = date('Y-m-d H:i:s', strtotime($aemschedule));
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "insert into mc_email_program_assigned (mail_id, client_id, assigned_date) values (?,?,?)";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($radioemtemp, $em_client, $aemschedule));
        $jsonresult = array('error' => '0', 'errmsg' => 'Email assigned!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/asignemail/updatedate/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $aemschedule = $allPostPutVars['aemschedule'];
    $hr = $allPostPutVars['hr'];
    $min = $allPostPutVars['min'];
    $period = $allPostPutVars['period'];

    $aemschedule .= $hr . ":" . $min . ":00" . $period;

    $aemschedule = date('Y-m-d H:i:s', strtotime($aemschedule));
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "update mc_email_program_assigned set assigned_date=? where  id=?";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($aemschedule, $id));
        $jsonresult = array('error' => '0', 'errmsg' => 'Email assigned!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/emailsprogram/fetchtimeline/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $count = 0;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select a.id as seqid, a.assigned_date as d, a.mail_stage,a.status,  b.mail_heading, b.id as mailid 
		from mc_email_program_assigned as a inner join mc_email_program as b on a.mail_id=b.id 
		where  client_id='$id' order by mail_stage desc";

        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        if ($rst->rowCount() > 0) {
            $count = $rst->rowCount();
        }
        $jsonresult = array('error' => '0', 'errmsg' => 'Email retrieved!', 'count' => $count, 'results' => $results);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Email retreival failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/emailsprogram/process/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id']; //email sequence id
    $sql_query = "select a.*, b.username, b.user_email from  mc_email_program_assigned as a inner join mc_user as b on a.client_id=b.id  where a.id='$id'  ";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $results = $pdo->query($sql_query);

        if ($results->rowCount() > 0) {
            $item = $results->fetchAll(PDO::FETCH_ASSOC)[0];

            $to = $item['user_email'];
            $name = $item['username'];
            $mailid = $item['mail_id'];
            $seqid = $item['id'];

            if ($mailid > 0) {
                $sql_query = "select * from mc_email_program where id='$mailid'";
                $rstmail = $pdo->query($sql_query);
                if ($rstmail->rowCount() > 0) {
                    $mailcontent = $rstmail->fetchAll(PDO::FETCH_ASSOC);
                    $mailheading = $mailcontent[0]['mail_heading'];
                    $mailbody = $mailcontent[0]['email_body'];

                    if ($mailheading != '' && $mailbody != '') {
                        $mailbody = '<p>Hi ' . $name . ",</p>" . $mailbody;

                        $ds = DIRECTORY_SEPARATOR;
                        $path = $_SERVER['DOCUMENT_ROOT'] . $ds;


                        if (file_exists($path . "templates/black_template_01.txt")) {
                            $template_part = file_get_contents($path . "templates/black_template_01.txt");
                        }


                        $mailbody = str_replace("{mail_body}", $mailbody, $template_part);
                        sendemail($to, $mailheading, $mailbody, $mailbody);
                        $pdo->query(" update mc_email_program_assigned set status='1' where id='$seqid ' ");

                    }
                }

            }

        }
        $jsonresult = array('error' => '0', 'errmsg' => "Sequence processed.");
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong. Please retry sending email!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


//voicemail save
$app->post('/assignvoicemail/save/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $vm_schedule = $allPostPutVars['vm_assigndate'];
    $vm_description = $allPostPutVars['vm_description'];
    $id = $allPostPutVars['id'];
    $vmid = $allPostPutVars['vmid'];
    $status = $allPostPutVars['s'];
    $hr = $allPostPutVars['vm_schedulehr'];
    $min = $allPostPutVars['vm_schedulemin'];
    $period = $allPostPutVars['vm_scheduleper'];


    if ($hr == 0 && $min == 0) {
        $vm_schedule = date('Y-m-d H:i:s', strtotime($vm_schedule));
    } else {
        $vm_schedule .= " " . $hr . ":" . $min . ":00 " . $period;
        $vm_schedule = date('Y-m-d H:i:s', strtotime($vm_schedule));
    }

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($vmid == 0) {
            $sql_query = "insert into mc_client_tracking (a_date, description, client_id ) values ( ?, ?, ?)";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($vm_schedule, $vm_description, $id));
        } else {
            if ($status == 1) {
                $sql_query = "update mc_client_tracking set status= '1' where  id=? ";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($vmid));
            } else {
                $sql_query = "update mc_client_tracking set a_date=?, description=? where  id=? ";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($vm_schedule, $vm_description, $vmid));
            }

        }


        $jsonresult = array('error' => '0', 'errmsg' => 'Voice mail schedule saved!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Voice mail could not be saved!');
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/voicemaltrack/fetchtimeline/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $id = $allPostPutVars['id'];
    $count = 0;
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select a.id as id, b.id as mid, a.a_date as a, a.description as b,a.status as c, b.username as d   
from mc_client_tracking as a inner join mc_user as b on a.client_id=b.id 
where  client_id='$id' order by a_date ";

        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        if ($rst->rowCount() > 0) {
            $count = $rst->rowCount();
        }
        $jsonresult = array('error' => '0', 'errmsg' => 'Voicemails assigned are retrieved!', 'count' => $count, 'results' => $results);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Voicemails retreival failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//members get all members
$app->post('/voicemails/allmembers/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $goto = $allPostPutVars['page'];
    if ($goto == '')
        $goto = 1;
    $pagesize = 10;
    $start = ($goto - 1) * $pagesize;


    $goto2 = $allPostPutVars['page2'];
    if ($goto2 == '')
        $goto2 = 1;
    $start2 = ($goto2 - 1) * $pagesize;

    $keyword = $allPostPutVars['client'];
    if ($keyword != '')
        $where_name = " and username like  '$keyword%' ";
    else
        $where_name = " ";


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,
		'None' lastbroadcast, 'None' nextbroadcast, 'NA' da from mc_user where username <> '' and id <>'1' $where_name and  id in (select distinct client_id from  mc_client_tracking) order by username limit $start , $pagesize ");
        $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' $where_name and id in (select distinct client_id from  mc_client_tracking)  ";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page1count = ceil($result_count[0]['reccnt'] / 10);

        $i = 0;
        foreach ($members as $row) {
            //last broadcast
            $voicemailtrack = $pdo->query("select a_date from mc_client_tracking where status='1' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $members[$i]['lastbroadcast'] = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0]['a_date'];
            }
            //next broadcast scheduled
            $voicemailtrack = $pdo->query("select a_date, description from mc_client_tracking where status='0' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $actionrow = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0];
                $members[$i]['nextbroadcast'] = $actionrow['a_date'];
                $members[$i]['da'] = $actionrow['description'];
            }
            $i++;
        }
        //members without voicemail set
        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,
		'None' lastbroadcast, 'None' nextbroadcast , 'NA' da from mc_user where username <> '' and id <>'1' $where_name and id not in (select distinct client_id from  mc_client_tracking)  order by username limit $start2 , $pagesize ");
        $members_novm = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1'  $where_name and id not in (select distinct client_id from  mc_client_tracking) ";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page2count = ceil($result_count[0]['reccnt'] / 10);

        $i = 0;
        foreach ($members_novm as $row) {
            //last broadcast
            $voicemailtrack = $pdo->query("select a_date from mc_client_tracking where status='1' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $members_novm[$i]['lastbroadcast'] = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0]['a_date'];
            }
            //next broadcast scheduled
            $voicemailtrack = $pdo->query("select a_date from mc_client_tracking where status='0' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $members_novm[$i]['nextbroadcast'] = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0]['a_date'];
                $members[$i]['da'] = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0]['description'];
            }
            $i++;
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'members are found!',
            'results' => $members, 'page' => $page1count, 'results2' => $members_novm, 'page2' => $page2count);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage() . 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//members get email programs
$app->post('/emailprogram/allmembers/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $goto1 = $allPostPutVars['page1'];
    $pagesize = 10;
    $start1 = ($goto1 - 1) * $pagesize;

    $goto2 = $allPostPutVars['page2'];
    $start2 = ($goto2 - 1) * $pagesize;

    $goto3 = $allPostPutVars['page3'];
    $start3 = ($goto3 - 1) * $pagesize;

    $keyword = $allPostPutVars['client'];
    if ($keyword != '')
        $where_name = " and username like  '$keyword%' ";
    else
        $where_name = " ";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  
		user_phone as g,  image as h, 'None' sh from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name order by username limit $start1, $pagesize ");
        $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page1count = ceil($result_count[0]['reccnt'] / 10);

        $i = 0;
        foreach ($members as $row) {
            $voicemailtrack = $pdo->query("select a_date, description from mc_client_tracking where status='0' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $actionrow = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0];
                $members[$i]['sh'] = $actionrow['description'];
            }
            $i++;
        }


        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  
		user_phone as g,  image as h, 'None' sh from mc_user where username <> ''  and id<>'1' and  user_status='10' $where_name order by username limit $start2, $pagesize ");
        $membersold = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='10' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page2count = ceil($result_count[0]['reccnt'] / 10);
        $i = 0;
        foreach ($membersold as $row) {
            $voicemailtrack = $pdo->query("select a_date, description from mc_client_tracking where status='0' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $actionrow = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0];
                $membersold[$i]['sh'] = $actionrow['description'];
            }
            $i++;
        }

        $member_ex = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  
		user_phone as g,  image as h, 'None' sh from mc_user where username <> ''  and id<>'1' and  user_status='100' $where_name order by username limit $start3, $pagesize ");
        $memberex = $member_ex->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='100' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page3count = ceil($result_count[0]['reccnt'] / 10);
        $i = 0;
        foreach ($memberex as $row) {
            $voicemailtrack = $pdo->query("select a_date, description from mc_client_tracking where status='0' and client_id='" . $row["a"] . "' order by id desc");
            if ($voicemailtrack->rowCount() > 0) {
                $actionrow = $voicemailtrack->fetchAll(PDO::FETCH_ASSOC)[0];
                $memberex[$i]['sh'] = $actionrow['description'];
            }
            $i++;
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'Matching members are found!',
            'results' => $members, 'results_old' => $membersold, 'results_ex' => $memberex, 'page1' => $page1count, 'page2' => $page2count,
            'page3' => $page3count);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/employee/savetask/', function (Request $request, Response $response, $args) {

    $allPostPutVars = $request->getParsedBody();
    $vmid = $allPostPutVars['vmid'];
    $eid = $allPostPutVars['eid'];
    $mid = $allPostPutVars['mid'];
    $name = $allPostPutVars['name'];//related member name
    $adate = $allPostPutVars['adate'];
    $taskdesc = $allPostPutVars['taskdesc'];
    $receipent = $allPostPutVars['receipent'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($vmid > 0) {
            $sql_query = "insert into mc_employee_task (user_id, vm_id, assignedon, task_desc, related_mid )
			values ( ?, ?, ?, ?, ? )";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($eid, $vmid, $adate, $taskdesc, $mid));

            $sql_query = "select user_email from mc_user where id='$eid'";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $email = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['user_email'];
                $subject = "You have a new task assigned at MyCity.com";
                $ds = DIRECTORY_SEPARATOR;
                $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
                $mailbody = "";
                if (file_exists($path . "templates/black_template_01.txt")) {
                    $template_part = file_get_contents($path . "templates/black_template_01.txt");
                }
                if (file_exists($path . "templates/new_task.txt")) {
                    $mailbody = file_get_contents($path . "templates/new_task.txt");
                }
                $mailbody = str_replace("{receipent_name}", $receipent, $mailbody);
                $mailbody = str_replace("{task_date}", $adate, $mailbody);
                $mailbody = str_replace("{related_member}", $name, $mailbody);
                $mailbody = str_replace("{task_body}", $taskdesc, $mailbody);
                $mailbody = str_replace("{mail_body}", $mailbody, $template_part);
                sendemail($email, $subject, $mailbody, $mailbody);
            }
        }
        $jsonresult = array('error' => '0', 'email' => $email, 'mailbody' => $mailbody, 'errmsg' => 'Employee task notification saved!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Employee task notification could not be saved!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//program participant
$app->post('/member/joinprogram/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $eid = $allPostPutVars['id'];
    $state = $allPostPutVars['s'];
    $ppid = $allPostPutVars['ppid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($ppid != '' && $ppid > 0) {
            //update
            $sql_query = "update mc_program_client set status='$state' where id=?  ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($ppid));
            $jsonresult = array('error' => '0', 'errmsg' => 'Program joined successfully!');
        } else {
            //insert after checking
            $sql_query = "select count(*) as reccnt from mc_program_client where client_id='$eid'";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $count = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['reccnt'];
                if ($count == 0) {
                    if ($state != '' && $state != 0) {
                        $sql_query = "insert into mc_program_client (client_id, program_id, join_date, status ) values ( ?, ?, NOW(), '1' )";
                    } else {
                        $sql_query = "insert into mc_program_client (client_id, program_id, join_date ) values ( ?, ?, NOW() )";
                    }
                    $stmt = $pdo->prepare($sql_query);
                    $stmt->execute(array($eid, '1'));


                    //email notification

                    $ds = DIRECTORY_SEPARATOR;
                    $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
                    if (file_exists($path . "templates/black_template_01.txt")) {
                        $template_part = file_get_contents($path . "templates/black_template_01.txt");
                    }

                    if (file_exists($path . "templates/3touch_program_alert.txt")) {
                        $mailbody = file_get_contents($path . "templates/3touch_program_alert.txt");
                    }

                    $mailheading = "New Member Signup in 3 Touch Program";
                    $mailbody = str_replace("{mail_body}", $mailbody, $template_part);
                    sendemail("admin@mycity.com", $mailheading, $mailbody, $mailbody);
                    $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => 'Program invite sent successfully!');
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'Program invite already sent!');
                }
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage() . 'Could not join in the program now. Please retry!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

$app->post('/program/getmembers/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $programid = $allPostPutVars['program'];
    $mid = $allPostPutVars['mid'];

    $clients_selected = array();
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($mid != 0) {
            $sql_query = "select clients_selected from mc_program_client where  program_id='$programid' and client_id='$mid'  ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $clients_selected_col = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['clients_selected'];
                if ($clients_selected_col != '')
                    $clients_selected = json_decode($clients_selected_col, TRUE);

            }
        }


        $sql_query = "select a.id as id, a.program_id as p,  a.client_id as c, a.join_date as d , 
		b.user_email as e, b.username as un , b.image as h,  b.user_phone as ph , 0 tq, 0 sfield from mc_program_client as a inner join mc_user as b 
		on a.client_id=b.id where a.program_id='$programid' order by a.join_date desc ";
        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);

        $i = 0;
        foreach ($results as $row) {
            $totprogques = $pdo->query("select count(*) as totque from mc_program_client_answer 
			where  client_id='" . $row["c"] . "' and program_id='" . $row["p"] . "' ");
            if ($totprogques->rowCount() > 0) {
                $results[$i]['tq'] = $totprogques->fetchAll(PDO::FETCH_ASSOC)[0]['totque'];
            }

            //checking sort field
            $results[$i]['sfield'] = array_search($results[$i]['c'], $clients_selected) ? '1' : '0';
            $i++;
        }

        if ($mid != 0) {
            usort($results, function ($a, $b) {
                return $b['sfield'] - $a['sfield'];
            });
        } else {
            usort($results, function ($a, $b) {
                return $a['tq'] - $b['tq'];
            });
        }
        $jsonresult = array('error' => '0', 'page' => '1', 'results' => $results, 'errmsg' => 'Program participants fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'page' => '0', 'results' => '', 'errmsg' => 'Could not fetch program participants. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/program/getallquestions/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select  id as i, program_id as p,  question as q from mc_program_questions order by question ";
        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        $jsonresult = array('error' => '0', 'page' => '1', 'results' => $results, 'errmsg' => 'Program questions fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'page' => '0', 'results' => '', 'errmsg' => 'Could not fetch questions. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/program/participant/getquestions/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $pid = $allPostPutVars['pid'];
    $mid = $allPostPutVars['mid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select id as i, question as q, 'Not Answered' a, 0 asg from mc_program_questions where program_id='$pid' ";

        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            $questno = $results[$i]['i'];
            $rs_ans = $pdo->query("select * from  mc_program_client_answer where question_no='$questno' and client_id= '$mid' and program_id='$pid'");
            if ($rs_ans->rowCount() > 0) {
                $rs_row = $rs_ans->fetchAll(PDO::FETCH_ASSOC)[0];
                if ($rs_row['answer'] != '') {
                    $results[$i]['a'] = $rs_row['answer'];
                    $results[$i]['asg'] = '1';
                } else {
                    $results[$i]['a'] = 'NA';
                    $results[$i]['asg'] = '0';
                }
            }
        }

        $count = $rst->rowCount();

        $jsonresult = array('error' => '0', 'page' => '1', 'count' => $count, 'results' => $results, 'errmsg' => $qry . 'Program participant questions fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'page' => '0', 'results' => '', 'errmsg' => 'Could not fetch questions. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/program/questions/assign/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $totalq = $allPostPutVars['totalq'];
    $pid = $allPostPutVars['pid'];
    $mid = $allPostPutVars['mid'];
    $relid = $allPostPutVars['relid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($totalq > 0) {
            for ($i = 0; $i < $totalq; $i++) {
                $qid = $allPostPutVars['q_' . $i];
                $action = $allPostPutVars['act_' . $i];

                $sql_query = "select count(*) as rcnt from mc_program_client_answer " .
                    " where client_id='$mid' and program_id='$pid' and question_no='$qid' and relation_id='$relid'";
                $rst = $pdo->query($sql_query);
                $rscount = $rst->fetchAll(PDO::FETCH_ASSOC);
                if ($rscount[0]['rcnt'] > 0) {
                    //if($action== 0 ) //delete
                    //{
                    //$sql_query = "delete from  mc_program_client_answer where client_id='$mid' and program_id='$pid' and question_no='$qid' and relation_id='$relid' " ;
                    //$pdo->query($sql_query);
                    //}
                } else {
                    if ($action == 1) //insert
                    {
                        $sql_query = "insert into mc_program_client_answer (client_id, question_no, program_id, relation_id, qdate  ) values( ? , ? ,?, ?, now())";
                        $stmt = $pdo->prepare($sql_query);
                        $stmt->execute(array($mid, $qid, $pid, $relid));
                    }
                }
            }

            $jsonresult = array('error' => '0', 'errmsg' => 'Changes are saved!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save your responses. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/program/participant/answer/save/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $quesid = $allPostPutVars['quesid'];
    $ans = $allPostPutVars['ans'];
    $add_ans = $allPostPutVars['add_ans'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "update mc_program_client_answer set answer=?, adate=NOW(), add_answer=? where id  = ?";
        $stmt = $pdo->prepare($sql_query);
        $stmt->execute(array($ans, $add_ans, $quesid));

        $jsonresult = array('error' => '0', 'errmsg' => 'Answer saved successfully!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save your responses. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//may need to remove this function (check before removing)
$app->post('/member/program/answers/save/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $totalq = $allPostPutVars['totalq'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($totalq > 0) {
            for ($i = 0; $i < $totalq; $i++) {
                $sql_query = "update mc_program_client_answer set answer=? where id  = ?";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($allPostPutVars['a' . $i], $allPostPutVars['q' . $i]));
            }

            $jsonresult = array('error' => '0', 'errmsg' => 'Your responses are saved!');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage() . 'Could not save your responses. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/program/question/save/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $pid = $allPostPutVars['pid'];
    $question = $allPostPutVars['question'];
    $qid = $allPostPutVars['qid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select count(*) as reccnt from mc_program_questions where id='$qid' ";

        $rst = $pdo->query($sql_query);
        $rscount = $rst->fetchAll(PDO::FETCH_ASSOC);

        if ($rscount[0]['reccnt'] > 0) {
            $sql_query = "update mc_program_questions set question=?, program_id=?  where id=? ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($question, $pid, $qid));
            $jsonresult = array('error' => '10', 'errmsg' => 'Question updated successfully!');
        } else {
            $sql_query = "insert into mc_program_questions (question, program_id ) values( ? , ?)";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($question, $pid));
            $jsonresult = array('error' => '0', 'errmsg' => 'Question saved successfully!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/program/activity/log/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $pid = $allPostPutVars['pid'];
    $mid = $allPostPutVars['mid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select t1.answer as a, t1.add_answer as b, t1.adate as c, t1.relation_id as d, t2.client_name as e  
		from mc_program_client_answer as t1 
		inner join user_people as t2 on t1.relation_id=t2.id  
		where t1.client_id='$mid' and t1.program_id='$pid' and t1.adate <> '' order by t1.adate desc";

        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        $jsonresult = array('error' => '0', 'count' => $rst->rowCount(), 'errmsg' => '3 touch program activity fetched!', 'result' => $results);
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/program/participant/addtracking/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    $idcsv = $allPostPutVars['ids'];
    $pid = $allPostPutVars['pid'];


    if ($idcsv != '') {
        $ids = explode(',', $idcsv);
    }

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select clients_selected from mc_program_client where client_id='$mid' and program_id='$pid'";

        $rst = $pdo->query($sql_query);
        $rscount = $rst->fetchAll(PDO::FETCH_ASSOC)[0];

        if ($rscount['clients_selected'] == '') {
            $data = array();
            for ($i = 0; $i < sizeof($ids); $i++) {
                $data[($i + 1)] = $ids[$i];
            }

            $sql_query = "update mc_program_client set clients_selected=? where client_id= ?  and program_id= ? ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array(json_encode($data), $mid, $pid));
            $jsonresult = array('error' => '0', 'errmsg' => 'Program participant added to tracked list!');


        } else {
            $data = json_decode($rscount['clients_selected'], TRUE);

            for ($i = 0, $k = 0; $i < sizeof($ids); $i++) {
                if (!array_search($ids[$i], $data)) {
                    $data[(sizeof($data) + 1)] = $ids[$i];
                    $k++;
                }
            }
            if ($k > 0) {
                $sql_query = "update mc_program_client set clients_selected=? where client_id= ?  and program_id= ? ";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array(json_encode($data), $mid, $pid));
                $jsonresult = array('error' => '0', 'errmsg' => 'Program participant added to tracked list!');
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/program/participant/removetracking/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    $idcsv = $allPostPutVars['ids'];
    $pid = $allPostPutVars['pid'];

    if ($idcsv != '') {
        $ids = explode(',', $idcsv);
    }

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select clients_selected from mc_program_client where client_id='$mid' and program_id='$pid'";

        $rst = $pdo->query($sql_query);
        $rscount = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
        $newdata = array();
        if ($rscount['clients_selected'] != '') {

            $data = json_decode($rscount['clients_selected'], TRUE);

            $data = array_diff($data, $ids);


            $i = 0;
            foreach ($data as $value) {
                $newdata[($i + 1)] = $value;
                $i++;
            }

            $sql_query = "update mc_program_client set clients_selected=? where client_id= ?  and program_id= ? ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array(json_encode($newdata), $mid, $pid));
            $jsonresult = array('error' => '0', 'errmsg' => 'Program participant added to tracked list!');

        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/program/participant/getassignedquestions/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $pid = $allPostPutVars['pid'];
    $mid = $allPostPutVars['mid'];
    $relid = $allPostPutVars['relid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select a.id as i, b.id as qno, question as q, answer as a, answer_form as af, a.add_answer as ads  from mc_program_client_answer as a inner join mc_program_questions as b on a.question_no=b.id where client_id='$mid' and a.program_id='$pid' and a.relation_id='$relid' ";

        $rst = $pdo->query($sql_query);
        $results = $rst->fetchAll(PDO::FETCH_ASSOC);
        $count = $rst->rowCount();
        $jsonresult = array('error' => $sql_query, 'page' => '1', 'count' => $count, 'results' => $results, 'errmsg' => $qry . 'Program participant questions fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'page' => '0', 'results' => '', 'errmsg' => 'Could not fetch questions. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/program/participant/checkrelation/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $idtotrack = $allPostPutVars['idtotrack'];
    $mid = $allPostPutVars['mid'];
    $pid = $allPostPutVars['pid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select clients_selected from mc_program_client where client_id='$mid' and program_id='$pid'";

        $rst = $pdo->query($sql_query);
        $rscount = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
        if ($rscount['clients_selected'] != '') {
            $data = json_decode($rscount['clients_selected'], TRUE);
            if (!array_search($idtotrack, $data)) {
                $isallowed = 0;
            } else {
                $isallowed = 1;
            }
        } else {
            $isallowed = 0;
        }


        $jsonresult = array('error' => '0', 'page' => '1', 'allow' => $isallowed, 'errmsg' => 'Program participant info fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not fetch info. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/program/member/performance/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $programid = $allPostPutVars['program'];
    $mid = $allPostPutVars['mid'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($mid == 0) {
            $sql_query = "select a.id as a,  a.client_id as b, a.program_id as c, a.join_date as d , a.status as e , 
			a.clients_selected as f, b.username as un, b.image as h, 'na' relations from mc_program_client as a inner join mc_user as b 
			on a.client_id=b.id where a.clients_selected <> '' and a.program_id='$programid' order by username ";
            $rst = $pdo->query($sql_query);
            $results = $rst->fetchAll(PDO::FETCH_ASSOC);

            $i = 0;
            foreach ($results as $row) {
                $relationships = $row['f'];
                $ids = json_decode($relationships, TRUE);
                $ids = implode(",", array_values($ids));

                $rsrelations = $pdo->query("select id as a, client_name as b from user_people where id in (" . $ids . ") order by client_name");
                if ($rsrelations->rowCount() > 0) {
                    $relations = $rsrelations->fetchAll(PDO::FETCH_ASSOC);
                    $results[$i]['relations'] = $relations;
                }
                $i++;
            }
        } else {
            $sql_query = "select a.id as a,  a.client_id as b, a.program_id as c, a.join_date as d , a.status as e , 
			a.clients_selected as f, b.username as un, b.image as h, 'na' relations from mc_program_client as a inner join mc_user as b 
			on a.client_id=b.id where b.id='$mid'  and a.program_id='$programid' order by username ";
            $rst = $pdo->query($sql_query);
            $results = $rst->fetchAll(PDO::FETCH_ASSOC);

            $i = 0;
            foreach ($results as $row) {
                $relationships = $row['f'];
                $ids = json_decode($relationships, TRUE);
                $ids = implode(",", array_values($ids));

                $rsrelations = $pdo->query("select id as a, client_name as b from user_people where id in (" . $ids . ") order by client_name");
                if ($rsrelations->rowCount() > 0) {
                    $relations = $rsrelations->fetchAll(PDO::FETCH_ASSOC);
                    $results[$i]['relations'] = $relations;
                }
                $i++;
            }

        }

        if ($mid != 0) {
            usort($results, function ($a, $b) {
                return $b['sfield'] - $a['sfield'];
            });
        } else {
            usort($results, function ($a, $b) {
                return $a['tq'] - $b['tq'];
            });
        }

        $jsonresult = array('error' => '0', 'page' => '1', 'results' => $results, 'errmsg' => 'Program participants fetched!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'page' => '0', 'results' => '', 'errmsg' => 'Could not fetch program participants. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


$app->post('/member/getallknows/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $client = $allPostPutVars['client'];
    $tagslist = $allPostPutVars['tags'];
    $goto = $allPostPutVars['page'];

    $start = ($goto - 1) * 10;
    $pagesize = 10;


    if (isset($tagslist) && $tagslist != '' && $tagslist != 'null') {
        $searchtags = explode(',', $tagslist);
        if (!empty($searchtags)) {
            $searchTag = " ";
            for ($i = 0; $i < sizeof($searchtags); $i++) {
                $searchTag .= " FIND_IN_SET ( '" . $searchtags[$i] . "' , tags) ";

                if ($i < sizeof($searchtags) - 1) {
                    $searchTag .= " OR ";
                }
            }

        }
    }

    if ($client != '') {
        if ($searchTag != '') {
            $where = " and ( client_name like '" . $client . "%' or   $searchTag    ) ";
        } else {
            $where = " and  client_name like '" . $client . "%' ";
        }
    } else {
        if ($searchTag != '') {
            $where .= " and  ( " . $searchTag . ")";;
        } else {
            $where = " ";
        }
    }


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_query = "select  id as a, client_name as b,  client_profession as c, client_lifestyle as d, 
		client_phone as e,  client_email as f,  client_location as g, 
		client_zip as h from user_people where user_id = '$userid'  $where  ORDER by client_name limit $start, $pagesize ";
        $rst = $pdo->query($sql_query);
        $result = $rst->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from user_people where user_id = '$userid'  $where";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $pages = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'page' => $pages, 'results' => $result, 'errmsg' => 'Knows are fetched!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Knows could not be fetched!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/searchallknows/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    $page = $allPostPutVars['page'];
    $src_name = $allPostPutVars['src_name'];
    $src_vocation = $allPostPutVars['src_vocation'];
    $pagesize = 10;
    $start = ($page - 1) * $pagesize;

    $where_clause = '';

    if ($src_vocation != '') {
        $where_clause .= " and find_in_set('$src_vocation', t1.client_profession) ";
    }

    if ($src_name != '') {
        $where_clause .= " and t1.client_name like '$src_name%' ";
    }
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select t1.id as a, client_name as b, client_profession as c, 
		client_lifestyle as d, client_location as e, 0 f from user_people as t1 
		inner join mc_user as t2 on t1.user_id=t2.id 
		where t2.privacyoption='0' and t1.user_id <> '$mid'  $where_clause  limit $start, $pagesize";

        $rst = $pdo->query($sql_query);
        $members = $rst->fetchAll(PDO::FETCH_ASSOC);
        $final = array();
        for ($i = 0; $i < $rst->rowCount(); $i++) {
            $know_id = $members[$i][a];

            //calculating average rating
            $query_know_rating = "  select sum(ranking) as total_rank from  user_rating  where user_id='$know_id' ";

            $knowrating = $pdo->query($query_know_rating);
            $know_rating = $knowrating->fetchAll(PDO::FETCH_ASSOC)[0]['total_rank'];
            if (is_null($know_rating))
                $members[$i][f] = "0";
            else
                $members[$i][f] = $know_rating;

            if ($know_rating >= 20)
                $final[$i] = $members[$i];

        }

        usort($final, function ($a, $b) {
            return $b['f'] - $a['f'];
        });


        $jsonresult = array('error' => '10', 'results' => $final, 'errmsg' => 'Search result found!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/know/invitetojoin/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();

    $subject = "Join MyCity.com and grow referral partners.";
    $id = $allPostPutVars['id'];
    $mid = $allPostPutVars['mid'];

    $token = md5($id);
    $tokenlength = strlen($id);
    $token = $id . $token;

    try {

        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select client_name, client_email from user_people where id='$id'   ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $row = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $receipent_name = $row['client_name'];
            $receipent_email = $row['client_email'];
        }

        $sql_query = "select username, user_email from mc_user where id='$mid'   ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $row = $rst->fetchAll(PDO::FETCH_ASSOC)[0];
            $sender_name = $row['username'];
            $sender_email = $row['user_email'];
        }

        $ds = DIRECTORY_SEPARATOR;
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds;


        if (file_exists($path . "templates/black_template_01.txt")) {
            $template_part = file_get_contents($path . "templates/black_template_01.txt");

            if (file_exists($path . "templates/join_mycity_invite.txt")) {
                $mailbody = file_get_contents($path . "templates/join_mycity_invite.txt");
                $mailbody = str_replace("{receipent}", $receipent_name, $mailbody);
                $mailbody = str_replace("{sender}", $sender_name, $mailbody);
                $mailbody = str_replace("{tokenid}", $token, $mailbody);
                $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
                $mailbody = str_replace("{tokenlengthhash}", md5($tokenlength), $mailbody);

                $template_part = str_replace("{mail_body}", $mailbody, $template_part);
                sendemail($receipent_email, "noreply@mycity.com", $subject, $template_part, $template_part);

                $jsonresult = array('error' => '0', 'errmsg' => "Invitation to connect sent!");
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Email template missing. Please consult admin for assistance.");
            }
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Something went wrong while sending invite. Please retry again!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/linkedin/temporary/import/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $user_id = $allPostPutVars['mid'];


    include_once("../includes/lib/excel_reader.php");
    $ds = DIRECTORY_SEPARATOR;
    $apppath = '';
    $storeFolder = 'assets/uploads';
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds . $storeFolder . $ds . 'linkeden_' . $user_id . ".xls";

    if (!file_exists($targetPath)) {
        echo "nofile";
        return;
    }

    $new = 0;
    $excel = new PhpExcelReader;
    $excel->read($targetPath);
    $sheet = $excel->sheets[0];

    $nr_sheets = count($excel->sheets);


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($sheet['numRows'] <= 1) {
            echo "<p class='alert'>There are no data to import.</p>";
        } else {
            $x = 2;
            $voc = '';

            while ($x <= $sheet['numRows'])  //cycle every row
            {
                $cname = $sheet['cells'][$x][1];
                $profession = $sheet['cells'][$x][2];
                $phone = $sheet['cells'][$x][3];
                $email = $sheet['cells'][$x][4];
                $location = $sheet['cells'][$x][5];
                $rating = $sheet['cells'][$x][7];

                if (trim($cname) != '' && trim($email) != '' && trim($profession) != '') {
                    $insnewknow = "INSERT INTO mc_linkedin_import_temp 
					( userid, fullname, email, phone, profession,  entrydate ) 
					VALUES ( ?, ?, ?, ?, ? , NOW() )";

                    $stmt = $pdo->prepare($insnewknow);
                    $stmt->execute(array($user_id, $cname, $email, $phone, $profession));

                }
                $x++;
            }

        }
        $jsonresult = array('error' => '10', 'import_count' => $x - 2, 'errmsg' => 'Import complete!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Could not save question. Please retry again!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/linkedin/show/temporaryimports/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $userid = $allPostPutVars['userid'];
    $name = $allPostPutVars['name'];
    $goto = $allPostPutVars['goto'];
    $start = ($goto - 1) * 10;

    if ($name != '') {
        $where = " and client_name like '$name%'";
    }

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = " select id as a, client_name as b, client_email as c, company as d, client_profession as e, tags as f, 
		entrydate as g, user_id as h,  client_phone as i, 'Not Sent' j  from  user_people where  find_in_set('res', tags)   $where LIMIT $start,10 ";

        $sql_query_count = "select count(*) as recnt from user_people where  find_in_set('res', tags)  $where  ";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $allknows = $rst->fetchAll(PDO::FETCH_ASSOC);
            $i = 0;
            foreach ($allknows as $row) {
                $rid = $row['a'];
                $sql_log = "select * from mc_invite_log where mid='$userid' and knowid='$rid'   ";
                $logrst = $pdo->query($sql_log);
                if ($logrst->rowCount() > 0) {
                    $allknows[$i]['j'] = $logrst->fetchAll(PDO::FETCH_ASSOC)[0]['senddate'];
                }
                $i++;
            }

            $rst_count = $pdo->query($sql_query_count);
            $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);

            $pages = ceil($result_count[0]['recnt'] / 10);
            $jsonresult = array('error' => '0', 'errmsg' => 'Imported knows/contacts are retrieved!',
                'pages' => $pages, 'result' => $allknows);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No imported know/contact found!');
        }

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/connect/email/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $email = $allPostPutVars['email'];
    $name = $allPostPutVars['name'];
    $mid = $allPostPutVars['mid'];
    $id = $allPostPutVars['id'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from mc_user where id = '$mid'";

        $subject = "You received a connection request from Mycity";
        $ds = DIRECTORY_SEPARATOR;
        $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
        $mailbody = "";

        if (file_exists($path . "templates/black_template_01.txt")) {
            $template_part = file_get_contents($path . "templates/black_template_01.txt");
        }
        if (file_exists($path . "templates/connect_invite.txt")) {
            $mailbody = file_get_contents($path . "templates/connect_invite.txt");
        }


        $rst = $pdo->query($sql_query);
        if ($rst->rowCount() > 0) {
            $publicprofile = $rst->fetchAll(PDO::FETCH_ASSOC)[0]['publicprofile'];

            $publicprofile .= "&cts=" . $mid . "&ctr=" . $id;
            $mailbody = str_replace("{mail_body}", $mailbody, $template_part);
            $mailbody = str_replace("{name}", $name, $mailbody);
            $mailbody = str_replace("{profile_url}", $publicprofile, $mailbody);
            sendemail($email, $subject, $mailbody, $mailbody);
            $jsonresult = array('error' => '0', 'email' => $email, 'mail' => $mailbody,
                'errmsg' => 'MyCity connection request sent!');

            $sql_query = "insert into mc_invite_log ( mid, knowid , senddate  ) values(?,?, now() ) ";
            $stmt = $pdo->prepare($sql_query);
            $stmt->execute(array($mid, $id));
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'MyCity connection request could not be sent!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

//connection
$app->post('/member/getconnect/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $sid = $allPostPutVars['sid'];
    $rid = $allPostPutVars['rid'];


    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_query = "select * from user_people where id='$rid'";
        $rst = $pdo->query($sql_query);

        if ($rst->rowCount() > 0) {
            $result = $rst->fetchAll(PDO::FETCH_ASSOC);
            $phone = $result[0]['phone'];
            $email = $result[0]['email'];
            $name = $result[0]['fullname'];
            $pass = "user" . mt_rand(10, 1000);


            $results = $pdo->query("select * from mc_user where user_email = '$email' ");
            if ($results->rowCount() == 0) {
                $sql_query = "insert into mc_user ( user_email, user_pass, username, user_phone) values(?,?,?,? ) ";
                $stmt = $pdo->prepare($sql_query);
                $stmt->execute(array($email, md5($pass), $name, $phone));
                $log = array('error' => '0', 'errmsg' => 'Member created!');
                $receipentid = $pdo->lastInsertId();
                $stmt = $pdo->prepare("insert into mc_member_connections (firstpartner, secondpartner,request_type, requestdate ) values (?,?, '1', NOW() )  ");
                $stmt->execute(array($sid, $receipentid));
                $jsonresult = array('error' => '0', 'errmsg' => 'Connection accepted!');
            } else {
                $receipentid = $results->fetchAll(PDO::FETCH_ASSOC)[0]['id'];

                $results = $pdo->query("select * from mc_member_connections where  (firstpartner='$sid' and secondpartner='$receipentid') and  request_type='1'  ");
                if ($results->rowCount() == 0) {
                    $stmt = $pdo->prepare("insert into mc_member_connections (firstpartner, secondpartner,request_type, requestdate ) values (?,?, '1', NOW() )  ");
                    $stmt->execute(array($sid, $receipentid));
                    $jsonresult = array('error' => '0', 'errmsg' => 'Connection accepted!');
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'Connection already exists!');
                }

            }
        } else {
            $jsonresult = array('error' => '100', 'errmsg' => 'Connection request doest not exists.');
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Connection invite could not be processed.');
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/getassignedclient/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $goto1 = $allPostPutVars['page1'];
    $uid = $allPostPutVars['mid'];
    $pagesize = 10;
    $start1 = ($goto1 - 1) * $pagesize;
    $keyword = $allPostPutVars['client'];

    if ($keyword != '')
        $where_name = " and username like  '$keyword%' ";
    else
        $where_name = " ";

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query(" SELECT distinct related_mid as a, b.username, b.user_email from  mc_employee_task as a inner join mc_user as b on a.related_mid=b.id 
		where a.user_id='$uid' $where_name order by username limit $start1, $pagesize ");
        $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from  mc_employee_task as a inner join mc_user as b on a.related_mid=b.id 
		where a.user_id='$uid' $where_name";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page1count = ceil($result_count[0]['reccnt'] / 10);
        $jsonresult = array('error' => '0', 'errmsg' => 'Matching members are found!', 'results' => $members, 'page1' => $page1count);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/members/profiles/all/', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $goto1 = $allPostPutVars['page1'];
    $pagesize = 10;
    $start1 = ($goto1 - 1) * $pagesize;


    $keyword = $allPostPutVars['client'];
    if ($keyword != '')
        $where_name = " and username like  '$keyword%' ";
    else
        $where_name = " ";

    $type = $allPostPutVars['type'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $member_rs = $pdo->query("select id as a,  user_email as b,  user_pass as c,  username as d ,  user_role as e, 
		 user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,   publicprofile as k, 
		 profileisvisible as l,  tags as m ,  signup_type as n,  busi_name as o, user_type as p, 
		 busi_location_street as q,  busi_location as r,  busi_type as t,  busi_hours as u,  busi_website as v 
		 from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name  and is_employee='$type' order by username limit $start1, $pagesize ");
        $members = $member_rs->fetchAll(PDO::FETCH_ASSOC);

        $sql_query_count = "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='1' $where_name and is_employee='$type'";
        $rst_count = $pdo->query($sql_query_count);
        $result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
        $page1count = ceil($result_count[0]['reccnt'] / 10);

        $jsonresult = array('error' => '0', 'errmsg' => 'Matching members are found!',
            'results' => $members, 'page1' => $page1count);

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/editstaff/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $idcsv = $allPostPutVars['ids'];
    $role = $allPostPutVars['role'];
    $state = $allPostPutVars['state'];
    if ($role == 'admin') {
        if ($idcsv != '') {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("update mc_user set is_employee = '$state' WHERE  id  in ( $idcsv  )");
        }
    }
});

$app->post('/staff/activity/log/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $staffid = $allPostPutVars['staffid'];
    $cid = $allPostPutVars['cid'];
    $actdesc = $allPostPutVars['actdesc'];
    try {

        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmhdl = $pdo->prepare("insert into mc_employee_activity_log (emp_id, client_id, work_on, action) 
		values ( ?, ?, now(), ?)  ");
        $stmhdl->execute(array($staffid, $cid, $actdesc));
        $jsonresult = array('error' => '1', 'errmsg' => 'Activity details saved successfully!');

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No matching member found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/staff/activity/getlog/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $staffid = $allPostPutVars['mid'];
    $where_mid = '';

    try {
        if ($staffid != 0) {
            $where_mid = " where   emp_id='$staffid' ";
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tasks_rs = $pdo->query("select a.id as a, work_on as b, action as c, b.username as d, 
			b.user_pkg as e from mc_employee_activity_log as a inner join mc_user as b on a.client_id=b.id 
			$where_mid  order by work_on desc");
            $tasks = $tasks_rs->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $pdo = getPDO($this);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $tasks_rs = $pdo->query("select a.id as a, a.emp_id as ei, 0 en, work_on as b, action as c, b.username as d, 
			b.user_pkg as e from mc_employee_activity_log as a inner join mc_user as b on a.client_id=b.id 
			$where_mid  order by work_on desc");
            $tasks = $tasks_rs->fetchAll(PDO::FETCH_ASSOC);
            $i = 0;
            foreach ($tasks as $row) {
                $staff_rs = $pdo->query("select username from mc_user where id='" . $row['ei'] . "'");
                $staffname = $staff_rs->fetchAll(PDO::FETCH_ASSOC)[0]['username'];
                $tasks[$i]['en'] = $staffname;
                $i++;
            }

            usort($tasks, function ($a, $b) {
                return $b['ei'] - $a['ei'];
            });
        }
        $jsonresult = array('error' => '0', 'results' => $tasks, 'errmsg' => 'Activity details fetched!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'No activity log found!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});


$app->post('/know/copyfields/', function (Request $request, Response $response) {

    $allPostPutVars = $request->getParsedBody();
    $sourceid = $allPostPutVars['id'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rs_source = $pdo->query("select * from user_people where id='$sourceid' ");
        $source_know = $rs_source->fetchAll(PDO::FETCH_ASSOC);

        $phone = $source_know[0]['client_phone'];
        $name = $source_know[0]['client_name'];
        $email = $source_know[0]['client_email'];
        $cr_tag = $source_know[0]['tags'];
        $cr_location = $source_know[0]['client_location'];
        $zip = $source_know[0]['client_zip'];

        $inner_query = "select * from user_people where   
		client_name='$name' and client_email='$email' and 
		( client_zip = '' or client_location = ''  or 
		 client_zip IS NULL  or client_location IS NULL )  ";
        $rs_targets = $pdo->query($inner_query);

        if ($rs_targets->rowCount() > 0) {
            $targets = $rs_targets->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < $rs_targets->rowCount(); $i++) {
                $targetid = $targets[$i]['id'];
                $city = $targets[$i]['client_location'];
                $tags = $targets[$i]['tags'];

                if ($city != '') {
                    $city = $cr_location . "," . $city;
                } else {
                    $city = $cr_location;
                }
                $allcities = explode(',', $city);
                $allcities = array_unique(array_filter($allcities));
                $city = implode(',', $allcities);


                if ($tags != '') {
                    $tags = $cr_tag . "," . $tags;
                } else {
                    $tags = $cr_tag;
                }
                $alltags = explode(',', $tags);
                $alltags = array_unique(array_filter($alltags));
                $tags = implode(',', $alltags);

                $sql_query_update = 'update user_people set client_phone=?, client_zip=?, client_location=?, tags = ?  where id=?';
                $stmt = $pdo->prepare($sql_query_update);
                $stmt->execute(array($phone, $zip, $city, $tags, $targetid));
            }
        }

        $jsonresult = array('error' => '0', 'errmsg' => 'Fields are copied and updated to empty know records!');
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => 'Copying fields has failed!');
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/get_knows/tag/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    $tag = $allPostPutVars['tag'];
    $mzip = $allPostPutVars['mzip'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $allknows = array();
        $sql_query_zip = "SELECT zip FROM  user_details where user_id='$mid'  ";
        $rst_zip = $pdo->query($sql_query_zip);
        if ($rst_zip->rowCount() > 0) {
            $mzip = $rst_zip->fetchAll(PDO::FETCH_ASSOC)[0]['zip'];

            $sql_query = "select a.*, 0 distance from user_people as a inner join user_answers as b on a.id=b.user_id   
			where find_in_set('$tag', tags)  and a.user_id='$mid' 
			and a.client_zip <> ''  limit 0, 100 ";

            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $resultset = $rst->fetchAll(PDO::FETCH_ASSOC);

                foreach ($resultset as $row) {
                    $zipqry = "select * from mc_city_geolocation where zip in ('" . $row['client_zip'] . "', '" . $mzip . "' ) ";
                    $rsgeolocs = $pdo->query($zipqry);
                    if ($rsgeolocs->rowCount() == 2) {
                        $geolocs = $rsgeolocs->fetchAll(PDO::FETCH_ASSOC);
                        $latitude1 = $geolocs[0]['latitude'];
                        $longitude1 = $geolocs[0]['longitude'];
                        $latitude2 = $geolocs[1]['latitude'];
                        $longitude2 = $geolocs[1]['longitude'];

                        $theta = $longitude1 - $longitude2;
                        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                        $distance = acos($distance);
                        $distance = rad2deg($distance);
                        $distance = $distance * 60 * 1.1515;
                        $distance = (round($distance, 2));
                        if ($distance <= 30) {
                            $row['distance'] = $distance;
                        }
                        $allknows[] = $row;
                    }
                }

                $jsonresult = array('error' => '0', 'errmsg' => 'Connections are retrieved!',
                    'results' => $allknows);
            } else
                $jsonresult = array('error' => '10', 'errmsg' => 'No matching connection found!',
                    'results' => $allknows);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No matching connection found!',
                'results' => $allknows);
        }
    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }

    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


//rated 6 email
$app->post('/email/rated6invite/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $email = $allPostPutVars['email'];
    $receipentname = $allPostPutVars['receipent'];
    $emailbody = $allPostPutVars['emailbody'];
    $knowid = $allPostPutVars['knowid'];
    $partnerid = $allPostPutVars['partner'];
    $ds = DIRECTORY_SEPARATOR;
    $apppath = '';
    $path = $_SERVER['DOCUMENT_ROOT'] . $ds;
    $html = '';
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $subject = 'Invitation to join mycity.com ';
        $headers = "From: bob@mycity.com\r\n";
        $headers .= "Reply-To: bob@mycity.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        if ($email == '') {
            $sql_query = "select client_email FROM user_people  WHERE id  = '$knowid' ";
            $rst = $pdo->query($sql_query);
            if ($rst->rowCount() > 0) {
                $sp = $rst->fetchAll(PDO::FETCH_OBJ);
                $email = $sp[0]->client_email;
            }
        }

        if (file_exists($path . "templates/black_template_01.txt")) {
            $template_part = file_get_contents($path . "templates/black_template_01.txt");
        }
        $mailbody = str_replace("{mail_body}", $emailbody, $template_part);
        sendemail($email, $subject, $mailbody, $mailbody);
        $hash = md5($knowid . $partnerid);
        $pdo->query("update mc_invite_know_log 
		set is_sent='1'
		where know_id='$knowid' and  partner_id='$partnerid' ");
        $jsonresult = array('error' => '0', 'errmsg' => "Invite to join mycity.com sent!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => "Invite to join mycity.com could not be sent!");
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;

});

$app->post('/email/rated6invite/getlog/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $offset = $allPostPutVars['page'];

    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rs_invitelist = $pdo->query(" select a.*, 
		(select username  from mc_user where id = a.partner_id) as member_name, 
		(select client_name  from user_people where id = a.know_id) as know_name from mc_invite_know_log as a ");

        $jsonresult = array('error' => '0', 'results' => $rs_invitelist->fetchAll(PDO::FETCH_OBJ), 'errmsg' => "Knows invited to join mycity.com are fetched!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->post('/member/seo/get/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $mid = $allPostPutVars['mid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $pdo->query("select keywords, meta from user_details where user_id='$mid'  ");


        $jsonresult = array('error' => '0', 'results' => $result->fetchAll(PDO::FETCH_OBJ), 'errmsg' => "SEO keywords and meta tags updated!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'results' => '', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});

$app->post('/member/seo/save/', function (Request $request, Response $response) {
    $allPostPutVars = $request->getParsedBody();
    $tags = $allPostPutVars['tags'];
    $keywords = $allPostPutVars['keywords'];
    $mid = $allPostPutVars['mid'];
    try {
        $pdo = getPDO($this);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query("update user_details 
		set keywords='$keywords', meta='$tags'
		where user_id='$mid'  ");
        $jsonresult = array('error' => '0', 'errmsg' => "SEO keywords and meta tags updated!");

    } catch (PDOException $e) {
        $jsonresult = array('error' => '1', 'errmsg' => $e->getMessage());
    }
    $response->getBody()->write(json_encode($jsonresult));
    return $response;
});


$app->run();