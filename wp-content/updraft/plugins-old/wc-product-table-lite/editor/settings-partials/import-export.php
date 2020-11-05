<style>
  .wcpt-import-export-wrapper {
    margin: 20px 0;
  }

  .wcpt-import-export-button {
    padding: 16px 22px;
    background: #fff;
    border: 1px solid #bbb;
    border-radius: 4px;
    display: inline-block;
    font-size: 18px;
    margin-right: 10px;
    transition: .2s background-color;
  }

  .wcpt-import-icon svg, 
  .wcpt-export-icon svg {
    height: .9em;
    stroke-width: 2.5px;
    vertical-align: baseline;
    position: relative;
    top: 1px;
  }

  .wcpt-import-modal {
    display: none;  
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, .5);
    z-index: 10000;
  }

  .wcpt-import-modal > form {
    background: white;
    width: 300px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 40px;
    font-size: 16px; 
    line-height: 1.5em;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
  }

  .wcpt-import-modal > form:after {
    content: '';
    position: absolute;
    left: 10px;
    top: 10px;
    border: 2px solid #f7f7f7;
    width: calc(100% - 20px) ;
    height: calc(100% - 20px) ;
    border-radius: inherit;
    box-sizing: border-box;
    pointer-events: none;
  }

  .wcpt-import-modal > form > h2 {
    font-size: 20px; 
    font-weight: bold;
    margin: .25em 0 1em;
  }

  .wcpt-import-modal > form > ol {
    padding-left: 1em;
    margin: 1em 0;
  }

  .wcpt-show-import-modal > .wcpt-import-modal {
    display: block;
  }

  .wcpt-show-import-modal input[type="file"] {
    margin: 4px 0 8px;
    border: 2px solid rgba(0, 0, 0, 0.04);
    border-width: 2px 0px;
    padding: 16px 0;
  }

  .wcpt-show-import-modal input[type="submit"] {
    margin-top: 14px;
    font-size: 16px;
    padding: 10px 25px;
  }

  .wcpt-pro .wcpt-import-export-button {
    cursor: pointer;
    box-shadow: 1px 1px 1px rgba(0, 0, 0, .04);
  }

  .wcpt-pro .wcpt-import-export-button:hover {
    background: #f9f9f9;
    box-shadow: 0px 0px 0px rgba(0, 0, 0, .06);
  }

  .wcpt-import-export-button .wcpt-pro-badge {
    border-radius: 3px;
    font-size: 12px;
    background: #EF5350;
    color: white;
    padding: 4px 8px;
    margin-left: .75em;
    vertical-align: middle;
  }
</style>

<?php 
  if( empty( $wcpt_import_export_button_label_append ) ){
    $wcpt_import_export_button_label_append = 'settings';
  }

  if( empty( $wcpt_import_export_button_context ) ){
    $wcpt_import_export_button_context = 'settings';
  }
?>
<div class="wcpt-import-export-wrapper ">
		<span class="wcpt-import-button wcpt-import-export-button">
      <?php wcpt_icon('download', 'wcpt-import-icon'); ?>
      Import 
      <?php echo $wcpt_import_export_button_label_append; ?>
      <?php wcpt_pro_badge(); ?>
    </span>
		<span class="wcpt-export-button wcpt-import-export-button">
      <?php wcpt_icon('upload', 'wcpt-export-icon'); ?>
      Export 
      <?php echo $wcpt_import_export_button_label_append; ?>
      <?php wcpt_pro_badge(); ?>
    </span>
		<div class="wcpt-import-modal">
			<form method="POST" enctype="multipart/form-data">

        <h2>Import <?php echo $wcpt_import_export_button_label_append; ?></h2>

        <span>Please follow this sequence:</span>
        <ol>
          <li>Backup site database</li>
          <li>Import WooCommerce products</li>
          <li>Import the product tables</li>
          <li>Import the table settings</li>
        </ol>

				<input type="file" name="wcpt_import_file">
				<br>
				<input type="submit" class="wcpt-import-export-button" />

				<input type="hidden" name="wcpt_import_export_nonce" value="<?php echo wp_create_nonce( 'wcpt_import_export' ); ?>" />
				<input type="hidden" name="wcpt_context" value="<?php echo $wcpt_import_export_button_context; ?>" />
				<input type="hidden" name="wcpt_action" />
				<input type="hidden" name="wcpt_export_id" />
			</form>
		</div>
	</div>

  <?php if( defined('WCPT_PRO') ): ?>

    <script>
      (function($){
        $(function(){

          // import
          $('body').on('click', '.wcpt-import-button', function(){
            var $this = $(this),
                $wrapper = $this.parent();
            $wrapper.addClass('wcpt-show-import-modal');
            $('input[name="wcpt_action"]', $wrapper).val('import');
          })

          $('body').on('click', '.wcpt-import-modal', function(e){
            if(e.target === this){
              var cl = 'wcpt-show-import-modal';
              $(this).closest('.' + cl).removeClass(cl);
            }
          })

          // export
          $('body').on('click', '.wcpt-export-button', function(){
            var $this = $(this),
                $wrapper = $this.parent();
            $('input[name="wcpt_action"]', $wrapper).val('export');
            $wrapper.find('form').submit();
          })
        })
      })(jQuery)
    </script>
  <?php endif; ?>