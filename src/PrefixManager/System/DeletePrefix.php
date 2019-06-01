<?php
namespace PrefixManager\System;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\Server;
use PrefixManager\PrefixManager;

class DeletePrefix {
    public function __construct(PrefixManager $plugin) {
        $this->plugin = $plugin;
    }

    public function DeletePrefix($player) {
        $form = $this->plugin->ui->CustomForm(function (Player $player, array $data) {
            $name = $player->getName();
            if (!isset($this->plugin->pdata[strtolower($data[1])])) {
                $player->sendMessage("{$this->plugin->pre} 해당 유저는 접속한적이 없습니다.");
                return;
            }
            $this->target[$name] = $data[1];
            $form = $this->plugin->ui->SimpleForm(function (Player $player, array $data) {
                $name = $player->getName();
                $n = -1;
                foreach ($this->plugin->pdata[strtolower($this->target[$name])]["칭호"] as $prefix2 => $prefix3) {
                    $n++;
                    $this->data[$name][$n] = "{$this->plugin->pdata[strtolower($this->target[$name])]["칭호"]["{$prefix2}"]}";
                }
                unset($n);
                if (!isset($this->data[$name][$data[0]])) {
                    unset($this->target[$name]);
                    return;
                }
                $this->prefix[$name] = "{$this->data[$name][$data[0]]}";
                $form = $this->plugin->ui->ModalForm(function (Player $player, array $data) {
                    $name = $player->getName();
                    if ($data[0] == true) {
                        if ($this->prefix[$name] == "§f〔 §6모험가 §f〕") {
                            $player->sendMessage("{$this->plugin->pre} 기본칭호는 제거할 수 없습니다.");
                            unset($this->data[$name]);
                            unset($this->target[$name]);
                            unset($this->prefix[$name]);
                            return;
                        }
                        if (Server::getInstance()->getPlayer($this->target[$name]) instanceof Player) {
                            Server::getInstance()->getPlayer($this->target[$name])->sendMessage("{$this->plugin->pre} {$this->prefix[$name]} §a칭호가 삭제되었습니다.");
                        }
                        $this->plugin->removePrefix($this->target[$name], $this->prefix[$name]);
                        unset($this->data[$name]);
                        unset($this->target[$name]);
                        unset($this->prefix[$name]);
                        return;
                    } else {
                        unset($this->data[$name]);
                        unset($this->target[$name]);
                        unset($this->prefix[$name]);
                        return;
                    }
                });
                $form->setTitle("Tele Prefix");
                $form->setContent("\n§l{$this->target[$name]}님의 칭호 {$this->prefix[$name]}를 제거하시겠습니까?");
                $form->setButton1("§l§8[예]");
                $form->setButton2("§l§8[아니오]");
                $form->sendToPlayer($player);
            });
            $form->setTitle("Tele Prefix");
            foreach ($this->plugin->pdata[$this->target[$name]]["칭호"] as $prefix => $prefix1) {
                $form->addButton("{$prefix}");
            }
            $form->addButton("§l닫기");
            $form->sendToPlayer($player);
        });
        $form->setTitle("Tele Prefix");
        $form->addLabel("유저의 칭호를 제거합니다.");
        $form->addInput("닉네임", "닉네임");
        $form->sendToPlayer($player);
    }
}
