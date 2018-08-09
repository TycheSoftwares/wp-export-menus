<?php
/**
 * Plugin Name: Export WordPress Menus
 * Plugin URI: 
 * Description: Export your WordPress menus.
 * Version: 1.1
 * Author: Tyche Softwares
 * Author URI: http://tychesoftwares.com/
 * Text Domain: wp-export-menus
 * Domain Path: /i18n/languages/
 * Requires PHP: 5.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Export_Menus' ) ) {
	class WP_Export_Menus {

		const WEM_VERSION = '1.1';
	    
		public function __construct() {
			$this->wem_constants();
			add_action( 'export_filters', array( &$this, 'export_filters' ) );	
			add_filter( 'export_args', array( &$this, 'export_args' ) );
			add_action( 'admin_head', array( &$this, 'wem_export_add_js' ) );
			// Language Translation
			add_action ( 'init',      array( &$this, 'wem_update_po_file' ) );
			add_action ( 'init',      array( &$this, 'wem_add_files' ) );
			$is_admin = is_admin();

			if ( true === $is_admin ) {
				
				add_filter( 'ts_deativate_plugin_questions', array( $this, 'wem_deactivate_add_questions' ), 10, 1 );
				add_filter( 'ts_tracker_data',               array( $this, 'wem_ts_add_plugin_tracking_data' ), 10, 1 );
				add_filter( 'ts_tracker_opt_out_data',       array( $this, 'wem_get_data_for_opt_out' ), 10, 1 );
				add_action( 'admin_init',                    array( $this, 'wem_admin_actions' ) );
			}
		}

		function wem_add_files () {
			require_once( plugin_dir_path(__FILE__) . 'includes/wp-export-menus-all-component.php' );
		}
		
		function wem_constants () {
			if ( !defined( 'WEM_VERSION' ) ) {
					define ( 'WEM_VERSION', '1.1' );
			}
		}
		/**
         * This function will allowed customer to transalte the plugin string using .po and .pot file.
         * @hook init
         * @since 1.1
         */
        function  wem_update_po_file() {
            
            $domain = 'wp-export-menus';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
            if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' ) ) {
                return $loaded;
            } else {
                load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/i18n/languages/' );
            }
		}
		
		/**
		 * Display JavaScript on the page.
		 *
		 * @since 3.5.0
		 */
		public static function wem_export_add_js() {
		    ?>
    		<script type="text/javascript">
    			jQuery(document).ready(function($){
    		 		var form = jQuery('#export-filters'),
    		 			filters = form.find( '.export-filters' );
    		 		filters.hide();
    		 		jQuery( '.nav-menu-item-filters' ).hide();
    		 		jQuery( 'input[name=content]' ).change(function() {
    					filters.slideUp( 'fast' );
    					jQuery( '.nav-menu-item-filters' ).slideUp( 'fast' );
    					switch ( jQuery(this).val() ) {
    						case 'nav_menu_item': console.log( 'HERE' ); jQuery('#nav-menu-item-filters').slideDown(); break;
    					}
    		 		});
    			});
    		</script>
    		<?php
		}
		
		public function export_filters() {
			?>
			<p><label><input type="radio" name="content" value="nav_menu_item" /> <?php _e( 'Navigation Menu Items', 'wp-export-menus' ); ?></label></p>
			<ul id="nav-menu-item-filters" class="nav-menu-item-filters">
				<li>
					<fieldset>
					<legend class="screen-reader-text"><?php _e( 'Date range:', 'wp-export-menus' ); ?></legend>
					<label for="nav-menu-item-start-date" class="label-responsive"><?php _e( 'Start date:', 'wp-export-menus' ); ?></label>
					<select name="nav_menu_item_start_date" id="nav-menu-item-start-date">
						<option value="0"><?php _e( '&mdash; Select &mdash;', 'wp-export-menus' ); ?></option>
						<?php export_date_options( 'nav_menu_item' ); ?>
					</select>
					<label for="nav-menu-item-end-date" class="label-responsive"><?php _e( 'End date:', 'wp-export-menus' ); ?></label>
					<select name="nav-menu-item_end_date" id="nav-menu-item-end-date">
						<option value="0"><?php _e( '&mdash; Select &mdash;', 'wp-export-menus' ); ?></option>
						<?php export_date_options( 'nav_menu_item' ); ?>
					</select>
					</fieldset>
				</li>
			</ul>
			<?php
		}

		public static function export_args( $args ) {
			if( !isset( $_GET['download'] ) || empty( $_GET['content'] ) || $_GET['content'] !== 'nav_menu_item' )  {
					return;
			}
			self::wp_export_menu();
			exit;
		}

		/**
         * Generates the WXR export file for download - This is a rip of export_wp but supports only exporting menus and it's terms
         *
         *
         * @param array $args Filters defining what should be included in the export
         */
        public static function wp_export_menu( $args = array() ) {
            global $wpdb, $post;
            $sitename = sanitize_key( get_bloginfo( 'name' ) );
            if ( !empty( $sitename ) ) {
                $sitename .= '.';
            }
            $filename = $sitename . 'wordpress.' . date( 'Y-m-d' ) . '.xml';

            header( 'Content-Description: File Transfer' );
            header( 'Content-Disposition: attachment; filename=' . $filename );
            header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
            
            $where = $wpdb->prepare( "{$wpdb->posts}.post_type = %s", 'nav_menu_item' );

            // grab a snapshot of post IDs, just in case it changes during the export
            $post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE $where" );
        	echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
            ?>
<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->
<!-- You may use this file to transfer that content from one site to another. -->
<!-- This file is not intended to serve as a complete backup of your site. -->
            
<!-- To import this information into a WordPress site follow these steps: -->
<!-- 1. Log in to that site as an administrator. -->
<!-- 2. Go to Tools: Import in the WordPress admin panel. -->
<!-- 3. Install the "WordPress" importer from the list. -->
<!-- 4. Activate & Run Importer. -->
<!-- 5. Upload this file using the form provided on that page. -->
<!-- 6. You will first be asked to map the authors in this export file to users -->
<!--    on the site. For each author, you may choose to map to an -->
<!--    existing user on the site or to create a new user. -->
<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
<!--    contained in this file into your site. -->

            <?php the_generator( 'export' ); ?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/<?php echo '1.1'; ?>/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/<?php echo '1.1'; ?>/"
>
<channel>
    <title><?php bloginfo_rss( 'name' ); ?></title>
    <link><?php bloginfo_rss( 'url' ); ?></link>
    <description><?php bloginfo_rss( 'description' ); ?></description>
    <pubDate><?php echo date( 'D, d M Y H:i:s +0000' ); ?></pubDate>
    <language><?php echo get_option( 'rss_language' ); ?></language>
    <wp:wxr_version><?php echo '1.1'; ?></wp:wxr_version>
    <wp:base_site_url><?php echo self::wem_site_url(); ?></wp:base_site_url>
    <wp:base_blog_url><?php bloginfo_rss( 'url' ); ?></wp:base_blog_url>
                	
    <?php self::wem_nav_menu_terms(); ?>
    <?php self::wem_nav_menu_item_terms_and_posts( $post_ids ) ?>
        
    <?php do_action( 'rss2_head' ); ?>
        
        <?php if ( $post_ids ) {
            global $wp_query;
            $wp_query->in_the_loop = true; // Fake being in the loop.
        	// fetch 20 posts at a time rather than loading the entire table into memory
            while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
            	$where = "WHERE ID IN (" . join( ',', $next_posts ) . ")";
            	$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );
            	// Begin Loop
            	foreach ( $posts as $post ) {
            		setup_postdata( $post );
            		$is_sticky = is_sticky( $post->ID ) ? 1 : 0;
                    ?>
                	<item>
                		<title><?php echo apply_filters( 'the_title_rss', $post->post_title ); ?></title>
                		<link><?php the_permalink_rss() ?></link>
                		<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
                		<dc:creator><?php echo get_the_author_meta( 'login' ); ?></dc:creator>
                		<guid isPermaLink="false"><?php esc_url( the_guid() ); ?></guid>
                		<description></description>
                		<content:encoded><?php echo self::wem_cdata( apply_filters( 'the_content_export', $post->post_content ) ); ?></content:encoded>
                		<excerpt:encoded><?php echo self::wem_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) ); ?></excerpt:encoded>
                		<wp:post_id><?php echo $post->ID; ?></wp:post_id>
                		<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
                		<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
                		<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
                		<wp:ping_status><?php echo $post->ping_status; ?></wp:ping_status>
                		<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
                		<wp:status><?php echo $post->post_status; ?></wp:status>
                		<wp:post_parent><?php echo $post->post_parent; ?></wp:post_parent>
                		<wp:menu_order><?php echo $post->menu_order; ?></wp:menu_order>
                		<wp:post_type><?php echo $post->post_type; ?></wp:post_type>
                		<wp:post_password><?php echo $post->post_password; ?></wp:post_password>
                		<wp:is_sticky><?php echo $is_sticky; ?></wp:is_sticky>
                        <?php if ( $post->post_type == 'attachment' ) { ?>
                            <wp:attachment_url><?php echo wp_get_attachment_url( $post->ID ); ?></wp:attachment_url>
                        <?php } ?>
                        <?php 	self::wem_post_taxonomy(); ?>
                        <?php	$postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
                        if ( $postmeta ) {
                            foreach( $postmeta as $meta ) {
                                if ( $meta->meta_key != '_edit_lock' ) { ?>
                            		<wp:postmeta>
                            			<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
                            			<wp:meta_value><?php echo self::wem_cdata( $meta->meta_value ); ?></wp:meta_value>
                            		</wp:postmeta>
                                <?php } 
                            }
                        }?>
                        <?php	$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );
                        if ( $comments ) {
                            foreach ( $comments as $c ) { ?>
                        		<wp:comment>
                        			<wp:comment_id><?php echo $c->comment_ID; ?></wp:comment_id>
                        			<wp:comment_author><?php echo self::wem_cdata( $c->comment_author ); ?></wp:comment_author>
                        			<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
                        			<wp:comment_author_url><?php echo esc_url_raw( $c->comment_author_url ); ?></wp:comment_author_url>
                        			<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
                        			<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
                        			<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
                        			<wp:comment_content><?php echo self::wem_cdata( $c->comment_content ) ?></wp:comment_content>
                        			<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
                        			<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
                        			<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
                        			<wp:comment_user_id><?php echo $c->user_id; ?></wp:comment_user_id>
                        		</wp:comment>
                            <?php } 
                        }?>
                    </item>
                    <?php
                }
            }
        } ?>
    </channel>
