<?php

class ComicCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		// $crawler->getComic();
		Comic::model()->createDownloadedFile();
	}

}