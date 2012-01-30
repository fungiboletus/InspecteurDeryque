<?php
class CTools
{
	public static function toUtf8($charset, $texte)
	{
		try
		{
			return iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $texte);
		}
		catch (Exception $e)
		{
			try
			{
				// La plupart des boulets utilisent de l'iso…
				return iconv('ISO-8859-15', 'UTF-8//TRANSLIT//IGNORE', $texte);
			}
			catch (Exception $e)
			{
				return $texte;
			}
		}
	}
	
	public static function mimeToUtf8($input)
	{
		$output = '';

		$elements = imap_mime_header_decode($input);

		$nb_elements = count($elements);
		for ($i=0; $i<$nb_elements; ++$i)
		{
			$charset = $elements[$i]->charset;
			$text = $elements[$i]->text;
			
			if ($charset !== 'default')
			{
				$output .= CTools::toUtf8($charset, $text);
			}
			else
			{
				$output .= $text;
			}
		}
	
		return $output;
	}

	/* Fonction qui converti en unit?s standarts la taille d'un fichier */
	public static function nbBytesToKibis($nb_bytes)
	{
		static $unites = array (
			'octet',
			'kibi',
			'mébi',
			'gibi',
			'tébi',
			'pébi',
			'exbi',
			'zébi',
			'yobi'
		); // On a le temps de voir venir comme ?a

		// On regarde quelle unit? correspond
		$u = (int)log((double)$nb_bytes, 1024);

		// Si l'unit? est inconnue, tout en bits
		if (isset($unites[$u]) === false)
			$u = 0;

		// Conversion en valeur ? virgule
		$nb_kibis = $nb_bytes/pow(1024, $u);

		$tu = $unites[$u];

		if ($nb_kibis != 1)
		{
			$tu .= 's';
		}

		return array($nb_kibis, $tu, $u);
	}

	public static function getDataPath($hash, $createDirs = false) {

		$firstDir = 'Data/'.substr($hash, 0, 2);
		$secondDir = $firstDir.'/'.substr($hash, 2, 2);
		$dataPath = $secondDir.'/'.substr($hash, 4);

		if ($createDirs) {
			if ((!is_dir($firstDir) && !(mkdir($firstDir)&&touch("$firstDir/index.html"))) || (!is_dir($secondDir) && !(mkdir($secondDir)&&touch("$secondDir/index.html")))) {
				throw new exception(_('Unable to create dir'));
			}
		}

		return $dataPath;
	}

	public static function hackError()
	{
		$error = new Error();
		$error->teapot();
		throw new Exception('hack');
	}
}
?>
