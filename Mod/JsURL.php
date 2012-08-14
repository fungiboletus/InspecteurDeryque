<?php

/**
* Generate a text representation of an object in an url.
*
* Similar to Rison ou JsURL, but more simpler.
*
* Just for fun, this is a small state machine.
*
* And it use json_encode and rawurlencode php functions, it's funny !
*/
class JsURL
{
	const OBJECT_STATE = 1;
	const STRING_STATE = 2;
	const ESCAPE_STRING_STATE = 3;

	// This is not thread safe, but it's not important…
	private static $state = self::OBJECT_STATE;

	private static $string_buffer = '';

	public static function stringify($object)
	{
		// Start by taking the json object (yes)
		$json = json_encode($object, JSON_UNESCAPED_UNICODE);

		$new_json = '';

		$n = strlen($json);
		for ($i = 0; $i < $n; ++$i)
			$new_json .= self::process($json[$i]);

		return $new_json;
	}

	private static function process($char)
	{
		// It's ugly, but it's performant
		switch (self::$state)
		{
			case self::OBJECT_STATE:
				return self::objectProcess($char);
			case self::STRING_STATE:
				return self::stringProcess($char);
			case self::ESCAPE_STRING_STATE:
				return self::escapeStringProcess($char);
		}
	}

	public static function objectProcess($char)
	{
		switch ($char)
		{
			case '{':
				return '(';
			case '[':
				return '!(';
			case '}':
			case ']':
				return ')';
			case '"':
				self::$string_buffer = '';
				self::$state = self::STRING_STATE;
				return '\'';
			default:
				return $char;
		}
	}

	public static function stringProcess($char)
	{
		switch ($char)
		{
			case '"':
				self::$state = self::OBJECT_STATE;
				return rawurlencode(self::$string_buffer).'\'';
			case '\\':
				self::$state = self::ESCAPE_STRING_STATE;
				break;
			default:
				self::$string_buffer .= $char;

			return null;
		}
	}
	public static function escapeStringProcess($char)
	{
		self::$string_buffer .= $char;
		self::$state = self::STRING_STATE;
	}
}
 ?>