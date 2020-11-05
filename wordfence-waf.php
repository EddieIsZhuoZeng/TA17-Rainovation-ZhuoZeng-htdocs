<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists('/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", '/opt/bitnami/apps/wordpress/htdocs/wp-content/wflogs/');
	include_once '/opt/bitnami/apps/wordpress/htdocs/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>