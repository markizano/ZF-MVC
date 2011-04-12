<?php
/**
 *  Setup/Startup
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   ZF
 *  @package    Setup
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

defined("DS") || define('DS', DIRECTORY_SEPARATOR);

define('BASE_DIR', dirname(dirname(__FILE__)) . DS);

define('LIVE', preg_match('/(local|dev)/i', $_SERVER['HTTP_HOST'])? false: true, true);
define('DIR_APPLICATION', realpath(BASE_DIR . DS . 'application') . DS);

define('WEB_HTTP', "http://$_SERVER[SERVER_NAME]");
define('WEB_HTTPS', "https://$_SERVER[SERVER_NAME]");
define('WEB_CSS', '/assets/css/');
define('WEB_JS', '/assets/js/');
define('WEB_IMAGES', '/assets/images/');

if (isset($_SERVER['ENVIRONMENT'])) {
    define('ENVIRONMENT', $_SERVER['ENVIRONMENT']);
} else {
    $env = getenv('ENVIRONMENT');
    if ($env) {
        define('ENVIRONMENT', $env);
    } else {
        if (LIVE) {
            define('ENVIRONMENT', 'live');
        } else {
            define('ENVIRONMENT', 'dev');
        }
    }
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
	DIR_APPLICATION,
	BASE_DIR . 'library',
	get_include_path(),
)));

require_once 'Zend/Application.php';
$app = new Zend_Application(ENVIRONMENT, DIR_APPLICATION . 'configs/application.ini');
Zend_Registry::set('application', $app);

