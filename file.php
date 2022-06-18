<?php

$file = $argv[ 1 ];

try {
	if ( !file_exists( $file ) ) {
		throw new Error( "File $file does not exist!" );
	}
	if ( !is_readable( $file ) ) {
		throw new Error( "File $file is not readable!" );
	}
} catch ( Error $e ) {
	die( 'Error: ' . $e->getMessage() . "\n" );
}

include_once 'errors.php';
include_once 'tokens.php';
include_once 'lexer.php';

try {
	$lex = new Lexer( $file, true );
	print_r( $lex->tokens ) . "\n";
} catch ( LexError $lex_error ) {
	dump_error( 'LexError', $lex_error );
	die;
}