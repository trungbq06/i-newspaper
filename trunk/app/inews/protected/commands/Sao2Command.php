<?php

class Sao2Command extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->get2Sao();
	}

}