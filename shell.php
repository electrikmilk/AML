<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/19/2022 18:10
 */

// styles
const BOLD = 1;
const DIM = 2;
const INVERTED = 7;
const UNDERLINED = 4;

// color
const RED = 31;
const GREEN = 32;
const YELLOW = 33;
const CYAN = 36;

include_once 'includes/errors.php';
include_once 'includes/tokens.php';

include_once 'includes/lexer.php';
include_once 'includes/parser.php';
include_once 'includes/interpreter.php';

$input = null;

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

function interpret( string $code, bool $file = false ): void
{
	global $input;
	$input = $code;
	try {
		$lex = new Lexer( $code, $file );
	} catch ( AMLError $lex_error ) {
		dump_error( 'LexerError', $lex_error );
	}
	if ( isset( $lex->tokens ) ) {
		try {
			$parse = new Parser( $lex->tokens );
		} catch ( AMLError $parse_error ) {
			dump_error( 'ParserError', $parse_error );
		}
	}
	if ( isset( $parse->tokens ) ) {
		try {
			$interpret = new Interpreter( $parse->tokens );
		} catch ( AMLError $interpret_error ) {
			dump_error( 'InterpreterError', $interpret_error );
		}
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
	echo style( "Enter an equation and press return. Type \"q\" to close.\n", CYAN, BOLD );
	while ( true ) {
		echo style( '> ', GREEN );
		$input = input();
		if ( $input === 'q' ) {
			die;
		}
		interpret( $input );
		echo "\n";
	}
}