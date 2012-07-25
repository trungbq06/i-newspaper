<?php
Yii::import('application.components.common.*');
class Crawler {

    private $_curl = null;
    private $_parser = null;

    public function __construct() {
        $this->_curl = curl_init();
        $this->_parser = new Videopian();
    }

    public function __destruct() {
        if (!is_null($this->_curl))
            curl_close($this->_curl);
    }

    //Get content of one Url
    public function getURLContents($url) {
		// $strCookie = 'PHPSESSID=3k2rndan640vsv7qd8ahheo8r7; path=/';
 
		// session_write_close();
		
        $ch = curl_init();
		$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
		// curl_setopt($ch, CURLOPT_COOKIE, $strCookie );
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
    }

    //Get content with fake user-agent
    public function getURLContentWithFake($url, $useragent) {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $useragent,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 30,
        );
        curl_setopt_array($this->_curl, $options);

        $contents = curl_exec($this->_curl);

        return $contents;
    }

    //Post content one Url
    public function postURLContents($url, $params) {        
		foreach ( $params as $key => $value)
		{
			$post_items[] = $key . '=' . $value;
		}
		$paramsStr = implode ('&', $post_items);
        $options = array(
            CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => count($params),
            CURLOPT_POSTFIELDS => $paramsStr
        );
        curl_setopt_array($this->_curl, $options);
        $contents = curl_exec($this->_curl);

        return $contents;
    }

    /*
     * Get content between open and close tag
     * getFirst = true will return the first occurrence
     */

    public function getContent($content, $open, $close, $getFirst = false, $noTrim = false) {
		if (empty($content)) return false;
        $found = true;
        $result = array();
        $index = 0;
        $i = 0;

        while ($found) {
            $found = false;
            $start = stripos($content, $open, $index);
            if ($start !== false) {
                $end = stripos($content, $close, $start + strlen($open));
                if ($end!='') {
                    $index = $end + strlen($close);
                    $found = true;
                    $tmp = substr($content, $start + strlen($open), $end - $start - strlen($open));
                    $result[$i] = $this->removeExtraSpaces($tmp);
                    if ($getFirst)
                        return $noTrim ? $result[0] : trim($result[0]);
                    $i++;
                } else return '';
            }
        }

        return $result;
//        return ($getFirst) ? $result[0] : $result;
    }

    public function isUrlExists($url) {
        // Version 4.x supported
        $handle = curl_init($url);
        if (false === $handle) {
            return false;
        }
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
        curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15")); // request as if Firefox
        // curl_setopt($handle, CURLOPT_HTTPHEADER, Array("Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16")); // request as if Firefox
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
        $connectable = curl_exec($handle);
        curl_close($handle);
        return $connectable;
    }

    //Get all content using regex matches
    public function getAllMatches($regex, $content) {
        preg_match_all($regex, $content, $matches);
        return $maches;
    }

    public function removeExtraSpaces($content) {
        $content = str_replace("\t", "", $content);
        $content = str_replace("\r", "", $content);
        $content = str_replace("&nbsp;", "", $content);
        return $content;
    }

    //Clear all html tag in text
    public function clearHTMLTags($content) {
        while (true) {
            $open = stripos($content, "<");
            if ($open === false)
                break;
            $temp = substr($content, $open + 1, strlen($content) - 1);
            $close = stripos($temp, ">");
            $temp = substr($temp, 0, $close);
            $content = str_replace("<" . $temp . ">", "", $content);
        }

        return $this->removeExtraSpaces(str_replace("-->", "", $content));
    }
	
    /**
    * strip content in open - close tag
    * @param true strip content with open - content - close
    */
	public function stripContent($content, $open, $close, $all = false) {
		/*
		if (empty($content)) return '';
		
		$toDel = $this->getContent($content, $open, $close, true, true);
        // echo 'Todel = ' . $toDel. ' <br/>';
        if ($all) {
            $toDel = $open . $toDel . $close;
            // echo $toDel.'<br/>';
        }
		$content = str_replace($toDel, '', $content);
		*/
		$open = str_replace('<', '\<', $open);
		$open = str_replace('>', '\>', $open);
		$open = str_replace('/', '\/', $open);
		$open = str_replace('"', '\"', $open);
		
		$close = str_replace('<', '\<', $close);
		$close = str_replace('>', '\>', $close);
		$close = str_replace('/', '\/', $close);
		$close = str_replace('"', '\"', $close);
		
		// die('/(' . $open . ')(\w+)(\d+)(' . $close . ')/');
		$content = preg_replace('/(' . $open . ')(.*)(' . $close . ')/', '', $content);
		// $content = preg_replace('/(\<script)(.*)(\<\/script\>)/', '', $content);
		
		return $content;
	}
	
	public function getLotteryCity() {
		$content = $this->getURLContents('http://comment.dantri.com.vn/defaultkqsx.aspx');
		$content = $this->getContent($content, 'id="UtilitySonPCKQSX_ddlCity">', '</select>', true);
		$content = $this->getContent($content, 'value="', '"');
		
		foreach ($content as $c) {
			$city = new UtilityLotteryCity();
			$city->name = $c;
			$city->save();
		}
	}
	
	public function getTvChannel() {
		$content = $this->getURLContents('http://itv.vn/Lichphatsong.aspx');
		$content = $this->getContent($content, 'background-color:#C9C9C9;">', '</select>', true);
		$content = $this->getContent($content, '<option', 'option');
		
		foreach ($content as $c) {
			$channel = new UtilityTvChannel();
			$channel->name = $this->getContent($c, '>', '</', true);
			$channel->id = $this->getContent($c, '"', '"', true);
			$channel->save();
		}
	}
	
	public function getTvCalendar() {
		$url = 'http://itv.vn/Lichphatsong.aspx';
		$channel = UtilityTvChannel::model()->findAll();
		$params = array(
			'hdnChannel' => 2,
			'hdnDate' => date('d/m/Y'),
			'hdnIsAjax' => 0
		);
		foreach ($channel as $c) {
			$params['hdnChannel'] = $c->id;
			$content = $this->postURLContents($url, $params);
			// echo $content;
			$data = '<table  cellpadding="0" cellspacing="0" border="0" bordercolor="#C3C3C3">';
		    $data .= '<tr><td style="width:60px;height:19px;padding-left:5px;" align="left" class="Schedule_Text2">Giờ</td>';
		    $data .= '<td style="width:253px;height:19px;" align="left" class="Schedule_Text2">Chương trình</td></tr>';
			$times = $this->getContent($content, 'Times="', '"');
			$programs = $this->getContent($content, 'Programs="', '"');
			for ($i = 0;$i < count($times);$i++) {
				$data .= '<tr><td>' . $times[$i] . '</td><td>' . $programs[$i] . '</td></tr>';
			}
			$data .= '</table>';
			$day = date('Y-m-d');
			if (!UtilityTvCalendar::model()->isExist($c->id, $day)) {
				$tvCalendar = new UtilityTvCalendar;
				$tvCalendar->created_day = $day;
				$tvCalendar->channel_id = $c->id;
				$tvCalendar->updated_time = date('Y-m-d H:i:s');
				$tvCalendar->content = str_replace('<table', '<table width="100%"', $data);
				$tvCalendar->save();
			}
		}
		
	}
	
	public function getCinemaSchedule() {
		$url = 'http://comment.dantri.com.vn/defaultcinema.aspx';
		$content = $this->getURLContents($url);
		$day = $this->getContent($content, 'UtilitySonPCCinemaes_lblDate">', '</span>', true);
		$day = explode('/', $day);
		$day = $day[2] . '-' . $day[1] . '-' . $day[0];
		
		$detail = $this->getContent($content, '<div id="content0">', '</div>', true);
		// echo $detail;
		if (!UtilityCinemaSchedule::model()->findByPk($day)) {
			$schedule = new UtilityCinemaSchedule;
			$schedule->created_day = $day;
			$schedule->content = $detail;
			$schedule->save();
		}
	}
	
	public function getExchange() {
		$url = 'http://www.taichinhdientu.vn/services/infopage.aspx?svc=exchangerates';
		$content = $this->getURLContents($url);
		$day = date('Y-m-d');
		$temp = $this->getContent($content, '<div id="exchange">', '</div>', true);
		$temp = strip_tags($temp, '<table><thead><tbody><th><tr><td>');
		$check = UtilityExchange::model()->findByAttributes(array('day' => $day));
		if (empty($check)) {
			$exchange = new UtilityExchange;
			$exchange->day = $day;
			$exchange->content = str_replace('<table', '<table width="100%"', $temp);
			$exchange->save(false);
		}
	}
	
	public function getGold() {
		$url = 'http://www.taichinhdientu.vn/services/infopage.aspx?svc=goldrates';
		$content = $this->getURLContents($url);
		$day = date('Y-m-d');
		$temp = $this->getContent($content, '<div id="gold_price">', '</div>', true);
		$temp = strip_tags($temp, '<table><thead><tbody><th><tr><td>');
		$check = UtilityGold::model()->findByAttributes(array('day' => $day));
		if (empty($check)) {
			$exchange = new UtilityGold;
			$exchange->day = $day;
			$exchange->content = str_replace('<table', '<table width="100%"', $temp);
			$exchange->save(false);
		}
	}
	
	public function getOil() {
		$url = 'http://www.taichinhdientu.vn/services/infopage.aspx?svc=oilrates';
		$content = $this->getURLContents($url);
		$day = date('Y-m-d');
		$temp = $this->getContent($content, '<div id="gold_price">', '</div>', true);
		$temp = strip_tags($temp, '<table><thead><tbody><th><tr><td>');
		$check = UtilityOil::model()->findByAttributes(array('day' => $day));
		if (empty($check)) {
			$exchange = new UtilityOil;
			$exchange->day = $day;
			$exchange->content = str_replace('<table', '<table width="100%"', $temp);
			$exchange->save(false);
		}
	}
	
	public function getWeather() {
		$url = 'http://vov.vn/Services/Infopage.aspx?svc=weathers';
		$content = $this->getURLContents($url);
		$day = date('Y-m-d');
		$temp = $this->getContent($content, '<div id="weather">', '</div>', true);
		$temp = strip_tags($temp, '<table><thead><tbody><th><tr><td><img>');
		$check = UtilityWeather::model()->findByAttributes(array('day' => $day));
		if (empty($check)) {
			$exchange = new UtilityWeather;
			$exchange->day = $day;
			$exchange->content = str_replace('<table', '<table width="100%"', $temp);
			$exchange->save(false);
		}
	}
	
	public function getLottery() {
		$url = 'http://ketquaday.vn/';
		$content = $this->getURLContents($url);
		$city = $this->getContent($content, '<div class="main_table', '</div>');
		$results = $this->getContent($content, '<table border="0" width="100%" align="center">', '</table>');
		$regions = $this->getContent($content, '<h2>', '</h2>');
		$main = $this->getContent($content, '<div class="main_table">', '</div>', true);
		$day = $this->getContent($main, ',', '</b>', true);
		$day = explode('/', $day);
		$day = $day[2] . '-' . $day[1] . '-' . $day[0];

		$i = 0;
		foreach ($city as $c) {
			$name = $this->getContent($c, '<span class="txt_name">', '</span>', true);
			$r = '<table width="100%">' . $results[$i] . '</table></td></tr></table>';
			$lottery = new UtilityLottery;
			$lottery->created_day = $day;
			if ($regions[$i] == 'kết quả xổ số miền bắc') {
				$name = 'Miền Bắc';
			}
			
			$cityDetail = UtilityLotteryCity::model()->findByAttributes(array('name' => trim($name)));
			$cityId = $cityDetail->id;
			if (!UtilityLottery::model()->isExist($cityId, $day)) {
				$lottery->city_id = $cityId;
				$lottery->content = $r;
				$lottery->save();
			}
			
			$i++;
		}
	}
    
    function save_image($img,$fullpath){
        $ch = curl_init ($img);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($fullpath)){
            unlink($fullpath);
        }
        $fp = fopen($fullpath,'x');
        fwrite($fp, $rawdata);
        fclose($fp);
    }
    
    public function getKenh14() {
        $url = 'http://kenh14.vn/thoi-trang.chn';
        $contents = $this->getURLContents($url);
        $featured = $this->getContent($contents, '<div class="featurewrapper', '<div class="listnews">', true);
        echo $featured;
    }
	
	public function getComic() {
		// $url = 'http://vechai.info/search/';
		// $contents = $this->getURLContents($url);
		// die($contents);
		// $comics = $this->getContent($contents, 'tbl_body"><a', 'a>');
		// print_r($comics);die();
		// $comics = implode('~', $comics);
		$f = fopen('/srv/www/i-newspaper/comics.txt', 'r');
		// $f = fopen('/comics.txt', 'r');
		$comics = fgets($f);
		// echo $comics;die();
		$comics = explode('~', $comics);
		$k = 0;
		// fwrite($f, $comics);die();
		foreach ($comics as $one) {
			$k++;
			// echo $one;die();
			$link = $this->getContent($one, '="', '"', true);
			$comicTitle = $this->getContent($one, '">', '</', true);
			$comicTitle = trim($comicTitle);
			// $link = 'http://vechai.info/angel-beats-heavens-door/';
			// var_dump($comicTitle);die();
			// var_dump(Comic::model()->isExist($comicTitle));die();
			$exist = Comic::model()->isExist($comicTitle);
			// $exist = false;
			if (!$exist) {
				// die($comicTitle);
				$comic = new Comic;
				$comic->created_time = date('Y-m-d H:i:s');
				$comic->title = $comicTitle;
				$comic->title_vn = Utility::unicode2Anscii($comicTitle);
				$comic->description = '';
				
				// $link = 'http://vechai.info/bac-dau-than-quyen-tien-truyen/';
				$detail = $this->getURLContents($link);
				// die($detail);
				$textarea = $this->getContent($detail, '<textarea', '</textarea', true);
				// die($textarea);
				$chapLink = $this->getContent($textarea, 'id="vcfix"', '<div align="center">', true);
				$thumbnailUrl = $this->getContent($chapLink, "<img src='", "'", true);
				if (empty($thumbnailUrl)) 
					$thumbnailUrl = $this->getContent($chapLink, "<img src=\"", "\"", true);
				// die($thumbnailUrl);
				$comic->thumbnail_url = $thumbnailUrl;				
				
				$chapLink = $this->getContent($chapLink, 'a href', '/a>');				
				// print_r($chapLink);die();
				if (empty($chapLink)) continue;
				
				$comic->save(false);
				$comicId = $comic->id;
				$i = 0;
				if (!empty($chapLink)) {
					foreach ($chapLink as $chap) {
						// $chap = '"http://doctruyen.vechai.info/07-ghost-chap-72/" target="_blank"><span style="font-size: 12px;"><span style="color: #DC143C;">CHAP 72</span></span><';
						$chap = strip_tags($chap);
						$chap .= '~';
						// echo $chap;die();
						$cLink = $this->getContent($chap, '"', '"', true);
						$title = $this->getContent($chap, '">', '~', true);
						
						if (!strstr(strtolower($title), 'chap')) continue;
						if (strstr(strtolower($title), 'download')) continue;
						$title = trim($title);
						$title = ucwords($title);
						$chapter = new ComicChapter;
						$chapter->comic_id = $comicId;
						$chapter->title = $title;
						$chapter->created_time = date('Y-m-d H:i:s');
						$chapter->save(false);
						$chapterId = $chapter->id;
						
						// die($cLink);
						// $cLink = 'http://vc2.vechai.info/2011/04/anh-hung-vo-le-chap-5.html';
						$chapDetail = $this->getURLContents($cLink);
						// die($chapDetail);
						$chapImage = $this->getContent($chapDetail, '<div class="entry2">', '<h2', true);
						if (empty($chapImage)) {
							$chapImage = $this->getContent($chapDetail, "<textarea id=", '</textarea>', true);
						}
						// echo $chapImage;die();
						$imgList = $this->getContent($chapImage, '<img src="', '"');
						// print_r($chapImage);die();
						if (empty($imgList)) 
							$imgList = $this->getContent($chapImage, "<img src='", "'");
						if (empty($imgList))
							$imgList = $this->getContent($chapImage, 'src="', '"');
						if (empty($imgList))
							$imgList = $this->getContent($chapImage, "src='", "'");
						// var_dump($imgList);die();
						// Insert image to comic_image						
						if (!empty($imgList)) {
							foreach($imgList as $cImage) {
								$i++;
								// $cImage = $this->getContent($one, '"', '"', true);
								// if (empty($cImage))
									// $cImage = $this->getContent($one, '\'', '\'', true);
								// echo $cImage;die();
								$image = new ComicImage;
								$image->chapter_id = $chapterId;
								$image->image = $cImage;
								$image->created_time = date('Y-m-d H:i:s');
								$image->save(false);
							}
						}
					}
				}
				if ($i > 0) {
					$comic->approved = 1;
					$comic->save(false);
				}
			}
			// die();
			if ($k > 24) die('Finished');
		}
	}
	
	public function getWallpaper2() {
		$categories = WallpaperCategory::model()->findAll();
		foreach ($categories as $cat) {
			$url = "http://www.hdiphonewallpaper.com" . $cat->link;
			$contents = $this->getURLContents($url);
			$page = $this->getContent($contents, 'class=pages>', '</a>', true);
			$pageId = $this->getContent($page, "href='", '2.html', true);
			// echo $pageId;die();
			for ($i = 1;$i < 20;$i++) {
				$url = "http://www.hdiphonewallpaper.com" . $cat->link . '/' . $pageId . $i . '.html';
				echo $url;
				$contents = $this->getURLContents($url);
				// echo $contents;die();
				$items = $this->getContent($contents, '<td height="315" align="center">', 'border=\'0\'');
				// print_r($items);die();
				// echo '<img src="http://25.media.tumblr.com/tumblr_m0g7gkQmPl1qbd81ro1_500.jpg" width="160" height="107" />';
				// echo '<img src="http://26.media.tumblr.com/tumblr_m0dgtj4ELf1qbd81ro1_500.jpg" width="160" height="204" />';
				// echo '<img src="http://27.media.tumblr.com/tumblr_m0g7h0QIhE1qbd81ro1_500.jpg" width="160" height="204" />';die();
				if (!empty($items)) {
					foreach ($items as $item) {
						$detailLink = 'http://www.hdiphonewallpaper.com' . $this->getContent($item, "href='", "'>", true);
						$data['title'] = date('YmdHis');
						$data['category_id'] = $cat->id;
						$data['small_thumbnail_url'] = 'http://www.hdiphonewallpaper.com' . $this->getContent($item, "src='", "'", true);
						// echo $data['small_thumbnail_url'];die();
						// var_dump(pathinfo($data['thumbnail_url']));die();
						if (!News::isWallpaperExist($data['small_thumbnail_url'])) {
							$detail = $this->getURLContents($detailLink);
							$data['thumbnail_url'] = $this->getContent($detail, 'border="0" src="', '"', true);
							// echo $detailLink;
							// print_r($data);die();
							if (empty($data['thumbnail_url'])) continue;
							$path = '/tmp/save_img_xkcn_tmp.test';
							$pathFull = '/tmp/save_img_xkcn_tmp.testfull';
							$this->save_image($data['small_thumbnail_url'], $path);
							$this->save_image($data['thumbnail_url'], $pathFull);
							$imgSize = getimagesize($path);
							$imgSizeFull = getimagesize($pathFull);
							exec('rm -f ' . $path);
							$width320 = $imgSizeFull[0] > 320 ? 320 : $imgSizeFull[0];
							$height320 = ($width320 * $imgSizeFull[1]) / $imgSizeFull[0];							
							$photo = new Wallpaper;
							$photo->attributes = $data;
							$photo->width_160 = 160;
							$photo->height_160 = (160 * $imgSize[1]) / $imgSize[0];
							$photo->width_320 = $width320;
							$photo->height_320 = $height320;
							$photo->created_time = date('Y-m-d H:i:s');
							$photo->save(false);
						}
					}
				}
			}
		}
	}
	
	public function getWallpaper() {
		$categories = array('architecture', 'abstract', 'flowers', 'fooddrinks', 'funny');
		foreach ($categories as $cat) {
			for ($i = 1;$i < 200;$i++) {
				$url = "http://wallpaper.heaveniphone.com/index.php?no=$i&category=$cat";
				$contents = $this->getURLContents($url);
				// echo $contents;die();
				$items = $this->getContent($contents, '<img  class="link', 'border');
				// print_r($items);die();
				// echo '<img src="http://25.media.tumblr.com/tumblr_m0g7gkQmPl1qbd81ro1_500.jpg" width="160" height="107" />';
				// echo '<img src="http://26.media.tumblr.com/tumblr_m0dgtj4ELf1qbd81ro1_500.jpg" width="160" height="204" />';
				// echo '<img src="http://27.media.tumblr.com/tumblr_m0g7h0QIhE1qbd81ro1_500.jpg" width="160" height="204" />';die();
				foreach ($items as $item) {
					$data['title'] = date('YmdHis');
					$data['small_thumbnail_url'] = 'http://wallpaper.heaveniphone.com/' . $this->getContent($item, 'src="', '"', true);
					$data['thumbnail_url'] = str_replace('thumbs/', '', $data['small_thumbnail_url']);
					// echo $data['thumbnail_url'];die();
					// var_dump(pathinfo($data['thumbnail_url']));die();
					if (!News::isWallpaperExist($data['thumbnail_url'])) {
						$path = '/tmp/save_img_xkcn_tmp.test';
						$pathFull = '/tmp/save_img_xkcn_tmp.testfull';
						$this->save_image($data['small_thumbnail_url'], $path);
						$this->save_image($data['thumbnail_url'], $pathFull);
						$imgSize = getimagesize($path);
						$imgSizeFull = getimagesize($pathFull);
						exec('rm -f ' . $path);
						$width320 = $imgSizeFull[0] > 320 ? 320 : $imgSizeFull[0];
						$height320 = ($width320 * $imgSizeFull[1]) / $imgSizeFull[0];
						$photo = new Wallpaper;
						$photo->attributes = $data;
						$photo->width_160 = 160;
						$photo->height_160 = (160 * $imgSize[1]) / $imgSize[0];
						$photo->width_320 = $width320;
						$photo->height_320 = $height320;
						$photo->created_time = date('Y-m-d H:i:s');
						$photo->save(false);
					}
				}
			}
		}
	}
	
	public function getXKCN() {
		$url = 'http://feeds.feedburner.com/xkcn';
		$contents = $this->getURLContents($url);
		$items = $this->getContent($contents, '<item>', '</item>');
		// echo '<img src="http://25.media.tumblr.com/tumblr_m0g7gkQmPl1qbd81ro1_500.jpg" width="160" height="107" />';
		// echo '<img src="http://26.media.tumblr.com/tumblr_m0dgtj4ELf1qbd81ro1_500.jpg" width="160" height="204" />';
		// echo '<img src="http://27.media.tumblr.com/tumblr_m0g7h0QIhE1qbd81ro1_500.jpg" width="160" height="204" />';die();
		foreach ($items as $item) {
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$data['thumbnail_url'] = $this->getContent($item, 'img src="', '"', true);
            $data['small_thumbnail_url'] = str_replace('_500', '_250', $data['thumbnail_url']);
            // echo $data['thumbnail_url'];
            // var_dump(pathinfo($data['thumbnail_url']));die();
			if (!News::isXKCNExist($data['thumbnail_url'])) {
				$path = '/tmp/save_img_xkcn_tmp.test';
                $pathFull = '/tmp/save_img_xkcn_tmp.testfull';
				$this->save_image($data['small_thumbnail_url'], $path);
				$this->save_image($data['thumbnail_url'], $pathFull);
				$imgSize = getimagesize($path);
				$imgSizeFull = getimagesize($pathFull);
				exec('rm -f ' . $path);
                $width320 = $imgSizeFull[0] > 320 ? 320 : $imgSizeFull[0];
                $height320 = ($width320 * $imgSizeFull[1]) / $imgSizeFull[0];
				$photo = new Xkcn;
				$photo->attributes = $data;
				$photo->width_160 = 160;
				$photo->height_160 = (160 * $imgSize[1]) / $imgSize[0];
				$photo->width_320 = $width320;
				$photo->height_320 = $height320;
				$photo->created_time = date('Y-m-d H:i:s');
				$photo->save(false);
			}
		}
	}
	
	public function getXKCNFull() {
        for ($i = 279;$i < 400;$i++) {
            if ($i == 1) 
                $url = 'http://xkcn.info/';
            else $url = 'http://xkcn.info/page/' . $i;
            $contents = $this->getURLContents($url);
            $items = $this->getContent($contents, "<div class='photo-wrap'>", '</div>');
            // print_r($items);die();
            // echo '<img src="http://25.media.tumblr.com/tumblr_m0g7gkQmPl1qbd81ro1_500.jpg" width="160" height="107" />';
            // echo '<img src="http://26.media.tumblr.com/tumblr_m0dgtj4ELf1qbd81ro1_500.jpg" width="160" height="204" />';
            // echo '<img src="http://27.media.tumblr.com/tumblr_m0g7h0QIhE1qbd81ro1_500.jpg" width="160" height="204" />';die();
            foreach ($items as $item) {
                $data['title'] = 'Photo';
                $data['thumbnail_url'] = $this->getContent($item, 'img src=\'', '\'', true);
                $data['small_thumbnail_url'] = str_replace('_500', '_250', $data['thumbnail_url']);
                // echo $data['thumbnail_url'];
                // var_dump(pathinfo($data['thumbnail_url']));die();
                if (!News::isXKCNExist($data['thumbnail_url'])) {
                    $path = '/tmp/save_img_xkcn_tmp.test';
                    $this->save_image($data['small_thumbnail_url'], $path);
                    $imgSize = getimagesize($path);
                    exec('rm -f ' . $path);
                    $photo = new Xkcn;
                    $photo->attributes = $data;
                    $photo->width_160 = 160;
                    $photo->height_160 = (160 * $imgSize[1]) / $imgSize[0];
                    $photo->width_320 = 320;
                    $photo->height_320 = (320 * $imgSize[1]) / $imgSize[0];
                    $photo->created_time = date('Y-m-d H:i:s');
                    $photo->save(false);
                }
            }
        }
	}
	
	public function getITVietPhoto() {
		for ($page = 1;$page < 100;$page++) {
			if ($page == 1) $url = 'http://www.itviet.vn/thu-vien-anh';
			else $url = 'http://www.itviet.vn/thu-vien-anh/page/' . $page;
			// echo $url;die();
			$contents = $this->getURLContents($url);
			$contents = $this->getContent($contents, '<div id="colorcontentwrap4">', '<div class="cb">');
			foreach ($contents as $one) {
				$data['title'] = $this->getContent($one, 'rel="bookmark">', '<', true);
				$link = $this->getContent($one, 'href="', '"', true);
				$data['thumbnail_url'] = $this->getContent($one, 'src="', '"', true);
				$data['description'] = $this->getContent($one, '<p>', '</p>', true);
				$data['original_url'] = $link;
				$data['created_time'] = date('Y-m-d H:i:s');
				$detail = $this->getURLContents($link);
				$detail = $this->getContent($detail, '<div id="VietAd">', "<span class='st_fblike_hcount'>", true);
				$thumbnails = $this->getContent($detail, 'src="', '"');
				if (!News::model()->isPhotoExist($link)) {
					$photo = new Photo;
					$photo->attributes = $data;
					if ($photo->save(false)) {
						foreach ($thumbnails as $thumb) {
							if (strstr($thumb, 'source-news')) continue;
							$photoDetail = new PhotoDetail;
							$photoDetail->photo_id = $photo->id;
							$photoDetail->created_time = date('Y-m-d H:i:s');
							$photoDetail->thumbnail_url = $thumb;
							$photoDetail->save(false);
						}
					}
				}
			}
		}
	}
	
	public function getVietbao() {
		// echo $this->stripContent('<link rel="stylesheet" href="http://vietbao.vn/images/v2011/css/style20120112.css" type="text/css"  media="all" />
        // <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>     
        // <script type="text/javascript" src="http://vietbao.vn/images/v2011/jscripts/script20120112.js"></script>', '<script', '</script>');
		// die();
		$link = 'http://vietbao.vn/rss2/trang-nhat.rss';
		$link = 'http://vietbao.vn/live/Bong-da/rss.xml';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
        $siteId = 21;
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			$item = str_replace('&quot;', '"', $item);
			// echo $item;die();
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$data['headline'] = $this->getContent($item, '<description>', '</description>', true);
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
            $data['thumbnail_url'] = $this->getContent($item, 'src="', '"', true);
			if (empty($data['thumbnail_url'])) $data['thumbnail_url'] = '';
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
			$detail = $this->getURLContents($detailLink);
            // echo $detail;die();
            $newsContent = $this->getContent($detail, '<div class="content">', '<script type="text/javascript">', true);
			if (empty($newsContent)) {
				$newsContent = $this->getContent($detail, '<div class="box_detail">', '<div id="VnmVote">', true);
			}
			if (empty($newsContent)) {
				$newsContent = $this->getContent($detail, '<div class="box_detail">', '<div style="margin-top:5px;margin-bottom:5px;">', true);
			}
			if (empty($newsContent)) {
				$newsContent = $this->getContent($detail, '<div class="content">', '<div style="margin-top:5px;margin-bottom:5px;">', true);
			}
			if (empty($newsContent)) {
				$newsContent = $this->getContent($detail, '<div class="detailCT">', '<div class="author">', true);
			}
			if (empty($newsContent)) {
				$newsContent = $this->getContent($detail, '<div class="story-title">', '<script type="text/javascript">', true);
			}
			$newsContent = $this->stripContent($newsContent, '<div class="postby clearfix">', '</script>');
			$newsContent = $this->stripContent($newsContent, '<script', '</script>');			
			// $newsContent = $this->stripContent($newsContent, '<script', '</script>');			
			// die($newsContent);
			
            // echo $data['thumbnail_url'];die();
            // echo $newsContent;die();
            // echo $thumbnail;die();
            // echo $detail;die();
			// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
            // print_r($data);die();
			if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
				$data['content'] = $newsContent;
                $data['title_en'] = Utility::unicode2Anscii($data['title']);
                $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
				$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                $data['site_id'] = $siteId;
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				
				$news = new News;
				$news->attributes = $data;
				if ($news->save(false)) {
					
				}
			} else echo $detailLink;
            // die();
		}
	}
	
	public function getBaomoi() {
		$link = 'http://2sao.vn/rss/trangchu.rss';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
        $siteId = 9;
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			// echo $item;die();
			$title = $this->getContent($item, '<title>', '</title>', true);
            $data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
			$headline = $this->getContent($item, '<description>', '</description>', true);
            $data['headline'] = trim(strip_tags($this->getContent($headline, '<![CDATA[', ']]>', true)));
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
            $detailLink = trim($this->getContent($detailLink, '<![CDATA[', ']]>', true));
            $detailLink = str_replace('2sao.vietnamnet.vn', '2sao.vn', $detailLink);
			$detail = $this->getURLContents($detailLink);
            // echo $detail;die();
            $newsContent = $this->getContent($detail, 'class="detail_content">', '<div class="sharefacebook">', true);
            if (empty($newsContent)) {
                $newsContent = $this->getContent($detail, '<div class="content">', '<div style="margin-bottom: 10px;">', true);
            }
            $data['thumbnail_url'] = $this->getContent($headline, "src='", "'", true);
            // echo $data['thumbnail_url'];die();
            // echo $newsContent;die();
            // echo $thumbnail;die();
            // echo $detail;die();
			// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
            // print_r($data);die();
			if (!News::isExist($siteId, $detailLink)) {
				$data['content'] = $newsContent;
                $data['title_en'] = Utility::unicode2Anscii($data['title']);
                $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
				$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                $data['site_id'] = $siteId;
                // die($data['published_time']);
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				
				$news = new News;
				$news->attributes = $data;
				if ($news->save(false)) {
					
				}
			}
            // die();
		}
	}
	
	public function getBongda() {
		$link = 'http://www.bongda.com.vn/Rss/';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
        $siteId = 20;
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			// echo $item;die();
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$data['headline'] = $this->getContent($item, '<description>', '</description>', true);
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
            $data['thumbnail_url'] = '';
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
            // echo $detailLink;
			$detail = $this->getURLContents($detailLink);
            // echo $detail;die();
            $newsContent = '';
            if (strstr($detailLink, 'Thu-vien-Video')) {
                $newsContent = $this->getContent($detail, "<div id='clipView'>", '</div>', true);
                $newsContent = $this->getContent($newsContent, 'src="', '"', true);
                $data['youtube_video'] = 1;
                $data['headline'] = '';
            } else {
                $newsContent = $this->getContent($detail, 'class="read_news">', '<div id="ctl00_BD_art_pnlNewsInfo"', true);
                $thumbnails = $this->getContent($newsContent, 'src="', '"');
                // var_dump($thumbnails);die();
                if (!empty($thumbnails)) {
                    foreach ($thumbnails as $one) {
                        // echo $one;
                        $path = '/tmp/save_img_tmp.test';
                        $this->save_image($one, $path);
                        $imgSize = getimagesize($path);
                        // echo $imgSize[0];die();
                        if ($imgSize[0] > 100 && $imgSize[1] > 100) {
                            $data['thumbnail_url'] = $one;
                            break;
                        }
                    }
                }
                // echo $newsContent;die();
            }
            // echo $data['thumbnail_url'];die();
            // echo $newsContent;die();
            // echo $thumbnail;die();
            // echo $detail;die();
			// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
            // print_r($data);die();
			if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
				$data['content'] = $newsContent;
                $data['title_en'] = Utility::unicode2Anscii($data['title']);
                $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
				$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                $data['site_id'] = $siteId;
                // die($data['published_time']);
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				
				$news = new News;
				$news->attributes = $data;
				if ($news->save(false)) {
					
				}
			}
            // die();
		}
	}
	
	public function getYahoonews() {
		$yahoo = Yii::app()->params['site']['yahoo'];
		$siteId = 22;
		
		foreach ($yahoo as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$data['title'] = $this->getContent($item, '<title>', '</title>', true);
				$data['headline'] = $this->getContent($item, '<description>', '</description>', true);
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$data['thumbnail_url'] = $this->getContent($data['headline'], 'src="', '"', true);
				$data['headline'] = str_replace('&lt;', '<', $data['headline']);
				$data['headline'] = str_replace('&gt;', '>', $data['headline']);
				// $data['headline'] = str_replace('&lt;', '<', $data['headline']);
				$data['headline'] = strip_tags($data['headline']);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, '<div class="yom-mod yom-art-content ">', '<div class="yom-mod yom-follow"', true);
				// die($newsContent);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}
				// die();
			}
		}
	}
	
	public function getRadioOnline() {
		// $url = 'http://radio.vnmedia.vn/Browse.aspx?id=35';
		$cats = RadioCategory::model()->findAll();
		foreach ($cats as $cat) {
			// $url = 'http://radio.vnmedia.vn/Browse.aspx?id=' . $cat->id;
			for ($i = 1;$i < 6;$i++) {
				$url = $cat->url . '&page=' . $i;
				$contents = $this->getURLContents($url);
				// die($contents);
				// $data = $this->getContent($contents, '<div class="sub">', '</div>', true);
				$data = $this->getContent($contents, '<ul class="radio-tracks">', '</ul>', true);
				// print_r($data);die();
				$data = $this->getContent($data, '<li', '</li>');
				// $data = $this->getContent($data, '<a', '</li>');
				/*
				foreach ($data as $one) {
					// die($one);
					$name = $this->getContent($one, '">', '</a>', true);
					$link = 'http://radio.vnmedia.vn' . $this->getContent($one, 'href="', '"', true);
					$category = new RadioCategory;
					$category->name = $name;
					$category->url = $link;
					$category->created_time = date('Y-m-d H:i:s');
					$category->save(false);
				}
				print_r($data);die();*/
				if (!empty($data)) {
					foreach ($data as $one) {
						$link = 'http://radio.vnmedia.vn' . $this->getContent($one, 'href="', '"', true);
						$title = $this->getContent($one, '">', '</a>', true);
						$title = Utility::foreignToUnicode($title);
						// die($link);
						$detail = $this->getURLContents($link);
						// die($detail);
						$mediaUrl = $this->getContent($detail, "link: '", "'", true);
						// die($mediaUrl);
						$thumb = $this->getContent($detail, '<div class="fl">', '</div>', true);
						$thumb = 'http://radio.vnmedia.vn' . $this->getContent($thumb, '<img src="', '"', true);
						$exist = Radio::model()->findByAttributes(array('original_url' => $link));
						if (empty($exist)) {
							$radio = new Radio;
							$radio->title = $title;
							$radio->title_en = Utility::unicode2Anscii($title);
							$radio->headline = '';
							$radio->download_file = date('YmdHis') . '.mp3';
							$radio->original_url = $link;
							$radio->thumbnail_url = str_replace(' ', '%20', $thumb);
							$radio->media_url = str_replace(' ', '%20', $mediaUrl);
							$radio->category_id = $cat->id;
							$radio->created_time = date('Y-m-d H:i:s');
							$radio->save(false);
						}
					}
				}
			}
		}
	}
	
	public function fixEbook() {
		// $books = Book::model()->findAll();
		// foreach ($books as $book) {
			$chapter = Chapter::model()->findAllByAttributes(array('book_id' => 2));
			foreach ($chapter as $one) {
				// echo $one->heading;
				// $one->heading = str_replace($book->title, '', $one->heading);
				// $one->heading = str_replace("\n", ' ', $one->heading);
				// $one->heading = str_replace("\t", '', $one->heading);
				// $one->heading = str_replace("  ", ' ', $one->heading);
				// $one->heading = substr($one->heading, 1);
				// $one->content = substr($one->content, 7);
				// $one->heading = substr($one->heading, 10);
				// $one->heading = 'C' . $one->heading;
				$one->heading = 'CH' . $one->heading;
				$one->save(false);
				// die($one->heading);
			}
		// }
	}
	
	public function fixTruyen18() {
		Yii::app()->db->createCommand("DELETE FROM chapter WHERE content = ''")->execute();
		die();
		$chapters = Chapter::model()->findAll();
		// $chapters = Chapter::model()->findAllByAttributes(array('id' => 128));
		foreach ($chapters as $chapter) {
			$chapter->content = Utility::foreignToUnicode($chapter->content);
			$chapter->heading = Utility::foreignToUnicode($chapter->heading);
			// die($chapter->content);
			$chapter->save(false);
		}
	}
	
	public function getTruyen18() {
		// $url = 'http://www.truyenviet.com/truyen-nguoi-lon';
		$url = 'http://truyenviet.com/trang-chu/truyen-tam-linh';
		$content = $this->getURLContentWithFake($url, 'Firefox');
		// die($content);
		$content = $this->getContent($content, '<div id="main-content"', '<div class="module-body">', true);
		// die($content);
		$lists = $this->getContent($content, '<ul>', '</ul>', true);
		$lists = $this->getContent($lists, '<li>', '</li>');
		// print_r($lists);die();
		foreach ($lists as $list) {
			$detailUrl = 'http://truyenviet.com/' . $this->getContent($list, 'href="', '"', true);
			$title = $this->getContent($list, '">', '</a>', true);
			$detail = $this->getURLContentWithFake($detailUrl, 'Firefox');
			// die($detail);
			$detailLists = $this->getContent($detail, '<td headers="tableOrdering">', '</td>');
			// print_r($detailLists);die();
			foreach ($detailLists as $dList) {
				$dUrl = 'http://truyenviet.com/' . $this->getContent($dList, 'href="', '"', true);
				// $dUrl = 'http://truyenviet.com/truyen-nguoi-lon/5-0-9/760-tnl-1001-dem-x';
				// $dUrl = 'http://truyenviet.com/truyen-nguoi-lon/7-b/768-tnl-ba-con-mua';
				$title = $this->getContent($dList, '">', '</a>', true);
				
				$isExist = Book::model()->findByAttributes(array('title' => $title));
				if (empty($isExist)) {
					// die('Empty ' . $title);
					$book = new Book;
					$book->title = $title;
					$book->title_vn = Utility::unicode2Anscii($title);
					$book->thumbnail = 'cover.jpg';
					$book->save(false);
					$bookId = $book->id;
					
					$dContent = $this->getURLContents($dUrl);
					// die($dContent);
					$totalPage = $this->getContent($dContent, '<div class="pagenavcounter">', '/div>', true);
					$totalPage = $this->getContent($totalPage, 'tổng số ', '<', true);
					if (empty($totalPage)) $totalPage = 1;
					for ($i = 0;$i < $totalPage;$i++) {
						$cContent = $this->getURLContents($dUrl . '?start=' . $i);
						// die($cContent);
						$divContent = $this->getContent($cContent, '<div id="page">', 'align="center" class="pagenav">', true);
						// die($divContent);
						$divContent = $this->stripContent($divContent, '<div class="pagenavbar">', '<br />');
						// die($divContent);
						$saveContent = $this->getContent($divContent, '<p>', '<table', true);
						die($saveContent);
						if (!empty($saveContent)) {
							$saveContent = strip_tags($saveContent);
							$chapter = new Chapter;
							$chapter->title = 'Chương ' . ($i + 1);
							$chapter->content = $saveContent;
							$chapter->book_id = $bookId;
							$chapter->heading = substr($saveContent, 0, 100);
							$chapter->page = 1;
							$chapter->save(false);
						}
					}
				}
			}
		}
	}
    
    public function getSachnoi() {
        $url = 'http://sachnoi.vn/category/radio-show/qns/feed/';
        $url = 'http://sachnoi.vn/category/sach/doanh-nhan-danh-nhan/feed/';
        $content = $this->getURLContents($url);
        $contents = $this->getContent($content, '<item>', '</item>');
        // print_r($contents);die();
        foreach ($contents as $content) {
            // echo $content;die();
            $title = $this->getContent($content, '<title>', '</title>', true);
            $link = trim($this->getContent($content, '<link>', '</link>', true));
            $pubDate = $this->getContent($content, '<pubDate>', '</pubDate>', true);
            $description = $this->getContent($content, '<description>', '</description>', true);
            $thumbnail = $this->getContent($content, 'width=', 'class', true);
            $thumbnail = $this->getContent($thumbnail, 'src="', '"', true);
            // echo $thumbnail;die();
            $description = strip_tags($description);
            $mediaUrl = $this->getContent($content, '<enclosure', 'length', true);
            $mediaUrl = $this->getContent($mediaUrl, 'url="', '"', true);
            $description = str_replace($mediaUrl, '', $description);
            // $description = str_replace(']]>', '', $description);
            // echo $mediaUrl;die();
            // echo $description;die();
            $pos = strpos($link, '?utm_source');
            if (!empty($pos))
                $link = trim(substr($link, 0, strpos($link, '?utm_source')));
            // die($link);
            $exist = AudioBook::model()->isExist($link);
            $link = 'http://sachnoi.vn/50/dac-nhan-tam/';
            if (empty($exist)) {
                $detail = $this->getURLContents($link);
                die($detail);
				if (empty($mediaUrl)) {
					$media = $this->getContent($detail, '<object', '</object>', true);
					if (empty($media)) {
						// Playlist
						$media = $this->getContent($detail, 'file:"', '"', true);
						$media = str_replace('%3A', ':', $media);
						$media = str_replace('%2F', '/', $media);
						$mediaD = $this->getURLContents($media);
						$track = $this->getContent($mediaD, '<track>', '<track>');
						if (!empty($track)) {
							foreach ($track as $one) {
								$location = $this->getContent($one, '<location>', '</location>', true);
								// die($location);
								// $c = $this->getURLContents($location);
								// die($c);
							}
						}
					}
					print_r($media);die();
				}
                $audio = new AudioBook;
                $audio->title = $title;
                $audio->title_en = Utility::unicode2Anscii($title);
                $audio->headine = $description;
                $audio->headine_en = Utility::unicode2Anscii($description);
                $audio->thumbnail_url = $thumbnail;
                $audio->media_url = $mediaUrl;
                $audio->original_url = $link;
                $date = date('Y-m-d H:i:s', strtotime($pubDate));
                // die($date);
                $audio->created_time = $date;
                
                
                $audio->save(false);
            }
        }
    }
	
	public function parseEbooks2() {
        $bookDir = 'E:\i-newspaper\app\inews\protected\data\Lan dau ben nhau\\';
		$thumbDir = 'E:\i-newspaper\app\inews\protected\data\Lan dau ben nhau\thumb\\';
        if ($handle = opendir($bookDir)) {
            echo "Directory handle: $handle<br/>";
            echo "Entries:<br/>";
            
            // $xmlParser = new simple_html_dom();
            
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
				// $dFolder = $bookDir . $entry . DS . 'OEBPS' . DS;
				$dFolder = $bookDir . $entry . DS;
                $opfFile = $dFolder . 'content.opf';
                // echo $opfFile;die();
                if (file_exists($opfFile)) {
					$authorTmp = explode('-', $entry);
					$authorName = trim($authorTmp[1]);
					$author = Author::model()->findByAttributes(array('name' => $authorName));
					$authorId = 0;
					if (!empty($author)) $authorId = $author->id;
					else {
						$author = new Author;
						$author->name = $authorName;
						$author->name_vn = Utility::unicode2Anscii($authorName);
						$author->save(false);
						$authorId = $author->id;
					}
					// die($authorId);
					$data = file_get_contents($opfFile);
                    // echo 'Exists' . $opfFile;continue;
					// echo $data;die();
					$book = new Book;
					$book->title = $this->getContent($data, '<dc:title>', '</dc:title>', true);
					$book->title = $this->getContent($book->title, '<![CDATA[', ']]>', true);
					$book->title_vn = Utility::unicode2Anscii($book->title);
					// $thumbnail = 'cover' . date('YmdHis') . '.png';
					$thumbnail = 'cover' . date('YmdHis') . '.jpg';
					$book->thumbnail = $thumbnail;
					rename($dFolder . 'cover.jpg', $dFolder . $thumbnail);
					// copy($dFolder . 'cover.jpg', $thumbDir . $thumbnail);
					// copy($dFolder . 'cover.png', $thumbDir . $thumbnail);
					// copy($dFolder . $thumbnail, $thumbDir . $thumbnail);
					// $author = $this->getContent($data, '<dc:creator>', '</dc:creator>', true);
					// $book->author = (!empty($author)) ? $author : '';
					$book->author_id = $authorId;
					$book->save(false);
					$bookId = $book->id;
					// echo $book->title;
					// $chapHandle = opendir($dFolder);
					// echo "<br/>";
					$i = 0;
					// echo $data;die();
					$chaps = $this->getContent($data, 'item href="', '" id="');
					// $chaps = $this->getContent($data, 'media-type="application/xhtml+xml" href="', '"');
					// print_r($chaps);die();
					// while (false !== ($chap = readdir($chapHandle))) {
					foreach ($chaps as $chap) {
						// echo $chap."<br/>";die();
						// echo substr($chap, -4)."<br/>";
						// if ($chap == '.' || $chap == '..') continue;
						// if (substr($chap, -4) != 'html') break;
						$info = pathinfo($chap);
						// print_r($info);die();
						if ($info['extension'] != 'html') continue;
						if ($chap == 'toc.html' || $chap == 'welcome.html' || $chap == 'lastpage.html') continue;
						$i++;
						$chap = $dFolder . $chap;
						// echo $chap."<br/>";die();
						$chapContent = file_get_contents($chap);
						// die($chapContent);
						// $chapContent = $this->getContent($chapContent, '<body', '</body>', true);
						$chapter = new Chapter;
						$chapter->book_id = $book->id;
						$title = '<h1' .  $this->getContent($chapContent, '<h1', '</h1>', true) . '</h1>';
						// $title = strip_tags($this->getContent($chapContent, 'class="style32">', '</p>', true));
						// $title = $this->getContent($chapContent, '<span class="calibre9">', '</span>', true);
						if (empty($title)) $title = $this->getContent($chapContent, '<span class="calibre19">', '</span>', true);
						// $title2 = $this->getContent($chapContent, 'class="style28">', '</p>');
						$title2 = $title2[1];
						$title .= ' ' . $title2;
						// $title = $this->getContent($chapContent, '<h4 class="calibre3">', '</h4>', true);
						// $title = $this->getContent($chapContent, '<span class="calibre12">', '</span>', true);
						// $title = '<h2' . $this->getContent($chapContent, '<h2 class="calibre13"', '</h2>', true) . '</h2>';
						if (!empty($title)) $title = strip_tags($title);
						$chapter->title = (!empty($title)) ? $title : 'Chương ' . $i;
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						// $chapter->title = $this->getContent($chapContent, '<h3 class="chapter_heading">', '</h3>', true);
						// $chapContent = '<div' . $this->getContent($chapContent, '<div align="justify"', '</div>', true) . '</div>';
						// $chapContent = str_replace('http://www.e-thuvien.com', '', $chapContent);
						// echo $chap;
						$chapContent = strip_tags($chapContent);
						// $chapContent = str_replace('Unknown', '', $chapContent);
						// echo $chapContent;die();
						$chapter->content = $chapContent;
						$chapter->heading = substr($chapContent, 0, 150);
						$chapter->heading = str_replace("\n", ' ', $chapter->heading);
						$chapter->heading = str_replace("\t", '', $chapter->heading);
						$chapter->heading = str_replace("  ", ' ', $chapter->heading);
						$chapter->page = 1;
						// echo $chapter->title . 'TRUNG' . $chapter->heading . 'TRUNG' . $chapter->content;die();
						$chapter->save(false);
						// die();
					}
                    // $xmlParser->load_file($opfFile);
                    // var_dump($xmlParser);
                    // $title = $xmlParser->find('dc:title', 0)->plaintext;
                    // $author = $xmlParser->find('dc:creator', 0)->plaintext;
                    // $chapters = $xmlParser->find('item');
                    // var_dump($chapters[0]->plaintext);
                }
            }
            
            closedir($handle);
        }
    }
	
	public function parseEbooks() {
        $bookDir = 'E:\i-newspaper\app\inews\\protected\data\books\\';
        if ($handle = opendir($bookDir)) {
            echo "Directory handle: $handle<br/>";
            echo "Entries:<br/>";
            
            // $xmlParser = new simple_html_dom();
            
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
				$dFolder = $bookDir . $entry . DS;
                $opfFile = $dFolder . 'content.opf';
                // echo $opfFile;
                if (file_exists($opfFile)) {
					$data = file_get_contents($opfFile);
                    // echo 'Exists' . $opfFile;die();
					// echo $data;
					$book = new Book;
					$book->title = $this->getContent($data, '<dc:title>', '</dc:title>', true);
					$book->title_vn = Utility::unicode2Anscii($book->title);
					$book->thumbnail = 'cover' . date('YmdHis') . '.jpg';
					$author = $this->getContent($data, '<dc:creator>', '</dc:creator>', true);
					$book->author = (!empty($author)) ? $author : '';
					$book->save(false);
					$bookId = $book->id;
					// echo $book->title;
					$chapHandle = opendir($dFolder);
					// echo "<br/>";
					$i = 0;
					$chaps = $this->getContent($data, 'item href="', '" id');
					// print_r($chaps);die();
					// while (false !== ($chap = readdir($chapHandle))) {
					foreach ($chaps as $chap) {
						// echo $chap."<br/>";die();
						// echo substr($chap, -4)."<br/>";
						// if ($chap == '.' || $chap == '..') continue;
						// if (substr($chap, -4) != 'html') break;
						$info = pathinfo($chap);
						// print_r($info);die();
						if ($info['extension'] != 'html') continue;
						$i++;
						$chap = $dFolder . $chap;
						// echo $chap."<br/>";die();
						$chapContent = file_get_contents($chap);
						// die($chapContent);
						// $chapContent = $this->getContent($chapContent, '<body', '</body>', true);
						$chapter = new Chapter;
						$chapter->book_id = $book->id;
						$chapter->title = 'Chương ' . $i;
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						$chapContent = str_replace('@page { margin-bottom: 5.000000pt; margin-top: 5.000000pt; }', '', $chapContent);
						// $chapter->title = $this->getContent($chapContent, '<h3 class="chapter_heading">', '</h3>', true);
						
						$chapContent = str_replace('http://www.e-thuvien.com', '', $chapContent);
						// echo $chap;
						// echo $chapContent;die();
						$chapContent = strip_tags($chapContent);						
						$chapter->content = $chapContent;
						$chapter->heading = substr($chapContent, 0, 150);
						$chapter->heading = str_replace("\n", ' ', $chapter->heading);
						$chapter->heading = str_replace("\t", '', $chapter->heading);
						$chapter->heading = str_replace("  ", ' ', $chapter->heading);
						$chapter->page = 1;
						$chapter->save(false);
						// die();
					}
                    // $xmlParser->load_file($opfFile);
                    // var_dump($xmlParser);
                    // $title = $xmlParser->find('dc:title', 0)->plaintext;
                    // $author = $xmlParser->find('dc:creator', 0)->plaintext;
                    // $chapters = $xmlParser->find('item');
                    // var_dump($chapters[0]->plaintext);
                }
            }
            
            closedir($handle);
        }
    }
	
	public function fixDanhnhan() {
		$books = Book::model()->findAll();
		foreach ($books as $book) {
			$chapter = new Chapter;
			$chapter->book_id = $book->id;
			$chapter->title = 'Tiểu sử';
			$chapter->page = 1;
			$chapter->heading = substr($book->content, 0, 80);
			$chapter->content = $book->content;
			$chapter->save(false);
			$book->content = '';
			$book->save(false);
			// die();
			
			// $book->content = strip_tags($book->content);
			// $book->save(false);
		}
	}
	
	public function getDanhnhan() {
		// $link = 'http://www.vinhanonline.com/index.php?option=option=com_content&view=article&id=%s:danh-nhan-viet-nam-a&catid=83:nhan-vat-lich-su&Itemid=200';
		// $contents = $this->getURLContents($link);
		// $urls = $this->getContent($contents, '<tr align="center">', '</tr>', true);
		$ids = array(150, 161, 162,163,164,165,166,167,169,170,171,172,173,174,175,176,177,178,179);
		$url = 'http://www.vinhanonline.com/index.php?option=com_content&view=article&id=%s:danh-nhan-viet-nam-a&catid=83:nhan-vat-lich-su&Itemid=200';
		$dLink = 'http://www.vinhanonline.com/index.php?option=com_content&view=article&id=%s';
		// print_r($urls);die();
		foreach ($ids as $id) {
			$tmpUrl = sprintf($url, $id);
			$content = $this->getURLContents($tmpUrl);
			// echo $url;die();
			// echo $content;die();
			$content = $this->getContent($content, '<table', '</table', true);
			// print_r($content);die();
			$content = $this->getContent($content, '<ul>', '</ul>', true);
			$content = $this->getContent($content, '<li>', '</li>');
			if (!empty($content)) {
				foreach ($content as $one) {
					// echo $one;
					$link = $this->getContent($one, 'href="', '"', true);
					$aId = $this->getContent($link, 'id=', ':', true);
					$tmpLink = sprintf($dLink, $aId);
					// echo $tmpLink;
					
					$data['created_time'] = date('Y-m-d H:i:s');
					$detail = $this->getURLContents($tmpLink);
					$data['title'] = $this->getContent($detail, 'class="contentpagetitle">', '</a>', true);
					$data['title_vn'] = Utility::unicode2Anscii($data['title']);
					// die($detail);
					$detail = $this->getContent($detail, '<div class="article-content">', '<div', true);
					$data['content'] = $detail;
					if (!empty($data['title'])) {
						$book = new Book;
						$book->attributes = $data;
						$book->save(false);
					}
				}
			}
		}
		
	}
	
	public function getFood() {
		$categories = Category::model()->findAll();
		foreach ($categories as $category) {
			for ($page = 1;$page < 30;$page++) {
				$link = $category->link;
				if ($page > 1) {
					$link = str_replace('.html', '_' . $page . '.html', $link);
				}
				$contents = $this->getURLContents($link);
				$items = $this->getContent($contents, '<!-- START DISH ITEMS -->', '<!-- END DISH ITEMS -->', true);
				$details = $this->getContent($items, '<div style="text-align:justify">', '</div>');
				// print_r($details);die();
				$thumbs = $this->getContent($items, '<img class="image"', '</a>');
				$i = 0;
				if (!empty($details)) {
					foreach ($details as $detail) {
						$url = 'http://www.vnnavi.com/dishes/' . $this->getContent($detail, 'href="', '"', true);
						if (!Food::model()->isExist($url)) {
							$headline = $this->getContent($detail, '<span style="line-height:20px">', '<a', true);
							$data['headline'] = trim(strip_tags(str_replace('-', '', $headline)));
							$data['original_url'] = $url;
							$data['title'] = $this->getContent($detail, '<strong>', '</strong>', true);
							$thumb = $thumbs[$i];
							$thumbnail = 'http://www.vnnavi.com/dishes/' . str_replace('./', '', $this->getContent($thumb, 'src="', '"', true));
							// die($thumbnail);
							/*$savePath = '/tmp/image_food.test';
							$this->save_image($thumbnail, $savePath);
							$handle = fopen($savePath, "rb");
							$image = fread($handle, filesize($savePath));
							fclose($handle);*/
							$data['thumbnail_url'] = $thumbnail;
							$data['title_vn'] = Utility::unicode2Anscii($data['title']);
							$data['category_id'] = $category->id;
							$data['created_time'] = date('Y-m-d H:i:s');
							
							$detailPage = $this->getURLContents($url);
							$data['content'] = $this->getContent($detailPage, '<!-- START DISH CONTENT -->', '<!-- END DISH CONTENT -->', true);
							$food = new Food;
							$food->attributes = $data;
							$food->headline = $data['headline'];
							$food->thumbnail_url = $data['thumbnail_url'];
							// print_r($data);die();
							$food->save(false);
						}
						
						$i++;
					}
				}
			}
		}	
	}
	
	public function getGoTech() {
		$goTech = Yii::app()->params['site']['vnews'];
		$siteId = 35;
		
		foreach ($goTech as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<description><![CDATA[', ']]></description>', true);
				$thumbnailUrl = $this->getContent($headline, "src='", "' />", true);
				// die($thumbnailUrl);
				$headline = strip_tags($headline);
				// die($headline);
				$data['headline'] = $headline;
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				// die($data['published_time']);
				$detailLink = $this->getContent($item, '<link><![CDATA[', ']]></link>', true);
				// $detailLink = 'http://www.pcworld.com.vn/articles/cong-nghe/ung-dung/2012/03/1231421/14-website-huu-ich-tren-di-dong/';
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="shownews">', '<div class="actionnews">', true);
				$newsContent = '<div ' . $this->getContent($detail, '<div id="ContentContainer"', '<div id="content-tag"', true);
				$data['thumbnail_url'] = $thumbnailUrl;
				$source = $this->getContent($detail, ' <div class="gn_info">', '</div>', true);
				$source = strip_tags($source);
				$source = explode('-', $source);
				$source = trim($source[1]);
				$data['source'] = $source;
				// die($data['source']);
                $newsContent = str_replace('<img', '<img width="290"', $newsContent);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
					$toStrip = $this->getContent($newsContent, 'style="', '"');
					if (!empty($toStrip)) {
						foreach ($toStrip as $strip) {
							$newsContent = str_replace($strip, '290px;', $newsContent);
						}
					}
					
					$toStrip = $this->getContent($newsContent, 'object style="', '"');
					if (!empty($toStrip)) {
						foreach ($toStrip as $strip) {
							$newsContent = str_replace($strip, 'width:290px;height: 200px;', $newsContent);
						}
					}
					$newsContent = str_replace('width="640"', 'width="290"', $newsContent);
					$newsContent = str_replace('height="360"', 'height="200"', $newsContent);
					
					$links = $this->getContent($newsContent, '<a href="', '"');
					if (!empty($links)) {
						foreach ($links as $strip) {
							$newsContent = str_replace($strip, '#', $newsContent);
						}
					}
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function getMonngon() {
		// $test = Food::model()->findByPk(5);
		// echo $test->content;die();
		$url = 'http://afamily.vn/tags/mon-ngon-mua-he/trang-%s.chn';
		for ($i = 1;$i < 14;$i++) {
			$tmpUrl = sprintf($url, $i);
			$content = $this->getURLContents($tmpUrl);
			$items = $this->getContent($content, '<div class="catalogiesbox_top fl">', '</div>');
			foreach ($items as $item) {
				$link = 'http://afamily.vn' . $this->getContent($item, 'href="', '"', true);
				$data['original_url'] = $link;
				$data['thumbnail'] = $this->getContent($item, 'src="', '"', true);
				$data['title'] = $this->getContent($item, 'title="', '"', true);
				$data['headline'] = $this->getContent($item, '<p>', '</p>', true);
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['title_vn'] = Utility::unicode2Anscii($data['title']);
				$detail = $this->getURLContents($link);
				// die($detail);
				$dContent = '<div>' . $this->getContent($detail, '<div class="detail_content fl mgt15">', '<div class="tag fl mgt20">', true);
				$dContent = str_replace('<img', '<img width=125', $dContent);
				$dContent = str_replace('<IMG', '<IMG width=125', $dContent);
				$dContent = str_replace('style="MARGIN: 5px"', 'style="MARGIN: 2px"', $dContent);
				$data['content'] = $dContent;
				$food = new Food;
				$food->attributes = $data;
				// print_r($data);die();
				$food->save(false);
				// die($dContent);
			}
		}
	}
	
	public function getSanhdieuTimnhanh() {
		for ($i = 1;$i < 200;$i++) {
            $link = 'http://thegioisanhdieu.timnhanh.com/m/tag/tinh_yeu/780/' . $i;
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<li class="li_ctn">', '</li>');
            // die($link);
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
            $j = 0;
			foreach ($items as $item) {
				// echo $item;die();
				$title = $this->getContent($item, '<div class="title_name">', '</div>', true);
                $data['title'] = $this->getContent($title, '">', '<', true);
				$data['headline'] = $this->getContent($item, '<span class="news_intro">', '<', true);
				$data['thumbnail_url'] = $this->getContent($item, 'background-image:url(', ')', true);
				$detailLink = $this->getContent($item, '<a href="', '"', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$avatar = $this->getContent($detail, '<a class="img_avatar">', '</a>', true);
				$avatar = $this->getContent($avatar, 'background-image:url(', ')"', true);
				$newsContent = $this->getContent($detail, '<div class="news_detail">', '</div>', true);
				$thumbnail = $data['thumbnail_url'];
				$savePath = '/tmp/image_eva_' . $i . '_' . $j . '.test';
				$this->save_image($thumbnail, $savePath);
				$savePathLarge = '/tmp/image_eva_' . $i . '_' . $j . '.large_test';
				$this->save_image($avatar, $savePathLarge);
				// $image = chunk_split(base64_encode(file_get_contents($savePath)));
				$largeImage = chunk_split(base64_encode(file_get_contents($savePathLarge)));
				$handle = fopen($savePath, "rb");
				$image = fread($handle, filesize($savePath));
				fclose($handle);
				$handle = fopen($savePathLarge, "rb");
				$largeImage = fread($handle, filesize($savePathLarge));
				fclose($handle);
				// die($newsContent);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!Handbook::isExist($detailLink) && !empty($newsContent) && !empty($data['title'])) {
					$data['content'] = $newsContent;
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['published_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new Handbook;
					$news->attributes = $data;
					$news->thumbnail = $image;
					
					if ($news->save(false)) {
						
					}
				}
				// die();
			}
		}
	}
	
	public function getPcWorld() {
		$pcworld = Yii::app()->params['site']['pcworld'];
		$siteId = 29;
		
		foreach ($pcworld as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = $headline;
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				// $detailLink = 'http://www.pcworld.com.vn/articles/cong-nghe/ung-dung/2012/03/1231421/14-website-huu-ich-tren-di-dong/';
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, 'id="ar-content-html">', '<div style="clear: both">', true);
				$data['thumbnail_url'] = $this->getContent($$item, 'thumbnail url="', "'", true);
                $newsContent = str_replace('src="/', 'src="http://www.pcworld.com.vn/', $newsContent);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
					$toStrip = $this->getContent($newsContent, 'style="width:', '"');
					if (!empty($toStrip)) {
						foreach ($toStrip as $strip) {
							$newsContent = str_replace($strip, '290px;', $newsContent);
						}
					}
					
					$links = $this->getContent($newsContent, '<a href="', '"');
					if (!empty($links)) {
						foreach ($links as $strip) {
							$newsContent = str_replace($strip, '#', $newsContent);
						}
					}
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function getGenK() {
		$genK = Yii::app()->params['site']['genk'];
		$siteId = 31;
		
		foreach ($genK as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<description><![CDATA[', ']]></description>', true);
				$data['headline'] = strip_tags($headline);
				$published_time = $this->getContent($item, '<pubDate>', '</pubDate>', true);
                $data['published_time'] = $published_time;
				$detailLink = $this->getContent($item, '<link><![CDATA[', ']]></link>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, '<div class="assessment-main1">', '<div class="share">', true);
				$data['thumbnail_url'] = $this->getContent($headline, "<img src='", "'", true);
                $newsContent = str_replace('<img', '<img width=290 ', $newsContent);
                $newsContent = str_replace('<IMG', '<img width=290 ', $newsContent);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
					$toStrip = $this->getContent($newsContent, 'style="width:', '"');
					foreach ($toStrip as $strip) {
						$newsContent = str_replace($strip, '290px;', $newsContent);
					}
					$links = $this->getContent($newsContent, '<a href="', '"');
					foreach ($links as $strip) {
						$newsContent = str_replace($strip, '#', $newsContent);
					}
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function getZingnews() {
		$zing = Yii::app()->params['site']['zingnews'];
		$siteId = 32;
		
		foreach ($zing as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<description>', '</description>', true);
                $headline = str_replace('&#60;', '<', $headline);
				// $data['headline'] = strip_tags($headline);
				$data['headline'] = $this->getContent($headline, '<![CDATA[', ']]>', true);
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($item, '<content><![CDATA[', ']]></content>', true);
				$data['thumbnail_url'] = $this->getContent($headline, 'img src="', '"', true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function get24h() {
		$b24h = Yii::app()->params['site']['24h'];
		$siteId = 33;
		
		foreach ($b24h as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = strip_tags($headline);
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
                $detailLink = str_replace('http://www', 'http://hn', $detailLink);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, '<div class="text-conent">', '<div class="baiviet-tags">', true);
				$data['thumbnail_url'] = $this->getContent($headline, 'img src="', '"', true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function getAutonet() {
		$autonet = Yii::app()->params['site']['autonet'];
		$siteId = 10;
		
		foreach ($autonet as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<doc>', '</doc>');
            // print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<str name="title">', '</str>', true);
				$data['title'] = $title;
				$headline = $this->getContent($item, '<str name="lead">', '</str>', true);
				$data['headline'] = strip_tags($headline);
				$data['published_time'] = $this->getContent($item, '<date name="date">', '</date>', true);
                // echo $data['published_time'];die();
				$detailLink = $this->getContent($item, '<str name="url">', '</str>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, '<div id="content" class="content box">', '<div class="clear">', true);
				$data['thumbnail_url'] = 'http://autonet.com.vn' . $this->getContent($item, '<str name="avatar">', '</str>', true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
                    $newsContent = str_replace('src="', 'src="http://autonet.com.vn', $newsContent);
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
                    $newsContent = str_replace('width: 460px;', 'width: 290px;', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
			// die();
		}
	}
	
	public function get2Sao() {
		$sao2 = Yii::app()->params['site']['2sao'];
		$siteId = 9;
		
		foreach ($sao2 as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = trim(strip_tags($this->getContent($headline, '<![CDATA[', ']]>', true)));
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detailLink = trim($this->getContent($detailLink, '<![CDATA[', ']]>', true));
				$detailLink = str_replace('2sao.vietnamnet.vn', '2sao.vn', $detailLink);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, 'class="detail_content">', '<div class="sharefacebook">', true);
				if (empty($newsContent)) {
					$newsContent = $this->getContent($detail, '<div class="content">', '<div style="margin-bottom: 10px;">', true);
				}
				$data['thumbnail_url'] = $this->getContent($headline, "src='", "'", true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
					$toStrip = $this->getContent($newsContent, 'style="width:', '"');
					foreach ($toStrip as $strip) {
						$newsContent = str_replace($strip, '290px;', $newsContent);
					}
					$links = $this->getContent($newsContent, '<a href="', '"');
					foreach ($links as $strip) {
						$newsContent = str_replace('<a href="' . $strip . '"', '<a href="#"', $newsContent);
					}
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$newsContent = str_replace('<IMG', '<img width=290', $newsContent);
					// die($newsContent);
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					$data['category_id'] = $c;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}				
			}
		}
	}
	
	public function getTuvitrondoi() {
		$link = 'http://www5.xemtuong.net/tuvi/tuvi_tron_doi/index.php?canchi=2013&phai=nam&xem.x=24&xem.y=8';
		$content = $this->getURLContents($link);
		echo $content;
	}
	
	public function getCafeF() {
		$cafeF = Yii::app()->params['site']['cafef'];
		$siteId = 8;
		
		foreach ($cafeF as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				// echo $item;die();
				$i++;
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = trim(strip_tags($this->getContent($headline, '<![CDATA[', ']]>', true)));
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, 'class="KenhF_Content_News3">', '<div style="margin-bottom: 10px;">', true);
				if (empty($newsContent)) {
					$newsContent = $this->getContent($detail, '<div class="content">', '<div style="margin-bottom: 10px;">', true);
				}
				$data['thumbnail_url'] = $this->getContent($headline, 'src="', '"', true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink) && !empty($newsContent)) {
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					$data['category_id'] = $c;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							$lastId = $news->id;
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}
				// die();
			}
		}
	}
	
	public function getLandCafe() {
		$cafeF = Yii::app()->params['site']['land_cafe'];
		$siteId = 30;
		
		foreach ($cafeF as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				// echo $item;die();
				$i++;
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = trim(strip_tags($this->getContent($headline, '<![CDATA[', ']]>', true)));
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detail = $this->getURLContents($detailLink);
				// echo $detail;die();
				$newsContent = $this->getContent($detail, 'class="KenhF_Content_News3">', '<div style="margin-bottom: 10px;">', true);
				if (empty($newsContent)) {
					$newsContent = $this->getContent($detail, '<div class="content">', '<div style="margin-bottom: 10px;">', true);
				}
				$data['thumbnail_url'] = $this->getContent($headline, 'src="', '"', true);
				// echo $data['thumbnail_url'];die();
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				// print_r($data);die();
				if (!News::isExist($siteId, $detailLink)) {
					$data['content'] = $newsContent;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					$data['category_id'] = $c;
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						if ($i <= 5) {
							$lastId = $news->id;
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}
				// die();
			}
		}
	}
	
	public function getDothi() {
		$link = 'http://dothi.net/rss/0/trang-chu.rss';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
        $siteId = 7;
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			// echo $item;die();
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$headline = $this->getContent($item, '<description>', '</description>', true);
            $data['headline'] = strip_tags($headline);
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
			$detail = $this->getURLContents($detailLink);
            // echo $detail;die();            
            $newsContent = $this->getContent($detail, '<div class="padT4 padB4 titleDdtail clor9 f-11">', '<div class="padT4 padB4">', true);
            $newsContent = $this->stripContent($newsContent, '<div class="fRight">', '</div>');
            $newsContent = str_replace('<div class="fRight"></div>', '', $newsContent);
            $newsContent = $this->stripContent($newsContent, '<div class="fRight">', '</div>');
            $newsContent = str_replace('<div class="fRight"></div>', '', $newsContent);
            $newsContent = $this->stripContent($newsContent, '<span class="fLeft">', '</span>');
            $data['thumbnail_url'] = $this->getContent($newsContent, 'src="', '"', true);
            // echo $data['thumbnail_url'];die();
            // echo $newsContent;die();
            // echo $thumbnail;die();
            // echo $detail;die();
			// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
			if (!News::isExist($siteId, $detailLink)) {
				$data['content'] = $newsContent;
                $data['title_en'] = Utility::unicode2Anscii($data['title']);
                $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
				$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                $data['site_id'] = $siteId;
                // die($data['published_time']);
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				
				$news = new News;
				$news->attributes = $data;
				if ($news->save(false)) {
					
				}
			}
            // die();
		}
	}
	
	public function getKhoahoc() {
		$link = 'http://www.khoahoc.com.vn/congnghemoi/cong-nghe-moi/rss.aspx';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
        $siteId = 6;
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			// echo $item;die();
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$headline = $this->getContent($item, '<description>', '</description>', true);
            $headline = str_replace('&lt;', '<', $headline);
            $headline = str_replace('&gt;', '>', $headline);
            $data['headline'] = strip_tags($headline);
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
			$detail = $this->getURLContents($detailLink);
            // echo $detail;die();
            $thumbnail = $this->getContent($detail, '<div id="ctl00_CPH1_TinChiTiet1_divImage"', '</div>', true);
            $data['thumbnail_url'] = 'http://www.khoahoc.com.vn' . $this->getContent($headline, 'src="', '"', true);
            $newsContent = $this->getContent($detail, '<div id="divContent" style="text-align:justify">', '</div>', true);
            $newsContent = str_replace('src="', 'src="http://www.khoahoc.com.vn', $newsContent);
            // echo $newsContent;die();
            // echo $thumbnail;die();
            // echo $detail;die();
			// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
			if (!News::isExist($siteId, $detailLink)) {
				$data['content'] = $newsContent;
                $data['title_en'] = Utility::unicode2Anscii($data['title']);
                $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
				$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                $data['site_id'] = $siteId;
                // die($data['published_time']);
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				$data['category_id'] = $c;
				
				$news = new News;
				$news->attributes = $data;
				if ($news->save(false)) {
					
				}
			}
		}
	}
	
	public function getNgoisaoDetail($siteId, $categoryId, $link) {
		$data['category_id'] = $categoryId;
		
		$detail = $this->getURLContents($link);
		
		$data['title'] = $this->getContent($detail, '<H1 class=Title>', '</H1>', true);
		$data['headline'] = trim(strip_tags($this->getContent($detail, '<H2 class=Lead>', '</H2>', true)));
		$published_time = $this->getContent($detail, 'getElementById("pDateTime").innerHTML="', '"', true);
		$published_time = explode(',', $published_time);
		$published_time = $published_time[1] . ' ' . $published_time[2];
		$published_time = explode(' ', trim($published_time));
		$time = $published_time[2];
		$published_time = $published_time[0];
		$published_time = explode('/', $published_time);
		$published_time = $published_time[2] . '-' . $published_time[1] . '-' . $published_time[0];
		$published_time .= ' ' . $time;
		$data['published_time'] = $published_time;
		// echo $published_time;die();
		
		// die($data['published_time']);
		// echo $detail;die();
		$newsContent = $this->getContent($detail, '</H2>', '<div class="detailNS">', true);
		$newsContent = str_replace('src="', 'src="http://ngoisao.net', $newsContent);
		$data['thumbnail_url'] = $this->getContent($newsContent, '<img src="', '"', true);
		// echo $newsContent;die();
		// echo $thumbnail;die();
		// echo $detail;die();
		// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
		// echo $newsContent;die();
		$newsContent = str_replace('<IMG', '<IMG width=290', $newsContent);
		$newsContent = str_replace('<img', '<img width=290', $newsContent);
		$data['content'] = $newsContent;
		$data['title_en'] = Utility::unicode2Anscii($data['title']);
		$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
		$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
		// die($data['published_time']);
		$data['site_id'] = $siteId;
		// die($data['published_time']);
		$data['created_time'] = date('Y-m-d H:i:s');
		$data['original_url'] = $link;
		// var_dump($data);die();
        if (!empty($newsContent) && !empty($data['title'])) {
            $news = new News;
            $news->attributes = $data;
            if ($news->save(false)) {
                
            }
        }
		
	}
	
	public function getNgoisao() {
		$ngoisao = Yii::app()->params['site']['ngoisaonet'];
		$siteId = 5;
		
		foreach ($ngoisao as $c => $link) {
            $contents = $this->getURLContents($link);
            $items = $this->getContent($contents, '<item>', '</item>');
            // print_r($items);
            // echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
            // die();
            $i = 0;
            foreach ($items as $item) {
                // echo $item;die();
                $i++;
                $title = $this->getContent($item, '<title>', '</title>', true);
                $data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
                $description = $this->getContent($item, '<description>', '</description>', true);
                $data['headline'] = trim(strip_tags($this->getContent($description, '<![CDATA[', ']]>', true)));
                $data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
                $detailLink = $this->getContent($item, '<link>', '</link>', true);
                $detail = $this->getURLContents($detailLink);
                // echo $detail;die();
                $thumbnail = $this->getContent($detail, '<div id="ctl00_CPH1_TinChiTiet1_divImage"', '</div>', true);
                $data['thumbnail_url'] = 'http://ngoisao.net' . $this->getContent($description, '<img src="', '">', true);
                $newsContent = $this->getContent($detail, '</H2>', '<div class="detailNS">', true);
                $newsContent = str_replace('src="', 'src="http://ngoisao.net', $newsContent);
                // echo $newsContent;die();
                // echo $thumbnail;die();
                // echo $detail;die();
                // $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
                // echo $newsContent;die();
                if (!News::isExist($siteId, $detailLink)) {
					$newsContent = str_replace('<IMG', '<IMG width=290', $newsContent);
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
                    $data['content'] = $newsContent;
                    $data['title_en'] = Utility::unicode2Anscii($data['title']);
                    $data['headline_en'] = Utility::unicode2Anscii($data['headline']);
                    $data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
                    $data['site_id'] = $siteId;
                    // die($data['published_time']);
                    $data['created_time'] = date('Y-m-d H:i:s');
                    $data['original_url'] = $detailLink;
					$data['category_id'] = $c;
                    
                    $news = new News;
                    $news->attributes = $data;
                    if ($news->save(false)) {
                        if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
                    }
                }
            }
        }
	}
	
	public function getVnEconomy() {
		$vneconomy = Yii::app()->params['site']['vneconomy'];
		$siteId = 4;
		
		foreach ($vneconomy as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			$i = 0;
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$title = $this->getContent($item, '<title>', '</title>', true);
				$data['title'] = trim($this->getContent($title, '<![CDATA[', ']]>', true));
				$headline = $this->getContent($item, '<description>', '</description>', true);
				$data['headline'] = trim(strip_tags($this->getContent($headline, '<![CDATA[', ']]>', true)));
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				$detailLink = trim($this->getContent($detailLink, '<![CDATA[', ']]>', true));
				$detail = $this->getURLContents($detailLink);
				$data['thumbnail_url'] = $this->getContent($headline, 'src="', '"', true);
				$newsContent = $this->getContent($detail, '600px;line-height:22px">', '</div>', true);
				// echo $newsContent;die();
				// echo $thumbnail;die();
				// echo $detail;die();
				// $newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
				// echo $newsContent;die();
				if (!News::isExist($siteId, $detailLink)) {
					$newsContent = str_replace('<IMG', '<IMG width=290', $newsContent);
					$newsContent = str_replace('<img', '<img width=290', $newsContent);
					$data['content'] = $newsContent;
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					$data['site_id'] = $siteId;
					// die($data['published_time']);
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['original_url'] = $detailLink;
					$data['category_id'] = $c;
					$data['title_en'] = Utility::unicode2Anscii($data['title']);
					$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
					
					$news = new News;
					$news->attributes = $data;
					if ($news->save(false)) {
						//Add first news to featured
						if ($i <= 5) {
							// $lastId = Yii::app()->db->getLastInsertID();
							$lastId = $news->id;
							// die($lastId);
							$newsFeatured = new NewsFeatured;
							$newsFeatured->attributes = array(
								'news_id' 		=> $lastId,
								'created_time' 	=> date('Y-m-d H:i:s')
							);
							$newsFeatured->save(false);
						}
					}
				}
			}
		}
	}
	
	public function getVOAEnglish() {
		$link = 'http://www.voanews.com/templates/Articles.rss?sectionPath=/learningenglish/home';
		$contents = $this->getURLContents($link);
		$items = $this->getContent($contents, '<item>', '</item>');
		// print_r($items);
		// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
		// die();
		foreach ($items as $item) {
			// echo $item;die();
			$data['title'] = $this->getContent($item, '<title>', '</title>', true);
			$data['description'] = $this->getContent($item, '<description>', '</description>', true);
			$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
			$detailLink = $this->getContent($item, '<link>', '</link>', true);
			$detail = $this->getURLContents($detailLink);
			$newsContent = $this->getContent($detail, '<div class="articleBody">', '<div class="clearDiv"></div>', true);
			// echo $newsContent;die();
			if (!Voa::isExist($detailLink)) {
				$data['content'] = $newsContent;
				$data['published_time'] = gmdate('Y-m-d H:i:s', strtotime($data['published_time']));
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['original_url'] = $detailLink;
				$mediaUrl = $this->getContent($newsContent, 'value="file=', '&amp;', true);
				if (!empty($mediaUrl)) $data['media'] = $mediaUrl;
				else $data['media'] = '';
				$voa = new Voa;
				$voa->attributes = $data;
				if ($voa->save(false)) {
					
				}
			}
		}
	}
	
	public function getVnexpressVideo() {
		$baseUrl = 'http://vnexpress.net/video/ContentSearch.asp?ID=';
		$category = NewsCategory::model()->getClipCategory();
		
		foreach ($category as $cat) {
			// Get 10 page
			for ($i = 1;$i < 10;$i++) {
				$url = $baseUrl . $cat['id'] . '&page=' . $i;
				// echo $url;die();
				$content = $this->getURLContents($url);
				$videos = $this->getContent($content, 'CRV_Content', '</div>');
				if (!empty($videos)) {
					foreach ($videos as $video) {
						$data = array();
						$data['category_id'] = $cat['id'];
						$detail = $this->getContent($video, 'href="', '">', true);
						$originalUrl = 'http://vnexpress.net' . $detail;
						if (!Clip::model()->isExist($originalUrl)) {
							// echo $detail;die();
							$data['thumbnail_url'] = $this->getContent($video, 'src="', '"', true);
							$detailContent = $this->getURLContents($originalUrl);
							$title = $this->getContent($detailContent, 'NewsTitle', '/div>', true);
							$data['title'] = $this->getContent($title, '>', '<', true);
							$headline = $this->getContent($detailContent, 'NewsLead', '/div>', true);
							$data['headline'] = $this->getContent($headline, '>', '<', true);
							$time = $this->getContent($detailContent, 'NewsTime', '/div>', true);
							$time = $this->getContent($time, '>', '<', true);
							$time = explode('/', $time);
							$time = $time[2] . '-' . $time[1] . '-' . $time[0];
							$data['published_time'] = date('Y-m-d H:i:s', strtotime($time));
							$data['created_time'] = date('Y-m-d H:i:s');
							
							$script = $this->getContent($detailContent, 'createPlayer(', ')', true);
							$params = explode(',', $script);
							$xmlPath = 'http://vnexpress.net/' . urldecode('%2FService%2FFlashVideo%2FPlayListVideoPage.asp%3Fid%3D' . $params[0] . '%26f%3D' . $params[2]);
							$playerContent = $this->getURLContents($xmlPath);
							$data['streaming_url'] = $this->getContent($playerContent, 'link="', '"', true);
							$data['title_en'] = Utility::unicode2Anscii($data['title']);
							$data['headline_en'] = Utility::unicode2Anscii($data['headline']);						
							$data['original_url'] = $originalUrl;
							$data['content'] = '';
							$data['thumbnail_url'] = str_replace(' ', '%20', $data['thumbnail_url']);
							if ($data['streaming_url'] != '') {
								$data['streaming_url'] = str_replace(' ', '%20', $data['streaming_url']);
								$data['content'] = '<video poster="' . $data['thumbnail_url'] . '" controls>
									<source src="' . $data['streaming_url'] . '" type=\'video/mp4; codecs="avc1.4D401E, mp4a.40.2"\' />
									</video>';
							}

							// $i++;
							$clip = new Clip;
							$clip->attributes = $data;
							try {
								$clip->save(false);
							} catch (Exception $ex) {
								var_dump($ex);
							}
							// die('saved');
						}
					}
				}
			}
		}
		// $content = $this->getURLContents($videoPage);
		// echo $content;
	}
	
	public function getVnexpress() {
		$params = Yii::app()->params;
		$vnexpress = $params['site']['vnexpress'];
		
		foreach ($vnexpress as $c => $link) {
			// echo $link.'<br/>';
			$content = $this->getURLContents($link);
            // echo $content;
			$items = $this->getContent($content, '<item>', '</item>');
            // die(var_dump($items));
			$i = 0;
			// echo 'Total ' . count($items);
			foreach ($items as $item) {				
				$i++;
				// echo 'New i = ' . $i . '<br/>';
				$data = array();
				$data['title'] = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$headline = $this->getContent($item, '<description><![CDATA[', ']]></description>', true);
				$data['thumbnail_url'] = '';
				$thumbnail = $this->getContent($headline, '<IMG SRC="', '">', true);
				if (!empty($thumbnail))
					$data['thumbnail_url'] = $thumbnail;
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link>', '</link>', true);
				// $detailLink = 'http://vnexpress.net/gl/vi-tinh/2011/11/dien-thoai-sony-ericsson-dung-chip-loi-kep-lo-dien/';
				// $detailLink = 'http://vnexpress.net/gl/the-thao/bong-da/2011/11/real-sap-chi-dam-de-mua-neymar/';
                // $detailLink = 'http://vnexpress.net/gl/cuoi/video/2011/10/nhung-pha-tai-nan-hai-huoc/';
                // $detailLink = 'http://vnexpress.net/gl/suc-khoe/2011/10/xon-xao-clip-em-be-bi-bac-si-tu-choi-chua-benh-vi-ngheo/';
                // $detailLink = 'http://vnexpress.net/gl/doi-song/gia-dinh/2011/10/day-tre-cach-su-dung-dong-tien-hieu-qua/';
                // $detailLink = 'http://vnexpress.net/gl/ban-doc-viet/2011/10/hieu-the-nao-ve-thoa-thuan-nguyen-tac-giai-quyet-van-de-tren-bien-viet-trung/';
                // $detailLink = 'http://vnexpress.net/gl/cuoi/video/2011/10/nhung-tinh-huong-kho-do-trong-dam-cuoi/';
                // $detailLink = 'http://vnexpress.net/gl/van-hoa/2011/10/truc-diem-duoc-du-doan-doat-ngoi-hoa-hau-quoc-te/';
                // $detailLink = 'http://vnexpress.net/gl/vi-tinh/san-pham-moi/2011/10/nhung-cai-tien-noi-bat-nhat-cua-nen-tang-android-4-0/';
                // $detailLink = 'http://vnexpress.net/gl/the-thao/bong-da/2011/10/thu-mon-ghi-ban-bang-cu-phat-bong/';
                // $detailLink = 'http://vnexpress.net/gl/kinh-doanh/quoc-te/2011/10/can-canh-khach-san-tot-nhat-the-gioi/';
                // $detailLink = 'http://vnexpress.net/gl/van-hoa/2011/09/ve-dep-cua-tan-hoa-hau-hoan-vu/';
				// $detailLink = 'http://ebank.vnexpress.net/gl/ebank/thi-truong/2011/10/ty-gia-ngan-hang-va-cho-den-di-nguoc-dong-1/';
				if (!News::isExist(1, $detailLink)) {
					$detail = $this->getURLContents($detailLink);
					// echo $detail;die();
					$startTag = '<div class="content">';
					if (strstr($detail, 'cpms_content="true">'))
						$startTag = 'cpms_content="true">';
					$tmp = $this->getContent($detail, $startTag, '<div class="tag-parent">', true);
					$tmp = str_replace('src="/', 'src="http://vnexpress.net/', $tmp);
					// echo $tmp;die();
					// print_r($data);
					// die();
					if (!empty($tmp)) {
						// $toDel = $this->getContent($tmp, '<BR>', '</H2>', true);
						// echo 'Todel ' . $toDel;
						// $tmp = str_replace($toDel, '', $tmp);
						$tmp = $this->stripContent($tmp, '<BR>', '</H2>');
						// $tmp = $this->stripContent($tmp, '<script', '</script>');
						$toDel = $this->getContent($tmp, 'ShowTopicJS', ')', true);
						$toDel = 'ShowTopicJS ' . $toDel . ');';
						$tmp = str_replace($toDel, '', $tmp);
						$tmp = $this->stripContent($tmp, '<TABLE cellSpacing=0 cellPadding=3 width="60%" align=center bgColor=#ffff80 border=0>', '</TABLE>');
						$tmp = $this->stripContent($tmp, '<TABLE cellSpacing=0 cellPadding=3 align=center bgColor=#ffff80 border=0>', '</TABLE>');
						$tmp = $this->stripContent($tmp, '<TABLE cellSpacing=0 cellPadding=3 width=350 align=center bgColor=#ffffcc border=1>', '</TABLE>');
						$tmp = $this->stripContent($tmp, '<TABLE cellSpacing=0 cellPadding=3 width=350 align=center bgColor=#ffffbb border=1>', '</TABLE>');
						$tmp = $this->stripContent($tmp, '<TABLE cellSpacing=0 cellPadding=3 align=center bgColor=#ffff80 border=0>', '</TABLE>');
						$strip = $this->getContent($tmp, 'width=', ' ');
						foreach ($strip as $toStrip) {
							$tmp = str_replace('width='.$toStrip, 'width=290', $tmp);
						}
						$tmp = str_replace('width=29000%', 'width=100%', $tmp);
						$tmp = str_replace('<img', '<img width=290', $tmp);
						$tmp = str_replace('<IMG', '<IMG width=290', $tmp);
						$tmp = $this->stripContent($tmp, 'height=', ' ', true);
						$youtubeData = array();
						if (strstr($tmp, 'href=')) {
							$newLink = $this->getContent($tmp, 'href=', '</');
							// var_dump($newLink);die();
							if (!empty($newLink)) {
								$k = 0;
								foreach ($newLink as $oneLink) {
									if (!strstr($oneLink, 'http')) continue;
									$linkToReplace = $oneLink;
									$oneLink = $this->getContent($oneLink, '"', '"', true);
									// echo $oneLink;die();
									// $oneLink = 'http://vnexpress.net/gl/van-hoa/2011/09/ve-dep-cua-tan-hoa-hau-hoan-vu/page_3.asp';
									// $oneLink = 'http://vnexpress.net/gl/the-thao/bong-da/2011/10/thu-mon-ghi-ban-bang-cu-phat-bong/page_1.asp';
									if ((strstr($oneLink, '/Page') || strstr($oneLink, '/page')) && strstr($oneLink, 'http://vnexpress.net')) {
										// echo 'One link ' . $oneLink;
										$tmpContent = $this->getURLContents($oneLink);
										// echo strlen($tmpContent);die();
										if (empty($tmpContent) || strlen($tmpContent) < 250) {
											$oneLink = str_replace('/Page', '/page', $oneLink);
											$tmpContent = $this->getURLContents($oneLink);
										}
										// die($tmpContent);
										if (strstr($tmpContent, 'createPlayerEmbed')) {
											// die('come');
											$playerContent = $this->getContent($tmpContent, 'createPlayerEmbed(', ')', true);
											$params = explode(',', $playerContent);
											// var_dump($params);
											$xmlPath = 'http://vnexpress.net' . urldecode("%2FService%2FFlashVideo%2FPlayListVideoPage.asp%3Fid%3D" . $params[0] . "%26f%3D" . $params[4]);
											$videoInfo = $this->getURLContents($xmlPath);
											$videoUrl = $this->getContent($videoInfo, 'link="', '"', true);
											$videoThumbnail = $this->getContent($videoInfo, 'descriptionImage="', '"', true);
											$videoTag = '<video poster="' . $videoThumbnail . '" controls>
											<source src="' . $videoUrl . '" type=\'video/mp4; codecs="avc1.4D401E, mp4a.40.2"\' />
											</video>';
											// die('<A href=' . $linkToReplace . '</A>');
											$tmp = str_replace('<A href=' . $linkToReplace . '</A>', $videoTag, $tmp);
										} else {
											$concatContent = '';
											$startTag = '<div class="content">';
											if (strstr($tmpContent, 'cpms_content="true">'))
												$startTag = 'cpms_content="true">';
											$concatContent .= $this->getContent($tmpContent, $startTag, '</div>', true);
											$concatContent = str_replace('src="/', 'src="http://vnexpress.net/', $concatContent);
											$strip = $this->getContent($concatContent, 'width=', ' ');
											foreach ($strip as $toStrip) {
												$tmp = str_replace('width='.$toStrip, 'width=290', $concatContent);
											}
											// Get youtube video link
											if (strstr($concatContent, 'http://www.youtube.com/v/')) {
												$k++;
												$data['youtube_video'] = 1;
												$videoId = $this->getContent($concatContent, 'http://www.youtube.com/v/', '?', true);
												$videoUrl = 'http://www.youtube.com/watch?v=' . $videoId;
												$key = 'YOUTUBE_VIDEO_' . $k;
												$youtubeData[$key] = $videoUrl;
												// var_dump($this->_parser->get('http://www.youtube.com/watch?v=6uhhBDuxD5Y&feature=related'));
												// var_dump($this->_parser->get($videoUrl));die();
												/*
												$videoInfo = $this->_parser->get($videoUrl);
												$videoUrl = '';
												// var_dump($videoInfo);
												if ($videoInfo->files['video/mp4'] != '')
													$videoUrl = $videoInfo->files['video/mp4'];
												if ($videoUrl != '') {
													$videoTag = '<video poster="' . $videoInfo->thumbnails[0]->url . '" controls>
													<source src="' . $videoUrl . '" type=\'video/mp4; codecs="avc1.4D401E, mp4a.40.2"\' />
													</video>';
													$concatContent = $videoTag;
												} else $concatContent = '';
												// die($concatContent);
												// die('<A href=' . $linkToReplace . '</A>');
												// die($tmp);
												*/
												$tmp = str_replace('<A href=' . $linkToReplace . '</A>', $key, $tmp);
											} else $tmp = str_replace('<A href=' . $linkToReplace . '</A>', $concatContent, $tmp);
										}          
									} else {
										$tmp = str_replace('<A href=' . $linkToReplace . '</A>', strip_tags("<A href=" . $linkToReplace . "</A>"), $tmp);
									}
								}
							}
						}
						
						if (strstr($tmp, 'createPlayerEmbed')) {
							// die('come');
							$playerContent = $this->getContent($tmp, 'createPlayerEmbed(', ')', true);
							$params = explode(',', $playerContent);
							// var_dump($params);
							$xmlPath = 'http://vnexpress.net' . urldecode("%2FService%2FFlashVideo%2FPlayListVideoPage.asp%3Fid%3D" . $params[0] . "%26f%3D" . $params[4]);
							$videoInfo = $this->getURLContents($xmlPath);
							$videoUrl = $this->getContent($videoInfo, 'link="', '"', true);
							$videoThumbnail = $this->getContent($videoInfo, 'descriptionImage="', '"', true);
							$videoTag = '<video poster="' . $videoThumbnail . '" controls>
							<source src="' . $videoUrl . '" type=\'video/mp4; codecs="avc1.4D401E, mp4a.40.2"\' />
							</video>';
							// die($tmp);
							$stringToReplace = $this->getContent($tmp, '<span id="FlashPlayer', '</span>', true);
							// die($stringToReplace);
							$tmp = str_replace('<span id="FlashPlayer' . $stringToReplace . '</span>', $videoTag, $tmp);
							// $tmp = $videoTag;
						}
						// die($tmp);
						$data['headline'] = '';
						$headline = '<H2' . $this->getContent($tmp, '<H2', '</H2>', true) . '</H2>';
						$headline = strip_tags($headline);
						if (!empty($headline))
							$data['headline'] = $headline;
						// die(var_dump($youtubeData));
						$data['youtube_data'] = json_encode($youtubeData);
						$data['title_en'] = Utility::unicode2Anscii($data['title']);
						$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
						
						$tmp = $this->stripContent($tmp, '<H1 ', '</H1>', true);
						$tmp = $this->stripContent($tmp, '<H2 ', '</H2>', true);
						$tmp = $this->stripContent($tmp, '<script', '</script>', true);
						// die($tmp);
						// $tmp = str_replace('H1', 'H3', $tmp);                    
						// $tmp = str_replace('H2', 'H3', $tmp);
						// die($tmp);
						$tmp = str_replace("\n", '', $tmp);
						// die($tmp);
						$data['content'] = $tmp;
						$data['original_url'] = $detailLink;
						$data['category_id'] = $c;
						$data['site_id'] = 1;
						$data['created_time'] = date('Y-m-d H:i:s');
						$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
						// print_r($data);die();
						// echo $tmp . '<br/><br/>';
					
						// $i++;
						$news = new News;
						$news->attributes = $data;
						try {
							if ($news->save(false)) {
								//Add first news to featured
								// echo 'I = ' . $i . '<br/>';
								if ($i <= 5) {
									// echo 'Add featured<br/>';
									$lastId = $news->id;
									$newsFeatured = new NewsFeatured;
									$newsFeatured->attributes = array(
										'news_id' 		=> $lastId,
										'created_time' 	=> date('Y-m-d H:i:s')
									);
									$newsFeatured->save(false);
								}
							}
							// die();
						} catch (Exception $ex) {
							var_dump($ex);
						}
						// die('saved');
					}
					// die('come');
				}
			}
		}
	}
	
	public function getDantri() {
		$params = Yii::app()->params;
		$dantri = $params['site']['dantri'];
		
		foreach ($dantri as $c => $link) {
			$content = $this->getURLContents($link);
			$items = $this->getContent($content, '<item>', '</item>');
			$i = 0;
			// echo $link.'<br/>';
			foreach ($items as $item) {
				$i++;
				// echo $item;die();
				$data = array();
				$data['title'] = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$headline = $this->getContent($item, '<description><![CDATA[', ']]></description>', true);
				// die($headline);
				$data['thumbnail_url'] = '';
				$thumbnail = $this->getContent($headline, 'ImgFilePath=/', '&width', true);
				if (empty($thumbnail))
					$thumbnail = $this->getContent($headline, 'src="', '"', true);
				if (!empty($thumbnail))
					$data['thumbnail_url'] = $thumbnail;
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link><![CDATA[', ']]></link>', true);
				// echo $detailLink . '<br/>';
				// $detailLink = 'http://dantri.com.vn/c673/s673-539159/xin-thay-dung-khoc.htm';
				// $detailLink = 'http://dantri.com.vn/c202/s202-539449/thu-tuc-tach-khau.htm';
				// $detailLink = 'http://dantri.com.vn/c20/s20-539679/o-to-tai-bi-tau-hoa-dam-bay-xuong-ruong.htm';
				// $detailLink = 'http://dantri.com.vn/c741/s741-539694/u23-viet-nam-dung-buoc-tai-ban-ket-truoc-indonesia.htm';
				// $detailLink = 'http://dantri.com.vn/c702/s702-527697/top-ba-sao-nhi-va-cau-chuyen-doi-ban-tho-rua.htm';
				if (!News::isExist(2, $detailLink)) {
					// die($headline);
					$detail = $this->getURLContents($detailLink);
					// echo $detail;die();
					$tmp = $this->getContent($detail, '<div class="fon31 mt1">', "id='hidNextUsing'", true);
					// die($tmp);
					if (empty($tmp))
						$tmp = $this->getContent($detail, 'ctl00_IDContent_BlogDetail1_hplTitle">', "id='hidNextUsing'", true);
					if (empty($tmp))
						$tmp = $this->getContent($detail, 'ctl00_IDContent_Tin_Chi_Tiet">', "id='hidNextUsing'", true);
					if (empty($tmp)) continue;
					$tmp .= "id='hidNextUsing'/>";
					// die($tmp) . '123';
					$startTag = '<div class="fon33 mt1">';
					if (strstr($detail, '<div class="blogsapo">'))
						$startTag = '<div class="blogsapo">';
					$data['headline'] = '';
					$headline = $this->getContent($tmp, $startTag, '</div>', true);
					if (!empty($headline))
						$data['headline'] = $headline;
					// $data['headline'] = $this->stripContent($data['headline'], '<br>', '</a>');
					// echo $headline;die();
					$tmp = str_replace($data['title'], '', $tmp);					
					$tmp = str_replace($data['headline'], '', $tmp);
					$stripHeadline = $this->getContent($data['headline'], '<br>', '</a>');
					if (!empty($stripHeadline)) {
						foreach ($stripHeadline as $headline) {
							$data['headline'] = str_replace($headline, '', $data['headline']);
						}
					}
					$data['headline'] = str_replace('<br></a>', '', $data['headline']);
					// print_r($data);die();					
					// echo $tmp;die();
					// $tmp = str_replace('src="/', 'src="http://vnexpress.net/', $tmp);
					if (!empty($tmp)) {
						$tmp = $this->stripContent($tmp,'<br><a href=', '</b></a></div>');
						$tmp = str_replace('<br><a href=</b></a>', '', $tmp);
						$tmp = str_replace('width=29000%', 'width=100%', $tmp);
						$tmp = str_replace('<img', '<img width=290', $tmp);
						$tmp = str_replace('<IMG', '<IMG width=290', $tmp);
						// echo $tmp;
						$data['content'] = $tmp;
						$data['original_url'] = $detailLink;
						$data['category_id'] = $c;
						$data['site_id'] = 2;
						$data['created_time'] = date('Y-m-d H:i:s');
						$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
						$data['title_en'] = Utility::unicode2Anscii($data['title']);
						$data['headline_en'] = Utility::unicode2Anscii($data['headline']);
						// print_r($data);die();
						// $tmp = strip_tags($tmp, '<img><p><br><table><tr><td><h1><h2>');
						// echo $tmp . '<br/><br/>';
						
						// $i++;
						$news = new News;
						$news->attributes = $data;
						try {
							if (!News::isExist(1, $detailLink)) {
								if ($news->save(false)) {
									//Add first news to featured
									if ($i <= 5) {
										// $lastId = Yii::app()->db->getLastInsertID();
										$lastId = $news->id;
										// die($lastId);
										$newsFeatured = new NewsFeatured;
										$newsFeatured->attributes = array(
											'news_id' 		=> $lastId,
											'created_time' 	=> date('Y-m-d H:i:s')
										);
										$newsFeatured->save(false);
									}
								}
							}
						} catch (Exception $ex) {
							var_dump($ex);
						}
						// die('saved');
					}
				}
				// die();
			}
		}
	}
	
    public function cacheMedia($limit) {
        $config = Vega_App::getInstance()->coreConfig;
        $crawlerDao = Vega_Dao::factory('Crawler');
        $this->_vega_log->write("Caching from media table");
        $audioLink = $crawlerDao->getSourceLink("media", $limit);
        $this->cache($audioLink, "media");

        $this->_vega_log->write("Caching from song table");
        $audioLink = $crawlerDao->getSourceLink("song", $limit);
        $this->cache($audioLink, $audioDir, "song");
    }

    /*
     * type: 1 - audio
     *       2 - video
     * table: table to update encode id
     */

    public function cache($source, $table) {
        $crawlerDao = Vega_Dao::factory('Crawler');
        $config = Vega_App::getInstance()->coreConfig;
        $videoExt = $config['mediaExt']['video'];
        $audioExt = $config['mediaExt']['audio'];
        $videoDir = $config['streaming']['video_file_src'] . "/";
        $audioDir = $config['streaming']['audio_file_src'] . "/";

        $i = 0;
        foreach ($source as $oneLink) {
            $source_link = $oneLink['source_link'];
            $source_link = trim($source_link);
            $id = $oneLink['id'];
            $lastDot = strrpos($source_link, '.');
            $ext = trim(substr($source_link, $lastDot + 1, strlen($source_link)));
            $type = "";
            $dir = "";
            if (in_array($ext, $audioExt)) {
                $type = 1;
                $dir = $audioDir;
            } else if (in_array($ext, $videoExt)) {
                $type = 2;
                $dir = $videoDir;
            }
            $fileDir = $dir . date("Y", time()) . "/" . date("m", time());
            if (!is_dir($fileDir))
                mkdir($fileDir, 0, true);
            $tmpFileSrc = date("Y", time()) . "/" . date("m", time());
            $fileName = uniqid() . "." . $ext;
            $tmpFileSrc .= "/" . $fileName;
            $fileSrc = $fileDir . "/" . $fileName;
            //echo "Downloading: <b>".$id."</b> - ".$source_link."<br/>";
            $this->_vega_log->write("Downloading: " . $id . " - " . $source_link);
            if ($this->isUrlExists($source_link)) {
                $download = $this->curl_download($source_link, $fileSrc);
                if ($download) {
                    if ($type == 1) {
                        $videoEncodeId = $crawlerDao->addAudioToEncode($source_link, $tmpFileSrc);
                        $this->_vega_log->write("Downloaded Path $fileSrc - Encode id = " . $videoEncodeId);
                    } else {
                        $videoEncodeId = $crawlerDao->addVideoToEncode($source_link, $tmpFileSrc);
                        $this->_vega_log->write("Downloaded Path $fileSrc - Encode id = " . $videoEncodeId);
                    }
                    $crawlerDao->updateFromEncode($id, $videoEncodeId, "encode_id", $table);
                } else
                    $this->_vega_log->write("Download Failded! See log for more information!");
                $i++;
                //            if ($i > 0) die;
            } else
                $this->_vega_log->write($source_link . " is not exist!");
        }
    }	

    function curl_download($url, $abosultePath) {
        if (file_exists($abosultePath)) {
            return;
        }

        $path = dirname($abosultePath);
        if (!is_dir($path))
            @chmod($path, 0777);

        $out = fopen($abosultePath, 'w+');
        if ($out === FALSE) {
            return false;
        }

        $url = str_replace(' ', '%20', $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret = false;
        } else {
            # 200 - OK
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($responseCode == 200)
                $ret = true;
            else {
                file_put_contents('D:\log\m.ringring\craw.log', $responseCode . ' - ' . $url . "\n", FILE_APPEND | LOCK_EX);
                $ret = false;
            }
        }

        curl_close($ch);
        fclose($out);
        return $ret;
    }

}