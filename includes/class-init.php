<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC' ) ) {
		/**
		 * Main TTLC Class
		 *
		 * @class TTLC
		 * @version 1.0
		 *
		 */
		final class TTLC extends TTLC_Functions {

			/**
			 * @var TTLC the single instance of the class
			 */
			protected static $instance = null;


			/**
			 * @var array all plugin's classes
			 */
			public $classes = array();


			/**
			 * Main TTLC Instance
			 *
			 * @since 1.0
			 * @static
			 * @see TTLC()
			 * @return TTLC - Main instance
			 */
			static public function instance() {
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}

				return self::$instance;
			}


			/**
			 * Create plugin classes
			 *
			 * @since 1.0
			 * @see TTLC()
			 *
			 * @param       $name
			 * @param array $params
			 *
			 * @return mixed
			 */
			public function __call( $name, array $params ) {

				if ( empty( $this->classes[ $name ] ) ) {
					$this->classes[ $name ] = apply_filters( 'ttlc_call_object_' . $name, false );
				}

				return $this->classes[ $name ];

			}

			/**
			 * Function for add classes to $this->classes
			 * for run using TTLC()
			 *
			 * @since 1.0
			 *
			 * @param string $class_name
			 * @param bool   $instance
			 */
			public function set_class( $class_name, $instance = false ) {
				if ( empty( $this->classes[ $class_name ] ) ) {
					$class                        = 'TTLC_' . $class_name;
					$this->classes[ $class_name ] = $instance ? $class::instance() : new $class;
				}
			}


			/**
			 * Cloning is forbidden.
			 *
			 * @since 1.0
			 */
			public function __clone() {
				_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TTLC_TEXTDOMAIN ), '1.0' );
			}


			/**
			 * Unserializing instances of this class is forbidden.
			 *
			 * @since 1.0
			 */
			public function __wakeup() {
				_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TTLC_TEXTDOMAIN ), '1.0' );
			}


			/**
			 * TTLC constructor.
			 *
			 * @since 1.0
			 */
			function __construct() {


				if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
					
					if ( $this->is_request( 'admin' ) ) {

						// Include TTLC classes
						$this->ttlc_class_loader();
						
						// Init TTLC
						add_action( 'init', array($this, 'init') );
						add_action( 'ttlc_before_render_template', array($this, 'clean_autologin_users') );
						
						// Update Ticketrilla to Standard Licenses
						
						add_action( 'init', array($this, 'update_ticketrilla_licenses') );
						
					} else {

						if ( isset( $_GET['ttlc_autologin_token'] ) && isset( $_GET['ttlc_autologin_key'] ) ) {
							add_action( 'init', array($this, 'autologin') );
						}
						
					}

					add_action( 'plugins_loaded', array($this, 'check_plugin_version') );
					add_action( 'clear_auth_cookie', array($this, 'check_autologin_user') );
					add_action( 'ttlc_background_update', array($this, 'background_update') );
					
					// Old TTLS license compatibility
					add_filter( 'ttlc_rest_send_request_data', array($this, 'license_type_compatibility') );

					add_filter( 'heartbeat_received', array($this, 'heartbeat_received'), 10, 3 );
					add_filter( 'heartbeat_send', array($this, 'heartbeat_send'), 10, 2 );
					add_filter( 'cron_schedules', array($this, 'background_update_interval') );

				}
				
			}

			/**
			 * Include required core files
			 *
			 * @since 1.0
			 *
			 * @return void
			 */
			protected function ttlc_class_loader() {
				require_once 'class-ajax.php';
				require_once 'class-support.php';
				require_once 'class-cpt.php';
				require_once 'class-model.php';
				require_once 'class-post.php';
				require_once 'class-product.php';
				require_once 'class-product-available.php';
				require_once 'class-password-reset.php';
				require_once 'class-ticket.php';
				require_once 'class-ticket-response.php';
				require_once 'class-newsletter.php';
				require_once 'class-attachment.php';
				require_once 'class-table-helper.php';
				require_once 'class-enqueue.php';
				require_once 'class-page.php';
				require_once 'class-pagination.php';
				require_once 'class-breadcrumbs.php';
				require_once 'class-ticket-processor.php';
				require_once 'class-ticket-list-processor.php';
				require_once 'class-newsletters-processor.php';
				require_once 'class-settings.php';
				require_once 'class-settings-attachments.php';
				require_once 'class-settings-autologin.php';
				require_once 'class-settings-updates.php';
				require_once 'class-settings-newsletters.php';
				require_once 'rest/class-abstract-rest.php';
				require_once 'rest/class-rest-server.php';
				require_once 'rest/class-rest-ticket.php';
				require_once 'rest/class-rest-newsletters.php';
				require_once 'rest/class-rest-user.php';

			}

			public function init() {

				// Set TTLC classes
				$this->set_classes();

				$this->protect_uploads();
				
			}

			/**
			 * Function for add classes to $this->classes
			 * for run using TTLC() depending on request type
			 *
			 * @since 1.0
			 *
			 */
			protected function set_classes() {

				$this->set_class( 'enqueue' );
				$this->set_class( 'support' );
				$this->set_class( 'cpt' );
				$this->set_class( 'page' );
				
				if( $this->is_request( 'ajax' ) ) {
					$this->set_class( 'ajax' );
				}

			}

			public function check_plugin_version() {
				if ( TTLC_PLUGIN_VERSION !== get_option('ttlc_version') ) {
					$this->activate();
				}
			}
			
			protected function protect_uploads() {
				global $is_apache;
				$upload_dir = wp_upload_dir( 'ttlc', true, true );
				if ( ( is_array( $upload_dir ) && $upload_dir['error'] === false ) && $this->write_test_php( $upload_dir ) ) {
					if ( $this->remote_test_php( $upload_dir ) ) {
						// Allow save files unzipped. Default — zipped.
						TTLC_Attachment::set_write_zip( false );
					} else {
						if ( $is_apache ) {
							$force_rewrite = isset( $_GET['global'] ) && $_GET['global'] === TTLC_HT_REWRITE_PARAM;
							if ( ! $force_rewrite && file_exists( trailingslashit( $upload_dir['path'] ) . '.htaccess' ) ) {
								add_action( 'admin_notices', array($this, 'configure_server_notice') );
								add_action( 'admin_notices', array($this, 'rewrite_config_notice') );
							} else {
								if ( ! $this->write_config() ) {
									add_action( 'admin_notices', array($this, 'upload_dir_writable_notice') );
								} elseif ( $force_rewrite ) {
									add_action( 'init', array( $this, 'redirect_to_main') );
								}
							}
						} else {
							add_action( 'admin_notices', array($this, 'configure_server_notice') );
						}
					}
				} else {
					add_action( 'admin_notices', array($this, 'upload_dir_writable_notice') );
				}
			}
			
			public function redirect_to_main() {
				wp_redirect( remove_query_arg( 'global' ) );
				exit;
			}
			
			protected function write_config() {
				require_once( ABSPATH . 'wp-admin/includes/misc.php' );
				$upload_dir = wp_upload_dir( 'ttlc' );
				$htaccess_file = trailingslashit( $upload_dir['path'] ) . '.htaccess';
				if ( is_writable( $upload_dir['path'] ) && ( ! file_exists( $htaccess_file ) || is_writable( $htaccess_file ) ) ) {
					return insert_with_markers( $htaccess_file, 'TTLC', 'php_flag engine off' );
				}
				return false;
			}

			public function rewrite_config_notice() {
			?>
			    <div class="notice notice-warning is-dismissible">
			        <p><?php esc_html_e( 'Or you can try to regenerate .htaccess file.', TTLC_TEXTDOMAIN ); ?></p>
			        <p><?php echo '<a class="button button-primary" href="' . esc_url( add_query_arg( 'global', TTLC_HT_REWRITE_PARAM ) ) . '">' . esc_html__( 'Regenerate', TTLC_TEXTDOMAIN ) . '</a>'; ?></p>
			    </div>
			<?php
	    	}

			public function configure_server_notice() {
				$upload_dir = wp_upload_dir( 'ttlc' );
			?>
			    <div class="notice notice-warning is-dismissible">
			        <p><?php echo esc_html( sprintf( __( 'Please, configure your server to prevent file execution in directory: %s.', TTLC_TEXTDOMAIN ), $upload_dir['path'] ) ); ?></p>
			    </div>
			<?php
	    	}
			
			public function upload_dir_writable_notice() {
			?>
			    <div class="notice notice-warning">
			        <p><?php esc_html_e( 'Please, check if uploads directory is writable.', TTLC_TEXTDOMAIN ); ?></p>
			    </div>
			<?php
	    	}
			
			protected function remote_test_php( $upload_dir ) {
				$remote_get = wp_remote_get( trailingslashit( $upload_dir['url'] ) . TTLC_TEST_PHP );
				if ( is_array( $remote_get ) ) {
					$body = $remote_get['body'];
					if ( $body !== 'PHP' ) {
						return true;
					}
				}
				return false;
			}
			
			protected function write_test_php( $upload_dir ) {
				global $wp_filesystem;
				require_once ( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
				$test_php_path = trailingslashit( $upload_dir['path'] ) . TTLC_TEST_PHP;
				if ( $wp_filesystem->exists( $test_php_path ) ) {
					return true;
				} else {
					if ( $wp_filesystem->is_writable( $upload_dir['path'] ) ) {
						
						return $wp_filesystem->put_contents( $test_php_path, '<?php echo "PHP"; ?>' );
					}
				}
				return false;
			}

			public function activate() {
				$role = get_role( 'administrator' );
				$role->add_cap( 'manage_ttlc' );
				if ( ! wp_next_scheduled( 'ttlc_background_update' ) && get_option( 'ttlc_updates_on', true ) ) {
					wp_schedule_event( time(), 'ttlc_interval', 'ttlc_background_update' );
				}
				$this->migrate_products();
				update_option( 'ttlc_version', TTLC_PLUGIN_VERSION );
			}
			
			public function deactivate() {
				if ( wp_next_scheduled( 'ttlc_background_update' ) ) {
					wp_unschedule_event( wp_next_scheduled( 'ttlc_background_update' ), 'ttlc_background_update' );
				}
			}

			private function migrate_products() {
				$products = TTLC_Product::find_all();
				if ( ! empty( $products['items'] ) ) {
					foreach( $products['items'] as $product ) {
						if ( empty( $product->external_id ) ) {
							$server = new TTLC_Rest_Server( array( 'server' => $product->server ) );
							$server->check();
							$response_body = $server->get_response_body();
							if ( $server->check_response() && ! empty( $response_body->product_list ) && is_array( $response_body->product_list ) ) {
								foreach( $response_body->product_list as $server_product ) {
									$server_product_slug = TTLC_Support::format_slug( $product->server, $server_product->post_name );
									if ( $server_product_slug == $product->slug ) {
										$product->external_id = $server_product->ID;
										$product->save();
										break;
									}
								}
							}
						}
					}
				}
			}
			
			public function ttls_errors() {
				return array(
					'ttls_server_noconfig' => __( 'Server is not configured', TTLC_TEXTDOMAIN ),
					'ttls_license_used' => __( 'This license is already used', TTLC_TEXTDOMAIN ),
				);
			}
			
			public function get_ttls_error( $code ) {
				$errors = $this->ttls_errors();
				if ( array_key_exists( $code, $errors ) ) {
					return $errors[$code];
				}
				return false;
			}

			public function get_csi() {
				$html = '';
				$theme = wp_get_theme();
				$plugins = get_plugins();
				$html .= '<strong>Server Software:</strong>';
				$html .= '<ul>';
				$html .= '<li>' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
				$html .= '<li>' . 'PHP Version: ' . phpversion() . '</li>';

				$html .= '<li>' . 'PHP Directives:<ul>';
				$html .= '<li>upload_max_filesize: ' . ini_get('upload_max_filesize') . '</li>';
				$html .= '<li>post_max_size: ' . ini_get('post_max_size') . '</li>';
				$html .= '<li>max_execution_time: ' . ini_get('max_execution_time') . '</li>';
				$html .= '<li>memory_limit: ' . ini_get('memory_limit') . '</li>';
				$html .= '</ul></li>';
				$html .= '<li>SSL: ' . ( empty( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] === 'off' ? 'None' : 'Active' ) . '</li>';

				$html .= '</ul>';
				$html .= '<strong>WP Information:</strong>';

				$html .= '<ul>';
				$html .= '<li>WP Version: ' . $GLOBALS['wp_version'] . '</li>';				
				$html .= '<li>WP Theme: ' . $theme->get( 'Name' ) . ' version ' . $theme->get( 'Version' ) . '</li>';
				$html .= '<li>WP Plugins Installed:<ul>';
				foreach ( $plugins as $plugin_path => $plugin ) {
					$active = is_plugin_active( $plugin_path ) ? ' <strong>Active</strong>' : ' not Active';
					$html .= '<li>' . $plugin['Name'] . ' version ' . $plugin['Version'] . $active . '</li>';
				}
				$html .= '</ul></li>';
				$html .= '</ul>';
				
				return $html;
			}
			
			public function get_csl( $ticket_id ) {
				$settings = new TTLC_Settings_Autologin;
				$settings->load();

				$old_users = get_users( array(
					'meta_key' => 'ttlc_ext_ticket_id',
					'meta_value' => $ticket_id,
				) );
				
				if ( ! empty( $old_users ) ) {

					foreach ( $old_users as $old_user ) {
						wp_delete_user( $old_user->ID, $settings->reassign_user );
					}
					
				}
					
				$user_id = wp_insert_user( array(
					'user_login' => sprintf( 'ttlc_autologin_user_%d_%d', $ticket_id, mt_rand( 0, 9999 ) ),
					'user_pass' => wp_generate_password(),
					'role' => $settings->role,
				) );
				
				if ( is_wp_error( $user_id ) ) {
					return $user_id;
				}
				
				update_user_meta( $user_id, 'ttlc_ext_ticket_id', $ticket_id );
				update_user_meta( $user_id, 'ttlc_logins', $settings->logins );

				$token = wp_generate_password( 22, false );
				$key = wp_generate_password( 48, false );
				
				update_user_meta( $user_id, 'ttlc_autologin_key', wp_hash_password( $key ) );				
				update_user_meta( $user_id, 'ttlc_autologin_token', $token );
				
				return add_query_arg( array(
					'ttlc_autologin_key' => $key,
					'ttlc_autologin_token' => $token,
				), home_url() );
				
			}
			
			public function autologin() {

				if ( is_user_logged_in() ) {
					wp_logout();
					wp_safe_redirect( add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $GLOBALS['wp']->request ) ) );
					exit();
				} 

				$token = sanitize_text_field( $_GET['ttlc_autologin_token'] );

				$users = get_users( array(
					'meta_key' => 'ttlc_autologin_token',
					'meta_value' => $token,
				) );
				
				if ( count( $users ) === 1 ) {
					$user_id = $users[0]->ID;
					
					if ( $this->check_autologin_user( $user_id ) ) {

						$key = sanitize_text_field( $_GET['ttlc_autologin_key'] );
						$hashed = get_user_meta( $user_id, 'ttlc_autologin_key', true );
	
						if ( wp_check_password( $key, $hashed ) ) {
						    wp_clear_auth_cookie();
						    wp_set_current_user ( $user_id );
						    wp_set_auth_cookie  ( $user_id );
						    $logins = get_user_meta( $user_id, 'ttlc_logins', true );
							update_user_meta( $user_id, 'ttlc_logins', $logins - 1 );
						    wp_safe_redirect( admin_url() );
						    exit();	
						}
						
					}
					
					
				}
				
				return false;
			}
			
			public function check_autologin_user( $user_id = false ) {
				require_once 'class-model.php';
				require_once 'class-settings.php';
				require_once 'class-settings-autologin.php';
				
				if ( ! $user_id ) {
					$user_id = get_current_user_id();
					if ( ! $user_id ) {
						return false;
					}
				}
				
				$settings = new TTLC_Settings_Autologin;
				$settings->load();
				
				$logins = get_user_meta( $user_id, 'ttlc_logins', true );

				if ( is_numeric( $logins ) ) {
					if ( $logins <= 0 ) {
						$this->delete_autologin_user( $user_id, $settings->reassign_user );
						return false;
					}
					
					$user_data = get_userdata( $user_id );
					$settings_days = 60 * 60 * 24 * $settings->days;
					
					if ( strtotime( 'now' ) - strtotime( $user_data->user_registered ) >= $settings_days ) {
						$this->delete_autologin_user( $user_id, $settings->reassign_user );
						return false;
					}
					
					return true;
					
				}
				
				return false;				
				
			}
			
			public function clean_autologin_users( $template ) {
				
				if ( in_array( $template, array('main', 'settings') ) ) {
					$cleaning_date = get_option( 'ttlc_cleaning_date' );
					$settings = new TTLC_Settings_Autologin;
					$settings->load();
					$term = 60 * 60 * 24 * absint( $settings->days );

					if ( ! $cleaning_date || time() - strtotime( $cleaning_date ) > $term ) {

						$date_users = get_users( array(
							'fields' => array('ID'),
							'meta_key' => 'ttlc_logins',
							'meta_compare' => 'EXISTS',
							'date_query' => array(
								array(
									'before' => gmdate( 'Y-m-d H:i:s', strtotime( sprintf('-%d days', $settings->days )) ),
								),
							),
						) );
						
						$date_users = array_map( function( $u ){
							return $u->ID;
						}, $date_users);

						$login_users = get_users( array(
							'fields' => array('ID'),
							'meta_key' => 'ttlc_logins',
							'meta_value' => 0,
						) );
						
						$login_users = array_map( function( $u ){
							return $u->ID;
						}, $login_users);
						
						$users = array_merge( $date_users, $login_users );
						
						if ( ! empty( $users ) ) {
							foreach ( $users as $user_id ) {
								$this->delete_autologin_user( $user_id, $settings->reassign_user );
							}
						}
						
						update_option( 'ttlc_cleaning_date', gmdate( 'Y-m-d H:i:s' ) );

					}
				}
			}
			
			protected function delete_autologin_user( $user_id, $reassign_user_id ) {
				require_once( ABSPATH . 'wp-admin/includes/user.php' );
				require_once 'rest/class-abstract-rest.php';
				require_once 'rest/class-rest-ticket.php';
				require_once 'class-model.php';
				require_once 'class-post.php';
				require_once 'class-product.php';
				require_once 'class-ticket.php';

				$external_ticket_id = get_user_meta( $user_id, 'ttlc_ext_ticket_id', true );

				wp_delete_user( $user_id, $reassign_user_id );
				
				$ticket_query = new WP_Query( array(
					'post_type' => TTLC_Ticket::post_type(),
					'posts_per_page' => 1,
					'meta_key' => TTLC_Ticket::PREFIX . 'external_id',
					'meta_value' => $external_ticket_id,
					'fields' => 'ids',
				) );
				
				if ( ! $ticket_query->have_posts() ) {
					return false;
				}
				
				$ticket_id = $ticket_query->get_posts()[0];
				$product_id = get_post_meta( $ticket_id, TTLC_Ticket::PREFIX . 'product_id', true );
				
				if ( ! $product_id ) {
					return false;
				}
				
				$product_post = get_post( $product_id );
				
				if ( $product_post === null ) {
					return false;
				}
				
				$product = new TTLC_Product( $product_post );

				$rest_data = array(
					'parent' => $external_ticket_id,
					'license_type' => $product->license,
					'response_status' => 'csle', // Client Server Login Expired
				);

				$rest_data = array_merge( $rest_data, $product->license_data );

				$rest_ticket = new TTLC_Rest_Ticket( array(
					'server' => $product->server,
					'login' => $product->login,
					'password' => $product->password,
					'data' => $rest_data,
				) );
				
				$rest_ticket->add();
				$response = $rest_ticket->get_response();
				
				if ( $response['response']['code'] === 200 ) {
					return true;
				}
				
				return false;
			}
			
			public function license_type_compatibility( $data ) {
				if ( ! empty( $data['license_type'] ) && $data['license_type'] === 'standard' ) {
					$data['license_type'] = 'ticketrilla';
				}
				return $data;
			}
			
			public function update_ticketrilla_licenses() {
				if ( ! get_option( 'ttlc_standard_license_type', false ) ) {
			        $connected = TTLC_Product::find_all();
			        foreach( $connected['items'] as $product ) {
				        if ( $product->license === 'ticketrilla' ) {
					        $product->license = 'standard';
					        $product->save();
				        }
			        }
			        update_option( 'ttlc_standard_license_type', true );
				}
			}
			
			public function get_pending_tickets_count( $product_id = false ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key' => TTLC_Ticket::PREFIX . 'status',
						'value' => 'replied',
					),
				);
				if( $product_id ) {
					$meta_query[] = array(
						'key' => TTLC_Ticket::PREFIX . 'product_id',
						'value' => $product_id,
					);
				}
				$tickets = TTLC_Ticket::find_all( array(
					'meta_query' => $meta_query,
				) );
				
				return $tickets['total'];
			}

			public function get_pending_newsletters_count( $product_id = false ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key' => TTLC_Newsletter::PREFIX . 'status',
						'value' => 'new',
					),
				);
				if( $product_id ) {
					$meta_query[] = array(
						'key' => TTLC_Newsletter::PREFIX . 'product_id',
						'value' => $product_id,
					);
				}
				$newsletters = TTLC_Newsletter::find_all( array(
					'meta_query' => $meta_query,
				) );
				
				return $newsletters['total'];
			}

			public function get_pending_events_count() {
				return $this->get_pending_tickets_count() + $this->get_pending_newsletters_count();
			}

			private function clean_local_tickets() {
				$tickets = TTLC_Ticket::find_all( array(
				) );
				foreach( $tickets['items'] as $ticket ) {
					wp_delete_post( $ticket->id, true );
				}
				
			}
			
			public function heartbeat_received( $response, $data, $screen_id ) {
				if ( empty( $data['ttlc_pending_counts_products'] ) ) {
					return $response;
				}
				$ids = $data['ttlc_pending_counts_products'];
				$pending_counts = $this->get_pending_counts_products( $ids );
				if ( empty( $response['ttlc_pending_counts'] ) ) {
					$response['ttlc_pending_counts'] = $pending_counts;
				} else {
					$response['ttlc_pending_counts'] = array_merge( $response['ttlc_pending_counts'], $pending_counts );
				}
				
				return $response;
			}

			public function heartbeat_send( $response, $data ) {
				$pending_counts = $this->get_main_pending_counts();
				if ( empty( $response['ttlc_pending_counts'] ) ) {
					$response['ttlc_pending_counts'] = $pending_counts;
				} else {
					$response['ttlc_pending_counts'] = array_merge( $response['ttlc_pending_counts'], $pending_counts );
				}
			    return $response;
			}
			
			private function get_main_pending_counts() {
				$counts = array();
				$counters = array('events', 'newsletters');
				foreach( $counters as $counter ) {
					$count = $this->get_pending_count( array(
						'counter' => $counter,
					) );
					if ( ! empty( $count ) ) {
						$counts[] = $count;
					}
				}
				return $counts;
			}
			
			private function get_pending_counts_products( $ids ) {
				$counts = array();
				$counter = 'tickets';
				foreach( $ids as $id ) {
					$count = $this->get_pending_count( array(
						'counter' => $counter,
						'params' => array($id),
					) );
					if ( ! empty( $count ) ) {
						$counts[] = $count;
					}
				}
				return $counts;
			}

			private function get_pending_count( $args ) {
				
				if ( ! empty( $args['counter'] ) && in_array( $args['counter'], array('events', 'tickets', 'newsletters') ) ) {
					$counter = $args['counter'];
					$method = 'get_pending_' . $counter . '_count';
					if ( method_exists( $this, $method ) ) {
						$params = empty( $args['params'] ) ? array() : $args['params'];
						$count = call_user_func_array( array($this, $method), $params );
						$selector_part = $counter . ( empty( $params ) ? '' : '-' . implode( '-', $params ) );
						if ( is_numeric( $count ) ) {
						    return array(
							    'selector' => '.ttlc__pending-' . $selector_part . '-count',
							    'value' => $count,
						    );
						}
					}
				}
				
				return false;
			}
			
			public function background_update() {
				if ( get_option( 'ttlc_updates_on', true ) ) {
					$this->update_product(); // Update product, tickets, newsletters
				}
			}
			
			private function update_product() {
				require_once 'class-model.php';
				require_once 'class-post.php';
				require_once 'class-product.php';
				require_once 'class-ticket.php';
				require_once 'class-ticket-response.php';
				require_once 'class-attachment.php';
				require_once 'class-newsletter.php';
				require_once 'class-ticket-processor.php';
				require_once 'class-newsletters-processor.php';
				require_once 'rest/class-abstract-rest.php';
				require_once 'rest/class-rest-server.php';
				require_once 'rest/class-rest-ticket.php';
				require_once 'rest/class-rest-newsletters.php';

				// First Check New Products

		        $products = TTLC_Product::find_one( array(
			        'meta_query' => array(
				        array(
					        'key' => TTLC_Product::PREFIX . 'update_time',
					        'compare' => 'NOT EXISTS',
				        ),
			        ),
		        ) );
		        
		        // Then Check Outdated Products
		        
		        if ( empty( $products['items'] ) ) {
			        $products = TTLC_Product::find_one( array(
				        'meta_query' => array(
					        array(
						        'key' => TTLC_Product::PREFIX . 'update_time',
						        'value' => time() - $this->get_bg_update_interval(),
						        'compare' => '<',
					        ),
				        ),
						'order' => 'ASC',
						'orderby' => 'meta_value_num',
						'meta_key' =>TTLC_Product::PREFIX . 'update_time',
	
			        ) );
		        }
		        
		        

				if ( ! empty( $products['items'][0] ) ) {
			        $product = $products['items'][0];

					$meta_query = array(
						'relation' => 'AND',
						array(
							'key' => TTLC_Ticket::PREFIX . 'product_id',
							'value' => $product->id,
						),
						array(
							'key' => TTLC_Ticket::PREFIX . 'status',
							'value' => 'closed',
							'compare' => '!=',
						)
					);
					
					$tickets = TTLC_Ticket::find_all( array(
						'meta_query' => $meta_query,
					) );
					
					foreach( $tickets['items'] as $ticket ) {
						$ticket_processor = new TTLC_Ticket_Processor( $product, array(
							'ticket_external_id' => $ticket->external_id,
							'order' => 'DESC',
							'page_num' => 1,
						) );
					}
					
					// Get Newsletters
					
					if ( $product->newsletters ) {
						$newsletters_processor = new TTLC_Newsletters_Processor( $product );
					}

					// Update Product Info

					$week = 7 * 24 * 60 * 60;

					if ( empty( $product->update_time ) || ( time() - $product->update_time ) >= $week ) {
						$server = new \TTLC_Rest_Server( array('server' => $product->server) );
						$server->check();		
						$response_body = $server->get_response_body();
						if ( ! is_wp_error( $server->get_response() ) ) {
							foreach( $response_body->product_list as $server_product ) {
								if ( $server_product->ID == $product->external_id ) {
									$product->type = $server_product->type;
									$product->title = $server_product->post_title;
									$product->content = $server_product->post_content;
									$product->thumbnail = $server_product->image;
									$product->author = $server_product->author_name;
									$product->author_uri = $server_product->author_link;
									$product->manual = $server_product->manual;
									$product->service_terms = $server_product->terms;
									$product->privacy_statement = $server_product->privacy;
									break;
								}
							}
						}

					}

			    $product->update_time = time();
			    $product->save();
				}
		        

			}
			
			public function background_update_interval( $schedule ) {
				if( empty( $schedule['ttlc_interval'] ) ) {
					$schedule['ttlc_interval'] = array(
						'interval' => $this->get_bg_update_interval(),
						'display' => 'TTLC interval',
					);
				}
				return $schedule;
			}

		}
	}

	/**
	 * Function for calling TTLC methods and variables
	 *
	 * @return TTLC
	 */
	function TTLC() {
		return TTLC::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['TTLC'] = TTLC();
