<?php

namespace App\Support;

class ArticleText
{
    public static function clean(?string $content): string
    {
        $content = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', (string) $content) ?? (string) $content;
        $content = strip_tags($content);
        $content = str_replace('rnrn', "\n\n", $content);

        return preg_replace('/(?<=[.!?:;])rn(?=\s|[A-Z0-9*\-])/', "\n", $content) ?? $content;
    }
}
