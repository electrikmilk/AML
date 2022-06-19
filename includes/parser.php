<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/19/2022 12:2
 */

class Parser
{
	public int $line = 1;
	public int $pos = 0;

	private array $integer_tokens = [ 'INT', 'FLOAT' ];
	private array $operator_tokens = [ 'PLUS', 'MINUS', 'MULTIPLY', 'DIVIDE' ];

	public array $tokens = [];

	/**
	 * @param array $lines
	 *
	 * @throws AMLError
	 */
	public function __construct( array $lines )
	{
		foreach ( $lines as $line => $tokens ) {
			$this->line = $line;
			$this->parse_line( $tokens );
		}
		$this->tokens = $lines;
	}

	/**
	 * @param array $tokens
	 *
	 * @return void
	 * @throws AMLError
	 */
	private function parse_line( array $tokens ): void
	{
		$last_token_type = null;
		foreach ( $tokens as $column => $token ) {
			$token_value = null;
			$this->pos = $column;
			if ( is_array( $token ) ) {
				$token_type = $token[ 0 ]->name;
				if ( isset( $token[ 1 ] ) ) {
					$token_value = $token[ 1 ];
				}
			} else {
				$token_type = $token->name;
			}
			// Check Grammar
			if ( in_array( $token_type, $this->integer_tokens, true ) && in_array( $last_token_type, $this->integer_tokens, true ) ) {
				throw new AMLError( $this, 'Cannot place two integers or floats together without a separating operator.' );
			}
			if ( in_array( $token_type, $this->operator_tokens, true ) && in_array( $last_token_type, $this->operator_tokens, true ) ) {
				throw new AMLError( $this, 'Cannot place two operators together without a separating integer or float.' );
			}
			if ( ( $token_type === 'CLOSURE_START' && in_array( $last_token_type, $this->integer_tokens, true ) ) || ( $last_token_type === 'CLOSURE_END' && in_array( $token_type, $this->integer_tokens, true ) ) ) {
				throw new AMLError( $this, 'Cannot place integer or float outside of a closure without a separating operator.' );
			}
			$last_token_type = $token_type;
		}
	}
}