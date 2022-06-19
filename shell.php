<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/19/2022 11:21
 */

// styles
const BOLD = 1;
const DIM = 2;
const INVERTED = 7;

// color
const RED = 31;
const GREEN = 32;
const YELLOW = 33;
const CYAN = 36;

include_once 'includes/errors.php';
include_once 'includes/tokens.php';
include_once 'includes/lexer.php';

function input(): string
{
	$stdin = fopen( 'php://stdin', 'rb' );
	do {
		$line = fgets( $stdin );
	} while ( $line === '' );
	fclose( $stdin );
	return trim( $line );
}

function style( string $line, ...$styles ): string
{
	return "\033[0m\033[" . implode( ';', $styles ) . "m$line\033[0m";
}

function interpret( string $input, bool $file = false ): void
{
	try {
		$lex = new Lexer( $input, $file );
		print_r( $lex->tokens );
	} catch ( AMLError $lex_error ) {
		dump_error( 'LexerError', $lex_error );
	}
}

if ( isset( $argv[ 1 ] ) ) {
	$file = $argv[ 1 ];
	try {
		if ( !file_exists( $file ) ) {
			throw new Error( "File $file does not exist!" );
		}
		if ( !is_readable( $file ) ) {
			throw new Error( "File $file is not readable!" );
		}
		interpret( $file, true );
	} catch ( Error $e ) {
		die( style( 'Error:', RED, BOLD ) . ' ' . $e->getMessage() . "\n" );
	}
} else {
	echo style( "Example: 2 + 2. Use \"exit\" or \"q\" to close.\n", CYAN, BOLD );
	while ( true ) {
		echo style( '> ', GREEN );
		$input = input();
		if ( $input === 'exit' || $input === 'q' ) {
			die;
		}
		interpret( $input );
		echo "\n";
	}
}