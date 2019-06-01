<?php
namespace PrefixManager\System;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\Server;
use PrefixManager\PrefixManager;

class AddPrefix {
    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function AddPrefix($player) {
        $form = $this->plugin->ui->CustomForm(function (Player $player, array $data) {
            $name = $player->getName();
            if (!isset($data[1]) or !isset($data[2])) {
                $player->sendMessage("{$this->plugin->pre} 닉네임 또는 칭호가 기입되지 않았습니다.");
                return;
            }
            if (!isset($this->plugin->pdata[strtolower($data[1])])) {
                $player->sendMessage("{$this->plugin->pre} 해당 유저를 찾아볼 수 없습니다.");
                return;
            }
            if (isset($this->plugin->pdata[strtolower($data[1])]["칭호"][$data[2]])) {
                $player->sendMessage("{$this->plugin->pre} 해당 유저는 이미 해당 칭호를 가지고 있습니다.");
                return;
            }
            $this->target[$name] = $data[1];
            $this->prefix[$name] = $data[2];
            $form = $this->plugin->ui->ModalForm(function (Player $player, array $data) {
                $name = $player->getName();
                if ($data[0] == true) {
                    if (Server::getInstance()->getPlayer($this->target[$name]) instanceof Player) {
                        Server::getInstance()->getPlayer($this->target[$name])->sendMessage("{$this->plugin->pre} {$this->prefix[$name]} §r§a칭호가 추가되었습니다.");
                    }
                    $player->sendMessage("{$this->plugin->pre} 성공적으로 추가하였습니다.");
                    $this->plugin->addPrefix($this->target[$name], $this->prefix[$name]);
                    unset($this->target[$name]);
                    unset($this->prefix[$name]);
                    return;
                } else {
                    unset($this->target[$name]);
                    unset($this->prefix[$name]);
                    return;
                }
            });
            $form->setTitle("Tele Prefix");
            $form->setContent("\n§l{$this->target[$name]}님에게 칭호 {$this->prefix[$name]}§r§l를 추가하시겠습니까?");
            $form->setButton1("§l§8[예]");
            $form->setButton2("§l§8[아니오]");
            $form->sendToPlayer($player);
        });
        $form->setTitle("Tele Prefix");
        $form->addLabel("유저의 칭호를 추가합니다.");
        $form->addInput("닉네임", "닉네임");
        $form->addInput("칭호", "칭호");
        $form->sendToPlayer($player);
    }
}
