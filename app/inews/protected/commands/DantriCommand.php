<?php

class DantriCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getDantri();
	}

}