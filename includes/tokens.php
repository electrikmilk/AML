<?php
/*
 * Copyright (c) 2022 Brandon Jordan
 * Last Modified: 6/18/2022 21:52
 */

enum TOKEN: string
{
	case INT = '0123456789';
	case FLOAT = '0123456789.';
	case PLUS = '+';
	case MINUS = '-';
	case MULTIPLY = '*';
	case DIVIDE = '/';
	case CLOSURE_START = '(';
	case CLOSURE_END = ')';
}