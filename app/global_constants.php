<?php
/* Global constants for site */



define("ADMIN_FOLDER", "admin/");
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', base_path() );
define('APP_PATH', app_path());


	
define('WEBSITE_URL', url('/').'/');
define('WEBSITE_JS_URL', WEBSITE_URL . 'js/');
define('WEBSITE_CSS_URL', WEBSITE_URL . 'css/');
define('WEBSITE_IMG_URL', WEBSITE_URL . 'img/');
define('WEBSITE_UPLOADS_ROOT_PATH', ROOT . DS . 'uploads' .DS );
define('WEBSITE_UPLOADS_URL', WEBSITE_URL . 'uploads/');

define('WEBSITE_ADMIN_URL', WEBSITE_URL.ADMIN_FOLDER );
define('WEBSITE_ADMIN_IMG_URL', WEBSITE_ADMIN_URL . 'img/');
define('WEBSITE_ADMIN_JS_URL', WEBSITE_ADMIN_URL . 'js/');
define('WEBSITE_ADMIN_FONT_URL', WEBSITE_ADMIN_URL . 'fonts/');
define('WEBSITE_ADMIN_CSS_URL', WEBSITE_ADMIN_URL . 'css/');

define('SETTING_FILE_PATH', APP_PATH . DS . 'settings.php');
define('MENU_FILE_PATH', APP_PATH . DS . 'menus.php');

define('CK_EDITOR_URL', WEBSITE_UPLOADS_URL . 'ckeditor_pic/');
define('CK_EDITOR_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH . 'ckeditor_pic' . DS);


define('SLIDER_URL', WEBSITE_UPLOADS_URL . 'slider/');
define('SLIDER_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'slider' . DS); 


define('USER_PROFILE_IMAGE_URL', WEBSITE_UPLOADS_URL . 'PROFILE_IMG');
define('USER_PROFILE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'PROFILE_IMG' . DS);
define('USER_PROFILE_IMAGE_MEDIUM_IMAGE_URL', WEBSITE_UPLOADS_URL . 'PROFILE_IMG');
define('USER_PROFILE_IMAGE_MEDIUM_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'PROFILE_IMG' . DS);
define('USER_PROFILE_IMAGE_THUMBNAIL_IMAGE_URL', WEBSITE_UPLOADS_URL . 'PROFILE_IMG');
define('USER_PROFILE_IMAGE_THUMBNAIL_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'PROFILE_IMG' . DS);

define('CEREMONY_EVENT_IMAGE_URL', WEBSITE_UPLOADS_URL . 'EVENTLOGO/');
define('CEREMONY_EVENT_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTLOGO' . DS); 

define('CEREMONY_EVENT_MEDIUM_IMAGE_URL', WEBSITE_UPLOADS_URL . 'EVENTLOGO/');
define('CEREMONY_EVENT_MEDIUM_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTLOGO/' . DS);

define('CEREMONY_EVENT_THUMBNAIL_IMAGE_URL', WEBSITE_UPLOADS_URL . 'EVENTLOGO/');
define('CEREMONY_EVENT_THUMBNAIL_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTLOGO/' . DS);

define('CEREMONY_EVENT_IMAGEMAIN_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTIMAGE/' . DS);

define('CEREMONY_EVENT_IMAGEDES_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTDESC/' . DS);

define('SPONSORPLATINUM_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'sponsorplatinum' . DS);

define('SPONSORPLATINUM_IMAGE_URL', WEBSITE_UPLOADS_URL .  'sponsorplatinum' . DS);


define('WHOWEARE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'whoweare' . DS);
define('WHOWEARE_IMAGE_URL', WEBSITE_UPLOADS_URL .  'whoweare' . DS);

define('TERMS_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'TERMS' . DS);
define('TERMS_IMAGE_URL', WEBSITE_UPLOADS_URL .  'TERMS' . DS);


define('ADS_IMAGE_URL', WEBSITE_UPLOADS_URL . 'ads/');
define('ADS_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'ads' . DS); 

define('MEDIA_IMAGE_URL', WEBSITE_UPLOADS_URL . 'media_partner/');
define('MEDIA_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'media_partner' . DS); 

define('BLOG_IMAGE_URL', WEBSITE_UPLOADS_URL . 'blog/');
define('BLOG_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'blog' . DS); 


define('MASTERS_IMAGE_URL', WEBSITE_UPLOADS_URL . 'masters/');
define('MASTERS_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'masters' . DS); 

define('EVENTDESC_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'event/imagedes' . DS);
define('EVENTDESC_IMAGE_URL', WEBSITE_UPLOADS_URL .  'event/imagedes' . DS);

define('EVENTIMAGE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'event/imagemain' . DS);

define('EVENTIMAGE_IMAGE_URL', WEBSITE_UPLOADS_URL .  'event/imagemain' . DS);

define('EVENTTERMS_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'terms' . DS);
define('EVENTTERMS_IMAGE_URL', WEBSITE_UPLOADS_URL .  'terms' . DS);

define('LICENSE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'LICENSE' . DS);
define('LICENSE_IMAGE_URL', WEBSITE_UPLOADS_URL .  'LICENSE' . DS);

define('EVENTLOGO_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'EVENTLOGO' . DS);
define('EVENTLOGO_IMAGE_URL', WEBSITE_UPLOADS_URL .  'EVENTLOGO' . DS);

define('PROFILE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'PROFILE_IMG' . DS);
define('PROFILE_IMAGE_URL', WEBSITE_UPLOADS_URL .  'PROFILE_IMG' . DS);

/**  Active Inactive global constant **/
define('ACTIVE',1);
define('INACTIVE',0);

define('ADMIN_ID', 1);
define('FRONT_USER', 2);

//////////////// extension 
define('IMAGE_EXTENSION','jpeg,jpg,png,gif,bmp');
