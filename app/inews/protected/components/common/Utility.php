<?php

//review
class Utility {
    
    /**
     * Get column specified by langAlias
     * @param object Object want to get column
     * @param key Key will be parsed
     */
    public static function t($object, $key) {
        $lang = Yii::app()->getLanguage();

        $langAlias = Yii::app()->params['langAlias'];
        $alias = isset($langAlias[$lang]) ? $langAlias[$lang] : '';
        if ($alias) {
            $key = ($alias) ? $key . '_' . $langAlias[$lang] : $key;
        }

        return $object->$key;
    }
    
    /**
     * Safe input from user
     * @param input input from user
     * @param objectType type of object will be return
     */
    public function safeInput($input, $objectType = 'string') {
        $input = trim($input);
        if ($objectType == 'int')
            return intval($input);
        else if ($objectType == 'string') {
            $input = strip_tags($input);
            return $input;
        }
    }
    
    public static function secondToTime($s) {
        $hour = floor($s / 3600);
        $minute = floor(($s - $hour * 3600) / 60);
        $second = $s - $hour * 3600 - $minute * 60;

        if ($hour == 0) {
            $hour = '';
        } else if ($hour < 10) {
            $hour = '0' . $hour . ':';
        } else {
            $hour = $hour . ':';
        }

        if ($minute == 0) {
            $minute = '00:';
        } else if ($minute < 10) {
            $minute = '0' . $minute . ':';
        } else {
            $minute = $minute . ':';
        }

        if ($second == 0) {
            $second = '00';
        } else if ($second < 10) {
            $second = '0' . $second;
        }

        return $hour . $minute . $second;
    }

    public static function timeFormat($time) {
        $datetime = date_parse($time);

        return $datetime['day'] . '/' . $datetime['month'] . '/' . $datetime['year'];
    }

    public static function truncate($string, $len = 30, $dots = TRUE) {
        $retVal = $string;

        /*
         * get current encoding:
         * "auto" is expanded to: ASCII,JIS,UTF-8,EUC-JP,SJIS
         */

        $encoding = mb_detect_encoding($string, "auto");

        // leng of string in current encoding
        $strlen = mb_strlen($string, $encoding);

        $delta = $strlen - $len;
        if ($delta > 0) {
            $shortText = "";

            // trim it by length in current encoding
            $shortText = mb_substr($string, 0, $len, $encoding);

            // find the last break word
            $breakPos = $len;
            $breakPatten = array(" ", ",", ".", ":", "_", "-", "+");
            foreach ($breakPatten as $id => $breakKey) {
                if (mb_strrpos($shortText, $breakKey, $encoding)) {
                    if ($id == "0") {
                        $breakPos = mb_strrpos($shortText, $breakKey, $encoding);
                    } else {
                        $breakPos = ( $breakPos > mb_strrpos($shortText, $breakKey, $encoding) ) ? $breakPos : mb_strrpos($shortText, $breakKey, $encoding);
                    }
                }
            }

            //remove break word
            $shortText = mb_substr($shortText, 0, $breakPos, $encoding);

            if ($dots)
                $shortText .= "...";

            $retVal = $shortText;
        }

        return $retVal;
    }

    public static function makePagingUrl($params) {
        $re = null;

        if ($params) {
            foreach ($params AS $param => $value) {
                $re .= $param . '=' . $value . '&';
            }
        }

        return $re;
    }

    public static function makeClipThumb($thumb, $link) {
        if (eregi('iphone', $_SERVER['HTTP_USER_AGENT']) || eregi('ipod', $_SERVER['HTTP_USER_AGENT'])) { //for iphone
            // iPhone
            $html = '<embed src="' . $thumb . '" href="' . $link . '" width="104" height="78" type="video/mp4" autoplay="false" target="myself" scale="1" align="left" class="thumb" controller="false" cache="true" />';
        } else {
            // Others
            $html = '<a href="' . $link . '"><img src="' . $thumb . '" width="104" height="78" alt="thumbnail" /></a>';
        }

        return $html;
    }

