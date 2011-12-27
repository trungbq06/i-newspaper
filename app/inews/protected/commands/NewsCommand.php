<?php

class NewsCommand extends CConsoleCommand {

	public function actionIndex($args = array()) {
		$crawler = new Crawler();
		$crawler->getVnexpress();
		$crawler->getExchange();
		$crawler->getGold();
		$crawler->getOil();
		$crawler->getWeather();
		$crawler->getLottery();
		
		$crawler->getVnexpressVideo();
		$crawler->getTvCalendar();
		$crawler->getLottery();
		$crawler->getCinemaSchedule();
		$crawler->getTvCalendar();
	}

}