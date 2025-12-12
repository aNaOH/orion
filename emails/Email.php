<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

abstract class Email
{
    protected string $to;
    protected string $subject;
    protected string $body;

    /** @var array<int, array> */
    protected array $attachments = [];

    public function __construct(string $to)
    {
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Destinatario inválido: $to");
        }

        $this->to = $to;
        $this->subject = $this->getSubject();
        $this->body = $this->renderBody(
            $this->getTemplatePath(),
            $this->getVariables(),
        );
    }

    abstract protected function getSubject(): string;
    abstract protected function getTemplatePath(): string;

    protected function getVariables(): array
    {
        return [];
    }

    protected function getFonts(): string
    {
        return "";
    }

    /** Previsualizar HTML final */
    public function getHtml(): string
    {
        return $this->renderBody(
            $this->getTemplatePath(),
            $this->getVariables(),
        );
    }

    /** Adjuntar imagen local con CID */
    protected function asset(string $path): string
    {
        $fullPath = __DIR__ . "/../" . ltrim($path, "/");
        if (!file_exists($fullPath)) {
            throw new RuntimeException("Archivo no encontrado: $fullPath");
        }

        $content = file_get_contents($fullPath);
        if ($content === false || $content === "") {
            throw new RuntimeException("No se pudo leer: $fullPath");
        }

        $filename = basename($fullPath) ?: "asset_" . uniqid();
        $cid = "cid_" . md5($fullPath . microtime(true));

        $mime = mime_content_type($fullPath) ?: "application/octet-stream";

        $this->attachments[] = [
            "cid" => $cid,
            "filename" => $filename,
            "content" => $content,
            "mime" => $mime,
            "inline" => true,
        ];

        return "cid:$cid";
    }

    /** Adjuntar imagen remota con CID */
    protected function onlineAsset(string $url): string
    {
        if (!preg_match("#^https?://#", $url)) {
            $url = $this->url($url);
        }

        $content = @file_get_contents($url);

        if ($content === false || $content === "") {
            throw new RuntimeException("No se pudo descargar $url");
        }

        $filename =
            basename(parse_url($url, PHP_URL_PATH)) ?:
            "image_" . uniqid() . ".jpg";
        $cid = "cid_" . md5($url . microtime(true));

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $mime = $ext
            ? mime_content_type("dummy.$ext")
            : "application/octet-stream";

        $this->attachments[] = [
            "cid" => $cid,
            "filename" => $filename,
            "content" => $content,
            "mime" => $mime,
            "inline" => true,
        ];

        return "cid:$cid";
    }

    /** Genera URL absoluta */
    protected function url(string $path): string
    {
        $base = $_ENV["APP_URL"] ?? "http://localhost";
        return rtrim($base, "/") . "/" . ltrim($path, "/");
    }

    /** Renderiza el template HTML */
    protected function renderBody(
        string $templatePath,
        array $variables,
    ): string {
        if (!file_exists($templatePath)) {
            throw new RuntimeException(
                "Plantilla no encontrada: $templatePath",
            );
        }

        $html = file_get_contents($templatePath);

        // Reemplazo simple
        foreach ($variables as $key => $value) {
            if (is_scalar($value)) {
                $html = str_replace("{{{$key}}}", (string) $value, $html);
            }
        }

        // Lógica
        $html = $this->processLogic($html, $variables);

        // Funciones
        $html = $this->processFunctions($html);

        // CSS
        $cssPath = __DIR__ . "/email.css";
        $styles = file_exists($cssPath) ? file_get_contents($cssPath) : "";

        $fonts = $this->getFonts();

        if (stripos($html, "<head>") === false) {
            $html = "<html><head>$fonts<style>$styles</style></head><body>$html</body></html>";
        } else {
            $html = preg_replace(
                "/<head>(.*?)<\/head>/is",
                "<head>$1$fonts<style>$styles</style></head>",
                $html,
            );
        }

        return $html;
    }

    /** Resolución de variables complejas */
    protected function resolveVariable(string $expression, array $variables)
    {
        $parts = preg_split("/\./", $expression);
        $value = $variables;

        foreach ($parts as $part) {
            if (preg_match('/^([a-zA-Z0-9_]+)\[([0-9]+)\]$/', $part, $m)) {
                $prop = $m[1];
                $index = $m[2];

                if (isset($value[$prop][$index])) {
                    $value = $value[$prop][$index];
                } else {
                    return "";
                }

                continue;
            }

            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
                continue;
            }

            if (is_object($value) && isset($value->$part)) {
                $value = $value->$part;
                continue;
            }

            return "";
        }

        return $value;
    }

    /** FOREACH, IF y variables */
    protected function processLogic(string $html, array $variables): string
    {
        // FOREACH
        $html = preg_replace_callback(
            "/\{\{\s*foreach\s+([a-zA-Z0-9_]+)\s+as\s+([a-zA-Z0-9_]+)(?::([a-zA-Z0-9_]+))?\s*\}\}(.*?)\{\{\s*endforeach\s*\}\}/is",
            function ($m) use ($variables) {
                [$full, $listName, $itemName, $indexName, $content] = $m;

                if (
                    !isset($variables[$listName]) ||
                    !is_iterable($variables[$listName])
                ) {
                    return "";
                }

                $output = "";
                $i = 0;

                foreach ($variables[$listName] as $value) {
                    $block = $content;

                    if (is_scalar($value)) {
                        $block = str_replace(
                            "{{{$itemName}}}",
                            (string) $value,
                            $block,
                        );
                    }

                    if ($indexName) {
                        $block = str_replace("{{{$indexName}}}", $i, $block);
                    }

                    $output .= $block;
                    $i++;
                }

                return $output;
            },
            $html,
        );

        // IF
        $html = preg_replace_callback(
            "/\{\{\s*if\s+([a-zA-Z0-9_]+)\s*\}\}(.*?)\{\{\s*endif\s*\}\}/is",
            function ($m) use ($variables) {
                return !empty($variables[$m[1]]) ? $m[2] : "";
            },
            $html,
        );

        // Variables complejas
        $html = preg_replace_callback(
            "/\{\{\s*([a-zA-Z0-9_]+(?:[.\[][a-zA-Z0-9_()\]]+)+)\s*\}\}/",
            fn($m) => $this->resolveVariable($m[1], $variables),
            $html,
        );

        return $html;
    }

    /** Procesa asset(), onlineAsset() y url() */
    protected function processFunctions(string $html): string
    {
        $html = preg_replace_callback(
            '/\{\{\s*asset\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->asset($m[1]),
            $html,
        );

        $html = preg_replace_callback(
            '/\{\{\s*onlineAsset\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->onlineAsset($m[1]),
            $html,
        );

        $html = preg_replace_callback(
            '/\{\{\s*url\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->url($m[1]),
            $html,
        );

        return $html;
    }

    /** Envío SMTP propio */
    public function send(): bool
    {
        return true;
        $this->validateBeforeSend();

        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV["SMTP_HOST"];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["SMTP_USER"];
            $mail->Password = $_ENV["SMTP_PASSWORD"];
            $mail->Port = intval($_ENV["SMTP_PORT"] ?? 587);
            $mail->SMTPSecure =
                $_ENV["SMTP_SECURE"] ?? PHPMailer::ENCRYPTION_STARTTLS;

            // Remitente
            $mail->setFrom($_ENV["EMAIL_FROM"], "Orion");

            // Destinatario
            $mail->addAddress($this->to);

            // Inline attachments
            foreach ($this->attachments as $att) {
                if ($att["inline"]) {
                    $mail->addStringEmbeddedImage(
                        $att["content"],
                        $att["cid"],
                        $att["filename"],
                        "base64",
                        $att["mime"],
                    );
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->AltBody = strip_tags($this->body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new RuntimeException(
                "Error enviando email: " . $e->getMessage(),
                0,
                $e,
            );
        }
    }

    protected function validateBeforeSend(): void
    {
        $required = [
            "SMTP_HOST",
            "SMTP_USER",
            "SMTP_PASSWORD",
            "SMTP_PORT",
            "EMAIL_FROM",
        ];

        foreach ($required as $key) {
            if (empty($_ENV[$key])) {
                throw new RuntimeException("$key no definido en el entorno");
            }
        }

        if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException("Destinatario inválido");
        }

        if (trim($this->subject) === "") {
            throw new RuntimeException("Subject vacío");
        }

        if (trim($this->body) === "") {
            throw new RuntimeException("Body vacío");
        }
    }
}
