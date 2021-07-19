<?php


namespace Yosimitso\WorkingForumBundle\Util;

/**
 * Class Slugify
 *
 * @package Yosimitso\WorkingForumBundle\Util
 */
abstract class Slugify
{
    /**
     * @return mixed|string
     * Generate a slug
     */
    public static function convert(string $string)
    {
        /** Mise en minuscules (chaîne utf-8 !) */
        $string = mb_strtolower($string, 'utf-8');

        /** Nettoyage des caractères */
        mb_regex_encoding('utf-8');

        $string = trim(
            preg_replace(
                '/ +/',
                ' ',
                mb_ereg_replace('[^a-zA-Z\p{L}]+', ' ', $string)
            )
        );

        /** strtr() sait gérer le multibyte */
        $string = strtr($string,
            [
                ' ' => '-',
                'à' => 'a',
                'á' => 'a',
                'â' => 'a',
                'ã' => 'a',
                'ä' => 'a',
                'å' => 'a',
                'æ' => 'a',
                'ç' => 'c',
                'è' => 'e',
                'é' => 'e',
                'ê' => 'e',
                'ë' => 'e',
                'ì' => 'i',
                'í' => 'i',
                'î' => 'i',
                'ï' => 'i',
                'ñ' => 'n',
                'ð' => 'o',
                'ò' => 'o',
                'ó' => 'o',
                'ô' => 'o',
                'õ' => 'o',
                'ö' => 'o',
                'œ' => 'o',
                'ø' => 'o',
                'š' => 's',
                'ù' => 'u',
                'ú' => 'u',
                'û' => 'u',
                'ü' => 'u',
                'ý' => 'y',
                'ÿ' => 'y',
                'ž' => 'z',
            ]
        );

        return $string;
    }
}
