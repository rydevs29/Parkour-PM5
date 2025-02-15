<?php

declare(strict_types=1);

namespace rydevs29\parkour;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\PressurePlate;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\world\World;

class Main extends PluginBase implements Listener {

    private Config $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "lobby" => [
                "world" => "hub", // Nama world lobby
                "x" => 0,
                "y" => 100,
                "z" => 0
            ]
        ]);
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($block instanceof PressurePlate && $block->getTypeId() === 147) { // Gold Pressure Plate
            $pos = $player->getPosition();
            $player->sendMessage("§eSpawnpoint disimpan!");
            $player->setSpawn($pos);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Hanya pemain yang bisa menjalankan perintah ini.");
            return false;
        }

        if ($command->getName() === "parkour" || $command->getName() === "hub") {
            $lobbyData = $this->config->get("lobby");
            $worldName = $lobbyData["world"];

            $world = $this->getServer()->getWorldManager()->getWorldByName($worldName);
            if ($world instanceof World) {
                $vector = new Vector3($lobbyData["x"], $lobbyData["y"], $lobbyData["z"]);
                $sender->teleport($vector);
                $sender->sendMessage("§aTeleport ke lobby di dunia '$worldName'!");
            } else {
                $sender->sendMessage("§cDunia '$worldName' tidak ditemukan!");
            }
            return true;
        }

        return false;
    }
}