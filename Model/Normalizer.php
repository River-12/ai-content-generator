<?php

namespace Riverstone\AiContentGenerator\Model;

class Normalizer
{
    /**
     * Remove the style, script and html tags from content

     * @param string $html
     * @return string
     */
    public function htmlToPlainText(string $html): string
    {
        $plainText = preg_replace('/<style[^>]*>.*<\/style>/Uis', '', $html);
        $plainText = preg_replace('/<script[^>]*>.*<\/script>/Uis', '', $plainText);
        $plainText = strip_tags($plainText);
        
        $plainText = html_entity_decode($plainText);
        $plainText = preg_replace('/\s+/u', ' ', $plainText);
        $plainText = trim($plainText);

        return $plainText;
    }
}
