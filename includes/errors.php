<?php
/**
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/18/2022 21:56
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
	echo style( $error->getMessage(), RED, BOLD ) . "\n\n";
	echo style( $error->getTraceAsString(), YELLOW ) . "\n";
}

class AMLError extends Exception
{
	public function __construct( $instance, $message )
	{
		global $file;
		++$instance->pos;
		$message = "($instance->line:$instance->pos) $message";
		if ( $file ) {
			$message = "$file $message";
		}
		parent::__construct( $message );
	}
}