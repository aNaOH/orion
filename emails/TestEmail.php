<?php

require_once __DIR__ . "/Email.php";

class TestEmail extends Email
{
    protected function getSubject(): string
    {
        return "Correo de prueba Orion";
    }

    protected function getTemplatePath(): string
    {
        return "emails/test.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => "Juan",
        ];
    }
}
