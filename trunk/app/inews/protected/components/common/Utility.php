<?php

class Utility {
	
	public static function foreignToUnicode($str) {
		$fromStr = array(
			'&Agrave;', '&agrave;', '&Aacute;', '&aacute;', '&Acirc;', '&acirc;', '&Atilde;', '&atilde;',
			'&#192;', '&#224;', '&#193;', '&#225;', '&#194;	', '&#226;', '&#195;', '&#227;',
			'&Iacute;', '&iacute;', '&Igrave;', '&igrave;', 
			'&#205;', '&#237;', '&#204;', '&#236;', 
			'&Egrave;', '&egrave;', '&Eacute;', '&eacute;', '&Ecirc;', '&ecirc;', 
			'&#200;', '&#232;', '&#201;', '&#233;', '&#202;', '&#234;', 
			'&Ograve;', '&ograve;', '&Ocirc;', '&ocirc;', '&Oacute;', '&oacute;', '&Otilde;', '&otilde;',
			'&#210;', '&#242;', '&#212;', '&#244;', '&#211;', '&#243;', '&#213;', '&#245;',
			'&Ugrave;', '&ugrave;', '&Uacute;', '&uacute;', 
			'&#217;', '&#249;', '&#218;', '&#250;', 
			'&ETH;',
			'&#208;',
			'&Yacute;', '&yacute;', 
			'&#221;', '&#253;', 
			'&quot;',
		);
		$toStr = array(
			'À', 'à', 'Á', 'á', 'Â', 'â', 'Ã', 'ã',
			'À', 'à', 'Á', 'á', 'Â', 'â', 'Ã', 'ã',
			'Í', 'í', 'Ì', 'ì', 
			'Í', 'í', 'Ì', 'ì', 
			'È', 'è', 'É', 'é', 'Ê', 'ê', 
			'È', 'è', 'É', 'é', 'Ê', 'ê', 
			'Ò', 'ò', 'Ô', 'ô', 'Ó', 'ó', 'Õ', 'õ',
			'Ò', 'ò', 'Ô', 'ô', 'Ó', 'ó', 'Õ', 'õ',
			'Ù', 'ù', 'Ú', 'ú', 
			'Ù', 'ù', 'Ú', 'ú', 
			'Đ',
			'Đ',
			'Ý', 'ý',
			'Ý', 'ý',
			'"'
		);
		
		for ($i = 0;$i < count($fromStr);$i++) {
			$str = str_replace($fromStr[$i], $toStr[$i], $str);
		}
		
		return $str;
	}
	
	/*
	*  Ham bo dau tieng Viet
	*/
	public static function unicode2Anscii($str) {
		$str = str_replace(array('á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ'), 'a', $str);
		$str = str_replace(array('é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ'), 'e', $str);
		$str = str_replace(array('í', 'ì', 'ỉ', 'ĩ', 'ị'), 'i', $str);
		$str = str_replace(array('ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ'), 'o', $str);
		$str = str_replace(array('ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ú', 'ù', 'ủ', 'ũ', 'ụ'), 'u', $str);
		$str = str_replace(array('ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ'), 'y', $str);
		$str = str_replace(array('Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ'), 'A', $str);
		$str = str_replace(array('É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ'), 'E', $str);
		$str = str_replace(array('Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị'), 'I', $str);
		$str = str_replace(array('Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ'), 'O', $str);
		$str = str_replace(array('Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ'), 'U', $str);
		$str = str_replace(array('Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'), 'Y', $str);
		$str = str_replace(array('đ', 'Đ'), array('d', 'D'), $str);

		return $str;	
	}
	
