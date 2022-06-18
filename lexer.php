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
	 * @param string $input
	 * @param bool $file
	 *
	 * @throws AMLError
	 */
	public function __construct( string $input, bool $file = false )
	{
		if ( $file === true ) {
			$f_point = fopen( $input, 'rb' );
			while ( $buffer = fgets( $f_point ) ) {
				$this->line( $buffer );
			}
		} else {
			$input = explode( PHP_EOL, $input );
			foreach ( $input as $line ) {
				$this->line( $line );
			}
		}
	}

	/**
	 * Prepare Line
	 * @throws AMLError
	 */
	private function line( string $line ): void
	{
		$this->line_chars = str_split( $line );
		$this->line_len = count( $this->line_chars );
		$this->reset_char_pos();
		$this->tokenize_line();
		++$this->line;
	}

	private function reset_char_pos(): void
	{
		$this->pos = -1;
		$this->advance_char();
	}

	private function advance_char(): void
	{
		++$this->pos;
		$this->current_char = ( $this->pos < $this->line_len ) ? $this->line_chars[ $this->pos ] : null;
	}

	/**
	 * Tokenize characters in line
	 * @throws AMLError
	 */
	private function tokenize_line(): void
	{
		while ( $this->current_char !== null ) {
			/* DEBUG */
			// echo "$this->line:$this->pos ($this->line_len) = $this->current_char\n";
			$matching_token = TOKEN::tryFrom( $this->current_char );
			if ( in_array( $this->current_char, [ ' ', "\t", PHP_EOL ], true ) ) {
				$this->advance_char();
			} elseif ( $matching_token ) {
				$this->tokens[ $this->line ][] = $matching_token;
				$this->advance_char();
			} elseif ( str_contains( TOKEN::INT->value, $this->current_char ) ) {
				$this->tokens[ $this->line ][] = $this->tokenize_int();
			} else {
				throw new AMLError( $this, "Illegal character $this->current_char" );
			}
		}
	}

	/**
	 * Tokenize Integers and Floats
	 * @throws AMLError
	 */
	private function tokenize_int(): array
	{
		$float = false;
		$int_str = '';
		while ( $this->current_char !== null && str_contains( TOKEN::INT->value . '.', $this->current_char ) ) {
			if ( $this->current_char === '.' ) {
				if ( $float !== true ) {
					$float = true;
				} else {
					throw new AMLError( $this, 'Invalid float' );
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