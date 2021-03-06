<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<?php
/**
* Template file for the plain HTML table
* wpDataTables Module
* 
* @author cjbug@ya.ru
* @since 10.10.2012
*
**/
?>
	<?php  ?>
<?php do_action('wpdatatables_before_table', $wpDataTable->getWpId()); ?>
<input type="hidden" id="<?php echo $wpDataTable->getId() ?>_desc" value='<?php echo $wpDataTable->getJsonDescription(); ?>' />
<table id="<?php echo $wpDataTable->getId() ?>" class="<?php if ($wpDataTable->isScrollable()) { ?>scroll<?php } ?> display responsive nowrap <?php echo $wpDataTable->getCSSClasses() ?> wpDataTable" style="<?php echo $wpDataTable->getCSSStyle() ?>" data-described-by='<?php echo $wpDataTable->getId() ?>_desc' data-wpdatatable_id="<?php echo $wpDataTable->getWpId(); ?>">
    <thead>
	<?php  ?>
	<tr>
            <?php do_action('wpdatatables_before_header', $wpDataTable->getWpId()); ?>
            <?php $expandShown = false; ?>
	    <?php foreach($wpDataTable->getColumns() as $dataColumn) { ?><th <?php if(!$expandShown && $dataColumn->isVisibleOnMobiles()){ ?>data-class="expand"<?php $expandShown = true; } ?> <?php if($dataColumn->getHiddenAttr()) { ?>data-hide="<?php echo $dataColumn->getHiddenAttr() ?>"<?php } ?> class="header <?php if( $dataColumn->sortEnabled() ) { ?>sort<?php } ?> <?php echo $dataColumn->getCSSClasses(); ?>" style="<?php echo $dataColumn->getCSSStyle(); ?>"><?php echo ( $dataColumn->getFilterType()->type != 'null') ? $dataColumn->getTitle() : '' ?></th><?php } ?>
            <?php do_action('wpdatatables_after_header', $wpDataTable->getWpId()); ?>
	</tr>
		<?php  ?>
    </thead>
    <tbody>
    <?php do_action('wpdatatables_before_first_row', $wpDataTable->getWpId()); ?>
	<?php foreach( $wpDataTable->getDataRows() as $wdtRowIndex => $wdtRowDataArr) { ?>
	<?php do_action('wpdatatables_before_row', $wpDataTable->getWpId(), $wdtRowIndex); ?>
	<tr id="table_<?php echo $wpDataTable->getWpId() ?>_row_<?php echo $wdtRowIndex; ?>">
	    <?php foreach( $wpDataTable->getColumnsByHeaders() as $dataColumnHeader => $dataColumn ) { ?>
			<td style="<?php echo $dataColumn->getCSSStyle();?>"><?php echo apply_filters( 'wpdatatables_filter_cell_output', $wpDataTable->returnCellValue( $wdtRowDataArr[ $dataColumnHeader ], $dataColumnHeader ), $wpDataTable->getWpId(), $dataColumnHeader ); ?></td>
			<?php  ?>
	    <?php } ?>
	</tr>
	<?php do_action('wpdatatables_after_row', $wpDataTable->getWpId(), $wdtRowIndex); ?>
	<?php } ?>
	<?php do_action('wpdatatables_after_last_row', $wpDataTable->getWpId()); ?>
    </tbody>
	<?php  ?>
    
</table>
<?php if ( get_option( 'wdtSiteLink' ) ) { ?><span class="powered_by_link d-block m-l-10 m-t-10 m-b-10">Generated by <a href="https://wpdatatables.com" target="_blank">wpDataTables</a></span><?php } ?>
<?php do_action('wpdatatables_after_table', $wpDataTable->getWpId()); ?>

<?php  ?>
