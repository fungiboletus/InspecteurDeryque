<?php
/**
 * Abstract class for the different views.
 */
abstract class AbstractView
{
	protected static $now; /**< time cache for performances. */
	
	/**
	 * Display a button.
	 * @param $url the wanted url.
	 * @param $class the class, for css display
	 * @param $text The text associated to the button
	 * @param $icon The button icon.
	 */
	protected static function showButton($url, $class, $text, $icon) {
		echo <<<END
			<a href="$url" class="btn large $class">
			<span class="icon_button ${icon}_text">$text</span></a>
END;
	}
	
	/**
	 * Converts time period to a more human readable format.
	 * @param $period The time to convert.
	 * @param $week OPTIONAL - indicates weeks instead of days.
	 * @return $str A string about the current period.
	 */
	public static function translateTimePeriod($period, $week = null)
	{
		switch($period)
		{
			case -1:
				return _('in the future');
			case 0:
				return _('tonight');
			case 1:
				return _('in late afternoon');
			case 2:
				return _('this afternoon');
			case 3:
				return _('around midday');
			case 4:
				return _('this morning');
			case 5:
				return _('early this morning');
			case 6:
				return _('in the night');
			case 7:
				return _('yesterday');
			case 8:
				return ($week) ? $week : _('this week');
			default:
				return _('more than a week');
		}
	}

    /**
     * Formats date.
     * @param $date Date to format.
     *        If $date is not a valid date array (e.g. NULL), get local date.
     * @return $d Formatted date in string.
     */
	public static function formateDate($date)
	{
		if (!is_array($date))
		{
			$date = self::locateDate($date);
		}

		$period = $date[0];

		if ($period < 0 || $period > 8)
		{
			$format = _('%A %d %B %Y à %Hh%M');
		}
		elseif ($period >= 7)
		{
			$format = _('%A à %Hh%M');
		}
		else
		{
			$format = _('%Hh%M');
		}

		return strftime($format, $date[1]);
	}

    /**
     * Analyzes a date.
     * @param $date Date to look at (either a timestamp or a string)
     * @return $array ($i,$t,$y) : 
     *          $i : if the date is in the future -1 is given, else a value >0.
     *          $t : The timestamp of the given date.
     *          $y : The year in String, or NULL.
     */
	public static function locateDate($date) {
		// It's interesting to note that it works fine
		if (is_numeric($date)) {
			$t = intval($date);
		} else {
			$t =  strtotime($date);
		}

		// The time is cached to improve performance
		if (!isset(self::$now)) {
			self::$now = time();
		}

		$n = self::$now;
		$an = getdate($n);

		// we start at midnight
		$hour_c = mktime(23, 59, 59, $an['mon'], $an['mday'], $an['year']);

		$hours = array(
			23400, // 1 From 18h30
			36000, // 2 From 14h
			45000, // 3 From 11h30
			57600, // 4 From 8h00
			68400, // 5 From 5h00
			86400, // 6 From 0h0
			172800,// 7 Yesterday
			604800,// 8 This week
			0
		);
		
		// If the date is not in the future
		if ($t <= $n)
		{
			$i;
			$hour = $hour_c;
			for ($i = 0; $t < $hour && $i < 9 ; ++$i)
			{
				$hour = $hour_c - $hours[$i];
			}

			return array($i, $t, ($i === 8) ? strftime('%A', $t) : null);
		}

		// When the date is in the future…
		return array(-1, $t, null);
	}

    /**
     * Get the icon associated to a given mimetype.
     * @param $mimetype String, the mimetype.
     * @return $str The file location if known,
     *              or a generic mimetype information,
     *              or unknown if not recognized
     */
	public static function getMimeIcone($mimetype)
	{

		$m;
		preg_match('/^([^;]*)/', $mimetype, $m);

		$file = str_replace('/', '-', $m[1]);

        if (file_exists("Img/mimes/$file.png"))
        {
                return $file;
        }

        $generics = array('image', 'audio', 'text', 'video', 'package', 'message');

        foreach($generics as $id => $generic)
		{
                if (strpos($mimetype, $generic) !== FALSE)
                {
                        return "$generic-x-generic";
                }
        }

        return 'unknown';
	}
}
?>
