<?php
namespace PrefixManager\System;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\Server;
use PrefixManager\PrefixManager;

class RemovePrefix {
    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function RemovePrefix($player) {
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
            $form = $this->plugin->ui->ModalForm(function (Player $player, array $data) {
                $name = $player->getName();
                if ($data[0] == true) {
                    if ($this->prefix[$name] == "§f〔 §6모험가 §f〕") {
                        $player->sendMessage("{$this->plugin->pre} 기본칭호는 제거할 수 없습니다.");
                        unset($this->data[$name]);
                        unset($this->prefix[$name]);
                        return;
                    }
                    if (Server::getInstance()->getPlayer($name) instanceof Player) {
                        Server::getInstance()->getPlayer($name)->sendMessage("{$this->plugin->pre} {$this->prefix[$name]} §r§a칭호가 삭제되었습니다.");
                    }
                    $this->plugin->removePrefix($name, $this->prefix[$name]);
                    unset($this->data[$name]);
                    unset($this->prefix[$name]);
                    return;
                } else {
                    unset($this->data[$name]);
                    unset($this->prefix[$name]);
                    return;
                }
            });
            $form->setTitle("Tele Prefix");
            $form->setContent("\n§l칭호 {$this->prefix[$name]}§r§l를 제거하시겠습니까?");
            $form->setButton1("§l§8[예]");
            $form->setButton2("§l§8[아니오]");
            $form->sendToPlayer($player);
        });
        $form->setTitle("Tele Prefix");
        foreach ($this->plugin->pdata[strtolower($name)]["칭호"] as $prefix => $prefix1) {
            $form->addButton("{$prefix}");
        }
        $form->addButton("§l닫기");
        $form->sendToPlayer($player);
    }
}
