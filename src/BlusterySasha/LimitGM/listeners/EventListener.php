<?php

declare(strict_types=1);

namespace BlusterySasha\LimitGM\listeners;

use pocketmine\block\BlockLegacyIds;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

class EventListener implements Listener {
	public function onPlace(BlockPlaceEvent $event) : void {
		$player = $event->getPlayer();
		if ($player->hasPermission("limitgm.bypass")) {
			return;
		}
		$blacklist = [BlockLegacyIds::FIRE, BlockLegacyIds::LAVA, BlockLegacyIds::ACTIVATOR_RAIL, BlockLegacyIds::WATER, BlockLegacyIds::HOPPER_BLOCK, BlockLegacyIds::PACKED_ICE, BlockLegacyIds::ICE, BlockLegacyIds::MONSTER_SPAWNER, BlockLegacyIds::SAND, BlockLegacyIds::ENDER_CHEST, BlockLegacyIds::RAIL, BlockLegacyIds::ENCHANTMENT_TABLE, BlockLegacyIds::ANVIL, BlockLegacyIds::ITEM_FRAME_BLOCK, BlockLegacyIds::SHULKER_BOX, BlockLegacyIds::TNT, BlockLegacyIds::DIAMOND_BLOCK, BlockLegacyIds::IRON_BLOCK, BlockLegacyIds::LAPIS_BLOCK, BlockLegacyIds::EMERALD_BLOCK, BlockLegacyIds::COAL_BLOCK, BlockLegacyIds::UNDYED_SHULKER_BOX, BlockLegacyIds::DIAMOND_ORE, BlockLegacyIds::QUARTZ_ORE, BlockLegacyIds::EMERALD_ORE, BlockLegacyIds::COAL_ORE, BlockLegacyIds::REDSTONE_ORE, BlockLegacyIds::LAPIS_ORE, BlockLegacyIds::IRON_ORE, BlockLegacyIds::GOLD_ORE, BlockLegacyIds::GOLD_BLOCK, BlockLegacyIds::OBSIDIAN, BlockLegacyIds::BEDROCK, BlockLegacyIds::END_PORTAL_FRAME, BlockLegacyIds::PISTON, BlockLegacyIds::STICKY_PISTON];
		if ($player->isCreative() && in_array($event->getBlock()->getId(), $blacklist)) {
			$player->sendTip("§l§7Вы не можете ставить данный блок в креативе!");
			$event->cancel();
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
		$blacklist = [BlockLegacyIds::WATER, BlockLegacyIds::LAVA, BlockLegacyIds::MONSTER_SPAWNER, BlockLegacyIds::HOPPER_BLOCK, BlockLegacyIds::ENDER_CHEST, BlockLegacyIds::CRAFTING_TABLE, BlockLegacyIds::CHEST, BlockLegacyIds::FURNACE, BlockLegacyIds::BURNING_FURNACE, BlockLegacyIds::TRAPPED_CHEST, BlockLegacyIds::ENCHANTMENT_TABLE, BlockLegacyIds::ANVIL, BlockLegacyIds::ITEM_FRAME_BLOCK, BlockLegacyIds::SHULKER_BOX, BlockLegacyIds::TNT, BlockLegacyIds::DROPPER, BlockLegacyIds::DISPENSER, BlockLegacyIds::UNDYED_SHULKER_BOX];
		if (in_array($event->getBlock()->getId(), $blacklist)) {
			$player->sendTip("§l§7Данный блок нельзя трогать в креативе!");
			$event->cancel();
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
		$event->cancel();
		$this->sendExplode($player);
	}

	public function onAttack(EntityDamageByEntityEvent $event) : void {
		$player = $event->getDamager();
		if (!($player instanceof Player) || !$player->isCreative() || $player->hasPermission("limitgm.bypass")) {
			return;
		}
		$player->sendTip("§l§7Вы не можете бить игроков в креативе!");
		$event->cancel();
		$this->sendExplode($player);
	}

	private function sendExplode(Player $player) : void {
		$loc = $player->getPosition()->asVector3();
		$pk = new PlaySoundPacket();
		$pk->x = $loc->x;
		$pk->y = $loc->y;
		$pk->z = $loc->z;
		$pk->volume = 1;
		$pk->pitch = 1;
		$pk->soundName = "cauldron.explode";
		$player->getNetworkSession()->sendDataPacket($pk);
	}
}