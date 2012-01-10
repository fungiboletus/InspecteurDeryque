<?php
class CVignette
{
	public static function cheminVignette($chemin,$ext,$x,$y)
	{

		$chemin_vignette = $chemin.'_'.$x.'_'.$y.'.';

		if(!file_exists($chemin_vignette.$ext))
		{
			if (!file_exists($chemin_vignette.'png'))
			{
				return self::resizeImage($chemin,$ext,$x,$y);
			}
			return $chemin_vignette.'png';
		}
		
		return $chemin_vignette.$ext;
	}

	public static function resizeImage($chemin,$ext,$maxX,$maxY)
	{
		$chemin_ext = $chemin.'.'.$ext;

		if (!file_exists($chemin_ext))
		{
			groaw("Pas de fichier : $chemin_ext");
			return false;
		}

		try
		{
			if (!function_exists('Imagick'))
			{
				if (!function_exists('imagecreatefromjpeg'))
				{
					groaw("Pas de miniatures possible :D");
					return false;
				}

				return self::resizeImageGD($chemin, $ext, $maxX, $maxY);
			}
			
			return self::resizeImageImagick($chemin, $ext, $maxX, $maxY);
		}
		catch (Exception $e)
		{
			groaw($e);
			return false;
		}	
	}

	public static function resizeImageImagick($chemin,$ext,$maxX,$maxY)
	{
		$thumb = new Imagick("$chemin.$ext");
	
		list($newX,$newY)=self::scaleImage(
			$thumb->getImageWidth(),
			$thumb->getImageHeight(),
			$maxX,
			$maxY);
	
		$thumb->thumbnailImage($newX,$newY);

		$nouveau_chemin = $chemin.'_'.$maxX.'_'.$mayY.'.'.$ext;
		$thumb->writeImage($nouveau_chemin);

		return $nouveau_chemin;
	}
	
	public static function resizeImageGD($chemin, $ext, $x,$y)
	{
		$chemin_ext = "$chemin.$ext";
		$info = getimagesize($chemin_ext);

		if ($info === false)
		{
			return false;
		}

		switch($info[2])
		{
			case IMAGETYPE_JPEG:
			case IMAGETYPE_JPEG2000:
				$img  = imagecreatefromjpeg($chemin_ext);
				break;
			case IMAGETYPE_PNG:
				$img = imagecreatefrompng($chemin_ext);
				break;
			case IMAGETYPE_GIF:
				$img = imagecreatefromgif($chemin_ext);
				break;
			default:
				return false;
		}

		$iX = $info[0];
		$iY = $info[1];

		list($tX,$tY)=self::scaleImage(
			$iX,
			$iY,
			$x,
			$y);

		$vignette = ImageCreateTrueColor($tX, $tY);
		imagealphablending($vignette, true);
		imagecopyresampled($vignette, $img, 0, 0, 0, 0, $tX, $tY, $iX, $iY);

		$nouveau_chemin = $chemin.'_'.$x.'_'.$y.'.png';
		imagepng($vignette, $nouveau_chemin);
		imagedestroy($img);
		imagedestroy($vignette);
		
		return $nouveau_chemin;
	}

	/**
	 * Calcule les nouvelles dimensions de l'image selon les contraintes
	 */
	public static function scaleImage($x,$y,$cx,$cy)
	{
		list($nx,$ny)=array($x,$y);

		if ($x>=$cx || $y>=$cx) 
		{

			if ($x>0) $rx=$cx/$x;
			if ($y>0) $ry=$cy/$y;

			if ($rx < $ry) $r = $rx;
			else $r = $ry;

			$nx=intval($x*$r);
			$ny=intval($y*$r);
		}

		return array($nx,$ny);
	}
}
?>
