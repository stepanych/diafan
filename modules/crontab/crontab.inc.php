<?php
/**
 * Подключение модуля «Расписание задач» для работы со списком задач
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */
if ( ! defined('DIAFAN'))
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

/**
 * Crontab_inc
 */
class Crontab_inc extends Diafan
{
	/**
   * @var integer индекс поля в массиве для времени в формате CRON
   */
	const MINUTE = 0;
	const HOUR = 1;
	const DAY = 2;
	const MONTH = 3;
	const WEEKDAY = 4;
	const YEAR = 5;

	/**
   * @var array Дополнительные переменные cron
   */
	const VARIABLES = array(
		// '@reboot'   => '',       // Запуск при загрузке (не поддерживается)
		'@yearly'   => '0 0 1 1 *', // Раз в год (в полночь первого января каждого месяца)
		'@annually' => '0 0 1 1 *', // Тоже что и @yearly
		'@monthly'  => '0 0 1 * *', // Раз в месяц (в полночь первого числа каждого месяца)
		'@weekly'   => '0 0 * * 0', // Раз в неделю (в полночь каждого воскресенья)
		'@daily'    => '0 0 * * *', // Раз в день (в полночь каждого дня)
		'@midnight' => '0 0 * * *', // В полночь (00:00)
		'@hourly'   => '0 * * * *'  // Каждый час (в ноль минут каждого часа)
	);

	/**
   * @var array Массив сокращенных наименований дней недели
   */
	const WEEKDAYS = array(
		0 => 'sun',
		1 => 'mon',
		2 => 'tue',
		3 => 'wed',
		4 => 'thu',
		5 => 'fri',
		6 => 'sat'
	);

	/**
   * @var array Массив сокращенных наименований месяцев
   */
	const MONTHS = array(
		1 => 'jan',
		2 => 'feb',
		3 => 'mar',
		4 => 'apr',
		5 => 'may',
		6 => 'jun',
		7 => 'jul',
		8 => 'aug',
		9 => 'sep',
		10 => 'oct',
		11 => 'nov',
		12 => 'dec'
	);

	/**
   * @var integer максимальный год
   */
	const MAX_YEAR = 9999;

	/**
   * @var integer минимальный год
   */
	const MIN_YEAR = 1970; // 1 января 1970 (00:00:00 GMT) - начало эпохи Unix

	/**
	 * Возвращает время выполнения в формате CRONTAB без лишних символов
	 *
	 * @param string $str время выполнения в формате CRONTAB
	 * @return string
	 */
	public function trim($str)
	{
		if(empty($str))
		{
			return false;
		}
		$charset = mb_detect_encoding($str);
		$str = iconv($charset, "UTF-8", $str);
		$str = str_replace(
			array("❄", "❅", "❆", "★", "☆", "✪", "✫", "✯", "⚝", "⚹", "✵", "❉", "❋", "✺", "✹", "✸", "✶", "✷", "✴", "✳", "✲", "✱", "✧", "✦", "⍟", "⊛", "×", "*"),
			"*", $str
		);
		$str = preg_replace('/\s/u', ' ', $str);
		$str = preg_replace('/\s{2,}/u', ' ', $str);
		$str = preg_replace('/[^a-z0-9 \*@]/ui', '', $str);
		$str = strtolower(trim($str));
		if(empty($str))
		{
			return false;
		}
		if(array_key_exists($str, self::VARIABLES))
		{
			return $str;
		}
		$returnValue = preg_match_all('/\S+/u', $str, $matches);
		if($returnValue != 5 && $returnValue != 6)
		{
			return false;
		}
		$matches = $matches[0];
		foreach($matches as $key => $value)
		{
			if(empty($value) && $value !== '0')
			{
				return false;
			}
			if($value == '*')
			{
				continue;
			}
			switch($key)
			{
				case self::MINUTE:
				case self::HOUR:
				case self::DAY:
				case self::YEAR:
					$value = $this->diafan->filter($value, "integer");
					if(self::MINUTE == $key && ($value < 0 || $value > 59)
					|| self::HOUR == $key && ($value < 0 || $value > 23)
					|| self::DAY == $key && ($value < 1 || $value > 31)
					|| self::YEAR == $key && ($value < self::MIN_YEAR || $value > self::MAX_YEAR))
					{
						return false;
					}
					$matches[$key] = $value;
					break;

				case self::MONTH:
				case self::WEEKDAY:
					if(self::MONTH == $key)
					{
						if(! in_array($value, self::MONTHS))
						{
							$value = $this->diafan->filter($value, "integer");
							if($value < 1 || $value > 12)
							{
								return false;
							}
						}
					}
					if(self::WEEKDAY == $key)
					{
						if(! in_array($value, self::WEEKDAYS))
						{
							$value = $this->diafan->filter($value, "integer");
							if($value < 0 || $value > 6)
							{
								return false;
							}
						}
					}
					$matches[$key] = $value;
					break;

				default:
					return false;
					break;
			}
		}
		if(isset($matches[self::MONTH]) && $matches[self::MONTH] != '*'
		&& isset($matches[self::DAY]) && $matches[self::DAY] != '*')
		{
			if(false === ($mounth = array_search($matches[self::MONTH], self::MONTHS)))
			{
				$mounth = $matches[self::MONTH];
			}
			if(isset($matches[self::YEAR]) && $matches[self::YEAR] != '*'
			&& ! checkdate($mounth, $matches[self::DAY], $matches[self::YEAR]))
			{
				return false;
			}
			else
			{
				$year = date("Y"); $checkdate = false;
				for($i=0; $i < 4; $i++)
				{
					if($checkdate = checkdate($mounth, $matches[self::DAY], ($year + $i)))
					{
						break;
					}
				}
				if(! $checkdate)
				{
					return false;
				}
			}
		}
		return implode(" ", $matches);
	}

