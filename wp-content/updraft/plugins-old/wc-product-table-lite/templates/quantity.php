<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( 
	! empty( $hide_if_sold_individually ) &&
	$product->is_sold_individually()
){
	return;
}

$args = apply_filters( 'woocommerce_quantity_input_args', array(
	'input_id'     => uniqid( 'quantity_' ),
	'input_name'   => 'quantity',
	'input_value'  => '1',
	'max_value'    => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
	'min_value'    => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
	'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
	'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
	'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
	'product_name' => $product ? $product->get_title() : '',
), $product );

// Apply sanity to min/max args - min cannot be lower than 0.
$args['min_value'] = max( $args['min_value'], 0 );
$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

// Max cannot be lower than min if defined.
if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
	$args['max_value'] = $args['min_value'];
}

extract( $args );

$controls_html_classes = '';

if( empty( $display_type ) ){
	$display_type = 'input';
}

if( empty( $controls ) ){
	$controls = 'browser';
}

if( 
	in_array( $controls, array('none', 'browser') ) ||
	'select' === $display_type
){
	$controls_html_classes .= 'wcpt-hide-controls';
}else{
	$controls_html_classes .= ' wcpt-controls-on-' . $controls . ' ';
}

if( $controls !== 'browser' ){
	$controls_html_classes .= ' wcpt-hide-browser-controls ';		
}

if( empty( $qty_label ) ){
	$qty_label = '';
}

if( empty( $max_qty ) ){
	$max_qty = 10;
}

?>
<div class="quantity wcpt-quantity-wrapper wcpt-noselect wcpt-display-type-<?php echo $display_type ?> <?php echo $controls_html_classes; ?> <?php echo $html_class; ?>">
	<?php if( $display_type === 'input' ): ?>
		<span class="wcpt-minus wcpt-qty-controller wcpt-noselect"></span
		><input 
			type="number" 
			id="<?php echo esc_attr( $input_id ); ?>" 
			class="input-text qty text" 
			<?php if( $product->get_sold_individually() ) echo 'disabled'; ?> 
			step="<?php echo esc_attr( $step ); ?>" 
			min="<?php echo esc_attr( $min_value ); ?>" 
			max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" 
			name="<?php echo esc_attr( $input_name ); ?>" 
			value="<?php echo esc_attr( $input_value ); ?>" 
			title="<?php echo esc_attr_x( 'Quantity', 'Product quantity input tooltip', 'woocommerce' ) ?>" 
			size="4" 
			pattern="<?php echo esc_attr( $pattern ); ?>" 
			inputmode="<?php echo esc_attr( $inputmode ); ?>" 
			aria-labelledby="<?php echo ! empty( $args['product_name'] ) ? sprintf( esc_attr__( '%s quantity', 'woocommerce' ), $args['product_name'] ) : ''; ?>" 
			autocomplete="off"
		/><span class="wcpt-plus wcpt-qty-controller wcpt-noselect"></span>
	<?php else: ?>
		<select 
			class="wcpt-qty-select" 
			data-wcpt-qty-label="<?php echo esc_attr($qty_label); ?>"
			data-wcpt-max-qty="<?php echo $max_qty; ?>"
			min="<?php echo $min_value; ?>"
		>
			<option value="<?php echo $min_value; ?>"><?php echo esc_html($qty_label) . $min_value; ?></option>
			<?php
				$val = $min_value;
				if( ! empty( $max_value ) ){
					$max_qty = $max_value;
				}
				while( $val < $max_qty ){
					$val += $step;
					echo '<option value="'. $val .'">'. $val .'</option>';
				}
			?>
		</select>
	<?php endif; ?>
</div>
