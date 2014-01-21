<?php

namespace Mynd\Lib\String;

class String
{
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }

    /**
     * reconstruction of a slugify string
     * info : special characters and accents are not reconstituted !
     * @author josedacosta <contact@josedacosta.info>
     * @param string $text slugify string
     * @return string unslugify string
     */
    public static function unslugify($text)
    {
        $text = preg_replace('-',' ', $text);
        $text = preg_replace('\s+',' ', $text);
        // $text = strtolower($text);
        return $text;
    }

    public static function parseAndExplode($str)
    {
        $INVALIDE_WORDS = array('est', 'sont', 'de', 'du', 'ca', 'ça', 'ce', 'et', 'la', 'le', 'les', 'un', 'une', 'ta', 'ton', 'tes', 'mon','ma', 'mes', 'son,', 'sa', 'ses', 'vos', 'nos', 'leurs', 'leur');
        $str = strip_tags($str);
        $array = preg_split('#[^a-zA-Z0-9éèàùêôîûŷâêäëïöüÿ\-]#i', $str);
        $output = array();
        foreach ($array as $el) {
            if ( (!in_array($el, $INVALIDE_WORDS)) && (\utf8_strlen($el) > 1) ) {
                $output[] = \utf8_strtolower($el);
            }
        }
        $output = array_unique($output);
        return $output;
    }

    /**
     * TODO : gestion des accents
     * @param unknown_type $string
     */
    public static function urlize($string)
    {
        return preg_replace('#([^0-9a-zA-Z\.])#', '-', $string);
    }

    public static function truncate($text, $length = 30, $truncate_string = '...')
    {
        if (utf8_strlen($text) > $length) {
            return utf8_substr_replace($text, $truncate_string, $length - utf8_strlen($truncate_string));
        } else {
            return $text;
        }
    }
}