	/**
	 * Возвращает время выполнения в формате UNIXTIME
	 *
	 * @param string $str время выполнения в формате CRONTAB
	 * @param integer $datestamp временная метка, используемая в качестве базы для вычисления относительных дат
	 * @return string
	 */
	public function parser($str, $datestamp = false)
	{
		if($datestamp === false) $datestamp = $this->time_sec_reset(time());
		$now = $datestamp;

		// чистим строчку CRONTAB
		$str = $this->trim($str);
		if(empty($str))
		{
			return false;
		}
		// определяем переменные в строчке CRONTAB
		if(array_key_exists($str, self::VARIABLES))
		{
			$str = self::VARIABLES[$str];
		}
		$returnValue = preg_match_all('/\S+/u', $str, $matches);
		if($returnValue != 5 && $returnValue != 6)
		{
			return false;
		}
		$matches = $matches[0];
		$datetime = array();
		foreach($matches as $key => $value)
		{
			if($value == '*') continue;
			switch($key)
			{
				case self::MINUTE:
				case self::HOUR:
				case self::DAY:
				case self::YEAR:
					$datetime[$key] = $value;
					break;

				case self::MONTH:
					if(false === ($datetime[$key] = array_search($value, self::MONTHS)))
					{
						$datetime[$key] = $value;
					}
					break;

				case self::WEEKDAY:
					if(false === ($datetime[$key] = array_search($value, self::WEEKDAYS)))
					{
						$datetime[$key] = $value;
					}
					break;

				default:
					break;
			}
		}

		// определяем переменные относительной метки времени
		$hour = date("H", $now);
		$minute = date("i", $now);
		$day = date("j", $now);
		$month = date("n", $now);
		$year = date("Y", $now);

		// проверяем переменные в строчке CRONTAB
		// переменные должны быть больше или равны переменным относительной метки времени
		if(isset($datetime[self::YEAR]))
		{
			if($datetime[self::YEAR] < $year) return false;
			elseif($datetime[self::YEAR] == $year)
			{
				if(isset($datetime[self::MONTH]))
				{
					if($datetime[self::MONTH] < $month) return false;
					elseif($datetime[self::MONTH] == $month)
					{
						if(isset($datetime[self::DAY]))
						{
							if($datetime[self::DAY] < $day) return false;
							elseif($datetime[self::DAY] == $day)
							{
								if(isset($datetime[self::HOUR]))
								{
									if($datetime[self::HOUR] < $hour) return false;
									elseif($datetime[self::HOUR] == $hour)
									{
										if(isset($datetime[self::MINUTE]))
										{
											if($datetime[self::MINUTE] < $minute) return false;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// подбираем относительную метку времени согласно дню недели, указанной в CRONTAB
		if(isset($datetime[self::WEEKDAY]))
		{
			$date = false;
			$max_year = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : self::MAX_YEAR;
			for($y=$year; $y <= $max_year; $y++)
			{
				if($y == $year) $days = $this->get_weekdays($datetime[self::WEEKDAY], mktime(0,0,0, date("n", $now), (date("j", $now) + 1), date("Y", $now)));
				else $days = $this->get_weekdays($datetime[self::WEEKDAY], mktime(0,0,0, 1,1,$y));
				if(! $days) continue;

				if(isset($datetime[self::MONTH]))
				{
					if(empty($days[$datetime[self::MONTH]])) continue;
					$days = array($datetime[self::MONTH] => $days[$datetime[self::MONTH]]);
				}
				if(isset($datetime[self::DAY]))
				{
					$d = array();
					foreach($days as $key => $value)
					{
						if(! in_array($datetime[self::DAY], $value)) continue;
						$d[$key][] = $datetime[self::DAY];
					}
					$days = $d; unset($d);
				}
				if(empty($days)) continue;
				foreach($days as $key => $value)
				{
					$datetime[self::MONTH] = $key;
					$datetime[self::DAY] = reset($days[$key]);
					break;
				}
				$datetime[self::YEAR] = $y;
				$date = true;
				break;
			}
			if(! $date)
			{
				return false;
			}
		}

		// сбрасываем переменные относительной метки времени,
		// если указанные значения переменных CRONTAB превышают значения переменных относительной метки времени
		if(isset($datetime[self::YEAR]))
		{
			if($datetime[self::YEAR] > $year)
			{
				$y = $datetime[self::YEAR];
				$m = isset($datetime[self::MONTH]) ? $datetime[self::MONTH] : 1;
				$d = isset($datetime[self::DAY]) ? $datetime[self::DAY] : 1;
				$h = isset($datetime[self::HOUR]) ? $datetime[self::HOUR] : 0;
				$i = isset($datetime[self::MINUTE]) ? $datetime[self::MINUTE] : 0;
				if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
				{
					return false;
				}
				$now = mktime($h,$i,0, $m,$d,$y);
				$hour = date("H", $now);
				$minute = date("i", $now);
				$day = date("j", $now);
				$month = date("n", $now);
				$year = date("Y", $now);
			}
		}
		if(isset($datetime[self::MONTH]))
		{
			if($datetime[self::MONTH] > $month)
			{
				$y = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : $year;
				$m = $datetime[self::MONTH];
				$d = isset($datetime[self::DAY]) ? $datetime[self::DAY] : 1;
				$h = isset($datetime[self::HOUR]) ? $datetime[self::HOUR] : 0;
				$i = isset($datetime[self::MINUTE]) ? $datetime[self::MINUTE] : 0;
				if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
				{
					return false;
				}
				$now = mktime($h,$i,0, $m,$d,$y);
				$hour = date("H", $now);
				$minute = date("i", $now);
				$day = date("j", $now);
				$month = date("n", $now);
				$year = date("Y", $now);
			}
		}
		if(isset($datetime[self::DAY]))
		{
			if($datetime[self::DAY] > $day)
			{
				$y = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : $year;
				$m = isset($datetime[self::MONTH]) ? $datetime[self::MONTH] : 1;
				$d = $datetime[self::DAY];
				$h = isset($datetime[self::HOUR]) ? $datetime[self::HOUR] : 0;
				$i = isset($datetime[self::MINUTE]) ? $datetime[self::MINUTE] : 0;
				if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
				{
					return false;
				}
				$now = mktime($h,$i,0, $m,$d,$y);
				$hour = date("H", $now);
				$minute = date("i", $now);
				$day = date("j", $now);
				$month = date("n", $now);
				$year = date("Y", $now);
			}
		}
		if(isset($datetime[self::HOUR]))
		{
			if($datetime[self::HOUR] > $hour)
			{
				$y = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : $year;
				$m = isset($datetime[self::MONTH]) ? $datetime[self::MONTH] : 1;
				$d = isset($datetime[self::DAY]) ? $datetime[self::DAY] : 1;
				$h = $datetime[self::HOUR];
				$i = isset($datetime[self::MINUTE]) ? $datetime[self::MINUTE] : 0;
				if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
				{
					return false;
				}
				$now = mktime($h,$i,0, $m,$d,$y);
				$hour = date("H", $now);
				$minute = date("i", $now);
				$day = date("j", $now);
				$month = date("n", $now);
				$year = date("Y", $now);
			}
		}
		if(isset($datetime[self::MINUTE]))
		{
			if($datetime[self::MINUTE] > $minute)
			{
				$y = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : $year;
				$m = isset($datetime[self::MONTH]) ? $datetime[self::MONTH] : $month;
				$d = isset($datetime[self::DAY]) ? $datetime[self::DAY] : $day;
				$h = isset($datetime[self::HOUR]) ? $datetime[self::HOUR] : $hour;
				$i = $datetime[self::MINUTE];
				if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
				{
					return false;
				}
				$now = mktime($h,$i,0, $m,$d,$y);
				$hour = date("H", $now);
				$minute = date("i", $now);
				$day = date("j", $now);
				$month = date("n", $now);
				$year = date("Y", $now);
			}
		}

		// если относительная метка времени превышает время текущей метки, то сдвигаем относительную метку на один шаг CRONTAB
		$y = isset($datetime[self::YEAR]) ? $datetime[self::YEAR] : $year;
		$m = isset($datetime[self::MONTH]) ? $datetime[self::MONTH] : $month;
		$d = isset($datetime[self::DAY]) ? $datetime[self::DAY] : $day;
		$h = isset($datetime[self::HOUR]) ? $datetime[self::HOUR] : $hour;
		$i = isset($datetime[self::MINUTE]) ? $datetime[self::MINUTE] : $minute;
		if(! checkdate($m, $d, $y) || $h < 0 || $h > 23 || $i < 0 || $i > 59)
		{
			return false;
		}
		$now = mktime($h,$i,0, $m,$d,$y);
		if($datestamp > $now)
		{
			$d1 = new DateTime(date('H:i:s d.m.Y', $datestamp));
			$d2 = new DateTime(date('H:i:s d.m.Y', $now));
			$data_diff = date_diff($d1, $d2);
			$y = 0;	$m = 0; $d = 0; $h = 0; $i = 0;
			if($data_diff->s > 0)
			{
				$i++;
			}
			if($data_diff->i > 0 || $i > 0)
			{
				if(! isset($datetime[self::MINUTE]))
				{
					$minute += ($data_diff->i + $i);
					if($minute > 59)
					{
						$minute = 0;
						$h++;
					}
				}
				else $h++;
			}
			if($data_diff->h > 0 || $h > 0)
			{
				if(! isset($datetime[self::HOUR]))
				{
					$hour += ($data_diff->h + $h);
					if($hour > 23)
					{
						$hour = 0;
						$d++;
					}
				}
				else $d++;
			}
			if($data_diff->d > 0 || $d > 0)
			{
				if(! isset($datetime[self::DAY]))
				{
					$day += ($data_diff->d + $d);
					if(! checkdate($month, $day, $year))
					{
						$day = 1;
						$m++;
					}
				}
				else $m++;
			}
			if($data_diff->m > 0 || $m > 0)
			{
				if(! isset($datetime[self::MONTH]))
				{
					$month += ($data_diff->m + $m);
					if(! checkdate($month, $day, $year))
					{
						$month = 1;
						$y++;
					}
				}
				else $y++;
			}
			if($data_diff->y > 0 || $y > 0)
			{
				if(! isset($datetime[self::YEAR]))
				{
					$year += ($data_diff->y + $y);
					for ($i=0; $i < 4; $i++)
					{
						$year += $i;
						if(! checkdate($month, $day, $year)) continue;
						break;
					}
				}
			}
			if(! checkdate($month, $day, $year) || $hour < 0 || $hour > 23 || $minute < 0 || $minute > 59)
			{
				return false;
			}
		}

		// определяем время согласно строчки CRONTAB
		if(! isset($datetime[self::HOUR])) $datetime[self::HOUR] = $hour;
		if(! isset($datetime[self::MINUTE])) $datetime[self::MINUTE] = $minute;
		if(! isset($datetime[self::MONTH])) $datetime[self::MONTH] = $month;
		if(! isset($datetime[self::DAY])) $datetime[self::DAY] = $day;
		if(! isset($datetime[self::YEAR])) $datetime[self::YEAR] = $year;
		$second = 0;
		if(! checkdate($datetime[self::MONTH], $datetime[self::DAY], $datetime[self::YEAR]))
		{
			return false;
		}
		return mktime($datetime[self::HOUR], $datetime[self::MINUTE], $second, $datetime[self::MONTH], $datetime[self::DAY], $datetime[self::YEAR]);
	}

	/**
	 * Отдает все дни в году, соответствующие определённому дню недели
	 *
	 * @param integer $num_day день недели (0 - 6), где 0=воскресенье, 1=понедельник и т.д.
	 * @param integer $datestamp временная метка, используемая в качестве базы для вычисления относительных дат
	 * @return array
	 */
	private function get_weekdays($num_week, $datestamp = false)
	{
		if($datestamp === false) $datestamp = time();
		$now = mktime(0,0,0, date("n", $datestamp), date("j", $datestamp), date("Y", $datestamp));

		$result = array();

		if($num_week == 0) $num_week = 7;
		$date = new DateTime(date('H:i:s d.m.Y', $now));
		$flag = false; $year = date("Y", $datestamp);
		while($date->format("Y") == $year)
		{
			if($flag === false)
			{
				if($date->format("N") == $num_week) $flag = true;
				else $date->add(new DateInterval("P1D"));
			}
			if($flag === true)
			{
				$result[(int)$date->format("m")][] = (int)$date->format("d");
				$date->add(new DateInterval("P1W"));
			}
		}
		unset($date);
		return $result;
	}

	/**
	 * Cбрас секунд во временной метке
	 *
	 * @param integer $datestamp временная метка
	 * @return integer
	 */
	public function time_sec_reset($datestamp = false)
	{
		if($datestamp === false) $datestamp = time();
		return mktime(date("H", $datestamp),date("i", $datestamp),0, date("n", $datestamp), date("j", $datestamp), date("Y", $datestamp));
	}
}
