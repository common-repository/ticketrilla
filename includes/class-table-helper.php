<?php
	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( ! class_exists( 'TTLC_Table_Helper' ) ) {

		class TTLC_Table_Helper {

			private $name;
			private $columns;
			private $data;

			function __construct( $name, $columns, $data ) {
            	$this->set_name( $name );
            	$this->set_columns( $columns );
            	$this->set_data( $data);

			}

			/**
			 * @param mixed $name
			 */
			function set_name( $name ) {
				$this->name = $name;
			}

			/**
			 * @return mixed
			 */
			function get_name() {
				return $this->name;
			}

			/**
			 * @param mixed $data
			 */
			
			private function set_data( $data ) {
				$this->data = $data;
			}

			/**
			 * @return mixed
			 */
			
			public function get_data() {
				return $this->data;
			}

			/**
			 * @param mixed $columns
			 */
			function set_columns( $columns ) {
				$columns       = apply_filters( "ttlc_table_{$this->name}_set_colums", $columns, $this->name );
				$columns       = apply_filters( "ttlc_table_set_colums", $columns, $this->name );
				$this->columns = $columns;
			}

			/**
			 * @return mixed
			 */
			function get_columns() {
				$columns = $this->columns;
				$columns = apply_filters( "ttlc_table_{$this->name}_get_colums", $this->columns, $this->name );
				$columns = apply_filters( "ttlc_table_get_colums", $this->columns, $this->name );

				return $columns;
			}

			public function get_col( $key, $array, $data, $iteration ) {
				$value = '';
				if ( array_key_exists( 'value', $array ) ) {
					$value = call_user_func( $array['value'], $data, $iteration );
				} else {
					if ( is_array( $data ) ) {
						$value = $data[$key];
					} elseif( is_object( $data ) ) {
						$value = $data->$key;
					}
				}

				$value = apply_filters( "ttlc_table_{$this->name}_get_col", $value, $key, $this->name );
				$value = apply_filters( "ttlc_table_get_col", $value, $key, $this->name );

				return $value;
			}
			
			public function render_row( $data, $iteration ) {
				echo '<tr>';
				foreach ( $this->get_columns() as $key => $value ) {
					echo "<td>" . $this->get_col( $key, $value, $data, $iteration ) . "</td>";
				}
				echo '</tr>';
			}
			
			public function render_head() {
				foreach ( $this->get_columns() as $key => $value ) { ?>
                    <th><?php esc_html_e( $value['label'] ); ?></th>
				<?php }
			}
			
			public function render_body() {
				$data = $this->get_data();
				foreach ( $data as $iteration => $value ) {
					$this->render_row( $value, $iteration );
				}
			}

			public function render( $class = '' ) {
				?>
                <table id="ttlc-<?php esc_attr_e( $this->name ); ?>" class="<?php esc_attr_e( $class ); ?>">
                    <thead>
                    <tr>
	                <?php $this->render_head(); ?>
                    </tr>
                    </thead>
                    <tbody>
					<?php $this->render_body(); ?>
                    </tbody>
                    <tfoot>
                    <tr>
	                <?php $this->render_head(); ?>
                    </tr>
                    </tfoot>
                </table>
				<?php
			}
		}
	}