<?php defined( 'ABSPATH' ) or die(); ?>

    <table class="form-table lana-downloads-manager">
        <tr>
            <th>
                <label for="upload-file-url">
					<?php _e( 'File (URL):', 'lana-downloads-manager' ); ?>
                </label>
            </th>
            <td>
                <input type="text" name="lana_download_file_url" id="upload-file-url" class="upload-file-url"
                       value="<?php echo esc_attr( get_post_meta( $post->ID, 'lana_download_file_url', true ) ); ?>"/>
                <input type="hidden" name="lana_download_file_id" id="upload-file-id" class="upload-file-id"
                       value="<?php echo esc_attr( get_post_meta( $post->ID, 'lana_download_file_id', true ) ); ?>"/>
            </td>
            <td>
                <a href="#" class="button upload-file-button"
                   data-dialog-title="<?php esc_attr_e( 'Choose a file', 'lana-downloads-manager' ); ?>"
                   data-dialog-button="<?php esc_attr_e( 'Insert file URL', 'lana-downloads-manager' ); ?>">
					<?php _e( 'Upload File', 'lana-downloads-manager' ); ?>
                </a>
            </td>
        </tr>
    </table>

<?php wp_nonce_field( 'save', 'lana_downloads_manager_nonce_field' ); ?>