<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Framework\Routes as Routes;

/**
 * @Routes\Root("");
*/

final class IndexController extends Controller{
    /**
     * @Routes\Get("");
    */
    public function IIndex(){
        $this->view->setBlock("content", "app:main/home");
        $this->view->renderContent("template_site")->withStatusCode(201)->render();
    }
    /**
     * @Routes\Get("home");
    */
    public function IHome(){
        $this->view->setBlock("content", "app:main/home");
        $this->view->renderContent("template_site")->withStatusCode(201)->render();
    }
    /**
     * @Routes\Get("projects");
    */
    public function IProjects(){
        $this->view->setBlock("content", "app:main/projects");
        $this->view->render("app:template_site");
    }

}

?>