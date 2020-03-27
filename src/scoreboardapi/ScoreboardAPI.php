<?php

namespace scoreboardapi;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use scoreboardapi\scoreboard\Scoreboard;

class ScoreboardAPI extends PluginBase
{

    /* @var $instance ScoreboardAPI*/
    private static $instance;
    /* @var $scoreboards Scoreboard[]*/
    private $scoreboards = [];

    public static function getInstance(){
        return self::$instance;
    }

    public function onEnable(){
        self::$instance = $this;

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick) : void {
                foreach(ScoreboardAPI::getInstance()->getAllScoreboard() as $scoreboard){
                    $scoreboard->onUpdate($currentTick);
                }
            }
        ), 1);

        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    /* @return Scoreboard[]*/
    public function getAllScoreboard() : array {
        return $this->scoreboards;
    }

    public function setScoreboard(Player $player, Scoreboard $scoreboard){
        $this->unsetScoreboard($player);
        $this->scoreboards[$player->getName()] = $scoreboard;
        $scoreboard->init();
    }

    public function getScoreboard(Player $player) : ?Scoreboard{
        return isset($this->scoreboards[$player->getName()]) ? $this->scoreboards[$player->getName()] : null;
    }

    public function unsetScoreboard(Player $player){
        if(isset($this->scoreboards[$player->getName()])){
            $this->scoreboards[$player->getName()]->fin();
            unset($this->scoreboards[$player->getName()]);
        }
    }

}