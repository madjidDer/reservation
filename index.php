<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	// Redirect default XAMPP landing page to the vacation-booking project
	header('Location: '.$uri.'/vacation-booking/');
	exit;
?>
