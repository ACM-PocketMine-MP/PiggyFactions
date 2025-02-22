<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\claims\unclaim;

use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\claims\UnclaimAllChunksEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\player\Player;

class UnclaimAllSubCommand extends FactionSubCommand
{
    protected ?string $parentNode = "unclaim";

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        if ($member->getFaction() !== $faction && !$member->isInAdminMode()) {
            $member->sendMessage("commands.unclaim.other-faction");
            return;
        }
        $ev = new UnclaimAllChunksEvent($faction, $member);
        $ev->call();
        if ($ev->isCancelled()) return;

        foreach (PiggyFactions::getInstance()->getClaimsManager()->getFactionClaims($faction) as $claim) {
            PiggyFactions::getInstance()->getClaimsManager()->deleteClaim($claim);
        }
        $member->sendMessage("commands.unclaim.all.success");
    }
}