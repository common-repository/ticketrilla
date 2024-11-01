<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Model' ) ) {

		class TTLC_Model {

		    /**
		     * The name of the default scenario.
		     */
		    const SCENARIO_DEFAULT = 'default';
		    
		    const PREFIX = '';
		    
		    /**
		     * @var array validation errors (attribute name => array of errors)
		     */
		    private $_errors;

		    /**
		     * @var string current scenario
		     */
		    private $_scenario = self::SCENARIO_DEFAULT;
		    
		    public function set_scenario( $scenario ) {
			    $this->_scenario = $scenario;
		    }

		    public function get_scenario( ) {
			    return $this->_scenario;
		    }

		    public function scenarios() {
		        return array();
		    }		    				

			public function attributes() {
				return array();
			}

			public function rules() {
				return array();
			}
			
			function __construct( $data = array(), $prefix = false ) {
				if ( ! empty( $data) ) {
					$this->populate( $data, $prefix );
				}
			}
			
			public function populate( $data, $prefix = false  ) {
				if ( is_array( $data ) ) {
					foreach ( $this->attribute_names() as $attribute ) {
						$key = ( $prefix ? static::PREFIX : '' ) . $attribute;
						if ( isset( $data[$key] ) ) {
							$this->$attribute = stripslashes( $data[$key] );
						}
					}
				}
			}
			
			public function attribute_names() {
				return array_keys( $this->attributes() );
			}
			
			public function validate( $clear_errors = true ) {
				if ( $clear_errors ) {
					$this->clear_errors();
				}
				
				foreach ( $this->rules() as $rule ) {
					if ( isset( $rule['on'] ) && $rule['on'] !== $this->_scenario ) {
						continue;
					}
					$error_message = isset( $rule['error_message'] ) ? $rule['error_message'] : false;
					$this->validate_attributes( $rule[0], $rule[1], $error_message, isset( $rule[2] ) ? $rule[2] : false );
				}
				
				return ! $this->has_errors();
			}
			
			public function clear_errors( $attribute = null ) {
				if ( $attribute === null ) {
		            $this->_errors = array();
		        } else {
		            unset($this->_errors[$attribute] );
		        }
			}
			
			protected function validate_attributes( $attribute_names, $validation_type, $error_message = false, $additional_data = false ) {
				foreach ( $attribute_names as $attribute ) {
					switch ( $validation_type ) {
						case 'required':
							if ( is_array( $this->$attribute ) ) {
								foreach ( $this->$attribute as $arr_attribute_key => $arr_attribute_val ) {
									$arr_attribute_val = trim( $arr_attribute_val );
									if ( empty( $arr_attribute_val ) ) {
										$this->add_error( $attribute, __( 'These fields are required', TTLC_TEXTDOMAIN ) );
										break;
									}
								}
							} else {
								$attribute_val = trim( $this->$attribute );
								if ( empty( $attribute_val ) ) {
									$this->add_error( $attribute, __( 'This field is required', TTLC_TEXTDOMAIN ) );
								}
							}
							break;
						case 'unique':
							if ( method_exists( get_called_class(), 'find_one' ) ) {
								switch ( $attribute ) {
									case 'slug':
										// If the Post is New
										if ( is_null( $this->id ) ) {
											$query = static::find_one( array('name' => $this->$attribute ) );
											if ( ! empty( $query['items'] ) ) {
												$this->add_error( $attribute, $error_message ? $error_message : __( 'The slug already exists', TTLC_TEXTDOMAIN ) );
											}
										}
										break;
								}
							} else {
								$this->add_error( $attribute, __( 'Method for unique-check not found', TTLC_TEXTDOMAIN ) );
							}
							break;
						case 'exist':
							if ( method_exists( get_called_class(), 'find_one' ) ) {
								$query = static::find_one( array('ID' => $this->$attribute ) );
								if ( empty( $query['items'] ) ) {
									$this->add_error( $attribute, __( 'This post does not exist', TTLC_TEXTDOMAIN ) );
								}

							} else {
								$this->add_error( $attribute, __( 'Method for exist-check not found', TTLC_TEXTDOMAIN ) );
							}
							break;
						case 'user_exists':
							if ( ! get_userdata( trim( $this->$attribute ) ) ) {
								$this->add_error( $attribute, __( 'This user does not exist', TTLC_TEXTDOMAIN ) );
							}
							break;
						case 'email':
							if ( ! is_email( trim( $this->$attribute ) ) ) {
								$this->add_error( $attribute, __( 'This E-mail is invalid', TTLC_TEXTDOMAIN ) );
							}
							break;
						case 'number':
							if ( ! is_numeric( trim ( $this->$attribute ) ) ) {
								$this->add_error( $attribute, __( 'This field must be numeric', TTLC_TEXTDOMAIN ) );
							} elseif( $additional_data && $additional_data === 'natural' && trim ( $this->$attribute ) < 1 ) {
									$this->add_error( $attribute, __( 'The value must be greater than 0', TTLC_TEXTDOMAIN ) );
							}
							break;
						case 'list':
							if ( is_array( $additional_data ) && ! empty( $additional_data ) ) {
								if ( ! in_array( trim ( $this->$attribute ), $additional_data ) ) {
									$this->add_error( $attribute, __( 'The value is not in a list', TTLC_TEXTDOMAIN ) );
								}
							} else {
								$this->add_error( $attribute, __( 'Additional data is required', TTLC_TEXTDOMAIN ) );
							}
							break;
					}
				}
			}

		    public function add_error( $attribute, $error = '' ) {
		        $this->_errors[$attribute][] = $error;
		    }			

			public function has_errors( $attribute = null ) {
			    return $attribute === null ? ! empty( $this->_errors ) : isset( $this->_errors[$attribute] );
			}

		    public function get_errors( $attribute = null ) {
		        if ( $attribute === null ) {
		            return $this->_errors === null ? array() : $this->_errors;
		        }
		        return isset( $this->_errors[$attribute] ) ? $this->_errors[$attribute] : array();
		    }
		    
		}
	}