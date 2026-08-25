<?php

require APP . 'libs/ImageThumb.class.php';
if (function_exists("set_time_limit") == 1 and get_cfg_var("safe_mode") == 0) {
	@set_time_limit(0);
}

function my_fix_uri($url)
{
	if (!empty($url)) {
		return (substr($url, -1) == '/') ? $url : $url . '/';
	} else {
		return './';
	}
}
function add_zero_str($str, $len = 0)
{
	$curr_len = strlen($str);
	$k = $len - $curr_len;
	if ($k > 0) {
		$str = str_repeat('0', $k) . $str;
	}
	return $str;
}

if (!empty($HTTP_GET_VARS)) {
	while (list($xxxname, $value) = each($HTTP_GET_VARS)) {
		$$xxxname = $value;
	}
}
if (!empty($HTTP_POST_VARS)) {
	while (list($xxxname, $value) = each($HTTP_POST_VARS)) {
		$$xxxname = $value;
	}
}
