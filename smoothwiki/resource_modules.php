<?php // This should be included (via require_once) into $IP/LocalSettings.php
$wgResourceModules['skins.smoothwiki'] = array(
	'dependencies' => array( 'jquery' ),
	'remoteBasePath' => $wgStylePath,
	'localBasePath' => $wgStyleDirectory,
	'remoteExtPath' => 'SmoothWiki',
); ?>
