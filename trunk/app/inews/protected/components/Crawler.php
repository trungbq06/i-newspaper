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
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'require',
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_ENCODING => "gzip",
        );
        curl_setopt_array($this->_curl, $options);
        try {
            $contents = curl_exec($this->_curl);
        } catch (Exception $ex) {
            Yii::log($ex->getMessage());
        }

        return $contents;
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
	
	public function getXKCN() {
		$url = 'http://xkcn.info/rss';
		$contents = $this->getURLContents($url);
        echo $contents;
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
				$this->save_image($data['small_thumbnail_url'], $path);
				$imgSize = getimagesize($path);
				exec('rm -f ' . $path);
				$photo = new Xkcn;
				$photo->attributes = $data;				
				$photo->width = 160;
				$photo->height = (160 * $imgSize[1]) / $imgSize[0];
				$photo->created_time = date('Y-m-d H:i:s');
				$photo->save(false);
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
		$siteId = 9;
		
		foreach ($yahoo as $c => $link) {
			$contents = $this->getURLContents($link);
			$items = $this->getContent($contents, '<item>', '</item>');
			$siteId = 22;
			// print_r($items);
			// echo gmdate('Y-m-d H:i:s', strtotime('Thu, 2 Feb 2012 15:06:56 GMT'));
			// die();
			foreach ($items as $item) {
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
	}
	
	public function getPcWorld() {
		$pcworld = Yii::app()->params['site']['2sao'];
        $pcworld = array(
            1 => 'http://rss.pcworld.com.vn/articles/tin-tuc/'
        );
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
			die();
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