	public static function composite2Unicode($str) {
		$chars_utf8 = array(
			array('ấ','ầ','ẩ','ẫ','ậ','Ấ','Ầ','Ẩ','Ẫ','Ậ','ắ','ằ','ẳ','ẵ','ặ','Ắ','Ằ','Ẳ','Ẵ','Ặ','á','à','ả','ã','ạ','â','ă','Á','À','Ả','Ã','Ạ','Â','Ă'),
			array('ế','ề','ể','ễ','ệ','Ế','Ề','Ể','Ễ','Ệ','é','è','ẻ','ẽ','ẹ','ê','É','È','Ẻ','Ẽ','Ẹ','Ê'),
			array('í','ì','ỉ','ĩ','ị','Í','Ì','Ỉ','Ĩ','Ị'),
			array('ố','ồ','ổ','ỗ','ộ','Ố','Ồ','Ổ','Ô','Ộ','ớ','ờ','ở','ỡ','ợ','Ớ','Ờ','Ở','Ỡ','Ợ','ó','ò','ỏ','õ','ọ','ô','ơ','Ó','Ò','Ỏ','Õ','Ọ','Ô','Ơ'),
			array('ứ','ừ','ử','ữ','ự','Ứ','Ừ','Ử','Ữ','Ự','ú','ù','ủ','ũ','ụ','ư','Ú','Ù','Ủ','Ũ','Ụ','Ư'),
			array('ý','ỳ','ỷ','ỹ','ỵ','Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
			array('đ','Đ'),
		);

		$chars_unicode = array(
			array('ấ','ầ','ẩ','ẫ','ậ','Ấ','Ầ','Ẩ','Ẫ','Ậ','ắ','ằ','ẳ','ẵ','ặ','Ắ','Ằ','Ẳ','Ẵ','Ặ','á','à','ả','ã','ạ','â','ă','Á','À','Ả','Ã','Ạ','Â','Ă'),
			array('ế','ề','ể','ễ','ệ','Ế','Ề','Ể','Ễ','Ệ','é','è','ẻ','ẽ','ẹ','ê','É','È','Ẻ','Ẽ','Ẹ','Ê'),
			array('í','ì','ỉ','ĩ','ị','Í','Ì','Ỉ','Ĩ','Ị'),
			array('ố','ồ','ổ','ỗ','ộ','Ố','Ồ','Ổ','Ô','Ộ','ớ','ờ','ở','ỡ','ợ','Ớ','Ờ','Ở','Ỡ','Ợ','ó','ò','ỏ','õ','ọ','ô','ơ','Ó','Ò','Ỏ','Õ','Ọ','Ô','Ơ'),
			array('ứ','ừ','ử','ữ','ự','Ứ','Ừ','Ử','Ữ','Ự','ú','ù','ủ','ũ','ụ','ư','Ú','Ù','Ủ','Ũ','Ụ','Ư'),
			array('ý','ỳ','ỷ','ỹ','ỵ','Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
			array('đ','Đ'),
		);
		for ($i = 0;$i < count($chars_utf8);++$i)
			for ($j = 0;$j < count($chars_utf8[$i]);++$j) {
				$str = str_replace($chars_unicode[$i][$j], $chars_utf8[$i][$j], $str);
			}
		return $str;
	}
	
	/**
     *
     * @param string $imgSrc
     * @param array $arrSize
     * @param string $imgTarget
     * @return mixed
     */
    public static function cropImage($imgSrc, $arrSize, $imgTarget = null) {
        $thumbnail_width = $arrSize[0]; $thumbnail_height = $arrSize[1];

        if (!$imgTarget) $imgTarget = $imgSrc;

        $newThumb = Utility::__getThumbnailResource($imgSrc, $thumbnail_width, $thumbnail_height);
        $ext = substr($imgTarget, -4);

        if ($ext == '.gif') {
            imagegif($newThumb, $imgTarget);
        } elseif ($ext == '.jpg') {
            imagejpeg($newThumb, $imgTarget);
        } elseif ($ext == '.png') {
            imagepng($newThumb, $imgTarget);
        } else {
            return false;
        }

        #die($imgTarget);
        return $imgTarget;
    }

    public static function __getThumbnailResource($imgSrc, $thumbnail_width, $thumbnail_height) {
        $arrInfo = getimagesize($imgSrc);
        $width_orig = $arrInfo[0];
        $height_orig = $arrInfo[1];
        $lmime = strtolower($arrInfo['mime']);

        if (strpos($lmime, 'gif') !== false) {
            $myImage = imagecreatefromgif($imgSrc);
        } elseif (strpos($lmime, 'png') !== false) {
            $myImage = imagecreatefrompng($imgSrc);
        } else {
            $myImage = imagecreatefromjpeg($imgSrc);
        }

        if ($thumbnail_height >= $height_orig && $thumbnail_width >= $width_orig) {
            return $myImage;
        }

        $ratio_orig = $width_orig/$height_orig;

        if ($thumbnail_height==0)
        {
           $new_width = $thumbnail_width;
           $new_height = $thumbnail_height = $thumbnail_width/$ratio_orig;
        }
        elseif ($thumbnail_width)
        {
            $new_width = $thumbnail_width = $thumbnail_height*$ratio_orig;
            $new_height = $thumbnail_height;
        }
        elseif ($thumbnail_width/$thumbnail_height > $ratio_orig)
        {
           $new_height = $thumbnail_width/$ratio_orig;
           $new_width = $thumbnail_width;
        }
        elseif ($thumbnail_width/$thumbnail_height < $ratio_orig)
        {
           $new_width = $thumbnail_height*$ratio_orig;
           $new_height = $thumbnail_height;
        }
        else
        {
            $new_width = $thumbnail_width;
            $new_height = $thumbnail_height;
        }

        $x_mid = $new_width/2;  //horizontal middle
        $y_mid = $new_height/2; //vertical middle

        $process = imagecreatetruecolor(round($new_width), round($new_height));

        imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
        $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

        imagedestroy($process);
        imagedestroy($myImage);
        return $thumb;
    }

}