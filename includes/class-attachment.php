<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
 
	if ( ! class_exists( 'TTLC_Attachment' ) ) {

		class TTLC_Attachment extends TTLC_Post {

			const PREFIX = 'ttlc_attachment_';
			
			public $id;
			public $ticket_id;
			public $title;
			public $size;
			public $type;
			public $url;
			public $md5;
			public $external_id;
			public $external_url;
			
			protected static $write_zip = true;
			
			public static function get_write_zip() {
				return self::$write_zip;
			}
			
			public static function set_write_zip( $value ) {
				if ( is_bool( $value ) ) {
					self::$write_zip = $value;
				}
			}

			public function attributes() {
				return array(
					'id' => esc_html__( 'ID', TTLC_TEXTDOMAIN),
					'ticket_id' => esc_html__( 'Ticket ID', TTLC_TEXTDOMAIN),
					'title' => esc_html__( 'Title', TTLC_TEXTDOMAIN),
					'size' => esc_html__( 'Size', TTLC_TEXTDOMAIN),
					'type' => esc_html__( 'Type', TTLC_TEXTDOMAIN),
					'url' => esc_html__( 'URL', TTLC_TEXTDOMAIN),
					'md5' => esc_html__( 'MD5', TTLC_TEXTDOMAIN),
					'external_id' => esc_html__( 'External ID', TTLC_TEXTDOMAIN),
					'external_url' => esc_html__( 'External URL', TTLC_TEXTDOMAIN),
				);
			}

			public function rules() {
				return array(
					array(
						array('title', 'size', 'type', 'url'),
						'required'
					),
				);
			}

			public function meta_attributes() {
				return array('ticket_id', 'size', 'type', 'url', 'external_id');
			}

			public function assign_ticket() {
				if ( isset( $this->id ) && isset( $this->ticket_id ) ) {
					add_post_meta( $this->id, static::PREFIX . 'ticket_id', $this->ticket_id, true );
				}
			}
			
			// Prepare attachment data for TTL Server
			
			public function export_data() {
				return array(
					'external_id' => $this->id,
					'name' => $this->title,
					'link' => $this->url,
					'size' => $this->size,
					'md5'  => $this->md5,
				);
			}

			public function download() {
				global $wp_filesystem;
				
				if ( isset( $this->url ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
					WP_Filesystem();
					$tmp_name = wp_tempnam( 'ttlc_temp' );
					$result = array('status' => true);
					if ( ! $tmp_name ) {
						$result = array( 
							'status' => false, 
							'status_code' => 'attachment_temp_error', 
							'message' => esc_html__( 'Could not create Temporary file.', TTLC_TEXTDOMAIN ),
						);
					} else {
						$response = wp_safe_remote_get( $this->url, array(
							'filename' => $tmp_name,
							'limit_response_size' => get_option( 'ttlc_attachments_size', 5 ) * 1024 * 1024,
							'timeout' => get_option( 'ttlc_attachments_time', 30 ),
							'stream' => true,
						) );
	
						if ( 200 != wp_remote_retrieve_response_code( $response ) ){
							$result = array( 
								'status' => false, 
								'status_code' => 'attachment_http_error', 
								'message' => trim( wp_remote_retrieve_response_message( $response ) ),
							);
						} else {

							// MD5 check
							
							if ( $this->md5 ) {
								$md5_check = verify_file_md5( $tmp_name, $this->md5 ); 
								if ( is_wp_error( $md5_check ) ) {
									$result = array( 
										'status' => false, 
										'status_code' => 'attachment_md5_error', 
										'message' => $md5_check->get_error_message() 
									);
								} else {

									$result = $this->write( $tmp_name, $this->title );
				
									if ( $result['status'] ) {
										$result = $this->save();
									}
								}
							} else {
								$result = array( 
									'status' => false, 
									'status_code' => 'attachment_md5_error', 
									'message' => esc_html__( 'MD5 not found', TTLC_TEXTDOMAIN ),
								);
							}
						}

						$wp_filesystem->delete( $tmp_name );
						
					}
					
					return $result;

				} else {
					return array( 'status' => false, 'message' => esc_html__( 'Empty URL of attachment', TTLC_TEXTDOMAIN ) );
				}
				
			}
			
			public function upload( $tmp_name, $name ) {
				global $wp_filesystem;
				require_once ( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
				$result = $this->write( $tmp_name, $name );
				if ( $result['status'] ) {
					$result = $this->insert( true );
				}
				$wp_filesystem->delete( $tmp_name );
				return $result;
			}

			public function write( $tmp_name, $name ) {
				global $wp_filesystem;
				require_once ( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
				$saved_file_ext = substr( strrchr( $name, '.' ), 1 );
				if ( ! $saved_file_ext ) { // if file hasn't extension, set - txt
					$saved_file_ext = 'txt';
				}

				$upload_dir = wp_upload_dir('ttlc'); // load it into uploads/ttlc

				if ( $upload_dir['error'] ) {
					return array( 'status' => false, 'status_code' => 'attachment_save_error', 'message' => $upload_dir['error'] );
				}

				if ( ! $wp_filesystem->is_writable( $upload_dir['path'] )  ) {
					return array( 'status' => false, 'status_code' => 'attachment_save_error', 'message' => __('Chosen directory is not writable.', TTLC_TEXTDOMAIN ) );
				}
				
				if ( $wp_filesystem->size( $tmp_name ) > get_option( 'ttlc_attachments_size', 5 ) * 1024 * 1024 ) {
					return array( 'status' => false, 'status_code' => 'attachment_save_error', 'message' => __('File size exceeds the limit.', TTLC_TEXTDOMAIN ) );
				}

				if ( self::get_write_zip() && ! self::is_archive( $tmp_name, $saved_file_ext ) ) {
					// ZIP File Write
					require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
					$new_file_name = mt_rand() . '.zip'; // generate random name
					$new_file_name = wp_unique_filename( $upload_dir['path'], $new_file_name ); // make sure it's unique
					$new_file = $upload_dir['path'] . $new_file_name;
					$archive = new PclZip( $new_file );
					$result = $archive->create( array(
						array(
							PCLZIP_ATT_FILE_NAME => $name,
							PCLZIP_ATT_FILE_CONTENT => $wp_filesystem->get_contents( $tmp_name ),
						)
					) );
					if ( $result == 0 ) {
						return array( 'status' => false, 'status_code' => 'attachment_save_error', 'message' => __( 'Can\'t zip file.', TTLC_TEXTDOMAIN ) );
					}
				} else {
					// Regular File Write
					$new_file_name = mt_rand() . '.' . $saved_file_ext; // generate random name
					$new_file_name = wp_unique_filename( $upload_dir['path'], $new_file_name ); // make sure it's unique
					$new_file = $upload_dir['path'] . $new_file_name;

					if ( ! $wp_filesystem->copy( $tmp_name, $new_file ) ) { // copy temp file
						return array( 'status' => false, 'status_code' => 'attachment_save_error', 'message' => __( 'Can\'t move file.', TTLC_TEXTDOMAIN ) );
					}
				}

				// Set correct file permissions.
				$stat = stat( dirname( $new_file ));
				$perms = $stat['mode'] & 0000666;
				$wp_filesystem->chmod( $new_file, $perms );
				
				$url = trailingslashit( $upload_dir['url'] ) . $new_file_name;
				$this->url = $url;
				$this->title = $name;
				$this->type = $saved_file_ext;
				$this->size = $wp_filesystem->size( $new_file );
				$this->md5 = md5_file( $new_file );
				
				return array( 'status' => true, 'message' => esc_html__( 'File Uploaded', TTLC_TEXTDOMAIN ) );
			}
			
			public function local_file_exists() {
				global $wp_filesystem;
				require_once ( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
				$upload_dir = wp_upload_dir('ttlc');
				$file_name = substr( $this->url, strrpos( $this->url, '/' ) + 1 );
				return $wp_filesystem->exists( $upload_dir['path'] . $file_name );
			}
			
			public static function is_archive( $tmp_name, $saved_file_ext ) {
				$archive_types = array(
					'zip' => 'application/zip',
					'rar' => 'application/x-rar-compressed',
					'7z' => 'application/x-7z-compressed',
					'tar' => 'application/x-tar',
					'tgz' => 'application/x-gzip',
					'gz' => 'application/x-gzip',
				);
				return array_key_exists( $saved_file_ext, $archive_types ) && $archive_types[$saved_file_ext] === mime_content_type( $tmp_name );
			}

			// Count size user friendly
			
			public static function format_size( $size ) {

				if ( $size > 1024 * 1024 ) {
					$size = round( $size / 1024 / 1024, 2).' MB';
				} elseif ( $size > 1024 * 10 ) { // 10 KB
					$size = round( $size / 1024, 0).' KB';
				} elseif ( $size > 1024 ) { // 1 KB
					$size = round( $size / 1024, 2) .' KB';
				} else {
					$size = $size .' B';
				}
				
				return $size;				
			}
			
			// Make max length of name - 20
			
			public static function format_name( $name ) {
				if ( strlen( $name ) > 20 ) {
					$name = substr( $name, 0, 15 ) . '...' . substr( $name, -10 ) ;
				}
				
				return $name;
			}

		}
	}