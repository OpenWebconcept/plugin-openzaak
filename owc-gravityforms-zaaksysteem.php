<?php

declare(strict_types=1);

/**
 * Plugin Name:       OWC GravityForms Zaaksysteem
 * Plugin URI:        https://www.openwebconcept.nl/
 * Description:       Combine one or more 'zaaksystemen' with Gravity Forms and WordPress
 * Version:           2.2.2
 * Author:            Yard | Digital Agency
 * Author URI:        https://www.yard.nl/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       owc-gravityforms-zaaksysteem
 * Domain Path:       /languages
 */

namespace OWC\Zaaksysteem;

/**
 * If this file is called directly, abort.
 */
if (! defined('WPINC')) {
    die;
}

define('OWC_GZ_DIR', basename(__DIR__));
define('OWC_GZ_NAME', 'OWC GravityForms Zaaksysteem');
define('OWC_GZ_SHORT_NAME', 'OWC Zaaksysteem');
define('OWC_GZ_PLUGIN_SLUG', 'owc-gravityforms-zaaksysteem');
define('OWC_GZ_ROOT_PATH', __DIR__);
define('OWC_GZ_VERSION', '2.2.2');

/**
 * Not all the members of the OpenWebconcept are using composer in the root of their project.
 * Therefore they are required to run a composer install inside this plugin directory.
 * In this case the composer autoload file needs to be required.
 */
$composerAutoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    require_once __DIR__ . '/autoloader.php';
    $autoloader = new Autoloader();
}

register_activation_hook(__FILE__, function () {
    flush_rewrite_rules(false);
});

/**
 * Begin execution of the plugin
 *
 * This hook is called once any activated plugins have been loaded. Is generally used for immediate filter setup, or
 * plugin overrides. The plugins_loaded action hook fires early, and precedes the setup_theme, after_setup_theme, init
 * and wp_loaded action hooks.
 */
add_action('plugins_loaded', function () {
    Foundation\Plugin::getInstance(__DIR__)->boot();
}, 10);
