<?php

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\Attachment;

abstract class Email
{
    protected string $to;
    protected string $subject;
    protected string $body;

    /** @var Attachment[] */
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
            throw new RuntimeException(
                "No se pudo leer el contenido de: $fullPath",
            );
        }

        $filename = basename($fullPath);
        if (empty($filename) || $filename === ".") {
            $filename = "asset_" . time() . "_" . uniqid();
        }

        $cid = "cid_" . md5($fullPath . time());
        if (empty($cid)) {
            throw new RuntimeException("Error generando Content-ID");
        }

        $attachment = new Attachment();
        $attachment->setContent($content);
        $attachment->setFilename($filename);
        $attachment->setDisposition("inline");
        $attachment->setId($cid);

        $this->attachments[] = $attachment;

        return "cid:$cid";
    }

    /** Adjuntar imagen remota con CID usando cURL */
    protected function onlineAsset(string $url): string
    {
        if (!preg_match("#^https?://#", $url)) {
            $url = $this->url($url);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($content === false || $content === "" || $httpCode >= 400) {
            throw new RuntimeException(
                "No se pudo descargar: $url (HTTP $httpCode)",
            );
        }

        $parsedPath = parse_url($url, PHP_URL_PATH);
        $filename = $parsedPath ? basename($parsedPath) : "";

        if (empty($filename) || $filename === ".") {
            $filename = "online_" . time() . "_" . uniqid() . ".jpg";
        }

        $cid = "cid_" . md5($url . time());

        $attachment = new Attachment();
        $attachment->setContent($content);
        $attachment->setFilename($filename);
        $attachment->setDisposition("inline");
        $attachment->setId($cid);

        $this->attachments[] = $attachment;

        return "cid:$cid";
    }

    /** Genera URL absoluta */
    protected function url(string $path): string
    {
        $base = $_ENV["APP_URL"] ?? "http://localhost";
        return rtrim($base, "/") . "/" . ltrim($path, "/");
    }

    /** Renderiza HTML con CSS, Google Fonts y funciones especiales */
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

        // Reemplazo de variables {{var}}
        foreach ($variables as $key => $value) {
            if (is_scalar($value)) {
                // solo strings, ints, floats, bool
                $html = str_replace("{{{$key}}}", (string) $value, $html);
            }
        }

        // Procesamiento de lógica
        $html = $this->processLogic($html, $variables);

        // Funciones especiales
        $html = $this->processFunctions($html);

        // CSS
        $cssPath = __DIR__ . "/email.css";
        $styles = file_exists($cssPath) ? file_get_contents($cssPath) : "";

        $fonts = $this->getFonts();

        if (stripos($html, "<head>") === false) {
            $html = "<html><head>{$fonts}<style>{$styles}</style></head><body>{$html}</body></html>";
        } else {
            $html = preg_replace(
                "/<head>(.*?)<\/head>/is",
                "<head>$1{$fonts}<style>{$styles}</style></head>",
                $html,
            );
        }

        return $html;
    }

    protected function resolveVariable(string $expression, array $variables)
    {
        // Ej: "user.friends[0].name"
        $parts = preg_split("/\./", $expression);

        $value = $variables;

        foreach ($parts as $part) {
            // Manejo de índice: algo[3]
            if (preg_match('/^([a-zA-Z0-9_]+)\[([0-9]+)\]$/', $part, $m)) {
                $varName = $m[1];
                $index = $m[2];

                if (
                    is_array($value) &&
                    isset($value[$varName]) &&
                    is_array($value[$varName]) &&
                    isset($value[$varName][$index])
                ) {
                    $value = $value[$varName][$index];
                    continue;
                }

                return "";
            }

            // Método sin argumentos: algo()
            if (preg_match('/^([a-zA-Z0-9_]+)\(\)$/', $part, $m)) {
                $method = $m[1];

                if (is_object($value) && method_exists($value, $method)) {
                    $value = $value->$method();
                    continue;
                }

                return "";
            }

            // Propiedad normal
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } elseif (
                is_object($value) &&
                (isset($value->$part) || method_exists($value, "__get"))
            ) {
                $value = $value->$part;
            } else {
                return "";
            }
        }

        return $value;
    }

    protected function processLogic(string $html, array $variables): string
    {
        // FOREACH con índice: {{ foreach items as item:index }}...{{ endforeach }}
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

                $result = "";
                $i = 0;

                foreach ($variables[$listName] as $value) {
                    $block = $content;

                    // Reemplazo del item
                    if (is_scalar($value)) {
                        $block = str_replace(
                            "{{{$itemName}}}",
                            (string) $value,
                            $block,
                        );
                    } else {
                        // Si el valor no es escalar, simplemente elimina {{item}}
                        // (el usuario debe usar {{item.prop}} o {{item.method()}})
                        $block = str_replace("{{{$itemName}}}", "", $block);
                    }

                    // Reemplazo del índice si fue declarado
                    if ($indexName) {
                        $block = str_replace("{{{$indexName}}}", $i, $block);
                    }

                    $result .= $block;
                    $i++;
                }
                return $result;
            },
            $html,
        );

        // IF: {{ if var }} ... {{ endif }}
        $html = preg_replace_callback(
            "/\{\{\s*if\s+([a-zA-Z0-9_]+)\s*\}\}(.*?)\{\{\s*endif\s*\}\}/is",
            function ($m) use ($variables) {
                [$full, $varName, $content] = $m;
                return !empty($variables[$varName]) ? $content : "";
            },
            $html,
        );

        // ACCESO A INDICES: {{ var[3] }}
        $html = preg_replace_callback(
            "/\{\{\s*([a-zA-Z0-9_]+)\[([0-9]+)\]\s*\}\}/",
            function ($m) use ($variables) {
                [$full, $arrName, $idx] = $m;

                if (
                    isset($variables[$arrName]) &&
                    is_array($variables[$arrName]) &&
                    isset($variables[$arrName][$idx])
                ) {
                    return $variables[$arrName][$idx];
                }

                return "";
            },
            $html,
        );

        $html = preg_replace_callback(
            "/\{\{\s*([a-zA-Z0-9_]+(?:[.\[][a-zA-Z0-9_()\]]+)+)\s*\}\}/",
            function ($m) use ($variables) {
                return $this->resolveVariable($m[1], $variables);
            },
            $html,
        );

        return $html;
    }

    /** Procesa asset(), onlineAsset() y url() */
    protected function processFunctions(string $html): string
    {
        // asset local
        $html = preg_replace_callback(
            '/\{\{\s*asset\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->asset($m[1]),
            $html,
        );

        // onlineAsset remoto
        $html = preg_replace_callback(
            '/\{\{\s*onlineAsset\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->onlineAsset($m[1]),
            $html,
        );

        // url()
        $html = preg_replace_callback(
            '/\{\{\s*url\(\'([^\']+)\'\)\s*\}\}/',
            fn($m) => $this->url($m[1]),
            $html,
        );

        return $html;
    }

    /** Envía el correo usando MailerSend SDK */
    public function send(): bool
    {
        $this->validateBeforeSend();

        $apiKey = $_ENV["MAILERSEND_API_KEY"] ?? null;
        $fromEmail = $_ENV["EMAIL_FROM"] ?? null;

        $mailerSend = new MailerSend(["api_key" => $apiKey]);
        $recipient = new Recipient($this->to, $this->to);

        $emailParams = new EmailParams();
        $emailParams
            ->setFrom($fromEmail)
            ->setFromName("Orion")
            ->setSubject($this->subject)
            ->setRecipients([$recipient])
            ->setHtml($this->body)
            ->setText("This is the text content");

        if (!empty($this->attachments)) {
            $emailParams->setAttachments($this->attachments);
        }

        try {
            $mailerSend->email->send($emailParams);
            return true;
        } catch (\MailerSend\Exceptions\MailerSendAssertException $e) {
            throw new RuntimeException(
                "Error al enviar el correo vía MailerSend: " . $e->getMessage(),
                0,
                $e,
            );
        }
    }

    /**
     * Valida los campos obligatorios antes de enviar el correo.
     * Lanza excepciones con detalles si algún campo es NULL o inválido.
     */
    protected function validateBeforeSend(): void
    {
        // API Key y remitente
        $apiKey = $_ENV["MAILERSEND_API_KEY"] ?? null;
        $fromEmail = $_ENV["EMAIL_FROM"] ?? null;

        if (is_null($apiKey) || trim($apiKey) === "") {
            throw new RuntimeException(
                "MAILERSEND_API_KEY no definido o vacío",
            );
        }

        if (is_null($fromEmail) || trim($fromEmail) === "") {
            throw new RuntimeException("EMAIL_FROM no definido o vacío");
        }

        // Destinatario
        if (
            is_null($this->to) ||
            !filter_var($this->to, FILTER_VALIDATE_EMAIL)
        ) {
            throw new RuntimeException(
                "Destinatario inválido: " . ($this->to ?? "NULL"),
            );
        }

        // Subject
        if (is_null($this->subject) || trim($this->subject) === "") {
            throw new RuntimeException("Subject no definido o vacío");
        }

        // Body
        if (is_null($this->body) || trim($this->body) === "") {
            throw new RuntimeException("Body del correo vacío");
        }

        // Validar attachments de manera más estricta
        foreach ($this->attachments as $i => $att) {
            $arr = $att->toArray();

            if (
                !isset($arr["content"]) ||
                is_null($arr["content"]) ||
                $arr["content"] === ""
            ) {
                throw new RuntimeException(
                    "Attachment #$i: content vacío, NULL o no definido (archivo: " .
                        ($arr["filename"] ?? "unknown") .
                        ")",
                );
            }

            if (
                !isset($arr["filename"]) ||
                is_null($arr["filename"]) ||
                trim($arr["filename"]) === ""
            ) {
                throw new RuntimeException(
                    "Attachment #$i: filename vacío, NULL o no definido",
                );
            }

            if (
                !isset($arr["disposition"]) ||
                is_null($arr["disposition"]) ||
                trim($arr["disposition"]) === ""
            ) {
                throw new RuntimeException(
                    "Attachment #$i: disposition vacío, NULL o no definido (archivo: {$arr["filename"]})",
                );
            }

            if (
                !isset($arr["id"]) ||
                is_null($arr["id"]) ||
                trim($arr["id"]) === ""
            ) {
                throw new RuntimeException(
                    "Attachment #$i: id (Content-ID) vacío, NULL o no definido (archivo: {$arr["filename"]})",
                );
            }
        }
    }
}
