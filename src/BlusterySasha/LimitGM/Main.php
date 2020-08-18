<?php

namespace BlusterySasha\LimitGM;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

	public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if ($player->isOp() || $player->hasPermission("system.bypass")) {
            return;
        }
        $block = $event->getBlock()->getId();
        $blacklist = [Block::FIRE, Block::LAVA, Block::ACTIVATOR_RAIL, Block::WATER, Block::HOPPER_BLOCK, Block::PACKED_ICE, Block::ICE, Block::MONSTER_SPAWNER, Block::SAND, Block::ENDER_CHEST, Block::RAIL, Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DIAMOND_BLOCK, Block::IRON_BLOCK, Block::LAPIS_BLOCK, Block::EMERALD_BLOCK, Block::COAL_BLOCK, Block::UNDYED_SHULKER_BOX, Block::DIAMOND_ORE, Block::QUARTZ_ORE, Block::EMERALD_ORE, Block::COAL_ORE, Block::REDSTONE_ORE, Block::LAPIS_ORE, Block::IRON_ORE, Block::GOLD_ORE, Block::GOLD_BLOCK, Block::OBSIDIAN, Block::BEDROCK, Block::END_PORTAL_FRAME, Block::PISTON, Block::STICKY_PISTON];
        if ($player->isCreative()) {
            if (in_array($blocks, $blacklist)) {
				$player->sendTip("§l§7Вы не можете ставить данный блок в креативе!");
                $event->setCancelled();
                $pk = new PlaySoundPacket();
                $pk->x = $player->getX();
                $pk->y = $player->getY();
                $pk->z = $player->getZ();
                $pk->volume = 1;
                $pk->pitch = 1;
                $pk->soundName = 'cauldron.explode';
                $player->dataPacket($pk);
            }
        }
    }

    public function onPlayerGameModeChange(PlayerGameModeChangeEvent $event) {
        $player = $event->getPlayer();
        if ($player->isOp() || $player->hasPermission("system.bypass")) {
            return;
        }
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
	    $player->sendTip("§l§7Предметы очищены, т. к. Вы перешли в другой режим!");
		$player->addTitle("§e§lПредметы очищены§7!", " ");
		$pk = new PlaySoundPacket();
        $pk->x = $player->getX();
        $pk->y = $player->getY();
        $pk->z = $player->getZ();
        $pk->volume = 1;
        $pk->pitch = 1;
        $pk->soundName = 'cauldron.explode';
        $player->dataPacket($pk);
    }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if ($player->isOp() || $player->hasPermission("system.bypass")) {
            return;
        }
        $block = $event->getBlock()->getId();
        $blacklist = [Block::WATER, Block::LAVA, Block::MONSTER_SPAWNER, Block::HOPPER_BLOCK, Block::ENDER_CHEST, Block::CRAFTING_TABLE, Block::CHEST, Block::FURNACE, Block::BURNING_FURNACE, Block::TRAPPED_CHEST, Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DROPPER, Block::DISPENSER, Block::UNDYED_SHULKER_BOX];
        if ($player->isCreative()) {
            if (in_array($blocks, $blacklist)) {
				$player->sendTip("§l§7Данный блок нельзя трогать в креативе!");
				$pk = new PlaySoundPacket();
				$pk->x = $player->getX();
				$pk->y = $player->getY();
				$pk->z = $player->getZ();
				$pk->volume = 1;
				$pk->pitch = 1;
				$pk->soundName = 'cauldron.explode';
				$player->dataPacket($pk);
				$event->setCancelled();
            }
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        if ($player->isOp() || $player->hasPermission("system.bypass")) {
            return;
        }
        if ($player->isCreative()) {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
			$player->addTitle("§c§lВы умерли§7!", " ");
			$pk = new PlaySoundPacket();
            $pk->x = $player->getX();
            $pk->y = $player->getY();
            $pk->z = $player->getZ();
            $pk->volume = 1;
            $pk->pitch = 1;
            $pk->soundName = 'cauldron.explode';
            $player->dataPacket($pk);
        }
    }

    public function onDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();
        if ($player->isOp() || $player->hasPermission("system.bypass")) {
            return;
        }
        if ($player->isCreative()) {
	        $player->sendTip("§l§7Вы не можете выбрасывать вещи в творческом режиме!");
			$pk = new PlaySoundPacket();
            $pk->x = $player->getX();
            $pk->y = $player->getY();
            $pk->z = $player->getZ();
            $pk->volume = 1;
            $pk->pitch = 1;
            $pk->soundName = 'cauldron.explode';
            $player->dataPacket($pk);
            $event->setCancelled();
        }
    }

    public function onAttack(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $player = $event->getDamager();
            if ($player->isOp() || $player->hasPermission("system.bypass")) {
                return;
            }
            if ($player instanceof Player) {
                if ($player->isCreative()) {
					$player->sendTip("§l§7Вы не можете бить игроков в креативе!");
					$pk = new PlaySoundPacket();
					$pk->x = $player->getX();
					$pk->y = $player->getY();
					$pk->z = $player->getZ();
					$pk->volume = 1;
					$pk->pitch = 1;
					$pk->soundName = 'cauldron.explode';
					$player->dataPacket($pk);
                    $event->setCancelled();
                }
            }
        }
    }
}
