<?php
namespace Framework\Routing;

use Framework\Utils\HttpStatusCode;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Framework\Route;
use App\Framework\Routes\Root;
use App\Framework\Routes\Get;
use App\Framework\Routes\Post;
use App\Framework\Routes\NotFound;
use Framework\Interfaces\IServiceProvider;
use Framework\Routing\Contracts\IRouterService;
use Framework\Exceptions\NotFoundHttpException;
use App\Middleware;
use Exception;
use DI\Annotation\Scope;

/**
 * @Scope("singleton")
 */
final class RouterHelper{

    //Função que verifica se o comentário é de uma rota Get | Post
    public static function isHttpMethod($annotation) {
        return $annotation instanceof Get || $annotation instanceof Post;
    }

    public static function isNotFoundAction($annotation) {
        return $annotation instanceof NotFound;
    }

    public static function isRouteDuplicated($routeKey, $availableRoutes) {
        return isset($availableRoutes[$routeKey]);
    }

    public static function sanitizeURL($url) {
        $url = explode("?", $url)[0];
        $url = preg_replace('/^\/+/', '', $url);
        $url = preg_replace('#(?<!:)//+#', '/', $url);
        return strtolower($url);
    }

    public static function getRootRoute($reflectionClass, $annotationReader) {
        $classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
        $rootRoute = "";
        
        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof Root) {
                $rootRoute = $annotation->value;
                break;
            }
        }
        
        return $rootRoute;
    }

    public static function getClassNamespace($filePath) {
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        
        $namespaceRegex = '/namespace\s+([^\s;]+)/';
        if (preg_match($namespaceRegex, $content, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    public static function canNavigate($routeMap,$target)  {
       
        foreach ($routeMap as $key => $route) {
            // Escape special characters in the key
            $escapedKey = preg_quote($key, '#');
            
            // Replace ? with [^\/]*
            $newKey = str_replace('\?', '[^\/]*', $escapedKey);
        
            // Check if the route is compatible with the target
            if (preg_match("#^$newKey$#", $target)) {
                return $key;
            }
        }
        //Caso não encontre uma rota compativel, retorna false
        return false;
    }


}
