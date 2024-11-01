<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$ticket = isset( $data['ticket'] ) ? $data['ticket'] : new TTLC_Ticket;
?>
								<form id="ttlc-add-ticket" class="ttlc__tickets-form" enctype="multipart/form-data">
									<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_add_ticket' ); ?>">
								<?php
									foreach ( $ticket->required_hidden_attributes() as $attribute ) {
										echo '<input type="hidden" name="' . esc_attr( $attribute ) . '" value="' . esc_attr( $ticket->$attribute ) . '">';
									}
									if ( $ticket instanceof TTLC_Ticket_Response ) {
										echo '<h4>' . esc_html__( 'Add Response', TTLC_TEXTDOMAIN ) . '</h4>';
									} elseif( $ticket instanceof TTLC_Ticket ) {
								?>
									<div class="form-group <?php esc_attr_e( $ticket->has_errors( 'title' ) ? 'has-error' : '' ); ?>">
										<input name="title" id="ttlc-ticket-title" type="text" maxlength="256" class="form-control" placeholder="<?php esc_html_e( 'Ticket Title', TTLC_TEXTDOMAIN ); ?>" value="<?php esc_attr_e( isset( $ticket->title ) ? $ticket->title : '' ); ?>">
									<?php
										if ( $ticket->has_errors( 'title' ) ) {
											foreach ( $ticket->get_errors( 'title' ) as $error_message ) {
												echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
											}
										}
									?>
									</div>
										
								<?php } ?>
									<div class="form-group <?php esc_attr_e( $ticket->has_errors( 'content' ) ? 'has-error' : '' ); ?>">
										<textarea name="content" id="ttlc-ckeditor" class="form-control" rows="10"><?php echo wp_kses_post( isset( $ticket->content ) ? $ticket->content : '' ); ?></textarea>
									<?php
										if ( $ticket->has_errors( 'content' ) ) {
											foreach ( $ticket->get_errors( 'content' ) as $error_message ) {
												echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
											}
										}
									?>
									</div>
									<div class="form-group <?php esc_attr_e( $ticket->has_errors( 'attachments' ) ? 'has-error' : '' ); ?>">
										<ul id="ttlc-attachments" class="ttlc__attachments clearfix">
											<li class="hidden ttlc-attachment-template"><span class="ttlc__attachments-icon"><i class="fa fa-file"></i></span>
												<div class="ttlc__attachments-info">
													<div class="ttlc__attachments-name title"></div>
													<div class="ttlc__attachments-size"><span class="size"></span><a href="#" title="<?php esc_html_e( 'Delete this attachment', TTLC_TEXTDOMAIN ); ?>" class="ttlc__attachments-delete ttlc-ticket-attachment-delete btn btn-xs btn-danger"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', TTLC_TEXTDOMAIN ); ?></a></div>
												</div>
											</li>
											<li class="ticket-attachment-btn">
												<input id="ttlc-ticket-attachment" type="file" name="attachment" multiple="">
												<label for="ttlc-ticket-attachment"><span class="ttlc__attachments-icon"><i class="fa fa-plus"></i></span><span class="ttlc__attachments-info"><span><?php esc_html_e( 'Add Attachment', TTLC_TEXTDOMAIN ); ?></span></span></label>
											</li>
										</ul>

										<p class="help-block"><?php echo esc_html( sprintf('Max size: %d MB', get_option( 'ttlc_attachments_size', 5 ) ) ); ?></p>
									<?php
										if ( $ticket->has_errors( 'attachments' ) ) {
											foreach ( $ticket->get_errors( 'attachments' ) as $error_message ) {
												echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
											}
										}
									?>
										<hr>
									</div>
									<div class="form-group <?php esc_attr_e( $ticket->has_errors( '_global' ) ? 'has-error' : '' ); ?>">
									<?php
										if ( $ticket->has_errors( '_global' ) ) {
											echo '<div class="pull-left">';
											foreach ( $ticket->get_errors( '_global' ) as $error_message ) {
												echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
											}
											echo '</div>';
										}
									?>
										<div class="text-right">
											<button type="submit" class="btn btn-dark"><?php $ticket instanceof TTLC_Ticket_Response ? esc_html_e( 'Send Response', TTLC_TEXTDOMAIN ) : esc_html_e( 'Send Ticket', TTLC_TEXTDOMAIN ); ?></button>
										</div>
									</div>
								</form>
