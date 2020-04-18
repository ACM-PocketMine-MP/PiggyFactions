<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\claims\ClaimsManager;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\Player;

class ClaimSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($args["type"] ?? null === "auto") {
            $member->setAutoClaiming(!$member->isAutoClaiming());
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.auto.toggled" . ($member->isAutoClaiming() ? "" : "-off"));
            return;
        }

        $claim = ClaimsManager::getInstance()->getClaim($sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        if ($claim !== null) {
            if ($claim->canBeOverClaimed() && $claim->getFaction() !== $faction) {
                LanguageManager::getInstance()->sendMessage($sender, "commands.claim.over-claimed");
                $claim->setFaction($faction);
                return;
            }
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.already-claimed");
            return;
        }
        if ($faction->getPower() / $this->plugin->getConfig()->getNested("factions.claim.cost", 1) < count(ClaimsManager::getInstance()->getFactionClaims($faction)) + 1) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.claim.no-power");
            return;
        }
        ClaimsManager::getInstance()->createClaim($faction, $sender->getLevel(), $sender->getLevel()->getChunkAtPosition($sender));
        LanguageManager::getInstance()->sendMessage($sender, "commands.claim.success");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type", true));
    }
}