    public static function getFriendlyTime($time, $lang) {
        $now = date("Y-m-d H:i:s");

        $secondsToNow = round(strtotime($now) - strtotime($time));

        if ($secondsToNow < 60) {
            if ($secondsToNow > 1) {
                return $secondsToNow . ' ' . $lang['seconds_ago'];
            } else {
                return $secondsToNow . ' ' . $lang['second_ago'];
            }
        } else if ($secondsToNow >= 60 && $secondsToNow < 3600) {
            $minutes = round($secondsToNow / 60);
            if ($minutes > 1) {
                return $minutes . ' ' . $lang['minutes_ago'];
            } else {
                return $minutes . ' ' . $lang['minute_ago'];
            }
        } else if ($secondsToNow > 3600 && $secondsToNow < 86400) {
            $hours = round($secondsToNow / 3600);
            if ($hours > 1) {
                return $hours . ' ' . $lang['hours_ago'];
            } else {
                return $hours . ' ' . $lang['hour_ago'];
            }
        } else if ($secondsToNow >= 86400 && $secondsToNow < 2592000) {
            $days = round($secondsToNow / 86400);
            if ($days > 1) {
                return $days . ' ' . $lang['days_ago'];
            } else {
                return $days . ' ' . $lang['day_ago'];
            }
        } else if ($secondsToNow >= 2592000 && $secondsToNow < 31104000) {
            $months = round($secondsToNow / 2592000);
            if ($months > 1) {
                return $months . ' ' . $lang['months_ago'];
            } else {
                return $months . ' ' . $lang['month_ago'];
            }
        } else {
            $years = round($secondsToNow / 31104000);
            if ($years > 1) {
                return $years . ' ' . $lang['years_ago'];
            } else {
                return $years . ' ' . $lang['year_ago'];
            }
        }
    }

    public static function storageSolutionEncode($objID, $isDir = false) {
        $step = 15; //so bit de ma hoa ten thu muc tren 1 cap
        if ($objID >= 97657)
            $step = 10;
        $layer = 3; //so cap thu muc
        $max_bits = PHP_INT_SIZE * 8;
        $result = "";

        for ($i = $layer; $i > 0; $i--) {
            $shift = $step * $i;
            $layer_name = $shift <= $max_bits ? $objID >> $shift : 0;

            $result .= $isDir ? DS . $layer_name : "/" . $layer_name;
        }

        return $result;
    }

    public static function isIphone() {
        if (eregi('iphone', $_SERVER['HTTP_USER_AGENT']) || eregi('ipod', $_SERVER['HTTP_USER_AGENT']) || eregi('ipad', $_SERVER['HTTP_USER_AGENT']))
            return true;
        else
            return false;
    }

    public static function makeVideoThumbnailUrl($clipId) {

        $filename = $clipId . '.jpg';
        return Yii::app()->params['video']['static_img_video'] . Utility::storageSolutionEncode($clipId) . '/' . $filename;
    }
    
    public static function makeLargeVideoThumbnailUrl($clipId) {
        $filename = $clipId . '_large.jpg';
        return Yii::app()->params['video']['static_img_video'] . self::storageSolutionEncode($clipId) . '/' . $filename;
    }

    public static function makeVideoDownloadUrl($clip) {
        $clipId = $clip->ID;

        $deviceProfile = Utility::getDeviceProfile();

        if (!$deviceProfile['download_protocol']) {
            // return false;
        }

        // $protocol = $deviceProfile['download_protocol'];
        $protocol = 'rtsp';

        $streaming_server = Yii::app()->params['streaming']['server'];

        // $storage_server = $clip->DIRECTORY;
        $storage_server = '';

        $mediaPath = $storage_server . Utility::storageSolutionEncode($clipId) . '/' . $clipId;

        // $fileName = str_replace("<id>",$clipId,$deviceProfile['filename']);
        $fileName = $clipId . '.3gp';
        //$extension = isIphone() ? "mp4" : "3gp";

        $url = "$protocol://$streaming_server/$storage_server" . Utility::storageSolutionEncode($clipId) . "/" . $clipId . "/" . $fileName;

        //die($url);

        return $url;
    }

    public static function makeVideoStreamingUrl($clip) {
        $clipId = $clip->ID;

        $deviceProfile = Utility::getDeviceProfile();

        if (!$deviceProfile['streaming_protocol']) {
            // return false;
        }

        // $protocol = $deviceProfile['streaming_protocol'];
        $protocol = 'rtsp';
        $streaming_server = Yii::app()->params['video']['streaming_server'];

        // $storage_server = $clip->DIRECTORY;
        $storage_server = '';

        $mediaPath = $storage_server . Utility::storageSolutionEncode($clipId) . '/' . $clipId;

        // $fileName = str_replace("<id>",$clipId,$deviceProfile['filename']);
        $fileName = $clipId . '.3gp';

        $url = "$protocol://$streaming_server/$storage_server" . Utility::storageSolutionEncode($clipId) . "/" . $clipId . "/" . $fileName;

        return $url;
    }

    static function getDeviceProfile() {
        return null;
        require_once 'Mobile/Profile/Manager.php';

        $mobileProfileManager = new Mobile_Profile_Manager();

        $deviceId = $mobileProfileManager->getDeviceID();

        $commonDao = Vega_Dao::factory('Mobitv_Common');

        $deviceProfile = $commonDao->getDeviceProfile($deviceId);

        return $deviceProfile;
    }
    
