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
		/*
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
		
		'db'=>array(
			'connectionString' => 'mysql:host=ilive.vn;dbname=inews',
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
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'limit'		=> 5,
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
            'ngoisaonet' => array(
                94      => 'http://ngoisao.net/rss/hau-truong.rss',
                95      => 'http://ngoisao.net/rss/the-thao.rss',
                96      => 'http://ngoisao.net/rss/thoi-cuoc.rss',
                97      => 'http://ngoisao.net/rss/phong-cach.rss',
                98      => 'http://ngoisao.net/rss/thu-gian.rss',
                99      => 'http://ngoisao.net/rss/goc-doc-gia.rss',
                100     => 'http://ngoisao.net/rss/cuoi.rss',
                101     => 'http://ngoisao.net/rss/showbiz-viet.rss',
                102     => 'http://ngoisao.net/rss/chau-a.rss',
                103     => 'http://ngoisao.net/rss/hollywood.rss',
                105     => 'http://ngoisao.net/rss/chuyen-la.rss',
                106     => 'http://ngoisao.net/rss/hinh-su.rss',
                107     => 'http://ngoisao.net/rss/thuong-truong.rss',
                108     => 'http://ngoisao.net/rss/thoi-trang.rss',
                109     => 'http://ngoisao.net/rss/tam-tinh.rss',
                110     => 'http://ngoisao.net/rss/lam-dep.rss',
                111     => 'http://ngoisao.net/rss/trac-nghiem.rss',
                112     => 'http://ngoisao.net/rss/an-choi-xem.rss',
                113     => 'http://ngoisao.net/rss/dan-choi.rss',
                114     => 'http://ngoisao.net/rss/cuoi.rss',
                115     => 'http://ngoisao.net/rss/game.rss',
                116     => 'http://ngoisao.net/rss/choi-blog.rss',
                117     => 'http://ngoisao.net/rss/thi-anh.rss',
                118     => 'http://ngoisao.net/rss/sach-hay.rss',
                119     => 'http://ngoisao.net/rss/go-roi.rss'
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
			'cafef' => array(
				1		=> 'http://cafef.vn/Trang-chu.rss',
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
			'land_cafe' => array(
				1		=> 'http://land.cafef.vn/Trang-chu.rss',
				90		=> 'http://land.cafef.vn/Thi-truong.rss',
				165		=> 'http://land.cafef.vn/Tin-tuc-du-an.rss',
				166		=> 'http://land.cafef.vn/Chinh-sach-quy-hoach.rss',
				167		=> 'http://land.cafef.vn/Phong-cach.rss'
			),
			'yahoo' 	=> array(
				2		=> 'http://vn.news.yahoo.com/rss/xahoi',
				174		=> 'http://vn.news.yahoo.com/rss/loisong',
				141		=> 'http://vn.news.yahoo.com/rss/quocte',
				142		=> 'http://vn.news.yahoo.com/rss/taichinh',
				144		=> 'http://vn.news.yahoo.com/rss/oto',
				145		=> 'http://vn.news.yahoo.com/rss/chungkhoan',
				146		=> 'http://vn.news.yahoo.com/rss/diaoc',
				147		=> 'http://vn.news.yahoo.com/rss/maytinh',
				148		=> 'http://vn.news.yahoo.com/rss/maytinh',
				149		=> 'http://vn.news.yahoo.com/rss/tv',
				150		=> 'http://vn.news.yahoo.com/rss/dienthoai',
				151		=> 'http://vn.news.yahoo.com/rss/mayanh',
				95		=> 'http://vn.news.yahoo.com/rss/bongdatrongnuoc',
				153		=> 'http://vn.news.yahoo.com/rss/bongdatrongnuoc',
				154		=> 'http://vn.news.yahoo.com/rss/bongdaquocte',
				155		=> 'http://vn.news.yahoo.com/rss/cacmonkhac',
				156		=> 'http://vn.news.yahoo.com/rss/thegioiteen',
				157		=> 'http://vn.news.yahoo.com/rss/amnhac',
				158		=> 'http://vn.news.yahoo.com/rss/amnhac',
				159		=> 'http://vn.news.yahoo.com/rss/sankhau',
				160		=> 'http://vn.news.yahoo.com/rss/lamdep',
				161		=> 'http://vn.news.yahoo.com/rss/nghethuat',
				162		=> 'http://vn.news.yahoo.com/rss/vuicuoi',
				163		=> 'http://vn.news.yahoo.com/rss/dulich',
				164		=> 'http://vn.news.yahoo.com/rss/amthuc',
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