<?php

class ImageController
    extends FrontLib_Controller_Action
{
    public function indexAction()
    {
        $this->view->layout()->disableLayout();
        
        echo "[";
        if ($handle = opendir(APPLICATION_PATH . "/../public/images/caroussel")) {
            while (false !== ($file = readdir($handle)))
                if ($file != "." && $file != "..") 
                    echo "'{$this->view->baseUrl()}/images/caroussel/$file',";
            
            closedir($handle);
        }
        echo "]";
        exit;
    }
}
