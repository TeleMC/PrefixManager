<?php
namespace PrefixManager;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;

class EventListener implements Listener {
    private $plugin;

    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $ev) {
        $player = $ev->getPlayer();
        $name = $player->getName();
        if (!isset($this->plugin->pdata[strtolower($name)])) {
            $this->plugin->pdata[strtolower($name)]["칭호"]["§f〔 §6모험가 §f〕"] = "§f〔 §6모험가 §f〕";
            $this->plugin->pdata[strtolower($name)]["선택"] = "§f〔 §6모험가 §f〕";
            $pre = "§f〔 §6모험가 §f〕";
            $tag = "{$pre} §f| {$name}";
            $player->setNameTag($tag);
        }
    }
}
