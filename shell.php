<?php

include_once 'errors.php';
include_once 'tokens.php';
include_once 'lexer.php';

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
	try {
		$lexer = new Lexer( $input );
		print_r( $lexer->tokens );
	} catch ( LexError $e ) {
		dump_error( 'LexError', $e );
	}
	echo "\n";
}