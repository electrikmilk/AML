<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/19/2022 21:50
 */

class Interpreter
{
	public int $line = 0;
	public int $pos = 0;
	public mixed $result = 0;

	private array $operator_tokens = [ 'PLUS', 'MINUS', 'MULTIPLY', 'DIVIDE' ];

	/**
	 * @param array $lines
	 *
	 * @throws AMLError
	 */
	public function __construct( array $lines )
	{
		foreach ( $lines as $line => $tokens ) {
			$this->line = $line;
			$this->exec_line( $tokens );
			$this->output( $this->result );
		}
	}

	public function output( string $str ): void
	{
		global $file;
		$equals = style( '=', DIM );
		if ( $file ) {
			$lines = explode( PHP_EOL, file_get_contents( $file ) );
			echo style( $lines[ $this->line - 1 ], YELLOW ) . " $equals " . style( $str, GREEN, BOLD ) . "\n";
		} else {
			echo "$equals " . style( $str, GREEN, BOLD ) . " \n";
		}
	}

	public function do_operation( string $operation, mixed $value ): void
	{
		switch ( $operation ) {
			case 'PLUS':
				$this->result += $value;
				break;
			case 'MINUS':
				$this->result -= $value;
				break;
			case 'MULTIPLY':
				$this->result *= $value;
				break;
			case 'DIVIDE':
				$this->result /= $value;
				break;
		}
	}

	/**
	 * @param array $tokens
	 *
	 * @return void
	 * @throws AMLError
	 */
	private function exec_line( array $tokens ): void
	{
		$operation = null;
		$save_operation = null;
		$save_result = 0;
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
			if ( $token_value && ( $token_type === 'INT' || $token_type === 'FLOAT' ) ) {
				if ( $operation !== null ) {
					$this->do_operation( $operation, $token_value );
					$operation = null;
				} else {
					$this->result = $token_value;
				}
			} elseif ( $token_type === 'CLOSURE_START' ) {
				$save_result = $this->result;
				$save_operation = $operation;
				$this->result = 0;
			} elseif ( $token_type === 'CLOSURE_END' ) {
				$this->do_operation( $save_operation, $save_result );
			} elseif ( in_array( $token_type, $this->operator_tokens, true ) ) {
				$operation = $token_type;
			} else {
				$operation = null;
				$this->result = 0;
			}
		}
	}
}