<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class VersionSubCommand extends FactionSubCommand
{
    protected bool $requiresPlayer = false;

    public function onBasicRun(CommandSender $sender, array $args): void
    {
        $translators = [
            "adeynes" => ["French"],
            "Aericio" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "ItsMax123" => ["French"],
            "MrAshshiddiq" => ["Indonesian"],
            "prprprprprprpr" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "SalmonDE" => ["German"],
            "SillierShark195" => ["Indonesian"],
            "steelfri_031" => ["French"],
            "superbobby2000" => ["French"],
            "Taylarity" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "TGPNG" => ["Chinese (Simplified)", "Chinese (Traditional)"],
            "UnEnanoMas" => ["Spanish"],
            "yuriiscute53925" => ["Serbian"],
        ];

        $sender->sendMessage(TextFormat::GOLD . "____________.[" . TextFormat::DARK_GREEN . "PiggyFactions " . TextFormat::GREEN . "v" . PiggyFactions::getInstance()->getDescription()->getVersion() . " (" . PiggyFactions::getInstance()->getPoggitBuildInfo()->getSpecificVersion() . ")" . TextFormat::GOLD . "].____________" . TextFormat::EOL .
            TextFormat::GOLD . "PiggyFactions is a modern factions plugin developed by " . TextFormat::YELLOW . "DaPigGuy (MCPEPIG) " . TextFormat::GOLD . "and " . TextFormat::YELLOW . "Aericio" . TextFormat::GOLD . "." . TextFormat::EOL .
            TextFormat::GOLD . "Translations provided by " . implode(", ", array_map(function (string $translator, array $languages): string {
                return TextFormat::YELLOW . $translator . " (" . implode(", ", $languages) . ")" . TextFormat::GOLD;
            }, array_keys($translators), $translators)) . TextFormat::EOL .
            TextFormat::GRAY . "Copyright 2020 DaPigGuy; Licensed under the Apache License.");
    }
}