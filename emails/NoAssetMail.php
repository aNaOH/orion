<?php

require_once __DIR__ . "/Email.php";

class NoAssetMail extends Email
{
    protected function getSubject(): string
    {
        return "Correo de prueba Orion";
    }

    protected function getTemplatePath(): string
    {
        return __DIR__ . "/templates/noasset_email.dmail";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => "Juan",
        ];
    }

    protected function getFonts(): string
    {
        return '<link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">';
    }
}
