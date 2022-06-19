<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/19/2022 18:13
 */

/**
 * @param string $str
 *
 * @return string
 */
function shell_banner( string $str ): string
{
	$str = style( " $str ", RED, INVERTED );
	$width = (int)shell_exec( 'tput cols' );
	$half = $width - strlen( $str );
	$line = style( str_repeat( "-", (int)( $half / 2 ) ), DIM );
	return "$line$str$line\n";
}

/**
 * @param string $type
 * @param AMLError $error
 *
 * @return void
 */
function dump_error( string $type, AMLError $error ): void
{
	echo shell_banner( $type ) . "\n";
	echo $error->getMessage() . "\n\n";
	echo style( $error->getTraceAsString(), YELLOW ) . "\n";
}

function print_line( int $num, string $line, $col = null )
{
	if ( $col !== null ) {
		$line_chars = str_split( $line );
		$error_column = $line_chars[ $col ];
		$line = str_replace( $error_column, style( $error_column, RED, BOLD, UNDERLINED ), $line );
	}
	++$num;
	return " $num | $line";
}

class AMLError extends Exception
{
	public function __construct( $instance, $message )
	{
		global $file, $input;
		$message = style( 'Error: ' . $message . "\n", RED );
		if ( $file ) {
			$input = file_get_contents( $input );
			--$instance->line;
		} else {
			$instance->line = 0;
		}
		$lines = explode( PHP_EOL, $input );
		$dashes = "\n" . style( str_repeat( '-', 5 ), DIM );
		$message .= $dashes . " $file ($instance->line:$instance->pos)";
		$print_lines = [];
		if ( $instance->line !== 0 ) {
			$print_lines[] = print_line( $instance->line - 1, $lines[ $instance->line - 2 ] );
		}
		$print_lines[] = print_line( $instance->line, $lines[ ( $instance->line ) ], $instance->pos );
		$print_lines[] = str_repeat( " ", $instance->pos + 5 ) . style( '^', RED );
		if ( count( $lines ) > 1 ) {
			$print_lines[] = print_line( $instance->line + 1, $lines[ $instance->line ] );
		}
		foreach ( $print_lines as $line ) {
			$message .= "\n$line";
		}
		$message .= $dashes;
		parent::__construct( $message );
	}
}