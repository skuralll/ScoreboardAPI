<?php

namespace scoreboardapi;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use scoreboardapi\scoreboard\Scoreboard;

class EventListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event){
        /*$test = new Scoreboard($event->getPlayer());
        $test->setDisplayName("テスト");
        $test->setLine(0, "テスト1");
        $test->setLine(1, "テスト2");
        ScoreboardAPI::getInstance()->setScoreboard($event->getPlayer(), $test);*///デバッグコード
    }

    public function onQuit(PlayerQuitEvent $event){
        ScoreboardAPI::getInstance()->unsetScoreboard($event->getPlayer());
    }

}