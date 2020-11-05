<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Cell Template Editor</title>
    <link rel="stylesheet" href="tabs.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="tabs.js"></script>
  </head>
  <body>
    <?php include ('tabs.php'); ?>
    <script type="text/javascript">
      jQuery(function($){
        $('.wcpt-tabs').wcpt_tabs()
        .on('tab_enabled tab_disabled', function(e, index){
        });
      })
    </script>
  </body>

</html>
