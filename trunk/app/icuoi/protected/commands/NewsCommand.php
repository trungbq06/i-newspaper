<?php

class NewsCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getTruyencuoi();
		$crawler->getClipTruyencuoi();
	}

}