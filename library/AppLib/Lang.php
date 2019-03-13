<?php

abstract class AppLib_Lang
{
    /**
     * @var Zend_Translate_Adapter
     */
    static protected $_translateAdapter = null;

    /**
     * @var string
     */
    static protected $_translationsPath = "";

    static public function init($lang = null, Zend_Translate_Adapter $adapter = null)
    {
        $sess = new Zend_Session_Namespace("language_adapter");

        $old_lang = $sess->lang;
        if (null != $lang && in_array($lang, array("fr", "de", "en")))
            /* Overwrite language to new one */
            $sess->lang = $lang;

        if (! isset($sess->lang) || ! in_array($sess->lang, array("fr", "de", "en")))
            /* Default language OR erronous session content */
            $sess->lang = "fr";

        static::setTranslationsPath();

        /* Initialize translate adapter */
        static::$_translateAdapter = new Zend_Translate("Zend_Translate_Adapter_Tmx",
                                            static::$_translationsPath,
                                            $sess->lang,
                                            array());

        /* Clear cache on language change. */
        if (Zend_Translate_Adapter::hasCache()
            && $sess->lang != $old_lang)
            Zend_Translate_Adapter::clearCache();
    }

    static public function tr($str)
    {
        if (! isset(static::$_translateAdapter))
            static::init();

        return static::$_translateAdapter->_($str);
    }

    static public function getCurrentLang()
    {
        $sess = new Zend_Session_Namespace("language_adapter");
        return $sess->lang;
    }

    abstract static protected function setTranslationsPath();
}
