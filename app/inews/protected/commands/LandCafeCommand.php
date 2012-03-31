<?php

class LandCafeCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getLandCafe();
	}

}