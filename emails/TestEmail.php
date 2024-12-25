<?php

require_once 'Email.php';

class TestEmail extends Email {
    public function __construct(string $to) {
        $subject = "Test Email";
        $body = $this->formatBody($this->generateContent());
        parent::__construct($to, $subject, $body);
    }

    private function generateContent(): string {
        return '
            <div class="container">
                <h1 class="title">Bienvenido a Orion</h1>
                <p class="text">Este es un correo de prueba para comprobar los estilos.</p>
                <a href="https://togetheronorion.com" class="button">Visita Orion</a>
            </div>
        ';
    }

    protected function formatBody(string $content): string {
        $styles = file_get_contents(__DIR__ . '/email.css');
        return "<html><head><style>{$styles}</style></head><body>{$content}</body></html>";
    }
}
