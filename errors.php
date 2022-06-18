<?php

function shell_banner( string $str )
{
	$str = "[$str]";
	$width = (int)shell_exec( 'tput cols' );
	$half = $width - strlen( $str );
	$line = str_repeat( "=", (int)( $half / 2 ) );
	return "$line$str$line\n";
}

function dump_error( string $type, AMLError $error )
{
	echo shell_banner( $type );
	echo $error->getMessage() . "\n\n";
	echo $error->getTraceAsString() . "\n";
}

class AMLError extends Exception
{
	public function __construct( $instance, $message )
	{
		global $file;
		++$instance->pos;
		$message = "$file ($instance->line:$instance->pos) $message";
		parent::__construct( $message );
	}
}