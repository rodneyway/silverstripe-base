<?php

if (PHP_SAPI != 'cli') {
	header("HTTP/1.0 404 Not Found");
	exit();
}


function mysite_base() {
	$MYSITE_BASE = realpath(dirname(dirname(__FILE__)));
	return $MYSITE_BASE;
}

function site_base() {
	return dirname(mysite_base());
}

function is_ss4() {
    return file_exists(site_base() . '/vendor/silverstripe/framework') && is_dir(site_base() . '/vendor/silverstripe/framework');
}

function all_releases() {
	static $releases;
	
	if ($releases != null) {
		return $releases;
	}

	$releases = dirname(site_base());
	$releases = glob($releases . '/*');

	if (count($releases)) {
		$realReleases = array();
		foreach ($releases as $releaseName) {
			if (preg_match('/\d{14}$/', $releaseName) && $releaseName != site_base()) {
				$realReleases[] = $releaseName;
			} 
		}
		$releases = $realReleases;
	}

	rsort($releases);

	return $releases;
}

function old_path() {
	static $old_path = null;
	if ($old_path != null) {
		return $old_path;
	}

	$releases = all_releases();
	foreach ($releases as $release) {
		if (file_exists($release . '/DEPLOYED')) {
			$old_path = $release;
			break;
		}
	}
	return $old_path;
}


$cwd = getcwd();

$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require site_base().'/vendor/silverstripe/framework/src/includes/autoload.php';
chdir(site_base().'/vendor/silverstripe/framework');
require_once 'src/Core/Kernel.php';
require_once 'src/Core/CoreKernel.php';

chdir($cwd);

$cmds = array();


