<?php
// locate and include partials - nav + cell template + heading content
$partials = array_diff( scandir( __DIR__ . '/partials'), array('..', '.', '.DS_Store') );
foreach( $partials as $partial ){
  if( substr($partial, -4) == '.php' ){
    echo '<script type="text/template" data-wcpt-partial="'. substr( $partial, 0, -4 ) .'">';
    if( ! ( 'add' == substr ( $partial , 0 , 3 ) ) ){
      $elm_name = ucwords( implode( ' ', explode( '_', substr( $partial, 0, -4 ) ) ) );
      echo '<h2>Edit element: \''. $elm_name .'\'</h2>';
    }
    include( 'partials/' . $partial );
    echo '</script>';
  }
}
?>
