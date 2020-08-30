<?php
$listOperations = array(
  'mkdir'=>'mkdir',
  'mkfile'=>'mkfile',
  'rename'=>'rename',
  'duplicate'=>'duplicate',
  'paste'=>'paste',
  'archive'=>'archive',
  'extract'=>'extract',
  'copy'=>'copy',
  'cut'=>'cut',
  'edit'=>'edit',
  'rm'=>'rm',
  'download'=>'download',
  'upload'=>'upload',
  'search'=>'search'
)
?>

<?php foreach($listOperations as $key => $listOperation) { ?>
<span class="list-col4-item">
  <input type="checkbox" class="fm-list-user-restrictions-item" id="<?php echo $key; ?>" name="<?php echo $key; ?>"
    value="<?php echo $key; ?>">
  <label for="vehicle1"><?php echo $listOperation; ?></label>
</span>
<?php } ?>