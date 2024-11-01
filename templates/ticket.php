<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$product_id = sanitize_key( $_GET['product_id'] );
	$product_post = get_post( $product_id );
	if ( $product_post ) {
		$product = new TTLC_Product( $product_post );
		$product_title = $product->title;
		$product_scenarios = $product->scenarios();
		
		$params = array();
		$params['ticket_external_id'] = isset( $_GET['ticket_id'] ) ? sanitize_key( $_GET['ticket_id'] ) : false;
		$params['page_num'] = isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ? (int) sanitize_key( $_GET['page_num'] ) : 1;
		$params['order'] = isset( $_GET['order'] ) && in_array( $_GET['order'], array('ASC', 'DESC') ) ? sanitize_text_field( $_GET['order'] ) : get_option( 'ttlc_responses_order', 'ASC' );
		
		$ticket_processor = new TTLC_Ticket_Processor( $product, $params );
		$ticket = $ticket_processor->result;
		if ( $ticket ) {
			$title = sprintf( '#%s. %s', $ticket->external_id, $ticket->title );
			$statuses = TTLC_Ticket::statuses();
			$responses_statuses = $ticket->responses_statuses();
			TTLC_Breadcrumbs::add_link( $product_title, add_query_arg( array('product_id' => $product_id), TTLC_Page::get_url( 'main' ) ) );
			TTLC_Breadcrumbs::add_head( $title );

			$this->render_template('header');

?>
				<div class="ttlc__header-title">
					<h1><?php echo esc_html__( 'Ticket', TTLC_TEXTDOMAIN ) . ': ' . esc_html( $product_title ); ?></h1>
				</div>
			</div>
			<div class="ttlc__content">
				<div class="ttlc__tickets <?php esc_attr_e( $ticket_processor->fallback ? 'ttlc__noresponse' : '' ); ?>">
					<div class="row">
						<div class="col-md-4 pull-right-md">							
							<div class="ttlc__status <?php echo esc_attr( $ticket->status ); ?>">
								<div class="ttlc__status-inner">
								<?php if ( $ticket->developer ) { ?>
									<span><?php echo esc_html( $ticket->developer ); ?></span>
								<?php
									} else {
									echo '<span>' . esc_html__( 'Agent not set', TTLC_TEXTDOMAIN ) . '</span>';
									}
								?>
									<div class="ttlc__status-badge label"><?php echo esc_html( $statuses[$ticket->status] ); ?></div>
								</div>
							</div>
							<div class="ttlc__user">
								<div class="ttlc__user-header">
									<?php
										$avatar = get_avatar( $product->email );
										if ( $avatar ) {
									?>
									<div class="ttlc__user-avatar"><?php echo wp_kses_post( $avatar ); ?></div>
									<?php } ?>
									<div class="ttlc__user-name">
									<?php 
										if ( isset( $product->name ) ) {
										echo '<span>' . esc_html( $product->name ) . '</span>';
										}
									?>
										<span><?php echo esc_html( $product->login ); ?></span>
									</div>
								</div>
								<div class="ttlc__user-body">
									<div class="ttlc__user-license"><?php esc_html_e( 'License type', TTLC_TEXTDOMAIN ); ?><span class="label label-primary"><?php echo esc_html( $product->license ); ?></span></div>
									<div class="ttlc__user-support">
									<?php if ( isset( $ticket->support_until ) ) { ?>
										<span><?php esc_html_e( 'Support until', TTLC_TEXTDOMAIN ); ?>:	<?php echo esc_html( $ticket->support_until ); ?></span>
									<?php } else { ?>
										<span><?php esc_html_e( 'No support', TTLC_TEXTDOMAIN ); ?></span>
									<?php } ?>
										<a href="<?php echo esc_url( $ticket->extend_support_url ); ?>" target="_blank" class="btn btn-info"><?php esc_html_e( 'Extend support', TTLC_TEXTDOMAIN ); ?></a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="ttlc__tickets-inner">
								<div class="ttlc__tickets-ticket">
									<div class="ttlc__tickets-ticket-header">
										<a class="btn btn-default ttlc-ticket-edit" href="#"><i class="fa fa-cog"></i> <?php esc_html_e( $ticket->status === 'closed' ? __( 'Open', TTLC_TEXTDOMAIN ) : __( 'Close', TTLC_TEXTDOMAIN ) ); ?>
											<form>
												<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_edit_ticket' ); ?>">
												<input type="hidden" name="action" value="ttlc/edit/ticket">
												<input type="hidden" name="server" value="<?php echo esc_attr( $product->server ); ?>">
												<input type="hidden" name="login" value="<?php echo esc_attr( $product->login ); ?>">
												<input type="hidden" name="password" value="<?php echo esc_attr( $product->password ); ?>">

												<input type="hidden" name="license" value="<?php echo esc_attr( $product->license ); ?>">
												<input type="hidden" name="license_data" value="<?php echo esc_attr( json_encode( $product->license_data ) ); ?>">
												<input type="hidden" name="external_id" value="<?php echo esc_attr( $ticket->external_id ); ?>">

												<input type="hidden" name="status" value="<?php esc_attr_e( $ticket->status === 'closed' ? 'reopen' : 'closed' ) ?>">
											</form>
										</a>
										<h4><?php echo esc_html( stripslashes( $title ) ); ?></h4>
										<hr>
									</div>
									<div class="ttlc__tickets-ticket-body">
										<?php echo wp_kses_post( $ticket->content ); ?>
										<?php if ( ! empty( $ticket->ticket_attachments ) ) { ?>
										<hr>
										<h5><?php esc_html_e( 'Attachments', TTLC_TEXTDOMAIN ); ?></h5>
										<ul class="ttlc__attachments clearfix">
										<?php
											foreach ( $ticket->ticket_attachments as $attachment ) {
												$this->render_template( 'attachment', $attachment );
											}
										?>
										<?php $this->render_template('attachment-loading'); ?>
										</ul>
										<?php } ?>
									</div>
									<?php if ( ! $ticket_processor->fallback ) { ?>
									<div class="ttlc__tickets-ticket-footer">
										<hr><a href="#ttlc__response" data-scroll class="btn btn-dark"><?php esc_html_e( 'Add Response', TTLC_TEXTDOMAIN ); ?></a>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<div class="ttlc__divider"></div>
					<div class="row">
						<?php
							$all_attachments = $ticket->get_all_attachments();
							if ( ! empty( $all_attachments ) ) { ?>
						<div class="col-md-4 pull-right-md">
							<div class="ttlc__allattachments">
								<div class="ttlc__allattachments-header">
									<h4><?php esc_html_e( 'All Attachments', TTLC_TEXTDOMAIN ); ?></h4>
								</div>
								<div class="ttlc__allattachments-body">								
									<ul class="ttlc__attachments clearfix">
									<?php
										
										foreach ( $all_attachments as $attachment ) {
											$this->render_template( 'attachment', $attachment );
										}
									?>
									<?php $this->render_template('attachment-loading'); ?>
									</ul>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="col-md-8">

						<?php if ( $ticket->responses_count ) { ?>
						
							<div class="ttlc__tickets-controls ttlc__tickets-controls-top">
								<?php $this->render_template('ticket-sort'); ?>
							</div>
							
							<ul class="ttlc__tickets-responses">
							<?php
								if ( isset( $_GET['load_more'] ) && isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ) {
									$page_num = (int) sanitize_key( $_GET['page_num'] );
									if ( $page_num > 1 ) {
							?>
								<li class="page"><span></span><span title="<?php esc_attr_e( sprintf( 'Page #%d', $page_num ), TTLC_TEXTDOMAIN ) ?>"><?php esc_html_e( sprintf( 'Page: %d', $page_num ), TTLC_TEXTDOMAIN ) ?></span></li>
							<?php } } ?>
								<?php 
									$time_label = 0;
									foreach ( $ticket->responses as $response) {
										
										$response_date = get_date_from_gmt( $response->date, 'd-m-Y' );
										$response_time = get_date_from_gmt( $response->date, 'H:i' );
										if ( is_null( $time_label ) || $time_label != $response_date ) {
											$time_label = $response_date;
											echo '<li class="time-label"><span>' . esc_html( $response_date ) . '</span></li>';
										}
								?>
									<li>
										<?php 
											if ( $response->type == 'response' ) {
												if ( $response->author_id == $ticket->client_id ) { // if client
													echo '<i class="fa fa-question"></i>';
												} else {
													echo '<i class="fa fa-share"></i>';
												}

											} else {

												// system messages					
												echo wp_kses_post( ( $response->type == 'take' ? '<i class="fa fa-hand-paper">' : '<i class="fa fa-cogs">' ) . '</i><div class="timeline-item">' );
						
											}
										?>
										<div class="ttlc__tickets-responses-header">
											<?php
											echo '<span>' . esc_html( $response_time ) . '</span>';
											if ( $response->type == 'response' ) {
												echo '<h4>' . esc_html( $response->author );
												if ( $response->author_id == $ticket->client_id ) { // if client
													echo ' <sup>' . esc_html__( 'Client', TTLC_TEXTDOMAIN ) . '</sup>';
												} else {
													echo ' <sup>' . esc_html( $response->author_pos ) . '</sup>';
												}
												echo '</h4>';
						
											} else {
												echo '<h4>';
												$sup = $response->author_id == $ticket->client_id ? ' <sup>' . esc_html__( 'Client', TTLC_TEXTDOMAIN ) . '</sup> ' : ' <sup>' . esc_html( $response->author_pos ) . '</sup>';
												if( $response->type == 'closed' ) {
													echo sprintf( $responses_statuses['closed'], esc_html( $response->author ) . $sup, array_key_exists( $response->reason, $responses_statuses['closed_reason'] ) ? $responses_statuses['closed_reason'][$response->reason] : wp_strip_all_tags( $response->reason, true ) );
	
												} elseif( array_key_exists( $response->type, $responses_statuses ) ) {
													echo sprintf( $responses_statuses[$response->type], esc_html( $response->author ) . $sup );
												} else {
													echo sprintf( __( '%s Unknown response type. Please, try to update the plugin.', TTLC_TEXTDOMAIN ), esc_html( $response->author ) . $sup );
												}
												echo '</h4></div>';
												if( in_array( $response->type, array('csir', 'csirc', 'cslr', 'cslrc') ) ) {
													$disabled = in_array( $response->type, array('csirc', 'cslrc') ) || $ticket_processor->fallback ? ' disabled' : '';
													$csir_data = in_array( $response->type, array('csir', 'cslr') ) ? $response->type : '';
													$csirs_data = $csir_data ? $csir_data . 's' : '';
													$csirr_data = $csir_data ? $csir_data . 'r' : '';

													echo '<div class="ttlc__tickets-responses-csir">';
													echo '<button class="csir-send btn btn-info"' . esc_html( $disabled  ) . ' data-type="' . esc_html( $csirs_data ) . '">' . esc_html__( 'Send', TTLC_TEXTDOMAIN ) . '</button>';
													echo ' <button class="csir-reject btn btn-danger"' . esc_html( $disabled ) . ' data-type="' . esc_html( $csirr_data ) . '">' . esc_html__( 'Reject', TTLC_TEXTDOMAIN ) . '</button>';
													echo '<input type="hidden" name="csir-id" value="' . esc_attr( $response->external_id ) . '">';
													if ( $response->type === 'csir' && ! $ticket_processor->fallback ) {
														$csi_preview = TTLC()->get_csi();
														echo '<div class="ttlc__label-info"><i class="fa fa-question-circle-o" aria-hidden="true"></i><div class="ttlc__label-info-hidden"><div class="ttlc__label-info-csi-preview">' . apply_filters( 'the_content', $csi_preview ) . '</div></div></div>';
													}
													echo '</div>';
												}
											} 
										?>
										</div>
										<?php if ( ! empty( $response->content ) || ! empty( $response->attachments ) ) { ?>
										<div class="ttlc__tickets-responses-body">
										<?php
											if ( ! empty( $response->content ) ) {					
												echo apply_filters( 'the_content', $response->content );
											}
											
											if ( ! empty( $response->attachments ) ) {
											echo '<ul class="ttlc__attachments clearfix">';
											foreach ( $response->attachments as $response_attachment ) {
												$this->render_template( 'attachment', $response_attachment );
												}
											$this->render_template( 'attachment-loading' );
											echo '</ul>';
											}		
										?>
										</div>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
							<div class="ttlc__tickets-controls"><a href="#ttlc-container" data-scroll class="btn btn-default">&uarr; <?php esc_html_e( 'To Up', TTLC_TEXTDOMAIN ); ?></a>
							<?php
																	
								$pagination = new TTLC_Pagination( $ticket->responses_count );
								$pagination->render( 'ticket' );
									
								$this->render_template('ticket-sort');
								
							?>

							</div>
		
							<?php } ?>

							<?php if ( ! $ticket_processor->fallback ) { ?>
							<div id="ttlc__response" class="ttlc__tickets-inner">
							<?php
								$this->render_template('add-ticket-form', array(
									'ticket' => new TTLC_Ticket_Response( array(
										'server' => $product->server,
										'login' => $product->login,
										'password' => $product->password,
										'license' => $product->license,
										'license_data' => json_encode( $product->license_data ),
										'parent_id' => $ticket->external_id,
								) ) ) );
							?>

							</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>

<?php
		$this->render_template('footer');
		} else {
			$this->render_template( 'error', array('message' => __( 'Ticket loading error', TTLC_TEXTDOMAIN ), 'product' => $product) );
		}
	}
?>
