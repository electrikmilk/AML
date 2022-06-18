<?php

include_once 'errors.php';
include_once 'tokens.php';

function get_input()
{
	$stdin = fopen( 'php://stdin', 'rb' );
	do {
		$line = fgets( $stdin );
	} while ( $line === '' );
	fclose( $stdin );
	return trim( $line );
}

while ( true ) {
	echo '> ';
	$input = get_input();
	if ( $input === 'exit' ) {
		die;
	}
	echo "\n";
}