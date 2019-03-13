<?php

class FrontLib_Lang
    extends AppLib_Lang
{
    static protected function setTranslationsPath()
    {
        self::$_translationsPath = APPLICATION_PATH . "/configs/translations.tmx";
    }
}
