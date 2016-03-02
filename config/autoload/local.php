<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

$env = getenv('API_ENV') ?: 'production';
return array(
	'db' => array(
		'username'	=> 	'root',
		'password' 	=>	'',
    ),
	'view_manager' => array(
		'display_exceptions' 		=> true,
		'display_errors'			=> true,
		'display_startup_errors'	=> true
	),
);
