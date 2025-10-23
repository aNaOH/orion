<?php

require_once "vendor/autoload.php";

class TailwindParsedown extends Parsedown
{
    // Sobrescribe el método para procesar encabezados (h1, h2, etc.)
    protected function blockHeader($Line)
    {
        $block = parent::blockHeader($Line);
        if ($block) {
            $level = $block["element"]["name"]; // Obtiene el nivel del encabezado (h1, h2, etc.)
            $textSize = "4xl";
            switch ($level) {
                case "h2":
                    $textSize = "2xl";
                    break;
                case "h3":
                    $textSize = "xl";
                    break;
                case "h4":
                    $textSize = "lg";
                    break;
            }
            $block["element"]["attributes"][
                "class"
            ] = "text-$textSize font-bold my-4";
        }
        return $block;
    }

    // Sobrescribe el método para procesar párrafos
    protected function blockParagraph($Line)
    {
        $block = parent::blockParagraph($Line);
        if ($block) {
            $block["element"]["attributes"]["class"] =
                "text-base leading-relaxed my-2";
        }
        return $block;
    }

    // Sobrescribe el método para listas (ul, ol)
    protected function blockList($Line)
    {
        $block = parent::blockList($Line);
        if ($block) {
            $block["element"]["attributes"]["class"] = "list-disc pl-5 my-2"; // o 'list-decimal' para listas ordenadas
        }
        return $block;
    }

    // Sobrescribe el método para procesar enlaces
    protected function inlineLink($Excerpt)
    {
        $link = parent::inlineLink($Excerpt);
        if ($link) {
            $link["element"]["attributes"]["class"] =
                "text-blue-500 hover:underline";
        }
        return $link;
    }

    // Sobrescribe el método para procesar imágenes
    protected function inlineImage($Excerpt)
    {
        $image = parent::inlineImage($Excerpt);
        if ($image) {
            $image["element"]["attributes"]["class"] =
                "max-w-full h-auto rounded";
        }
        return $image;
    }
}
