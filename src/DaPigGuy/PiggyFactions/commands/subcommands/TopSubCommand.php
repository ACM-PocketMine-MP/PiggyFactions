<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\flags\Flag;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\utils\RoundValue;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TopSubCommand extends FactionSubCommand
{
    const PAGE_LENGTH = 10;

    protected bool $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $types = [
            "online" => function (Faction $a, Faction $b): int {
                return count($b->getOnlineMembers()) - count($a->getOnlineMembers());
            },
            "members" => function (Faction $a, Faction $b): int {
                return count($b->getMembers()) - count($a->getMembers());
            },
            "power" => function (Faction $a, Faction $b): int {
                return (int)($b->getPower() - $a->getPower());
            }
        ];
        $type = $args["type"] ?? "power";
        if (!isset($types[$type])) return;

        if (PiggyFactions::getInstance()->isFactionBankEnabled()) {
            $types["money"] = function (Faction $a, Faction $b): int {
                return (int)($b->getMoney() - $a->getMoney());
            };
        }

        $factions = array_filter(PiggyFactions::getInstance()->getFactionsManager()->getFactions(), function (Faction $faction): bool {
            return !$faction->getFlag(Flag::SAFEZONE) && !$faction->getFlag(Flag::WARZONE);
        });
        usort($factions, $types[$type]);

        $page = (int)(($args["page"] ?? 1) - 1);
        $maxPages = (int)(ceil(count($factions) / self::PAGE_LENGTH) - 1);
        if ($page > $maxPages) $page = $maxPages;
        else if ($page < 0) $page = 0;

        $language = $sender instanceof Player ? PiggyFactions::getInstance()->getLanguageManager()->getPlayerLanguage($sender) : PiggyFactions::getInstance()->getLanguageManager()->getDefaultLanguage();
        $message = PiggyFactions::getInstance()->getLanguageManager()->getMessage($language, "commands.top.header", ["{PAGE}" => $page + 1, "{TOTALPAGES}" => ceil(count($factions) / self::PAGE_LENGTH), "{CATEGORY}" => ucfirst($type)]);
        foreach (array_slice($factions, $page * self::PAGE_LENGTH, self::PAGE_LENGTH) as $rank => $faction) {
            $message .= TextFormat::EOL . PiggyFactions::getInstance()->getLanguageManager()->getMessage($language, "commands.top.line." . $type, [
                    "{RELATIONCOLOR}" => $sender instanceof Player ? PiggyFactions::getInstance()->getLanguageManager()->getColorFor($sender, $faction) : PiggyFactions::getInstance()->getConfig()->getNested("symbols.colors.neutral", TextFormat::WHITE),
                    "{RANK}" => $rank + 1 + $page * self::PAGE_LENGTH,
                    "{FACTION}" => $faction->getName(),
                    "{ONLINE}" => count($faction->getOnlineMembers()),
                    "{MEMBERS}" => count($faction->getMembers()),
                    "{POWER}" => RoundValue::round($faction->getPower()),
                    "{TOTALPOWER}" => count($faction->getMembers()) * PiggyFactions::getInstance()->getConfig()->getNested("factions.power.max"),
                    "{MONEY}" => RoundValue::round($faction->getMoney())
                ]);
        }
        $sender->sendMessage($message);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("type", true));
        $this->registerArgument(1, new IntegerArgument("page", true));
    }
}