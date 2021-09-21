<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

require_once '../../init.php';
define('PATH_DEV', MAX_PATH . '/www/devel');
define('PATH_VAR', MAX_PATH . '/var');

define('PATH_ASSETS', MAX_PATH . '/www/devel/assets');

$pluginPath = $GLOBALS['_MAX']['CONF']['pluginPaths']['packages'];

require_once 'lib/toolbox.inc.php';
OX_DevToolbox::checkFilePermissions([PATH_DEV, PATH_VAR, MAX_PATH . $pluginPath]);
