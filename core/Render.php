<?php
namespace Framework;
use Framework\Exceptions\TemplateNotFoundException;
use Exception;


use App\Framework\Routing\IRouteManagerService;

#[\AllowDynamicProperties]
final class Render{
    private $viewRendered;

    const VIEW_EXTENSION = ".phtml";
    public function __construct(private IRouteManagerService $routerManager){
        $this->viewRendered = false;
    }

    public function json($data,$code = 200){
        if(!$this->viewRendered){
            header('Content-Type: application/json');
            echo json_encode($data);
            http_response_code($code);
            die();
        }
    }

    public function text($data,$code = 200){
        if(!$this->viewRendered){
            echo $data;
            http_response_code($code);
            die();
        }
    }

    public function view($templatePath,$template,$dataContext,$blockCollection,$params){
        if(!$this->viewRendered){
            //Caracteres UTF-8
            header('Content-Type: text/html; charset=utf-8');

            //Extrair variaveis de binding para memoria
            extract($dataContext);

            //Criar Block Collection
            $this->blockCollection = $blockCollection;
            $this->vwDataContext = $dataContext;
            $this->vwMessages = $params;
            $this->moduleDir = $templatePath;

            $template = TemplateDirectoryHelper::buildTemplatePath($templatePath,$template);

            //Path
            $templatePath = "{$template}.phtml";

            if(file_exists($templatePath)){
                $this->viewRendered = true;
                $this->initBlocksRecursive();

                $content = file_get_contents($templatePath);
                eval('?>' . $content);
            }else{
                throw new TemplateNotFoundException($templatePath);
            }
            return $this;
        }
    }

    private function initInternalBlocks($content){
        $blockMatches = [];
        $pattern = '/\{% block ([\w]+) %\}(.*?)\{% endblock %\}/s';
        preg_match_all($pattern, $content, $blockMatches, PREG_SET_ORDER);
        foreach($blockMatches as $block){
            $contentPattern = '/{% block [\w]+ %}([\s\S]*?){% endblock %}/';
            preg_match($contentPattern, $block[0], $blockMatch);

            $blockName = trim($block[1]);
            $blockContent = $blockMatch[1];
            $this->templateBlockCollection[trim($blockName)] = $blockContent;
        }
        $removePattern = '/{% block [\w]+ %}[\s\S]*?{% endblock %}/';
        return preg_replace($removePattern, '', $content);
    }


    protected function route($route,$params = []){
        if(!$route){
            echo "Tentando chamar uma rota pelo alias da controller, que ainda não esta implementado";
        }else{
            return $this->routerManager->buildUrl($route);
        }
    }

    public function include($content){
    
        if($this->viewRendered){
            extract($this->vwDataContext);
            $includePath = TemplateDirectoryHelper::buildTemplatePath($this->moduleDir,$content);
            if(file_exists($includePath.self::VIEW_EXTENSION )){
                eval('?>' . file_get_contents($includePath.self::VIEW_EXTENSION));
            }else{
                $msg = "Não encontrei o include desejado: {$includePath}";
                echo $msg;
            }
        }
    }

    public function error($id){
        return $this->vwMessages["errors"][$id] ?? false;
    }

    public function initBlocksRecursive(){
        $this->templateBlockCollection = [];

        foreach($this->blockCollection as $name=>$path){
            $blockPath = TemplateDirectoryHelper::buildTemplatePath($this->moduleDir,$this->blockCollection[$name]);
            $blockFullPath = $blockPath.self::VIEW_EXTENSION;
            if(file_exists($blockPath.self::VIEW_EXTENSION )){
                $content = file_get_contents($blockFullPath);
                $this->blockContent[$name] = $this->initInternalBlocks($content);
            }else{
                $msg = "O bloco registrado não possui arquivo valido: {$blockFullPath}";
                $this->blockContent[$name] = $msg;
            }
        }
    }

    public function block($name,$warn = true){
        if($this->viewRendered){
            if(isset($this->blockContent[$name])){
                extract($this->vwDataContext);
                eval('?>' . $this->blockContent[$name]);
            }elseif(isset($this->templateBlockCollection[trim($name)]) ){
                extract($this->vwDataContext);
                eval(' ?>' .$this->templateBlockCollection[trim($name)] );
            }elseif($warn){
                echo "Block not found!";
            }
        }
    }

    public function static($url){
        return "{$_ENV["SELF_URL"]}/$url";
    }
}
