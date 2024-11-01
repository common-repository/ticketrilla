<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	TTLC_Breadcrumbs::add_link( esc_html__( 'Ticketrilla: Client', TTLC_TEXTDOMAIN ), TTLC_Page::get_url( 'main' ) );
	$title = __( 'Settings', TTLC_TEXTDOMAIN );
	TTLC_Breadcrumbs::add_head( $title );
	$this->render_template( 'header' );

?>
				<div class="ttlc__header-title">
					<h1><?php echo esc_html( $title ); ?></h1>
				</div>
			</div>
			<div class="ttlc__content">
				<div class="ttlc__settings">
					<div class="row">
						<div class="col-md-12">
						<?php
							$attachments = new TTLC_Settings_Attachments;
							$attachments->load();							
							$this->render_template( 'settings-attachments-form', array('attachments' => $attachments) );
						?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						<?php 
							$autologin = new TTLC_Settings_Autologin;
							$autologin->load();
							$this->render_template( 'settings-autologin-form', array('autologin' => $autologin) );
						?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						<?php
							$updates = new TTLC_Settings_Updates;
							$updates->load();
							$this->render_template( 'settings-updates-form', array('updates' => $updates) );
						?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						<?php
							$newsletters = new TTLC_Settings_Newsletters;
							$newsletters->load();
							$this->render_template( 'settings-newsletters-form', array('newsletters' => $newsletters) );
						?>
						</div>
					</div>
				</div>
			</div>

<?php $this->render_template( 'footer' ); ?>
