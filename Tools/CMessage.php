<?php

class CMessage
{
	public $message;

	public function __construct($message)
	{
		$this->message = $message;

		if (!isset($_SESSION['CMessage_list']))
		{
			$_SESSION['CMessage_list'] = array();
		}

		$_SESSION['CMessage_list'][] = $this;
	}

	public function show()
	{
		echo "\t<div class=\"alert-message success\">$this->message</div>\n";
	}

	public static function showMessages()
	{
		if (isset($_SESSION['CMessage_list']))
		{
			$t = &$_SESSION['CMessage_list'];

			if (count($t) > 0)
			{
				echo "<div class=\"CMessage\">\n";
				
				do
				{
					array_pop($t)->show();
				} while (count($t) > 0);

				echo "</div>\n";
			}
		}
	}
}
?>
