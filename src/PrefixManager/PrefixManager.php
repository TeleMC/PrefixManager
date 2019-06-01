<?php
namespace PrefixManager;

use GuildManager\GuildManager;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use PrefixManager\System\AddPrefix;
use PrefixManager\System\DeletePrefix;
use PrefixManager\System\PrefixList;
use PrefixManager\System\RemovePrefix;
use PrefixManager\System\SelectPrefix;
use UiLibrary\UiLibrary;

class PrefixManager extends PluginBase {

    private static $instance = null;
    //public $pre = "§l§a[ §f칭호 §a]§r§a";
    public $pre = "§e•";

    public static function getInstance() {
        return self::$instance;
    }

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->prefix = new Config($this->getDataFolder() . "prefix.yml", Config::YAML);
        $this->pdata = $this->prefix->getAll();
        $this->ui = UiLibrary::getInstance();
        $this->Guild = GuildManager::getInstance();
        $this->AddPrefix = new AddPrefix($this);
        $this->DeletePrefix = new DeletePrefix($this);
        $this->SelectPrefix = new SelectPrefix($this);
        $this->RemovePrefix = new RemovePrefix($this);
        $this->PrefixList = new PrefixList($this);
    }

    public function onDisable() {
        $this->save();
    }

    public function save() {
        $this->prefix->setAll($this->pdata);
        $this->prefix->save();
    }

    public function addPrefix($name, $prefix) {
        if (isset($this->pdata[strtolower($name)]["칭호"]["{$prefix}"])) return;
        $this->pdata[strtolower($name)]["칭호"]["{$prefix}"] = "{$prefix}";
    }

    public function removePrefix($name, $prefix) {
        if (!isset($this->pdata[strtolower($name)]["칭호"]["{$prefix}"])) return;
        if ($prefix == "§f〔 §6모험가 §f〕") return;
        unset($this->pdata[strtolower($name)]["칭호"]["{$prefix}"]);
        if ($this->getPrefix($name) == $prefix) $this->pdata[strtolower($name)]["선택"] = "§f〔 §6모험가 §f〕";
    }

    public function getPrefix($name) {
        if (!isset($this->pdata[strtolower($name)])) return;
        return $this->pdata[strtolower($name)]["선택"];
    }

    public function selectPrefix($name, $prefix) {
        if (!isset($this->pdata[strtolower($name)])) return;
        $this->pdata[strtolower($name)]["선택"] = "{$prefix}";
    }

    public function PrefixUI($player) {
        if ($player instanceof Player) {
            $form = $this->ui->SimpleForm(function (Player $player, array $data) {
                if (!is_numeric($data[0])) return;
                $name = $player->getName();
                if ($data[0] == 0) {
                    if ($player->isOp()) $this->AddPrefix->AddPrefix($player);
                    if (!$player->isOp()) $this->SelectPrefix->SelectPrefix($player);
                }
                if ($data[0] == 1) {
                    if ($player->isOp()) $this->DeletePrefix->DeletePrefix($player);
                    if (!$player->isOp()) $this->RemovePrefix->RemovePrefix($player);
                }
                if ($data[0] == 2) {
                    if ($player->isOp()) $this->SelectPrefix->SelectPrefix($player);
                    if (!$player->isOp()) $this->PrefixList->PrefixList($player);
                }
                if ($data[0] == 3) {
                    if ($player->isOp()) $this->RemovePrefix->RemovePrefix($player);
                }
                if ($data[0] == 4) {
                    if ($player->isOp()) $this->PrefixList->PrefixList($player);
                }
            });
            $form->setTitle("Tele Prefix");
            if ($player->isOp()) {
                $form->addButton("§l칭호 추가\n§r유저의 칭호를 추가합니다.");
                $form->addButton("§l칭호 제거\n§r유저의 칭호를 제거합니다.");
            }
            $form->addButton("§l칭호 선택\n§r칭호를 추가합니다.");
            $form->addButton("§l칭호 제거\n§r칭호를 제거합니다.");
            $form->addButton("§l칭호 목록\n§r유저의 칭호를 확인합니다.");
            $form->addButton("§l닫기");
            $form->sendToPlayer($player);
        }
    }
}
