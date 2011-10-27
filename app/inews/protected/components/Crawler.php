<?php

class Crawler {

    private $_curl = null;

    public function __construct() {
        $this->_curl = curl_init();
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
        $paramsStr = implode(",", $params);
        $options = array(
            CURLOPT_URL => $url,
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

    public function getContent($content, $open, $close, $getFirst = false) {
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
                        return trim($result[0]);
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
        return trim($content);
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
		if (empty($content)) return '';
		
		$toDel = $this->getContent($content, $open, $close, true);
        if ($all) {
            $toDel = $open . $toDel . $close;
            // echo $toDel.'<br/>';
        }
		$content = str_replace($toDel, '', $content);
		
		return $content;
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
			// $i = 0;
			// echo 'Total ' . count($items);
			foreach ($items as $item) {				
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
                // $detailLink = 'http://vnexpress.net/gl/vi-tinh/san-pham-moi/2011/10/nhung-cai-tien-noi-bat-nhat-cua-nen-tang-android-4-0/';
                // $detailLink = 'http://vnexpress.net/gl/the-thao/bong-da/2011/10/thu-mon-ghi-ban-bang-cu-phat-bong/';
                // $detailLink = 'http://vnexpress.net/gl/kinh-doanh/quoc-te/2011/10/can-canh-khach-san-tot-nhat-the-gioi/';
                // $detailLink = 'http://vnexpress.net/gl/van-hoa/2011/09/ve-dep-cua-tan-hoa-hau-hoan-vu/';
				// $detailLink = 'http://ebank.vnexpress.net/gl/ebank/thi-truong/2011/10/ty-gia-ngan-hang-va-cho-den-di-nguoc-dong-1/';
				$detail = $this->getURLContents($detailLink);
				// echo $detail;
				$startTag = '<div class="content">';
				if (strstr($detail, 'cpms_content="true">'))
					$startTag = 'cpms_content="true">';
				$tmp = $this->getContent($detail, $startTag, '</div>', true);
				$tmp = str_replace('src="/', 'src="http://vnexpress.net/', $tmp);
				// echo $tmp;				
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
                        $tmp = str_replace('width='.$toStrip, 'width=320', $tmp);
                    }
                    $tmp = $this->stripContent($tmp, 'height=', ' ', true);
					
                    if (strstr($tmp, '<A href=')) {
                        $newLink = $this->getContent($tmp, '<A href=', '</A>');
                        // var_dump($newLink);die();
                        if (!empty($newLink)) {
                            foreach ($newLink as $oneLink) {
                                $linkToReplace = $oneLink;
                                $oneLink = $this->getContent($oneLink, '"', '"', true);
                                // echo $oneLink;
                                // $oneLink = 'http://vnexpress.net/gl/van-hoa/2011/09/ve-dep-cua-tan-hoa-hau-hoan-vu/page_3.asp';
                                // $oneLink = 'http://vnexpress.net/gl/the-thao/bong-da/2011/10/thu-mon-ghi-ban-bang-cu-phat-bong/page_1.asp';
                                $tmpContent = $this->getURLContents($oneLink);
                                if (empty($tmpContent)) {
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
                                    $startTag = '<div class="content">';
                                    if (strstr($tmpContent, 'cpms_content="true">'))
                                        $startTag = 'cpms_content="true">';
                                    $concatContent .= $this->getContent($tmpContent, $startTag, '</div>', true);
                                    $concatContent = str_replace('src="/', 'src="http://vnexpress.net/', $concatContent);
                                    // die($concatContent);
                                    // die('<A href=' . $linkToReplace . '</A>');
                                    // die($tmp);
                                    $tmp = str_replace('<A href=' . $linkToReplace . '</A>', $concatContent, $tmp);
                                }                                        
                            }
                        }
                    }
                    
					$data['headline'] = '';
					$headline = $this->getContent($tmp, '<H2 class=Lead>', '</H2>', true);
					if (!empty($headline))
						$data['headline'] = $headline;
                    
                    $tmp = $this->stripContent($tmp, '<H1 ', '</H1>', true);
					$tmp = $this->stripContent($tmp, '<H2 ', '</H2>', true);
                    $tmp = str_replace('H1', 'H3', $tmp);                    
                    $tmp = str_replace('H2', 'H3', $tmp);
					$data['content'] = $tmp;
					$data['original_url'] = $detailLink;
					$data['category_id'] = $c;
					$data['site_id'] = 1;
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					print_r($data);die();
					// echo $tmp . '<br/><br/>';
					if (!News::isExist(1, $detailLink)) {
						// $i++;
						$news = new News;
						$news->attributes = $data;
						try {
							if ($news->save(false)) {
								//Add first news to featured
								// echo 'I = ' . $i . '<br/>';
								if ($c == 1) {
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
						} catch (Exception $ex) {
							var_dump($ex);
						}
						die('saved');
					}
					// die('come');
				}
			}
		}
	}
	
	public function getDantri() {
		$params = Yii::app()->params;
		$vnexpress = $params['site']['dantri'];
		
		foreach ($vnexpress as $c => $link) {
			$content = $this->getURLContents($link);
			$items = $this->getContent($content, '<item>', '</item>');
			// $i = 0;
			// echo $link.'<br/>';
			foreach ($items as $item) {
				// echo $item;
				$data = array();
				$data['title'] = $this->getContent($item, '<title><![CDATA[', ']]></title>', true);
				$headline = $this->getContent($item, '<description><![CDATA[', ']]></description>', true);
				// die($data['headline']);
				$data['thumbnail_url'] = '';
				$thumbnail = $this->getContent($headline, 'ImgFilePath=/', '&width', true);
				if (!empty($thumbnail))
					$data['thumbnail_url'] = $thumbnail;
				$data['published_time'] = $this->getContent($item, '<pubDate>', '</pubDate>', true);
				$detailLink = $this->getContent($item, '<link><![CDATA[', ']]></link>', true);
				// echo $detailLink . '<br/>';
				// $detailLink = 'http://dantri.com.vn/c702/s702-527697/top-ba-sao-nhi-va-cau-chuyen-doi-ban-tho-rua.htm';
				$detail = $this->getURLContents($detailLink);
				// echo $detail;
				$tmp = $this->getContent($detail, '<div class="fon31 mt1">', "id='hidNextUsing'", true);
				if (empty($tmp))
					$tmp = $this->getContent($detail, 'ctl00_IDContent_BlogDetail1_hplTitle">', "id='hidNextUsing'", true);
				if (empty($tmp))
					$tmp = $this->getContent($detail, 'ctl00_IDContent_Tin_Chi_Tiet">', "id='hidNextUsing'", true);
				$tmp .= "id='hidNextUsing'/>";
				$startTag = '<div class="fon33 mt1">';
				if (strstr($detail, '<div class="blogsapo">'))
					$startTag = '<div class="blogsapo">';
					
				$data['headline'] = '';
				$headline = $this->getContent($tmp, $startTag, '</div>', true);
				if (!empty($headline))
					$data['headline'] = $headline;
				$data['headline'] = $this->stripContent($data['headline'], '<br>', '</a>');
				// print_r($data);die();
				// echo $tmp;die();
				// $tmp = str_replace('src="/', 'src="http://vnexpress.net/', $tmp);
				if (!empty($tmp)) {
					$tmp = $this->stripContent($tmp,'<br><a href=', '</b></a></div>');
					$tmp = str_replace('<br><a href=</b></a>', '', $tmp);
					// echo $tmp;
					$data['content'] = $tmp;
					$data['original_url'] = $detailLink;
					$data['category_id'] = $c;
					$data['site_id'] = 2;
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['published_time'] = date('Y-m-d H:i:s', strtotime($data['published_time']));
					// print_r($data);die();
					$tmp = strip_tags($tmp, '<img><p><br><table><tr><td><div><h1><h2>');
					// echo $tmp . '<br/><br/>';
					if (!News::isExist(2, $detailLink)) {
						// $i++;
						$news = new News;
						$news->attributes = $data;
						try {
							if ($news->save(false)) {
								//Add first news to featured
								if ($c == 1) {
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