<?php

namespace scoreboardapi;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class ScoreboardAPI extends PluginBase
{

    /* @var $instance ScoreboardAPI*/
    private static $instance;

    public static function getInstance(){
        return self::$instance;
    }

    public function onEnable(){
        self::$instance = $this;

        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

}