</rss>
            <?php
        }
	
        /**
         * Wrap given string in XML CDATA tag.
         *
         * @since 2.1.0
         *
         * @param string $str String to wrap in XML CDATA tag.
         */
        public static function wem_cdata( $str ) {
        	if ( seems_utf8( $str ) == false ) {
        		$str = utf8_encode( $str );
        	}
        	
        	$str = "<![CDATA[$str" . ( ( substr( $str, -1 ) == ']' ) ? ' ' : '') . "]]>";
        	return $str;
        }

        /**
         * Return the URL of the site
         * @since 2.5.0
         * @return string Site URL.
         */
        public static function wem_site_url() {
        	// ms: the base url
        	if ( is_multisite() ) {
        		return network_home_url();
        	} else {
        		return get_bloginfo_rss( 'url' );
        	}
        }

        /**
         * Output a term_name XML tag from a given term object
         *
         * @since 2.9.0
         *
         * @param object $term Term Object
         */
        public static function wem_term_name( $term ) {
        	if ( empty( $term->name ) )
        		return;
        
        	echo '<wp:term_name>' . self::wem_cdata( $term->name ) . '</wp:term_name>';
        }

        /**
         * Ouput all navigation menu terms
         *
         * @since 3.1.0
         */
        public static function wem_nav_menu_terms() {
        	$nav_menus = wp_get_nav_menus();
        	if ( empty( $nav_menus ) || ! is_array( $nav_menus ) )
        		return;
        
        	foreach ( $nav_menus as $menu ) {
        		echo "\t<wp:term><wp:term_id>{$menu->term_id}</wp:term_id><wp:term_taxonomy>nav_menu</wp:term_taxonomy><wp:term_slug>{$menu->slug}</wp:term_slug>";
        		self::wem_term_name( $menu );
        		echo "</wp:term>\n";
        	}
        }
	
    	public static function wem_nav_menu_item_terms_and_posts( &$post_ids ) {
    		$posts_to_add = array();
    		foreach( $post_ids as $post_id ) {
    			if( ($type = get_post_meta( $post_id, '_menu_item_type', true ) ) == 'taxonomy' ) {
    				$term = get_term( get_post_meta( $post_id, '_menu_item_object_id', true ), ($tax = get_post_meta( $post_id, '_menu_item_object', true )) );
    				echo "\t<wp:term><wp:term_id>{$term->term_id}</wp:term_id><wp:term_taxonomy>{$tax}</wp:term_taxonomy><wp:term_slug>{$term->slug}</wp:term_slug>";
    				self::wem_term_name( $term );
    				echo "</wp:term>\n";
    			} elseif( $type == 'post_type' && in_array( get_post_meta( $post_id, '_menu_item_object', true ), array( 'post', 'page' ) ) ) {
    				$posts_to_add[] = get_post_meta( $post_id, '_menu_item_object_id', true );
    			}
    		}
    		$post_ids = array_merge( $posts_to_add, $post_ids );
    	}

    	/**
    	 * Output list of taxonomy terms, in XML tag format, associated with a post
    	 *
    	 * @since 2.3.0
    	 */
    	public static function wem_post_taxonomy() {
    		global $post;
    		$taxonomies = get_object_taxonomies( $post->post_type );
    		if ( empty( $taxonomies ) ) {
    			return;
    		}
    		
    		$terms = wp_get_object_terms( $post->ID, $taxonomies );
    		foreach ( (array) $terms as $term ) {
    			echo "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . self::wem_cdata( $term->name ) . "</category>\n";
    		}
		}
		
		function wem_deactivate_add_questions ( $wem_deactivate_questions ) {

			$wem_deactivate_questions = array(
				0 => array(
					'id'                => 4, 
					'text'              => __( "WordPress Menus are not exported not getting exported.", "wem" ),
					'input_type'        => '',
					'input_placeholder' => ''
					)
	
			);
			return $wem_deactivate_questions;
		}
	
		function wem_admin_actions ( ) {
			/**
			 * We need to store the plugin version in DB, so we can show the welcome page and other contents.
			 */
			$wem_version_in_db = get_option( 'wem_version' ); 
			if ( $wem_version_in_db != self::WEM_VERSION ) {
				update_option( 'wem_version', self::WEM_VERSION );
			}
		}
	
		/**
		 * Plugin's data to be tracked when Allow option is choosed.
		 *
		 * @hook ts_tracker_data
		 *
		 * @param array $data Contains the data to be tracked.
		 *
		 * @return array Plugin's data to track.
		 * 
		 */
	
		public static function wem_ts_add_plugin_tracking_data ( $data ) {
			if ( isset( $_GET[ 'wem_tracker_optin' ] ) && isset( $_GET[ 'wem_tracker_nonce' ] ) && wp_verify_nonce( $_GET[ 'wem_tracker_nonce' ], 'wem_tracker_optin' ) ) {
	
				$plugin_data[ 'ts_meta_data_table_name' ] = 'ts_tracking_wem_meta_data';
				$plugin_data[ 'ts_plugin_name' ]		  = 'Export WordPress Menus';
				/**
				 * Add Plugin data
				 */
				$plugin_data[ 'wem_plugin_version' ]      = self::WEM_VERSION;
				
				$plugin_data[ 'wem_allow_tracking' ]      = get_option ( 'wem_allow_tracking' );
				$data[ 'plugin_data' ]                    = $plugin_data;
			}
			return $data;
		}
		
		/**
		 * Tracking data to send when No, thanks. button is clicked.
		 *
		 * @hook ts_tracker_opt_out_data
		 *
		 * @param array $params Parameters to pass for tracking data.
		 *
		 * @return array Data to track when opted out.
		 * 
		 */
		public static function wem_get_data_for_opt_out ( $params ) {
			$plugin_data[ 'ts_meta_data_table_name']   = 'ts_tracking_wem_meta_data';
			$plugin_data[ 'ts_plugin_name' ]		   = 'Export WordPress Menus';
			
			// Store count info
			$params[ 'plugin_data' ]  				   = $plugin_data;
			
			return $params;
		}
	}
	$WP_Export_Menus = new WP_Export_Menus();
}