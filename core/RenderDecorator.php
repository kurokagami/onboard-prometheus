<?php
namespace Framework;
use Exception;
use Framework\Render;
use App\Framework\Routing\IRouteManagerService;

#[\AllowDynamicProperties]
final class RenderDecorator{

    public function __construct(private Render $render){
    }

    public function json($data,$code = 200){
        $this->render->json($data,$code);
    }

    public function text($data,$code = 200){
        $this->render->text($data,$code);
    }
}
