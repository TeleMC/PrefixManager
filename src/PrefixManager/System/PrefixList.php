<?php
namespace PrefixManager\System;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\Server;
use PrefixManager\PrefixManager;

class PrefixList {
    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function PrefixList($player) {
        $name = $player->getName();
        $pre = "";
        $count = 0;
        foreach ($this->plugin->pdata[strtolower($name)]["칭호"] as $prefix => $prefix1) {
            $count++;
            $pre .= "§l§a[ §f{$count}번 §a] §r{$prefix}\n";
        }
        $form = $this->plugin->ui->SimpleForm(function (Player $player, array $data) {
        });
        $form->setTitle("Tele Prefix");
        $form->setContent("{$pre}");
        $form->addButton("§l닫기");
        $form->sendToPlayer($player);
    }
}
