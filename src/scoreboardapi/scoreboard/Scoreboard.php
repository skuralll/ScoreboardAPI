<?php

namespace scoreboardapi\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use scoreboardapi\ScoreboardAPI;

class Scoreboard
{

    const OBJECTIVE_NAME = "scoreboardapi";

    const SORTORDER_ASCENDING = 0;
    const SORTORDER_DESCENDING = 1;

    /* @var $owner Player*/
    protected $owner;
    /* @var $displayName string*/
    protected $displayName = "Scoreboard";
    /* @var $sortOrder int*/
    protected $sortOrder = self::SORTORDER_ASCENDING;
    /* @var $scores array[]*/
    protected $scores = [];//id=>[name, score]

    public static function create(Player $player, ...$args) : self{
        $scoreboard = new static($player, ...$args);
        ScoreboardAPI::getInstance()->setScoreboard($player, $scoreboard);
        return $scoreboard;
    }

    public function __construct(Player $player){
        $this->owner = $player;
    }

    public function init(){
        $this->show();
    }

    public function fin(){
        $this->hide();
    }

    public function onUpdate(int $currentTick){}

    public function show(){
        $this->sendShowPacket();
        foreach ($this->scores as $id => $data) $this->sendScorePacket($id);
    }

    public function hide(){
        $this->sendHidePacket();
    }

    public function setScore(int $id, string $name, int $score){
        $this->removeScore($id);
        $this->scores[$id] = ["name" => $name, "score" => $score];
        $this->sendScorePacket($id);
    }

    public function removeScore(int $id){
        $this->sendRemoveScorePacket($id);
        unset($this->scores[$id]);
    }

    public function setDisplayName(string $displayName){
        $this->displayName = $displayName;
        $this->show();
    }

    public function setLine(int $line, string $name){
        $this->setScore($line, $name, $line);
    }

    public function removeLine(int $line){
        $this->removeScore($line);
    }

    protected function sendShowPacket(){
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = self::OBJECTIVE_NAME;
        $pk->displayName = $this->displayName;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;

        $this->owner->dataPacket($pk);
    }

    protected function sendHidePacket(){
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = self::OBJECTIVE_NAME;

        $this->owner->dataPacket($pk);
    }

    protected function sendScorePacket(int $id){
        if(isset($this->scores[$id])){
            $entry = new ScorePacketEntry();
            $entry->objectiveName = self::OBJECTIVE_NAME;
            $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
            $entry->customName = $this->scores[$id]["name"];
            $entry->score = $this->scores[$id]["score"];
            $entry->scoreboardId = $id;

            $pk = new SetScorePacket();
            $pk->type = SetScorePacket::TYPE_CHANGE;
            $pk->entries[] = $entry;

            $this->owner->dataPacket($pk);
        }
    }

    protected function sendRemoveScorePacket(int $id){
        $entry = new ScorePacketEntry();
        $entry->objectiveName = self::OBJECTIVE_NAME;
        $entry->score = isset($this->scores[$id]) ? $this->scores[$id]["score"] : 0;
        $entry->scoreboardId = $id;

        $pk = new SetScorePacket();
        $pk->type = SetScorePacket::TYPE_REMOVE;
        $pk->entries[] = $entry;

        $this->owner->dataPacket($pk);
    }

}