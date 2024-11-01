<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	$data = $this->get_data();
	$model = $data['model'];
	$state = $model->get_state();
	$labels = $model->attributes();
?>

<div class="ttlc__settings-inner-footer">
<?php 
	if ( $model->has_errors() ) {
		echo '<span class="text-danger state">';
		foreach( $model->get_errors() as $attr_key => $errors ) {
			foreach ( $errors as $error ) {
				echo esc_html( $labels[$attr_key] . ': ' . $error ) . '<br>';
			}
		}
		echo '</span>';
	} elseif( is_array( $state ) ) {
		echo '<span class="text-' . esc_attr( $state['status'] ) . ' state">' . esc_html( $state['message'] ) . '</span>';
	} else {
		echo '<span class="text-warning state"></span>';
	}
?>
	<button type="submit" class="btn btn-dark disabled"><?php esc_html_e( 'Save', TTLC_TEXTDOMAIN ); ?></a>
</div>
