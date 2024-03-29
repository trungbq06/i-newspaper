<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.common.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'1',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			// 'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		/**/
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),/**/
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'sqlite:protected/data/vnfood_summer.sqlite',
			// 'connectionString' => 'mysql:host=123.30.188.115;dbname=inews',
			'emulatePrepare' => true,
			// 'username' => 'root',
			// 'password' => 'ilive123312',
			'charset' => 'utf8',
		),
		*/
		
		'db'=>array(
			'connectionString' => 'mysql:host=123.30.188.115;dbname=inews',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'ilive123312',
			'charset' => 'utf8',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/**/
				// array(
					// 'class'=>'CWebLogRoute',
                    // 'showInFireBug' => true
				// ),
				/**/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'limit'		=> 20,
		'not_release' => true,
		'site' => array(
			'vnexpress' => array(
				1		=> 'http://vnexpress.net/rss/gl/trang-chu.rss',
				2		=> 'http://vnexpress.net/rss/gl/xa-hoi.rss',
				3		=> 'http://vnexpress.net/rss/gl/the-gioi.rss',
				4		=> 'http://vnexpress.net/rss/gl/kinh-doanh.rss',
				5		=> 'http://vnexpress.net/rss/gl/van-hoa.rss',
				6		=> 'http://vnexpress.net/rss/gl/the-thao.rss',
				7		=> 'http://vnexpress.net/rss/gl/phap-luat.rss',
				8		=> 'http://vnexpress.net/rss/gl/doi-song.rss',
				9		=> 'http://vnexpress.net/rss/gl/khoa-hoc.rss',
				10		=> 'http://vnexpress.net/rss/gl/vi-tinh.rss',
				11		=> 'http://vnexpress.net/rss/gl/oto-xe-may.rss',
				12		=> 'http://vnexpress.net/rss/gl/ban-doc-viet.rss',
				13		=> 'http://vnexpress.net/rss/gl/ban-doc-viet-tam-su.rss',
				14		=> 'http://vnexpress.net/rss/gl/cuoi.rss'
			),
			'dantri'	=> array(
				1		=> 'http://dantri.com.vn/trangchu.rss',
				2		=> 'http://dantri.com.vn/xa-hoi.rss',
				
				31		=> 'http://dantri.com.vn/chinh-tri.rss',
				32		=> 'http://dantri.com.vn/phongsu.rss',
				33		=> 'http://dantri.com.vn/moi-truong.rss',
				
				3		=> 'http://dantri.com.vn/Thegioi.rss',
				34		=> 'http://dantri.com.vn/donga.rss',
				35		=> 'http://dantri.com.vn/eu.rss',
				36		=> 'http://dantri.com.vn/tgchaumy.rss',
				37		=> 'http://dantri.com.vn/tgdiemnong.rss',
				38		=> 'http://dantri.com.vn/kieubao.rss',
				39		=> 'http://dantri.com.vn/tet-viet-xa-xu.rss',
				
				6		=> 'http://dantri.com.vn/The-Thao.rss',
				40		=> 'http://www.dantri.com.vn/bongtrongnuoc.rss',				
				41		=> 'http://www.dantri.com.vn/bongquocte.rss',				
				42		=> 'http://www.dantri.com.vn/cupchauau.rss',				
				43		=> 'http://www.dantri.com.vn/bongdaanh.rss',				
				44		=> 'http://www.dantri.com.vn/bongdaitalia.rss',				
				45		=> 'http://www.dantri.com.vn/bongdataybannha.rss',				
				46		=> 'http://www.dantri.com.vn/tennis_duaxe.rss',				
				47		=> 'http://www.dantri.com.vn/cacmonkhac.rss',				
				48		=> 'http://www.dantri.com.vn/sea-games-26.rss',				
				
				16		=> 'http://dantri.com.vn/giaoduc-khuyenhoc.rss',
				49		=> 'http://www.dantri.com.vn/tuyensinh.rss',
				50		=> 'http://www.dantri.com.vn/duhoc.rss',
				51		=> 'http://www.dantri.com.vn/guong-sang.rss',
				52		=> 'http://www.dantri.com.vn/khuyen-hoc.rss',
				53		=> 'http://www.dantri.com.vn/nhantaidatviet.rss',
				54		=> 'http://www.dantri.com.vn/john-linh.rss',
				55		=> 'http://www.dantri.com.vn/tuyen-sinh.rss',
				
				17		=> 'http://dantri.com.vn/tamlongnhanai.rss',
				56		=> 'http://www.dantri.com.vn/danh-sach-ung-ho.rss',
				57		=> 'http://www.dantri.com.vn/ket-chuyen.rss',
				58		=> 'http://www.dantri.com.vn/hoan-canh.rss',
				
				4		=> 'http://dantri.com.vn/kinhdoanh.rss',
				59		=> 'http://www.dantri.com.vn/chungkhoan.rss',
				60		=> 'http://www.dantri.com.vn/thitruong.rss',
				61		=> 'http://www.dantri.com.vn/doanhnghiep.rss',
				62		=> 'http://www.dantri.com.vn/quocte.rss',
				
				18		=> 'http://dantri.com.vn/giaitri.rss',
				63		=> 'http://www.dantri.com.vn/van-hoa.rss',				
				64		=> 'http://www.dantri.com.vn/amnhac.rss',				
				65		=> 'http://www.dantri.com.vn/phim.rss',				
				66		=> 'http://www.dantri.com.vn/thoitrang.rss',				
				67		=> 'http://www.dantri.com.vn/anchoi.rss',				
				
				19		=> 'http://dantri.com.vn/skphapluat.rss',
				20		=> 'http://dantri.com.vn/nhipsongtre.rss',				
				68		=> 'http://dantri.com.vn/nguoiviettre.rss',				
				
				21		=> 'http://dantri.com.vn/tinhyeu-gioitinh.rss',
				69		=> 'http://dantri.com.vn/tinhyeu.rss',
				70		=> 'http://dantri.com.vn/giadinh.rss',
				71		=> 'http://dantri.com.vn/goctamhon.rss',
				
				22		=> 'http://dantri.com.vn/suckhoe.rss',				
				72		=> 'http://dantri.com.vn/gioitinh.rss',				
				73		=> 'http://dantri.com.vn/tu-van.rss',				
				74		=> 'http://dantri.com.vn/lam-dep.rss',				
				
				23		=> 'http://dantri.com.vn/cong-nghe.rss',
				75		=> 'http://dantri.com.vn/vitinh.rss',
				76		=> 'http://dantri.com.vn/dienthoai.rss',
				77		=> 'http://dantri.com.vn/thuthuat.rss',
				
				24		=> 'http://dantri.com.vn/otoxemay.rss',
				78		=> 'http://dantri.com.vn/thi-truong.rss',
				79		=> 'http://dantri.com.vn/nguoivaxe.rss',
				80		=> 'http://dantri.com.vn/vanhoaxe.rss',
				
				30		=> 'http://dantri.com.vn/diendan-bandoc.rss',
				81		=> 'http://dantri.com.vn/tu-van-phap-luat.rss',
				82		=> 'http://dantri.com.vn/hoi-amc.rss',
				83		=> 'http://dantri.com.vn/goc-anh.rss',
				
				// 25		=> 'http://dantri.com.vn/dien-dan.rss',
				26		=> 'http://dantri.com.vn/chuyenla.rss',
				// 27		=> 'http://dantri.com.vn/blog.rss',
				// 28		=> 'http://dantri.com.vn/nghe-nghiep.rss',
				// 29		=> 'http://dantri.com.vn/games_asp_.rss',
			),
			'vneconomy' => array(
				1		=> 'http://vneconomy.vn/rss/trang-chu',
				85		=> 'http://vneconomy.vn/rss/thoi-su',
				86		=> 'http://vneconomy.vn/rss/doanh-nghiep',
				87		=> 'http://vneconomy.vn/rss/tai-chinh',
				88		=> 'http://vneconomy.vn/rss/chung-khoan',
				89		=> 'http://vneconomy.vn/rss/giao-thuong',
				90		=> 'http://vneconomy.vn/rss/thi-truong',
				3		=> 'http://vneconomy.vn/rss/the-gioi',
				91		=> 'http://vneconomy.vn/rss/bat-dong-san',
				92		=> 'http://vneconomy.vn/rss/cuoc-song-so',
				93		=> 'http://vneconomy.vn/rss/xe-360',
			),
			'2sao' => array(
				1		=> 'http://2sao.vn/rss/trangchu.rss',
				120		=> 'http://2sao.vn/rss/sao.rss',
				121		=> 'http://2sao.vn/rss/amnhac.rss',
				122		=> 'http://2sao.vn/rss/phim.rss',
				123		=> 'http://2sao.vn/rss/hoidap.rss',
				124		=> 'http://2sao.vn/rss/sukien.rss',
				125		=> 'http://2sao.vn/rss/doisong.rss',
				126		=> 'http://2sao.vn/rss/congnghe.rss'
			),
			'land_cafe' => array(
				1		=> 'http://land.cafef.vn/Trang-chu.rss',
				128		=> 'http://cafef.vn/Tin-moi.rss',
				129		=> 'http://cafef.vn/Thi-truong-chung-khoan.rss',
				130		=> 'http://cafef.vn/Bat-dong-san.rss',
				131		=> 'http://cafef.vn/Doanh-nghiep.rss',
				132		=> 'http://cafef.vn/Tai-chinh-ngan-hang.rss',
				133		=> 'http://cafef.vn/Tai-chinh-quoc-te.rss',
				134		=> 'http://cafef.vn/Kinh-te-vi-mo.rss',
				135		=> 'http://cafef.vn/Hang-hoa-nguyen-lieu.rss',
				136		=> 'http://cafef.vn/Doanh-nhan.rss',
				137		=> 'http://cafef.vn/Hoi-nghi-hoi-thao.rss',
				138		=> 'http://cafef.vn/Doanh-nghiep-gioi-thieu.rss',
				139		=> 'http://cafef.vn/Lich-su-kien.rss',
				140		=> 'http://cafef.vn/Tin-doanh-nghiep.rss'
			),
			'pcworld'   => array(
				127		=> 'http://rss.pcworld.com.vn/articles/tin-tuc/',
				168		=> 'http://rss.pcworld.com.vn/articles/san-pham/',
				169		=> 'http://rss.pcworld.com.vn/articles/tieu-dung/',
				170		=> 'http://rss.pcworld.com.vn/articles/cong-nghe/',
				171		=> 'http://rss.pcworld.com.vn/articles/quan-ly/',
				172		=> 'http://rss.pcworld.com.vn/articles/chuyen-muc/',
				173		=> 'http://rss.pcworld.com.vn/articles/su-kien/',
			),
			'genk' => array(
				1		=> 'http://genk.vn/dien-thoai.rss'
			),
			'zingnews' => array(
				1		=> 'http://www.zing.vn/news/rss/new.html'
			),
			'24h' => array(
				1		=> 'http://www.24h.com.vn/upload/rss/tintuctrongngay.rss'
			),
			'autonet' => array(
				1		=>'http://autonet.com.vn/search/select/?q=siteid:258%20AND%20cateid:4633&start=0&rows=100&r=&wt=xml'
			),
			'go_tech' => array(
				1 => 'http://news.go.vn/rss/3/Kinh-te.htm'
				// 1 => 'http://tech.go.vn/tech/rss/cate/972/RssDetail.rss',
				// 2 => 'http://sao.go.vn/sao/rss/cate/976/RssDetail.rss'
			),
			'vnews' => array(
				2 		=> 'http://news.go.vn/rss/1/Xa-hoi.htm',
				176		=> 'http://news.go.vn/rss/12/Doi-noi-Doi-ngoai.htm',
				3		=> 'http://news.go.vn/rss/2/The-gioi.htm',
				5		=> 'http://news.go.vn/rss/6/Van-hoa.htm',
				177		=> 'http://news.go.vn/rss/42/Chuyen-cua-sao.htm',
				178		=> 'http://news.go.vn/rss/27/Am-nhac.htm',
				179		=> 'http://news.go.vn/rss/16/Du-lich.htm',
				180		=> 'http://news.go.vn/rss/14/Thoi-trang.htm',
				8		=> 'http://news.go.vn/rss/5/Doi-song.htm',
				181		=> 'http://news.go.vn/rss/15/Am-thuc.htm',
				182		=> 'http://news.go.vn/rss/36/Lam-dep.htm',
				183		=> 'http://news.go.vn/rss/37/Tinh-yeu-Hon-nhan.htm',
				184		=> 'http://news.go.vn/rss/4/Giao-duc.htm',
				185		=> 'http://news.go.vn/rss/34/Hoc-bong-Du-hoc.htm',
				186		=> 'http://news.go.vn/rss/35/Tuyen-sinh.htm',
				187		=> 'http://news.go.vn/rss/40/Dai-hoc-Van-Hien.htm',
				6		=> 'http://news.go.vn/rss/10/The-thao.htm',
				188		=> 'http://news.go.vn/rss/25/Bong-da.htm',
				189		=> 'http://news.go.vn/rss/26/Quan-vot.htm',
				190		=> 'http://news.go.vn/rss/30/The-gioi-xe.htm',
				191		=> 'http://news.go.vn/rss/9/KH-CN.htm',
				192		=> 'http://news.go.vn/rss/22/CNTT-Vien-thong.htm',
				193		=> 'http://news.go.vn/rss/24/Thiet-bi-Phan-cung.htm',
				194		=> 'http://news.go.vn/rss/23/Khoa-hoc-Tu-nhien.htm',
				7		=> 'http://news.go.vn/rss/7/Phap-luat.htm',
				195		=> 'http://news.go.vn/rss/33/Tu-van.htm',
				196		=> 'http://news.go.vn/rss/32/Hinh-su.htm',
				197		=> 'http://news.go.vn/rss/3/Kinh-te.htm',
				198		=> 'http://news.go.vn/rss/17/Tai-chinh.htm',
				199		=> 'http://news.go.vn/rss/18/Chung-khoan.htm',
				200		=> 'http://news.go.vn/rss/20/Thi-truong.htm',
				201		=> 'http://news.go.vn/rss/31/Do-thi.htm',
				202		=> 'http://news.go.vn/rss/38/Dau-tu-Quy-hoach.htm',
				204		=> 'http://news.go.vn/rss/8/Giai-tri.htm',
				205		=> 'http://news.go.vn/rss/29/Truyen-cuoi.htm',
				206		=> 'http://news.go.vn/rss/41/Game.htm',
			)
		)
	),
);