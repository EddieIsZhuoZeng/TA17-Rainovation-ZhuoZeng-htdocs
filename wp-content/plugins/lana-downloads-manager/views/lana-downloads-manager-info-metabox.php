<?php defined( 'ABSPATH' ) or die(); ?>

<?php $lana_downloads_manager_counter = get_option( 'lana_downloads_manager_counter', true ); ?>

<table class="form-table lana-downloads-manager-info">
    <tr>
        <th>
			<?php _e( 'Download Identification:', 'lana-downloads-manager' ); ?>
        </th>
        <td>
			<?php echo esc_html( '#' . $post->ID ); ?>
        </td>
    </tr>

	<?php if ( $lana_downloads_manager_counter ): ?>
        <tr>
            <th>
				<?php _e( 'Download Count:', 'lana-downloads-manager' ); ?>
            </th>
            <td>
				<?php echo lana_downloads_manager_get_download_count(); ?>
            </td>
        </tr>
	<?php endif; ?>
</table>