<?php
/**
 * Набор функций для работы с JSON
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
	$path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}

class Json
{
	/**
	 * Преобразует массив в формат JSON
	 *
	 * @param mixed $value значение, которое будет закодировано. Может быть любого типа, кроме resource.
	 * @param integer $options битовая маска, составляемая из значений JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT, JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE, JSON_THROW_ON_ERROR.
	 * @param integer $depth Устанавливает максимальную глубину. Должен быть больше нуля.
	 * @return string
	 */
	public static function to_json($value, $profiler = false, $options = (JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION), $depth = 512)
	{
		header('Content-Type: application/json; charset=utf-8');
		return self::encode($value, $options, $depth);
	}

	/**
	 * Преобразует массив в формат JSON
	 *
	 * @param mixed $value значение, которое будет закодировано. Может быть любого типа, кроме resource.
	 * @param integer $options битовая маска, составляемая из значений JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT, JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE, JSON_THROW_ON_ERROR.
	 * @param integer $depth Устанавливает максимальную глубину. Должен быть больше нуля.
	 * @return string
	 */
	public static function encode($value, $options = (JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION), $depth = 512)
	{
		$php_version_min = 50400; // PHP 5.4
		if(self::version_php() < $php_version_min)
		{
			// TO_DO: кириллица в ответе JSON - JSON_UNESCAPED_UNICODE
			$json = preg_replace_callback(
				"/\\\\u([a-f0-9]{4})/",
				function($matches) {
					return iconv('UCS-4LE','UTF-8',pack('V', hexdec('U' . $matches[0])));
				},
				json_encode($value)
			);
			$json = str_replace('&', '&amp;', $json);
			$json = str_replace(array('<', '>'), array('&lt;', '&gt;'), $json);
		}
		else
		{
			$json = json_encode($value, $options, $depth);
			if($json === false && $value && json_last_error() == JSON_ERROR_UTF8)
			{
				// $value = self::unicode_to_utf8($value);
				// $value = self::utf8ize($value);
				$json = json_encode($value, $options, $depth);
			}
		}
		return $json;
	}

	/**
	 * Декодирует строку JSON
	 *
	 * @param string $string cтрока json для декодирования
	 * @param boolean $assoc если TRUE, возвращаемые объекты будут преобразованы в ассоциативные массивы.
	 * @param integer $depth глубина рекурсии
	 * @param integer $options битовая маска из констант JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR.
	 * @return mixed
	 */
	public static function from_json($string, $assoc = true, $depth = 512, $options = 0)
	{
		$result = self::decode($string, $assoc, $depth, $options);
		if(! empty($result["profiler"])) $result["profiler"] = urldecode($result["profiler"]);
		return $result;
	}

	/**
	 * Декодирует строку JSON
	 *
	 * @param string $string cтрока json для декодирования
	 * @param boolean $assoc если TRUE, возвращаемые объекты будут преобразованы в ассоциативные массивы.
	 * @param integer $depth глубина рекурсии
	 * @param integer $options битовая маска из констант JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR.
	 * @return mixed
	 */
	public static function decode($string, $assoc = true, $depth = 512, $options = 0)
	{
		return json_decode(self::remove_BOM(self::remove_unwanted($string)), $assoc, $depth, $options);
	}

	/**
	 * Возвращает последнюю ошибку
	 *
	 * @return string
	 */
	public static function last_error()
	{
		// Создаем массив с ошибками.
		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach($constants["json"] as $name => $value)
		{
			if (!strncmp($name, "JSON_ERROR_", 11))
			{
				$json_errors[$value] = $name;
			}
		}
		$last_error = json_last_error();
		$error_code = ! empty($json_errors[$last_error]) ? $json_errors[$last_error] : '';

		$error_message = '';
		switch($last_error)
		{
			case JSON_ERROR_NONE:
				$error_message = 'No errors'; // Ошибок нет
				break;
			case JSON_ERROR_DEPTH:
				$error_message = 'Maximum stack depth exceeded'; // Достигнута максимальная глубина стека
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$error_message = 'Underflow or the modes mismatch'; // Некорректные разряды или несоответствие режимов
				break;
			case JSON_ERROR_CTRL_CHAR:
				$error_message = 'Unexpected control character found'; // Некорректный управляющий символ
				break;
			case JSON_ERROR_SYNTAX:
				$error_message = 'Syntax error, malformed JSON'; // Синтаксическая ошибка, некорректный JSON
				break;
			case JSON_ERROR_UTF8:
				$error_message = 'Malformed UTF-8 characters, possibly incorrectly encoded'; // Некорректные символы UTF-8, возможно неверно закодирован
				break;
			default:
				$error_message = 'Unknown error'; // Неизвестная ошибка
				break;
		}

		return $error_code.(! empty($error_code) && ! empty($error_message) ? ': ' : '').$error_message;
	}

	/**
	 * Возвращает версию PHP
	 *
	 * @param integer $length возвращает версию в виде числа или строчки
	 * @return mixed(integer|string)
	 */
	private static function version_php($length = false, $glue = '.')
	{
		if(! defined('PHP_VERSION_ID'))
		{
			$version = phpversion();
			$version = explode('.', $version);
			define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
		}
		if(! $length)
		{
			return (int) PHP_VERSION_ID;
		}
		$version = array();
		$version[] = (int) ($php_version_min / 10000);
		$version[] = (int) (($php_version_min % 10000) / 100);
		$version[] = (int) (($php_version_min % 100) / 10);
		return implode($glue, array_slice($version, 0, $length));
	}

	/**
	 * Конвертирует UNICODE в UTF8
	 *
	 * @param mixed(string|array) $mixed значение
	 * @return mixed
	 */
	public static function unicode_to_utf8($string)
	{
		if(is_array($mixed))
		{
			foreach ($mixed as $key => $value)
			{
				$mixed[$key] = unicode_to_utf8($value);
			}
		}
		elseif(is_string($mixed))
		{
			return html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
		}
		return $mixed;
	}

	/**
	 * Конвертирует значение в UTF8
	 *
	 * @param mixed(string|array) $mixed значение
	 * @return mixed
	 */
	public static function utf8ize($mixed)
	{
		if(is_array($mixed))
		{
			foreach ($mixed as $key => $value)
			{
				$mixed[$key] = utf8ize($value);
			}
		}
		elseif(is_string($mixed))
		{
			return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
		}
		return $mixed;
	}

	/**
	 * Удаляет unwanted
	 *
	 * @param string $string cтрока json для декодирования
	 * @return string
	 */
	private static function remove_unwanted($string)
	{
		// This will remove unwanted characters.
		// Check http://www.php.net/chr for details
		for ($i = 0; $i <= 31; ++$i)
		{
			$string = str_replace(chr($i), "", $string);
		}
		return str_replace(chr(127), "", $string);
	}

	/**
	 * Удаляет BOM
	 *
	 * @param string $string cтрока json для декодирования
	 * @return string
	 */
	private static function remove_BOM($string)
	{
		// This is the most common part
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters
		if(0 === strpos(bin2hex($string), 'efbbbf'))
		{
			return substr($string, 3);
		}
		return $string;
	}
}

