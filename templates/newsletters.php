<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	$paged = isset( $_GET['page_num'] ) && is_numeric( $_GET['page_num'] ) ? (int) sanitize_key( $_GET['page_num'] ) : 1;
	$status = isset( $_GET['filter'] ) && array_key_exists( $_GET['filter'], TTLC_Newsletter::statuses() ) ? sanitize_text_field( $_GET['filter'] ) : false;
	$meta_query = array();
	if ( $status ) {
		$meta_query[] = array(
			'key' => TTLC_Newsletter::PREFIX . 'status',
			'value' => $status,
		);
	}

	$newsletters = TTLC_Newsletter::find( array(
		'paged' => $paged,
		'meta_query' => $meta_query,
	) );
	
	$newsletters_list = $newsletters['items'];	

	TTLC_Breadcrumbs::add_link( esc_html__( 'Ticketrilla: Client', TTLC_TEXTDOMAIN ), TTLC_Page::get_url( 'main' ) );
	TTLC_Breadcrumbs::add_head( __( 'Newsletters', TTLC_TEXTDOMAIN ) );
	$this->render_template('header');

?>

	<div class="ttlc__header-title">
		<h1><?php esc_html_e( 'Newsletters', TTLC_TEXTDOMAIN ); ?></h1><a href="<?php echo esc_url( TTLC_Page::get_url( 'settings' ) . '#ttlc-newsletters-settings' ); ?>" class="btn btn-info"><?php esc_html_e( 'Settings', TTLC_TEXTDOMAIN ); ?></a>
	</div>
<?php
	
	$filter_args = array();
	foreach ( TTLC_Newsletter::statuses() as $status_key => $status_label ) {
		$filter_args[$status_key] = array(
			'label' => $status_label,
		);
	}	
	
	$this->render_template( 'filter', $filter_args );

?>
</div>

<div class="ttlc__content">
	<div class="ttlc__tickets">
		<div class="ttlc__newsletters-inner">
		<?php
			if ( empty( $newsletters_list ) ) {

				esc_html_e( 'No Newsletters Found', TTLC_TEXTDOMAIN );
				
			} else {

				$table = new TTLC_Table_Helper(
					'newsletters-table',
					array(
						'status' => array(
							'label' => esc_html__( 'Status', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								switch ( $data->status ) {
									case 'read': $label = 'warning'; break;
									case 'new': $label = 'success'; break;
									default: $label = 'info';
								}
								$statuses = $data->statuses();
								return '<span class="btn btn-block btn-xs btn-' . $label . '">' . esc_html( strtolower( $statuses[$data->status] ) ) . '</span>';
							}
						),
						'product' => array(
							'label' => esc_html__( 'Product', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								return $data->product;
							}
						),
						'title' => array(
							'label' => esc_html__( 'Title', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								$modal_id = 'ttlc-modal-newsletter-' . esc_attr( $data->id );
								
								return '<a class="newsletter-modal-link" href="#" title="' . esc_attr( $data->title ) . '" data-target="#' . $modal_id . '">#' . esc_html( $data->external_id ) . '. ' . esc_html( $data->title ) . '</a>';
							}
						),
						'date' => array(
							'label' => esc_html__( 'Date', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								return get_date_from_gmt( $data->date, 'd F Y, H:i' );
							}
						),
					),
					$newsletters_list
				);
				$table->render( 'table table-striped' );
				
				// Modals
				
				foreach ( $newsletters_list as $newsletter ) {
					$this->render_template( 'newsletter-modal', array('modal_id' => 'ttlc-modal-newsletter-' . esc_attr( $newsletter->id ), 'newsletter' => $newsletter ) );
				}
			}
		?>
		</div>
		<?php
			
			if ( ! empty( $newsletters_list ) ) {
				
				$pagination = new TTLC_Pagination( $newsletters['total'] );
				$pagination->render();
				
			}
		?>
	</div>
</div>
<?php
	$this->render_template('footer');
?>