    //FlashVars version, use to optimize request
    public static function renderPlayer($clip, $width=448, $height=372) {
        $clipId = $clip->ID;
        $protocol = "http";
        $serverAdress = isset(Yii::app()->params['video']['streaming_server']) ? Yii::app()->params['video']['streaming_server'] : '';
        $mediaPath = self::storageSolutionEncode($clipId) . '/' . $clipId;
        $extension = "mp4";
        $videoUrl = "$protocol://$serverAdress/$mediaPath/$clipId.$extension";
        $previewUrl = self::makeLargeVideoThumbnailUrl($clipId);
        
        $html = "<script type='text/javascript' src='/js/admin/swfobject.js'></script>
        <script type=\"text/javascript\">
            swfobject.registerObject(\"player\",\"9.0.98\",\"/swf/expressInstall.swf\");
        </script>

        <object type=\"application/x-shockwave-flash\" data=\"/swf/player.swf\" width=\"$width\" height=\"$height\">
            <param name=\"movie\" value=\"/swf/player-viral.swf\" />
            <param name=\"allowfullscreen\" value=\"true\" />
            <param name=\"allowscriptaccess\" value=\"always\" />
            <param name=\"wmode\" value=\"transparent\" />
            <param name=\"flashvars\" value=\"file=$videoUrl&image=$previewUrl&skin=/swf/modieus.swf\" />
            <p><a href=\"http://get.adobe.com/flashplayer\">Get Flash</a> to see this player.</p>
        </object>";
        
        // $html = '<iframe width="480" height="360" src="http://www.youtube.com/embed/XWCwc1_sYMY" frameborder="0" allowfullscreen></iframe>';
        
        return $html;
    }
    
    public static function getThumbnailList($clip)
    {
        $clipId = $clip->ID;
        $thumbs = array();

        $baseDir  = Yii::app()->params['video']['static_img_video'] . Utility::storageSolutionEncode($clipId);
        $baseURL  = Yii::app()->params['video']['static_img_video'] . Utility::storageSolutionEncode($clipId) . '/' . $clipId;

            for($i=0; $i <= 9; $i++)
            {
                $thumbnailDir   = $baseDir.DS.$clipId."-000".$i.".jpg";
                $thumbnailURL   = "$baseURL/".$clipId."-000".$i.".jpg";
                $thumb['name']  = $clipId."-000".$i.".jpg";
                $thumb['url']   = $thumbnailURL;

                $thumbs[] = $thumb;
            }

        return $thumbs;
    }
    
    public static function changeClipThumbnail($clipId, $newThumbName)
    {
        $baseDir  = Yii::app()->params['video']['data_path'] . DS . Yii::app()->params['video']['static_dir'] . self::storageSolutionEncode($clipId);
        $fileSource     = $baseDir . DS . $clipId . DS . $newThumbName;
        $fileDest       = $baseDir . DS . $clipId . '.jpg';
        $fileDestLarge  = $baseDir . DS . $clipId . '_large.jpg';
        
        if(!self::copyAndResizeImage($fileSource, $fileDest, 128, 96))
        {
            return false;
        }
        return self::copyAndResizeImage($fileSource, $fileDestLarge, 0, 0);
    }
    
    /****************************************************************
     * Function: copyAndResizeImage - Copy and Resize image file
     * @param String	- Name of file source
     * @param String	- Name of file dest
     * @param Integer	- Size of file dest in width
     * @param Integer	- Size of file dest in height
     * @return Bool		- True or False, true if success
     ****************************************************************/
    public static function copyAndResizeImage($fileSource, $fileDest, $newWidth=0, $newHeight=0)
    {
        if(!file_exists($fileSource))
        {
            return false;
        }
        $sourceFileInfo = getimagesize($fileSource);
        $width 	= $sourceFileInfo[0];
        $height = $sourceFileInfo[1];
        $mime 	= $sourceFileInfo["mime"];

        if(!$newWidth) $newWidth = $width;
        if(!$newHeight) $newHeight = $height;

        $xR 	= $newWidth/$width;
        $yR 	= $newHeight/$height;
        if ($xR < $yR)
        {
            $newHeight = floor($height*$xR);
        }
        elseif ($xR > $yR)
        {
            $newWidth = floor($width*$yR);
        }

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        switch ($mime)
        {
            case "image/jpeg":
                $source = imagecreatefromjpeg($fileSource);
                break;
            case "image/gif":
                $source = imagecreatefromgif($fileSource);
                break;
            case "image/png":
                $source = imagecreatefrompng($fileSource);
                break;
        }

        // Resize
        if(!imagecopyresized($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height))
        {
            return false;
        }

        // Output
        if(!imagejpeg($thumbnail, $fileDest))	return false;
        if(!imagedestroy($source))				return false;
        return true;
    }

    // in a helper file
    public static function yiiParam($name, $default = null) {
        if (isset(Yii::app()->params[$name]))
            return Yii::app()->params[$name];
        else
            return $default;
    }

     public static function logSMS($data) {

    }

      public static function logChargingException($data) {

    }

      public static function logChargingResult($data) {
          //luu ra file
          //luu ra db
      }


   
}

?>