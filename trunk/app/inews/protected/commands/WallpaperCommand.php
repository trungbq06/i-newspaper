<?php

class WallpaperCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		// $crawler->getXkcn();
		$crawler->getWallpaper2();
	}

}