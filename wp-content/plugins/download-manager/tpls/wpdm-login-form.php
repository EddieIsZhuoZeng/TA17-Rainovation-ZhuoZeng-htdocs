<?php use WPDM\Session;

if(!defined('ABSPATH')) die();

?>
<div class="w3eden">
    <div id="wpdmlogin" <?php if(wpdm_query_var('action') == 'lostpassword') echo 'class="lostpass"'; ?>>
        <?php if(isset($params['logo']) && $params['logo'] != '' && !is_user_logged_in()){ ?>
            <div class="text-center wpdmlogin-logo">
                <a href="<?php echo home_url('/'); ?>"><img alt="Logo" src="<?php echo $params['logo'];?>" /></a>
            </div>
        <?php } ?>






        <?php do_action("wpdm_before_login_form"); ?>


        <form name="loginform" id="loginform" action="" method="post" class="login-form" >

            <input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />

            <div id="__signin_msg"><?php
                $wpdm_signup_success = Session::get('__wpdm_signup_success');
                if(trim($wpdm_signup_success) !== '' && isset($_GET['signedup'])){
                    ?>
                    <div class="alert alert-success dismis-on-click">
                        <?php echo $wpdm_signup_success; ?>
                    </div>
                    <?php
                }
                ?></div>


            <?php  if(isset($params['note_before'])) {  ?>
                <div class="alert alert-info alert-note-before mb-3" >
                    <?php echo $params['note_before']; ?>
                </div>
            <?php } ?>

            <?php echo \WPDM\libs\User::signinForm($params); ?>


            <?php  if(isset($params['note_after'])) {  ?>
                <div class="alert alert-info alter-note-after mb-3" >
                    <?php echo $params['note_after']; ?>
                </div>
            <?php } ?>

            <?php do_action("wpdm_login_form"); ?>
            <?php do_action("login_form"); ?>

            <div class="row login-form-meta-text text-muted mb-3" style="font-size: 10px">
                <div class="col-5"><label><input class="wpdm-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /><?php _e( "Remember Me" , "download-manager" ); ?></label></div>
                <div class="col-7 text-right"><a class="color-blue" href="<?php echo wpdm_lostpassword_url(); ?>"><?php _e( "Forgot Password?" , "download-manager" ); ?></a></div>
            </div>

            <div class="row">
                <div class="col-md-12"><button type="submit" name="wp-submit" id="loginform-submit" class="btn btn-block btn-primary btn-lg"><i class="fas fa-user-shield"></i> &nbsp;<?php _e( "Login" , "download-manager" ); ?></button></div>
                <div class="col-md-12">
                    <?php
                    $__wpdm_social_login = get_option('__wpdm_social_login');
                    $__wpdm_social_login = is_array($__wpdm_social_login)?$__wpdm_social_login:array();
                    if(is_array($__wpdm_social_login) && count($__wpdm_social_login) > 1) { ?>
                        <div class="text-center card card-default" style="margin: 20px 0 0 0">
                            <div class="card-header"  data-toggle="collapse" href="#socialllogin" role="button" aria-expanded="false" aria-controls="socialllogin"><?php _e("Or connect using your social account", "download-manager") ?></div>
                            <div class="collapse" id="socialllogin">
                            <div class="card-body">
                                <?php if(isset($__wpdm_social_login['facebook'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=facebook'); ?>', 'Facebook', 400,400);" class="btn btn-social wpdm-facebook wpdm-facebook-connect"><i class="fab fa-facebook-f"></i></button><?php } ?>
                                <?php if(isset($__wpdm_social_login['twitter'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=twitter'); ?>', 'Twitter', 400,400);" class="btn btn-social wpdm-twitter wpdm-linkedin-connect"><i class="fab fa-twitter"></i></button><?php } ?>
                                <?php if(isset($__wpdm_social_login['linkedin'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=linkedin'); ?>', 'LinkedIn', 400,400);" class="btn btn-social wpdm-linkedin wpdm-twitter-connect"><i class="fab fa-linkedin-in"></i></button><?php } ?>
                                <?php if(isset($__wpdm_social_login['google'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=google'); ?>', 'Google', 400,400);" class="btn btn-social wpdm-google-plus wpdm-google-connect"><i class="fab fa-google"></i></button><?php } ?>
                            </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if(isset($regurl) && $regurl != ''){ ?>
                    <div class="col-md-12"><br/><a href="<?php echo $regurl; ?>" name="wp-submit" class="btn btn-block btn-link btn-xs wpdm-reg-link  color-primary"><?php _e( "Don't have an account yet?" , "download-manager" ); ?> <i class="fas fa-user-plus"></i> <?php _e( "Register Now" , "download-manager" ); ?></a></div>
                <?php } ?>
            </div>


            <input type="hidden" name="redirect_to" value="<?php echo $log_redirect; ?>" />



        </form>



        <?php do_action("wpdm_after_login_form"); ?>

    </div>


</div>

<script>
    jQuery(function ($) {
        var llbl = $('#loginform-submit').html();
        $('#loginform').submit(function () {
            $('#loginform-submit').html("<i class='fa fa-spin fa-sync'></i> <?php _e( "Logging In..." , "download-manager" ); ?>").attr('disabled', 'disabled');
            WPDM.blockUI('#loginform');
            $(this).ajaxSubmit({
                success: function (res) {
                    WPDM.unblockUI('#loginform');
                    if (!res.success) {
                        $('form .alert-danger').hide();
                        $('#loginform').prepend("<div class='alert alert-danger' data-title='<?php _e( "LOGIN FAILED!" , "download-manager" ); ?>'>"+res.message+"</div>");
                        $('#loginform-submit').html(llbl).removeAttr('disabled');
                        <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                        try {
                            grecaptcha.reset();
                        } catch (e) {

                        }
                        <?php } ?>
                    } else {
                        $('#loginform-submit').html(wpdm_asset.spinner+" "+res.message);
                        location.href = "<?php echo $log_redirect; ?>";
                    }
                }
            });
            return false;
        });

        $('body').on('click', 'form .alert-danger', function(){
            $(this).slideUp();
        });

    });
</script>
