<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$product_id = sanitize_key( $_GET['product_id'] );
	$product_post = get_post( $product_id );
	if ( $product_post ) {
		$product = new TTLC_Product( $product_post );
		$user = TTLC_Rest_User::can_license( $product );
		if ( $user->check_response() ) {
			$user_response = $user->get_response_body();
			$product->support_until = isset( $user_response->have_support_until ) ? $user_response->have_support_until : null;
			$product->support_url = isset( $user_response->support_link ) ? $user_response->support_link : '#';
			$product_title = $product->title;
			TTLC_Breadcrumbs::add_link( $product_title, add_query_arg( array('product_id' => $product_id), TTLC_Page::get_url( 'main' ) ) );
			TTLC_Breadcrumbs::add_head( __( 'Add Ticket', TTLC_TEXTDOMAIN ) );
			
			$this->render_template('header');
?>

				<div class="ttlc__header-title">
					<h1><?php echo esc_html__( 'Add New Ticket', TTLC_TEXTDOMAIN ) . ': ' . esc_html( $product_title ); ?></h1>
				</div>
			</div>
			<div class="ttlc__content">
				<div class="ttlc__tickets">
					<div class="row">
						<div class="col-md-4 pull-right-md">
							<div class="ttlc__user">
								<div class="ttlc__user-header">
								<?php
									$avatar = get_avatar( $product->email );
									if ( $avatar ) {
								?>
									<div class="ttlc__user-avatar"><?php echo wp_kses_post( $avatar ); ?></div>
								<?php } ?>
									<div class="ttlc__user-name"><?php echo isset( $product->user_name ) ? '<span>' . esc_html( $product->user_name ) .'</span>' : ''; ?><span><?php echo esc_html( $product->login ); ?></span></div>
								</div>
								<div class="ttlc__user-body">
									<div class="ttlc__user-license"><?php esc_html_e( 'License type', TTLC_TEXTDOMAIN ); ?><span class="label label-primary"><?php echo esc_html( $product->license ); ?></span></div>
									<div class="ttlc__user-support">
									<?php if ( isset( $product->support_until ) ) { ?>
										<span><?php esc_html_e( 'Support until', TTLC_TEXTDOMAIN ); ?>:	<?php echo esc_html( $product->support_until ); ?></span>
									<?php } else { ?>
										<span><?php esc_html_e( 'No support', TTLC_TEXTDOMAIN ); ?></span>
									<?php } ?>
										<a href="<?php echo esc_url( $product->support_url ); ?>" target="_blank" class="btn btn-info"><?php esc_html_e( 'Extend support', TTLC_TEXTDOMAIN ); ?></a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="ttlc__tickets-inner">
							<?php 
								$this->render_template('add-ticket-form', array(
									'ticket' => new TTLC_Ticket( array(
										'server' => $product->server,
										'login' => $product->login,
										'password' => $product->password,
										'license' => $product->license,
										'license_data' => json_encode( $product->license_data ),
								) ) ) );
							?>
							</div>
						</div>
					</div>
				</div>
			</div>

<?php
		$this->render_template('footer');
		} else {
			$this->render_template( 'error', array('message' => $user->get_message(), 'product' => $product) );
		}
	}
?>
