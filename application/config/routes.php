<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'home';
$route['404_override'] = 'page_not_found';
$route['translate_uri_dashes'] = FALSE;
$route['profile/(:any)'] = 'profile/view/$1';
$route['profile/(:any)/(:num)'] = 'profile/view/$1';
$route['profile/invite/'] = 'profile/invite/';
$route['mails/inbox'] = 'mails';
$route['mails/inbox/(:num)'] = 'mails'; 
$route['mails/inbox/(:num)/(:num)'] = 'mails'; 
$route['mails/outbox/(:num)'] = 'mails/outbox'; 
$route['mails/outbox/(:num)/(:num)'] = 'mails/outbox'; 
$route['business/search'] = 'business';
$route['business/search/(:num)'] = 'business';
$route['business/search/knows'] = 'business/knows';
$route['business/search/knows/(:num)'] = 'business/knows';
$route['connection/(:num)'] = 'connection';
$route['connection/import-from-linkedin'] = 'connection/import_from_linkedin';
$route['configuration/privacy'] = 'configuration/index';
$route['my-partners'] = 'my_partners';
$route['my-partners/highest-rated'] = 'my_partners/highest_rated';
$route['nearby-members'] = 'member/nearby_members'; //members who are connected and are nearby 
$route['dashboard/reverse-tracking'] = 'dashboard/reverse_tracking';
$route['dashboard/help'] = 'faqs/help_instruction'; 
$route['terms-and-conditions'] = 'terms_and_conditions'; 
$route['blog/(:any)'] = 'blog/read'; 
$route['join-my-city'] = 'join_my_city'; 
$route['my-network'] = 'my_network';
$route['my-network/(:num)'] = 'my_network';
$route['my-network/add'] = 'my_network/add';
$route['my-network/import_from_linkedin'] = 'my_network/import_from_linkedin'; 
$route['my-network/file_upload'] = 'my_network/file_upload';
$route['my-network/import'] = 'my_network/import';
$route['my-network/connections'] = 'my_network/my_connections';
$route['my-network/connections/(:num)'] = 'my_network/my_connections'; 
$route['my-network/wizard'] = 'my_network/wizard';
$route['my-network/search'] = 'my_network/search';
$route['my-network/autocomplete_left_name'] = 'my_network/autocomplete_left_name';
$route['my-network/import'] = 'my_network/import';
$route['my-network/import-delayed'] = 'my_network/import_delayed';
$route['my-network/requests_received'] = 'my_network/requests_received';
$route['my-network/autocomplete_name'] = 'my_network/autocomplete_name';
$route['dashboard/client-tracking'] = 'dashboard/client_tracking';
$route['dashboard/client-tracking/(:any)'] = 'dashboard/client_tracking';
$route['dashboard/client-tracking/(:any)/(:num)'] = 'dashboard/client_tracking/(:any)/';
$route['dashboard/client-tracking/(:any)/(:num)/(:num)'] = 'dashboard/client_tracking/(:any)/(:num)/';
$route['program/relations/timeline'] = 'program/relations';
$route['program/relations/timeline/(:num)'] = 'program/relations';
$route['program/relations/(:num)'] = 'program/relations';  
$route['program/question/change/(:num)'] = 'program/question';  
$route['dashboard/setup-email'] = 'dashboard/setup_email';   
$route['dashboard/setup-email/(:num)'] = 'dashboard/setup_email';  
$route['dashboard/clients-voice-mails'] = 'dashboard/clients_voice_mails';  
$route['dashboard/clients-voice-mails/(:num)'] = 'dashboard/clients_voice_mails';
$route['dashboard/clients-voice-mails/(:num)/(:num)'] = 'dashboard/clients_voice_mails';  
$route['dashboard/client-without-voice-mails'] = 'dashboard/clients_no_voice_mails'; 
$route['dashboard/client-without-voice-mails/(:num)'] = 'dashboard/clients_no_voice_mails'; 
$route['dashboard/client-without-voice-mails/(:num)/(:num)'] = 'dashboard/clients_no_voice_mails';
$route['member'] = 'member';
$route['member/(:num)'] = 'member';
$route['member/compose-email'] = 'member/compose_email';
$route['member/compose-email/(:num)'] = 'member/compose_email';
$route['member/switch-account'] = 'member/switch_account';
$route['member/switch-account/(:num)'] = 'member/switch_account';
$route['login/switch-user'] = 'login/switch_user';
$route['manage-vocations'] = 'Manage_vocations';
$route['manage-vocations/common-vocation'] = 'Manage_vocations/common_vocation';
$route['manage-vocations/common-vocation/change/(:num)'] = 'Manage_vocations/common_vocation';
$route['manage-lifestyle'] = 'Manage_lifestyle';
$route['manage-helpbutton'] = 'Manage_helpbutton';
$route['manage-helpbutton/change/(:num)'] = 'Manage_helpbutton';
$route['testimonials/manage/change/(:num)'] = 'testimonials/manage/change';
$route['manage-tags'] = 'Manage_tags';
$route['faqs/edit/(:num)'] = 'faqs/edit';
$route['manage-groups'] = 'Manage_groups'; 
$route['manage-groups/manage-new-listing'] = 'Manage_groups/new_listing_request';
$route['manage-groups/manage-new-listing/add/(:num)'] = 'Manage_groups/new_listing_request';
$route['manage-groups/manage-new-listing/remove/(:num)'] = 'Manage_groups/new_listing_request';
$route['manage-groups/manage-new-listing/add/(:num)'] = 'Manage_groups/new_listing_request';
$route['manage-groups/manage-new-listing/remove/(:num)'] = 'Manage_groups/new_listing_request'; 
$route['member/incomplete-signup'] = 'member/incomplete_signup';
$route['member/incomplete-signup/(:num)'] = 'member/incomplete_signup';
$route['dashboard/top-rated-knows'] = 'dashboard/top_rated_knows';
$route['dashboard/top-rated-knows/invite'] = 'dashboard/top_rated_knows';
$route['dashboard/top-rated-knows/(:num)'] = 'dashboard/top_rated_knows';
$route['dashboard/top-rated-knows/(:num)/(:num)'] = 'dashboard/top_rated_knows';
$route['dashboard/top-rated-knows/invite/(:num)'] = 'dashboard/top_rated_knows';
$route['dashboard/top-rated-knows/invite/(:num)/(:num)'] = 'dashboard/top_rated_knows';
$route['invite-knows'] = 'Invite_knows';
$route['invite-knows/autocomplete_know_names'] = 'Invite_knows/autocomplete_know_names'; 
$route['dashboard/search-log'] = 'dashboard/search_log'; 
$route['sign-up'] = 'Sign_up'; 