/**
 * Json_exception
 *
 * Исключение для работы с JSON
 */
class Json_exception extends Exception{}

if (! function_exists('json_encode'))
{
    /**
     * Convert a PHP scalar, array or hash to JS scalar/array/hash. This function is
     * an analog of json_encode(), but it can work with a non-UTF8 input and does not
     * analyze the passed data. Output format must be fully JSON compatible.
     *
     * A port of JsHttpRequest (http://en.dklab.ru)
     * (C) Dmitry Koterov, http://en.dklab.ru
     *
     * @param mixed $a   Any structure to convert to JS.
     * @return string    JavaScript equivalent structure.
     */
    function json_encode($a = false)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a)) {
            if (is_float($a)) {
                // Always use "." for floats.
                $a = str_replace(",", ".", strval($a));
            }

            static $jsonReplaces = array(
                array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
                array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
            );
            return '"'.str_replace($jsonReplaces[0], $jsonReplaces[1], $a).'"';
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
            if (key($a) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($a as $v) {
                $result[] = json_encode($v);
            }
            return '[ '.join(', ', $result).' ]';
        } else {
            foreach ($a as $k => $v) {
                $result[] = json_encode($k).': '.json_encode($v);
            }
            return '{ '.join(', ', $result).' }';
        }
    }
}
