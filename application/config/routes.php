<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = 'meet/index';
$route['register'] = 'meet/register';
$route['newreg'] = 'meet/newreg';
$route['member/(:any)'] = 'meet/member/$1';
$route['friendlist/(:any)'] = 'meet/friendlist/$1/$2';
$route['forum/(:any)'] = 'meet/forum/$1/$2';
$route['chatboard/(:any)'] = 'meet/chatboard/$1';
$route['logout'] = 'meet/logout';

// Ajax
$route['meet_ajax/checkAccount'] = 'meet_ajax/checkAccount';
$route['meet_ajax/checkEmail'] = 'meet_ajax/checkEmail';
$route['meet_ajax/get_section'] = 'meet_ajax/get_section';
$route['meet_ajax/save_profile'] = 'meet_ajax/save_profile';
$route['meet_ajax/update_statue'] = 'meet_ajax/update_statue';
$route['meet_ajax/update_pic'] = 'meet_ajax/update_pic';
$route['meet_ajax/crop_pic'] = 'meet_ajax/crop_pic';
$route['meet_ajax/addpage'] = 'meet_ajax/addpage';
$route['meet_ajax/addpage_boardlist'] = 'meet_ajax/addpage_boardlist';
$route['meet_ajax/addpage_forum'] = 'meet_ajax/addpage_forum';
$route['meet_ajax/open_forum'] = 'meet_ajax/open_forum';
$route['meet_ajax/post_msg'] = 'meet_ajax/post_msg';
$route['meet_ajax/login'] = 'meet_ajax/login';

$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */