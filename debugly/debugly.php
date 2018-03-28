<?php
/**
 * @package Debugly
 * @version 1.0
 */
/*
Plugin Name: Debugly
Description: Provides a simple way to view and download debug info/data.
Version: 1.0
Text Domain: debugly
*/

defined( 'ABSPATH' ) || die();

define( 'DEBUGLY_FILE', trailingslashit( dirname( __FILE__ ) ) . 'debugly.php' );
define( 'DEBUGLY_DIR', plugin_dir_path( DEBUGLY_FILE ) );
define( 'DEBUGLY_URL', plugins_url( '/', DEBUGLY_FILE ) );

function debugly_download() {
	$debugly = new Debugly();
	if (isset($_REQUEST['action']))
	{
		if ($_REQUEST['page'] === 'debugly' && $_REQUEST['action'] === 'download')
		{
			ob_start();
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=debugly.log");
			print_r($debugly);
			$debugly_output = ob_get_contents();
			ob_clean();
			echo $debugly_output;
			exit();
		}
	}
}
add_action('admin_init', 'debugly_download');

function debugly_load_menu() {
    add_menu_page( 'Debugly', 'Debugly', 'read', 'debugly', 'debugly_admin_screen', 'dashicons-chart-area', 21 );
}

function debugly_admin_screen() {
    $debugly = new Debugly();
    ?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('#accordion').find('.accordion-toggle').click(function(){
			//Expand or collapse this panel
			jQuery(this).addClass('active');
			jQuery(this).next().slideToggle('fast', function() {
				if (jQuery(this).is(':visible')) {
					jQuery(this).prev().addClass('active');
				}
				else {
					jQuery(this).prev().removeClass('active');
				}
			});
			//Hide the other panels
			jQuery('.accordion-content').not($(this).next()).slideUp('fast');
			jQuery('.accordion-toggle').not($(this)).removeClass('active');
		});
	});
</script>
<style>
	.accordion-toggle {
		cursor: pointer; 
		margin: 0;
		padding: 10px;
		border-left: solid 1px #ccc;
		border-right: solid 1px #ccc;
		border-bottom: solid 1px #ccc;
		background-color: #e5e5e5;
	}
	.accordion-toggle:first-child {
		border-top: solid 1px #ccc;
	}
	.accordion-toggle:hover, .accordion-toggle.active {
		color: #fff;
		background-color: #0073aa;
	}
	.accordion-content { 
		display: none;
		background-color: #fff;
		padding: 10px;
		border-left: solid 1px #ccc;
		border-right: solid 1px #ccc;
		border-bottom: solid 1px #ccc;
	}
	.accordion-content.default {display: block;}
</style>
<div class="wrap">
	<p style="text-align: right;">
		<a href="<?php echo admin_url() . 'admin.php?page=debugly&action=download'; ?>" target="_blank">Download Log</a>
	</p>
	<p>
		Click section to view results
	</p>
	<div id="accordion">
	<?php
	foreach ($debugly as $key => $value) 
	{
		?>
		<h4 class="accordion-toggle"><?php echo $key; ?></h4>
		<div class="accordion-content">
			<pre><?php print_r($value); ?></pre>
		</div>
		<?php
	}
	?>
	</div>
</div>
<?php
}
add_action('admin_menu', 'debugly_load_menu');

class Debugly {
    
	public $apache;
	public $bloginfo;
	public $classes;
	public $constants;
	public $constants_wordpress;
	public $functions;
	public $menus;
	public $options;
	public $php_extensions;
	public $php_ini;
	public $plugins;
	public $plugins_active;
	public $post_types;
	//public $scripts;
	public $shortcodes;
	public $styles;
	public $themes;
	public $users;
	public $variables;
    
    public function __construct() {
        $this->apache = $this->apache();
		$this->bloginfo = $this->bloginfo();
		$this->classes = $this->classes();
		$this->constants = $this->constants();
		$this->constants_wordpress = $this->constants_wordpress();
		$this->functions = $this->functions();
		$this->menus = $this->menus();
		$this->options = $this->options();
		$this->php_extensions = $this->php_extensions();
		$this->php_ini = $this->php_ini();
		$this->plugins = $this->plugins();
		$this->plugins_active = $this->plugins_active();
		$this->post_types = $this->post_types();
		//$this->scripts = $this->scripts();
		$this->shortcodes = $this->shortcodes();
		$this->styles = $this->styles();
		$this->themes = $this->themes();
		$this->users = $this->users();
		$this->variables = $this->variables();
    }
	
	private function apache() {
		$apache = array();
		if ( ! preg_match( '/[Aa]pache/', $_SERVER["SERVER_SOFTWARE"] ) ) {
			return 'N/A';
		}
		$apache['version'] = apache_get_version();
		$apache['modules'] = apache_get_modules();
		return $apache;
	}
    
    private function bloginfo() {
		$bloginfo = array(
			'name'                 => get_bloginfo( 'name' ),
			'description'          => get_bloginfo( 'description' ),
			'wpurl'                => get_bloginfo( 'wpurl' ),
			'url'                  => get_bloginfo( 'url' ),
			'admin_email'          => get_bloginfo( 'admin_email' ),
			'charset'              => get_bloginfo( 'charset' ),
			'version'              => get_bloginfo( 'version' ),
			'html_type'            => get_bloginfo( 'html_type' ),
			'language'             => get_bloginfo( 'language' ),
			'stylesheet_url'       => get_bloginfo( 'stylesheet_url' ),
			'stylesheet_directory' => get_bloginfo( 'stylesheet_directory' ),
			'template_url'         => get_bloginfo( 'template_url' ),
			'template_directory'   => get_bloginfo( 'template_directory' ),
			'pingback_url'         => get_bloginfo( 'pingback_url' ),
			'atom_url'             => get_bloginfo( 'atom_url' ),
			'rdf_url'              => get_bloginfo( 'rdf_url' ),
			'rss_url'              => get_bloginfo( 'rss_url' ),
			'rss2_url'             => get_bloginfo( 'rss2_url' ),
			'comments_atom_url'    => get_bloginfo( 'comments_atom_url' ),
			'comments_rss2_url'    => get_bloginfo( 'comments_rss2_url' ),
		);
		return $bloginfo;
	}
	
	private function classes() {
		return get_declared_classes();
	}
	
	private function constants() {
		return get_defined_constants();
	}

	private function constants_wordpress() {
		$wp_constants = array(
			'AUTOSAVE_INTERVAL',
			'CORE_UPGRADE_SKIP_NEW_BUNDLED',
			'DISABLE_WP_CRON',
			'EMPTY_TRASH_DAYS',
			'IMAGE_EDIT_OVERWRITE',
			'MEDIA_TRASH',
			'WPLANG',
			'WP_DEFAULT_THEME',
			'WP_CRON_LOCK_TIMEOUT',
			'WP_MAIL_INTERVAL',
			'WP_POST_REVISIONS',
			'WP_MAX_MEMORY_LIMIT',
			'WP_MEMORY_LIMIT',
			'APP_REQUEST',
			'COMMENTS_TEMPLATE',
			'DOING_AJAX',
			'DOING_AUTOSAVE',
			'DOING_CRON',
			'IFRAME_REQUEST',
			'IS_PROFILE_PAGE',
			'SHORTINIT',
			'WP_ADMIN',
			'WP_BLOG_ADMIN',
			'WP_IMPORTING',
			'WP_INSTALLING',
			'WP_LOAD_IMPORTERS',
			'WP_NETWORK_ADMIN',
			'WP_REPAIRING',
			'WP_SETUP_CONFIG',
			'WP_UNINSTALL_PLUGIN',
			'WP_USER_ADMIN',
			'XMLRPC_REQUEST',
			'ABSPATH',
			'WPINC',
			'WP_LANG_DIR',
			'WP_PLUGIN_DIR',
			'WP_PLUGIN_URL',
			'WP_CONTENT_DIR',
			'WP_CONTENT_URL',
			'WP_HOME',
			'WP_SITEURL',
			'WP_TEMP_DIR',
			'WPMU_PLUGIN_DIR',
			'WPMU_PLUGIN_URL',
			'DB_CHARSET',
			'DB_COLLATE',
			'DB_HOST',
			'DB_NAME',
			'DB_PASSWORD',
			'DB_USER',
			'WP_ALLOW_REPAIR',
			'CUSTOM_USER_TABLE',
			'CUSTOM_USER_META_TABLE',
			'ALLOW_SUBDIRECTORY_INSTALL',
			'BLOGUPLOADDIR',
			'BLOG_ID_CURRENT_SITE',
			'DOMAIN_CURRENT_SITE',
			'DIEONDBERROR',
			'ERRORLOGFILE',
			'MULTISITE',
			'NOBLOGREDIRECT',
			'PATH_CURRENT_SITE',
			'UPLOADBLOGSDIR',
			'SITE_ID_CURRENT_SITE',
			'SUBDOMAIN_INSTALL',
			'SUNRISE',
			'UPLOADS',
			'WPMU_ACCEL_REDIRECT',
			'WPMU_SENDFILE',
			'WP_ALLOW_MULTISITE',
			'WP_CACHE',
			'COMPRESS_CSS',
			'COMPRESS_SCRIPTS',
			'CONCATENATE_SCRIPTS',
			'ENFORCE_GZIP',
			'FS_CHMOD_DIR',
			'FS_CHMOD_FILE',
			'FS_CONNECT_TIMEOUT',
			'FS_METHOD',
			'FS_TIMEOUT',
			'FTP_BASE',
			'FTP_CONTENT_DIR',
			'FTP_HOST',
			'FTP_LANG_DIR',
			'FTP_PASS',
			'FTP_PLUGIN_DIR',
			'FTP_PRIKEY',
			'FTP_PUBKEY',
			'FTP_SSH',
			'FTP_SSL',
			'FTP_USER',
			'WP_PROXY_BYPASS_HOSTS',
			'WP_PROXY_HOST',
			'WP_PROXY_PASSWORD',
			'WP_PROXY_PORT',
			'WP_PROXY_USERNAME',
			'WP_HTTP_BLOCK_EXTERNAL',
			'WP_ACCESSIBLE_HOSTS',
			'BACKGROUND_IMAGE',
			'HEADER_IMAGE',
			'HEADER_IMAGE_HEIGHT',
			'HEADER_IMAGE_WIDTH',
			'HEADER_TEXTCOLOR',
			'NO_HEADER_TEXT',
			'STYLESHEETPATH',
			'TEMPLATEPATH',
			'WP_USE_THEMES',
			'SAVEQUERIES',
			'SCRIPT_DEBUG',
			'WP_DEBUG',
			'WP_DEBUG_DISPLAY',
			'WP_DEBUG_LOG',
			'ADMIN_COOKIE_PATH',
			'ALLOW_UNFILTERED_UPLOADS',
			'AUTH_COOKIE',
			'AUTH_KEY',
			'AUTH_SALT',
			'COOKIEHASH',
			'COOKIEPATH',
			'COOKIE_DOMAIN',
			'CUSTOM_TAGS',
			'DISALLOW_FILE_EDIT',
			'DISALLOW_FILE_MODS',
			'DISALLOW_UNFILTERED_HTML',
			'FORCE_SSL_ADMIN',
			'FORCE_SSL_LOGIN',
			'LOGGED_IN_COOKIE',
			'LOGGED_IN_KEY',
			'LOGGED_IN_SALT',
			'NONCE_KEY',
			'NONCE_SALT',
			'PASS_COOKIE',
			'PLUGINS_COOKIE_PATH',
			'SECURE_AUTH_COOKIE',
			'SECURE_AUTH_KEY',
			'SECURE_AUTH_SALT',
			'SITECOOKIEPATH',
			'TEST_COOKIE',
			'USER_COOKIE'
		);
		ksort( $wp_constants );
		$constants = array();
		foreach ( $wp_constants as &$constant ) {
			if ( ! defined( $constant ) ) {
				$constants[ $constant ] = 'undefined';
			} else {
				$value = constant( $constant );
				if ( $value === false ) {
					$constants[ $constant ] = false;
				} elseif ( $value === '' ) {
					$constants[ $constant ] = '';
				} else {
					$constants[ $constant ] = $value;
				}
			}
		}
		return $constants;
	}
	
	private function functions() {
		$functions = get_defined_functions();
		return $functions['user'];
	}
	
	private function menus() {
		return get_registered_nav_menus();
	}
	
	private function options() {
		$options = wp_load_alloptions();
		$options_array = array();
		foreach ( $options as $key => $value ) {
			$options_array[ $key ] = maybe_unserialize( $value );
		}
		return $options_array;
	}
	
	private function php_extensions() {
		return get_loaded_extensions();
	}
	
	private function php_ini() {
		return ini_get_all();
	}
	
	private function plugins() {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		return get_plugins();
	}
	
	private function plugins_active() {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		$plugins = get_plugins();
		$active_plugins = array();
		foreach ( $plugins as $plugin => $info ) {
			if ( is_plugin_active( $plugin ) ) {
				$active_plugins[ $plugin ] = $info;
			}
		}
		return $active_plugins;
	}
	
	private function post_types() {
		return get_post_types( '', 'objects' );
	}
	
	private function scripts() {
		global $wp_scripts;
		return $wp_scripts->registered;
	}
	
	private function shortcodes() {
		global $shortcode_tags;
		return $shortcode_tags;
	}
	
	private function styles() {
		global $wp_styles;
		return $wp_styles->registered;
	}
	
	private function themes() {
		$themes = array();
		$themes['current'] = wp_get_theme();
		$themes['all'] = wp_get_themes();
		return $themes;
	}
	
	private function users() {
		$users = get_users();
		return $users;
	}
	
	private function variables() {
		$variables = array();
		$variables['COOKIES'] = $_COOKIE;
		$variables['SERVER'] = $_SERVER;
		return $variables;
	}
    
}