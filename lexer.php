<?php

class Lexer
{
	public int $line = 1;
	public int $pos = -1;

	private array $line_chars;
	private int $line_len = 0;
	public string|null $current_char;

	public array $tokens = [];

	/**
	 * @param string $file
	 *
	 * @throws LexError
	 */
	public function __construct( string $input, bool $file = false )
	{
		if ( $file === true ) {
			$f_point = fopen( $input, 'rb' );
			while ( $buffer = fgets( $f_point ) ) {
				$this->line( $buffer );
			}
		} else {
			$input = explode( "\n", $input );
			foreach ( $input as $line ) {
				$this->line( $line );
			}
		}
	}

	private function line( string $line )
	{
		$this->line_chars = str_split( $line );
		$this->line_len = count( $this->line_chars );
		$this->reset_char_pos();
		$this->tokenize_line();
		++$this->line;
	}

	private function reset_char_pos()
	{
		$this->pos = -1;
		$this->advance_char();
	}

	private function advance_char()
	{
		++$this->pos;
		$this->current_char = ( $this->pos < $this->line_len ) ? $this->line_chars[ $this->pos ] : null;
	}

	/**
	 * Tokenize characters in line
	 * @throws LexError
	 */
	private function tokenize_line()
	{
		while ( $this->current_char !== null ) {
			/* DEBUG */
			// echo "$this->line:$this->pos ($this->line_len) = $this->current_char\n";
			$matching_token = TOKEN::tryFrom( $this->current_char );
			if ( in_array( $this->current_char, [ ' ', "\t", "\n" ], true ) ) {
				$this->advance_char();
			} elseif ( $matching_token ) {
				$this->tokens[ $this->line ][] = $matching_token;
				$this->advance_char();
			} elseif ( str_contains( TOKEN::INT->value, $this->current_char ) ) {
				$this->tokens[ $this->line ][] = $this->tokenize_int();
			} else {
				throw new LexError( $this, 1 );
			}
		}
	}

	/**
	 * Tokenize Integers and Floats
	 * @throws LexError
	 */
	private function tokenize_int()
	{
		$float = false;
		$int_str = '';
		while ( $this->current_char !== null && str_contains( TOKEN::INT->value . '.', $this->current_char ) ) {
			if ( $this->current_char === '.' ) {
				if ( $float !== true ) {
					$float = true;
				} else {
					throw new LexError( $this, 2 );
				}
			}
			$int_str .= $this->current_char;
			$this->advance_char();
		}
		if ( $float === true ) {
			return [ TOKEN::FLOAT, (double)$int_str ];
		}
		return [ TOKEN::INT, (int)$int_str ];
	}
}

class LexError extends MainException
{
	public function __construct( Lexer $instance, $code = 0 )
	{
		$message = match ( $code ) {
			1 => "Illegal character $instance->current_char",
			2 => 'Invalid float',
			default => 'Unknown Error',
		};
		parent::__construct( $instance, $message );
	}
}