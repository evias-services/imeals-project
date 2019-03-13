<?php

class AppLib_Service_Application
    extends AppLib_Service_Abstract
{
    /**
     * getVersion
     *
     * @return string
     **/
    public function getVersion()
    {
        $version_file_path = realpath(dirname(__FILE__) . "/../") . "/VERSION";
        $content = file_get_contents($version_file_path);

        return trim($content);
    }
}

