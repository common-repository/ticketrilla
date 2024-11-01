<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Post' ) ) {

		class TTLC_Post extends TTLC_Model {
			
			public function populate( $data, $prefix = false ) {
				foreach ( $this->attribute_names() as $attribute ) {
					
					if ( is_array( $data ) ) {
						$key = ( $prefix ? static::PREFIX : '' ) . $attribute;
						$this->$attribute = isset( $data[$key] ) ? stripslashes( $data[$key] ) : null;
						
					} elseif( is_object( $data ) ) {
						if ( $data instanceof WP_Post ) {
							$value = null;
							switch ( $attribute ) {
								case 'id':
									$value = $data->ID;
									break;
								case 'parent_id':
									$value = $data->post_parent;
								case 'title':
									$value = $data->post_title;
									break;
								case 'slug':
									$value = $data->post_name;
									break;
								case 'content':
									$value = $data->post_content;
									break;
								case 'date':
									$value = $data->post_date_gmt;
									break;
								default:
									if ( in_array( $attribute, $this->meta_attributes() ) ) {
										$key =  static::PREFIX . $attribute;
										$value = $data->$key;
										$value = $this->filter_meta( $attribute, $value );
									} elseif ( method_exists( $this, 'load_' . $attribute ) ) {
										$value = call_user_func( array( $this, 'load_' . $attribute ), $attribute );
									}
							}
							if ( isset( $value ) ) {
								$this->$attribute = $value;
							}
						} else {
							$key = ( $prefix ? static::PREFIX : '' ) . $attribute;
							$this->$attribute = isset( $data->$key ) ? stripslashes( $data->$key ) : null;
						}
					}

				}

			}
			
			public function trash() {
				if ( ! wp_trash_post( $this->id ) ) {
					return new WP_Error('ttlc_trash_error', __( 'Post not trashed', TTLC_TEXTDOMAIN ) );
				}
				return true;
			}
			
			public function untrash() {
				if ( ! wp_untrash_post( $this->id ) ) {
					return new WP_Error('ttlc_untrash_error', __( 'Post not untrashed', TTLC_TEXTDOMAIN ) );
				}
				return true;
			}
			
			public function save( $run_validation = true ) {
			    if ( $this->id ) {
			        return $this->update( $run_validation );
			    } else {
			        return $this->insert( $run_validation );
			    }		
    		}
    		
    		public function insert( $run_validation ) {
	    		if ( $run_validation && ! $this->validate() ) {
		    		return array('status' => false, 'message' => esc_html__( 'Post not inserted due to validation error', TTLC_TEXTDOMAIN ) );
	    		}

				$post_data = $this->prepare_post_data();

				$result = wp_insert_post( wp_slash( $post_data ) );
				if ( $result === 0 || is_wp_error( $result ) ) {
					return array( 'status' => false, 'message' => __( 'Failed adding post to db.', TTLC_TEXTDOMAIN ) );
				} else {
					$this->id = $result;
					return array( 'status' => true, 'message'   => __( 'Post added to db', TTLC_TEXTDOMAIN ) );
				}    		
    		}
    		
    		public function update( $run_validation ) {
	    		if ( $run_validation && ! $this->validate() ) {
		    		return array('status' => false, 'message' => esc_html__( 'Post not updated due to validation error', TTLC_TEXTDOMAIN ) );
	    		}

				$post_data = $this->prepare_post_data();
				$post_data['ID'] = $this->id;
				
				$result = wp_update_post( wp_slash( $post_data ) );
				if ( $result === 0 || is_wp_error( $result ) ) {
					return array( 'status' => false, 'message' => __( 'Failed updating post in db.', TTLC_TEXTDOMAIN ) );
				} else {
					return array( 'status' => true, 'message'   => __( 'Post updated in db', TTLC_TEXTDOMAIN ) );
				}
    		}
    		
    		protected function prepare_post_data() {
	    		$post_data = array(
					'post_type'      => static::post_type(),
					'meta_input'	 => $this->prepare_meta(),
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_status'    => 'publish',
	    		);
	    		
	    		if ( isset( $this->title ) ) {
		    		$post_data['post_title'] = $this->title;
	    		}

	    		if ( isset( $this->slug ) ) {
		    		$post_data['post_name'] = $this->slug;
	    		}

	    		if ( isset( $this->content ) ) {
		    		$post_data['post_content'] = $this->content;
	    		}

	    		if ( isset( $this->parent_id ) ) {
		    		$post_data['post_parent'] = $this->parent_id;
	    		}

	    		if ( isset( $this->date ) ) {
		    		$post_data['post_date_gmt'] = $this->date;
	    		}
	    		
	    		return $post_data;
    		}
    		
    		protected function prepare_meta() {
	    		$meta = array();
	    		foreach ( $this->meta_attributes() as $meta_attribute ) {
		    		$key = static::PREFIX . $meta_attribute;
		    		$value = $this->$meta_attribute;
		    		if ( isset( $value ) ) {
			    		$meta[$key] = $this->process_meta( $meta_attribute, $value );
		    		}
	    		}
	    		return $meta;
    		}
    		
    		public function meta_attributes(){
	    		return array();
    		}
    		
    		protected function process_meta( $meta_attribute, $value ) {

	    		// If $key = 'something', do somtething with $value
	    		
	    		$value = sanitize_text_field( $value );

	    		return $value;
    		}
    		
    		protected function filter_meta( $meta_attribute, $value ) {

	    		// If $key = 'something', do somtething with $value

	    		return $value;
    		}
			
			public static function post_type() {
				return strtolower( get_called_class() );
			}
			
			public static function find( $condition = array() ) {
				$args = array(
					'post_type' => static::post_type(),
					'posts_per_page' => TTLC_PPP,
				);
				$query = new WP_Query( array_merge_recursive( $args, $condition ) );
				$posts = array();
				foreach( $query->posts as $wp_post ) {
					$posts[] = new static( $wp_post );
				}
				return array(
					'items' => $posts,
					'total' => $query->found_posts,
				);
			}
			
			public static function find_one( $condition = array() ) {
				return static::find( array_merge( $condition, array('posts_per_page' => 1) ) );
			}			

			public static function find_all( $condition = array() ) {
				return static::find( array_merge( $condition, array('posts_per_page' => -1, 'nopaging' => true ) ) );
			}			

		}
	}