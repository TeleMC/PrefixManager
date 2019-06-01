<?php
namespace PrefixManager\System;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\Server;
use PrefixManager\PrefixManager;

class SelectPrefix {
    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function SelectPrefix($player) {
        $name = $player->getName();
        $form = $this->plugin->ui->SimpleForm(function (Player $player, array $data) {
            $name = $player->getName();
            $n = -1;
            foreach ($this->plugin->pdata[strtolower($name)]["칭호"] as $prefix2 => $prefix3) {
                $n++;
                $this->data[$name][$n] = "{$this->plugin->pdata[strtolower($name)]["칭호"]["{$prefix2}"]}";
            }
            unset($n);
            if (!isset($this->data[$name][$data[0]])) return;
            $this->prefix[$name] = "{$this->data[$name][$data[0]]}";
            $player->sendMessage("{$this->plugin->pre} {$this->prefix[$name]} §r§a(을)를 선택하셨습니다!");
            $this->plugin->selectPrefix($name, $this->prefix[$name]);
            unset($this->prefix[$name]);
            unset($this->data[$name]);
        });
        $form->setTitle("Tele Prefix");
        foreach ($this->plugin->pdata[strtolower($name)]["칭호"] as $prefix => $prefix1) {
            $form->addButton("{$prefix}");
        }
        $form->addButton("§l닫기");
        $form->sendToPlayer($player);
    }
}
