


<div id="package-icons" class="tab-pane">
    <div class="w3eden"><input style="background: url(<?php echo esc_url(get_post_meta($post->ID,'__wpdm_icon', true)); ?>) no-repeat;background-size: 24px;padding-left: 40px;background-position:8px center;" id="wpdmiconurl" placeholder="<?php _e( "Icon URL" , "download-manager" ); ?>" value="<?php echo esc_url(get_post_meta($post->ID,'__wpdm_icon', true)); ?>" type="text"  name="file[icon]"  class="form-control input-lg" ></div>
    <br clear="all" />
    <?php
    $path = WPDM_BASE_DIR."assets/file-type-icons/";
    $_upload_dir = wp_upload_dir();
    $_upload_basedir = $_upload_dir['basedir'];
    $c_path = $_upload_basedir.'/wpdm-file-type-icons/';
    $c_url = $_upload_dir['baseurl'].'/wpdm-file-type-icons/';
    $scan = scandir( $path );
    $k = 0;
    $fileinfo = array();
    foreach( $scan as $v )
    {
        if( $v=='.' or $v=='..' or is_dir($path.$v) ) continue;

        $fileinfo[$k]['file'] = 'download-manager/assets/file-type-icons/'.$v;
        $fileinfo[$k]['name'] = $v;
        $k++;
    }

    if(file_exists($c_path)) {
        $c_scan = scandir( $c_path );
        if(is_array($c_scan)) {
            foreach ($c_scan as $v) {
                if ($v == '.' or $v == '..' or is_dir($path . $v)) continue;

                $fileinfo[$k]['file'] = $c_url . $v;
                $fileinfo[$k]['name'] = $v;
                $k++;
            }
        }
    }



    ?>
    <div id="w-icons">
        <img  id="icon-loading" src="<?php  echo plugins_url('download-manager/assets/images/loading.gif'); ?>" style=";display:none;padding:5px; margin:1px; float:left; border:#fff 2px solid;height: 32px;width:auto; " />
        <?php
        $img = array('jpg','gif','jpeg','png', 'svg');
        foreach($fileinfo as $index=>$value): $tmpvar = explode(".",$value['file']); $ext = strtolower(end($tmpvar)); if(in_array($ext,$img)): ?>
            <label>
                <img class="wdmiconfile" id="<?php echo !strstr($value['file'], '://')?md5(plugins_url().'/'.$value['file']):md5($value['file']); ?>" src="<?php  echo !strstr($value['file'], '://')?plugins_url().'/'.esc_attr($value['file']):esc_url($value['file']); ?>" alt="<?php echo $value['name'] ?>" style="padding:5px; margin:1px; float:left; border:#fff 2px solid;height: 32px;width:auto; " />
            </label>
        <?php endif; endforeach; ?>
    </div>
    <script type="text/javascript">
        //border:#CCCCCC 2px solid

        <?php if(isset($_GET['action'])&&$_GET['action']=='edit'){ ?>
        jQuery('#<?php echo md5(get_post_meta($post->ID,'__wpdm_icon', true)) ?>').addClass("iactive");
        <?php } ?>
        jQuery('body').on('click', 'img.wdmiconfile',function(){
            jQuery('#wpdmiconurl').val(jQuery(this).attr('src'));
            jQuery('#wpdmiconurl').css('background-image','url('+jQuery(this).attr('src')+')');
            jQuery('img.wdmiconfile').removeClass('iactive');
            jQuery(this).addClass('iactive');



        });
        jQuery('#wpdmiconurl').on('change', function(){
            jQuery('#wpdmiconurl').css('background-image','url('+jQuery(this).val()+')');
        });




    </script>
    <style>

        .iactive{
            -moz-box-shadow:    inset 0 0 10px #5FAC4F;
            -webkit-box-shadow: inset 0 0 10px #5FAC4F;
            box-shadow:         inset 0 0 10px #5FAC4F;
            background: #D9FCD1;
        }
    </style>

    <div class="clear"></div>
</div>

