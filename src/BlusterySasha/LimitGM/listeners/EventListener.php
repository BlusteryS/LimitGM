<?php

declare(strict_types=1);

namespace BlusterySasha\LimitGM\listeners;

use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class EventListener implements Listener {
	public function onPlace(BlockPlaceEvent $event) : void {
		$player = $event->getPlayer();
		if ($player->hasPermission("limitgm.bypass")) {
			return;
		}
		$blacklist = [Block::FIRE, Block::LAVA, Block::ACTIVATOR_RAIL, Block::WATER, Block::HOPPER_BLOCK, Block::PACKED_ICE, Block::ICE, Block::MONSTER_SPAWNER, Block::SAND, Block::ENDER_CHEST, Block::RAIL, Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DIAMOND_BLOCK, Block::IRON_BLOCK, Block::LAPIS_BLOCK, Block::EMERALD_BLOCK, Block::COAL_BLOCK, Block::UNDYED_SHULKER_BOX, Block::DIAMOND_ORE, Block::QUARTZ_ORE, Block::EMERALD_ORE, Block::COAL_ORE, Block::REDSTONE_ORE, Block::LAPIS_ORE, Block::IRON_ORE, Block::GOLD_ORE, Block::GOLD_BLOCK, Block::OBSIDIAN, Block::BEDROCK, Block::END_PORTAL_FRAME, Block::PISTON, Block::STICKY_PISTON];
		if ($player->isCreative() && in_array($event->getBlock()->getId(), $blacklist)) {
			$player->sendTip("§l§7Вы не можете ставить данный блок в креативе!");
			$event->setCancelled();
			$this->sendExplode($player);
		}
	}

	public function onPlayerGameModeChange(PlayerGameModeChangeEvent $event) : void {
		$player = $event->getPlayer();
		if ($player->hasPermission("limitgm.bypass")) {
			return;
		}
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->sendTip("§l§7Предметы очищены, т. к. вы перешли в другой режим!");
		$player->sendTitle("§e§lПредметы очищены§7!");
		$this->sendExplode($player);
	}

	public function onInteract(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		if (!$player->isCreative() || $player->hasPermission("limitgm.bypass")) {
			return;
		}
		$blacklist = [Block::WATER, Block::LAVA, Block::MONSTER_SPAWNER, Block::HOPPER_BLOCK, Block::ENDER_CHEST, Block::CRAFTING_TABLE, Block::CHEST, Block::FURNACE, Block::BURNING_FURNACE, Block::TRAPPED_CHEST, Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DROPPER, Block::DISPENSER, Block::UNDYED_SHULKER_BOX];
		if (in_array($event->getBlock()->getId(), $blacklist)) {
			$player->sendTip("§l§7Данный блок нельзя трогать в креативе!");
			$event->setCancelled();
			$this->sendExplode($player);
		}
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void {
		$player = $event->getPlayer();
		if (!$player->isCreative() || $player->hasPermission("limitgm.bypass")) {
			return;
		}
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->sendTitle("§c§lВы умерли§7!");
		$this->sendExplode($player);
	}

	public function onDropItem(PlayerDropItemEvent $event) : void {
		$player = $event->getPlayer();
		if (!$player->isCreative() || $player->hasPermission("limitgm.bypass")) {
			return;
		}
		$player->sendTip("§l§7Вы не можете выбрасывать вещи в творческом режиме!");
		$event->setCancelled();
		$this->sendExplode($player);
	}

	public function onAttack(EntityDamageByEntityEvent $event) : void {
		$player = $event->getDamager();
		if (!($player instanceof Player) || !$player->isCreative() || $player->hasPermission("limitgm.bypass")) {
			return;
		}
		$player->sendTip("§l§7Вы не можете бить игроков в креативе!");
		$event->setCancelled();
		$this->sendExplode($player);
	}

	private function sendExplode(Player $player) : void {
		$pk = new PlaySoundPacket();
		$pk->x = $player->getX();
		$pk->y = $player->getY();
		$pk->z = $player->getZ();
		$pk->volume = 1;
		$pk->pitch = 1;
		$pk->soundName = "cauldron.explode";
		$player->dataPacket($pk);
	}
}