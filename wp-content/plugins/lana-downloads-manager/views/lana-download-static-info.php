<?php defined( 'ABSPATH' ) or die(); ?>

<div class="lana-download-static-info">
    <p>
        <b><?php _e( 'Download Permalink:', 'lana-downloads-manager' ); ?></b>
        <a href="<?php echo esc_url( lana_downloads_manager_get_download_url() ); ?>" target="_blank">
			<?php echo esc_url( lana_downloads_manager_get_download_url() ); ?>
        </a>
    </p>
    <p>
        <b><?php _e( 'Shortcode:', 'lana-downloads-manager' ); ?></b>
        <code><?php echo esc_html( lana_downloads_manager_get_download_shortcode() ); ?></code>
    </p>
</div>