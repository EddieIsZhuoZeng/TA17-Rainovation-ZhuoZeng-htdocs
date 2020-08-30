<?php
/**
 * Author: shahnuralam
 * Date: 2018-12-28
 * Time: 22:46
 */
if (!defined('ABSPATH')) die();
?>
<script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'></script>
<div  id="reCaptchaLock_<?php echo $package['ID']; ?>"></div>
<div id="msg_<?php echo $package['ID']; ?>"></div>
<script type="text/javascript">
    var ctz = new Date().getMilliseconds();
    var siteurl = "<?php echo home_url('/?__wpdmnocache='); ?>"+ctz,force="<?php echo $force; ?>";
    var verifyCallback_<?php echo $package['ID']; ?> = function(response) {
        jQuery.post(siteurl,{__wpdm_ID:<?php echo $package['ID'];?>,dataType:'json',execute:'wpdm_getlink',force:force,social:'c',reCaptchaVerify:response,action:'wpdm_ajax_call'},function(res){
            if(res.downloadurl!='' && res.downloadurl != undefined && res!= undefined ) {

                if(window.parent == undefined)
                    location.href = res.downloadurl;
                else
                    window.parent.location.href = res.downloadurl;

                jQuery('#reCaptchaLock_<?php echo $package['ID']; ?>').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn <?php echo wpdm_download_button_style(true, $package['ID']); ?>"><?php _e( "Download" , "download-manager" ); ?></a>');
            } else {
                jQuery('#msg_<?php echo $package['ID']; ?>').html(''+res.error);
            }
        });
    };
    var widgetId2;
    var onloadCallback = function() {
        grecaptcha.render('reCaptchaLock_<?php echo $package['ID']; ?>', {
            'sitekey' : '<?php echo get_option('_wpdm_recaptcha_site_key'); ?>',
            'callback' : verifyCallback_<?php echo $package['ID']; ?>,
            'theme' : 'light'
        });
    };
</script>

