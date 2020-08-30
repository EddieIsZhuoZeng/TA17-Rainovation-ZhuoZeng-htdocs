<?php
 $locales = array(
  'English' =>'en',
  'العربية' =>'ar',
  'Български' =>'bg',
  'Català' =>'ca',
  'Čeština' =>'cs',
  'Dansk' =>'da',
  'Deutsch' =>'de',
  'Ελληνικά' =>'el',
  'Español' =>'es',
  'فارسی' =>'fa',
  'Føroyskt' =>'fo',
  'Français' =>'fr',
  'Français (Canada)' =>'fr_CA',
  'עברית' =>'he',
  'Hrvatski' =>'hr',
  'Magyar' =>'hu',
  'Bahasa Indonesia' =>'id',
  'Italiano' =>'it',
  '日本語' =>'ja',
  '한국어' =>'ko',
  'Nederlands' => 'nl',
  'Norsk' => 'no',
  'Polski' =>'pl',
  'Português' =>'pt_BR',
  'Română' =>'ro',
  'Pусский' =>'ru',
  'සිංහල' =>'si',
  'Slovenčina' =>'sk',
  'Slovenščina' =>'sl',
  'Srpski' =>'sr',
  'Svenska' =>'sv',
  'Türkçe' =>'tr',
  'ئۇيغۇرچە' =>'ug_CN',
  'Український' =>'uk',
  'Tiếng Việt' =>'vi',
  '简体中文' =>'zh_CN',
  '正體中文' =>'zh_TW',
);
?>

<select name="fm_locale" id="fm_locale" class="njt-fs-settting-width-half">
  <?php foreach($locales as $key => $locale) { ?>
  <option value="<?php echo $locale;?>"
    <?php echo (isset($this->options['njt_fs_file_manager_settings']['fm_locale']) && $this->options['njt_fs_file_manager_settings']['fm_locale'] == $locale) ? 'selected="selected"' : '';?>>
    <?php echo $key;?></option>
  <?php } ?>
</select>