<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/26/18
 * Time: 12:33 AM
 */


?>
<!DOCTYPE html>
<html style="background: transparent">
<head>
    <title>Download <?php the_title(); ?></title>
    <script>
        var wpdm_home_url = "<?php echo home_url('/'); ?>";
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Mono:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <!--
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/js/front.js"></script>
    -->
    <?php
    \WPDM\libs\Apply::uiColors();
    ?>
    <style>
        body{
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 400;
            font-size: 14px;
            letter-spacing: 0;
        }
        .w3eden .alert:before{
            text-transform: uppercase !important;
        }
        .w3eden .alert{
            letter-spacing: 0 !important;
        }
        .w3eden .card .card-header{
            font-size: 14pt;
        }
        .w3eden .card .card-body{
            font-size: 13pt;
        }
    </style>
</head>
<body class="w3eden">
<div style='display: table;vertical-align: middle;height: 90%;position: absolute;width: 90%;margin-left: 5%;'>
    <div style='text-align: center;height: 100%;display: table-cell;vertical-align: middle'>
        <div style='max-width: 70% !important;display: inline-block;font-size: 13pt'>
            <?php
            echo do_shortcode($msg);
            ?>
        </div></div></div>


</body>
</html>
