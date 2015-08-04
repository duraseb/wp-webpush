<?php
/**
 * @package WebPush
 */
/*
Plugin Name: WebPush
Plugin URI: https://github.com/duraseb/wp-webpush
Description: Sends desktop notifications to your users about new content on your site
Version: 0.1.0
Author: Sebastian Szulc
Author URI: https://github.com/duraseb
*/

if (!function_exists('add_action')) exit;

require_once dirname( __FILE__ ) . '/webpushWidget.class.php';

add_action('init', 'webpush_init');

function webpush_admin_css() {
    ?><style>
        .toplevel_page_webpush_options > .wp-menu-image > img {
            width: 16px;
            padding-top: 7px;
        }
        .wrap .osrodek_form {
            display: block;
            vertical-align: top;
        }
        .wrap .osrodek_form input[type=text] {
                width: 500px;
            }
        }
    </style>
    <?php
}

function webpush_menu() {
        add_menu_page('WebPush configuration', 'WebPush', 'manage_options', 'webpush_options', 'webpush_main_options', plugins_url('/i/webpush-ico.png', __FILE__));
        add_submenu_page('webpush_options', 'WebPush Stats', 'Statistics', 'manage_options', 'webpush_stats', 'webpush_submenu_stats');
}

function sanatorium_main_options() {
        if (!current_user_can('manage_options'))    {
                wp_die( __('You do not have sufficient permissions to access this page.') );
        }

?>
<div class="wrap">
        <div id="icon-themes" class="icon32"></div>
        <h2>WebPush Configuration</h2>
        <p>Adjust settings for your WebPush configuration</p>
</div>
<?php
}

function webpush_submenu_stats() {
    if (!current_user_can('manage_options'))    {
            wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    ?>
    <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2>WebPush - Stats</h2>
    </div>
    <?php
}

function webpush_init()
{
    add_rewrite_tag('%subscription_id%', '([^/]+)');
    add_rewrite_tag('%endpoint%', '([^/]+)');
    add_rewrite_tag('%webpush_handler%', '([^/]+)');
    add_action('wp_head', 'webpush_head');
    add_action('parse_request', 'webpush_parse_request');
    add_action('widgets_init', 'webpush_register_widgets');
    add_action('admin_menu', 'webpush_menu');
    add_action('admin_head', 'webpush_admin_css');

    wp_enqueue_script('webpush-script',
        plugins_url('/js/webpush.js', __FILE__ ),
        array('jquery')
    );
}

function webpush_parse_request(&$wp) {
    if (array_key_exists('webpush_handler', $wp->query_vars)) {
        webpush_handle_request($wp->query_vars);
    }
}

function webpush_handle_request($q) {
    switch ($q['webpush_handler']) {
        case 'manifest':
            header('Content-Type: application/json; charset=UTF-8');
            echo webpush_manifest();
            break;
        case 'serviceworker':
            header('Content-Type: application/javascript; charset=UTF-8');
            echo webpush_serviceworker();
            break;
        case 'subscribe':
            echo webpush_serviceworker($q);
            break;
        case 'notifications':
            echo webpush_notifications($q);
            break;
    }
    exit;
}

function webpush_manifest() {
    // TODO read values from config
    $manifest = array(
        "name" => "Blog title",
        "short_name" => "Blog short name",
        "icons" => [
                [
                      "src" => plugins_url("/i/webpush-ico.png", __FILE__),
                      "sizes" => "120x120",
                      "type" => "image/png"
                ],
        ],
        "start_url" => "/",
        "display" => "standalone",
        "gcm_sender_id" => "1111111111",
        "gcm_user_visible_only" => true
    );
    return json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function webpush_serviceworker() {
    $sw_filename = plugin_dir_path(__FILE__) . 'js/service_worker.js';
    if (file_exists($sw_filename)) {
        return file_get_contents($sw_filename);
    }
    return '';
}

function webpush_notifications($q) {
}


function webpush_register_widgets()
{
    register_widget('WebpushWidget');
}

function webpush_head()
{
?>
<link rel="manifest" href="<?php echo site_url('?webpush_handler=manifest'); ?>">
<?php
}
