<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Framework\Routes as Routes;
use App\Contracts\IMessagesService;
use App\Factories\IMessageFactory;
use App\Framework\Request;
use App\Framework\Routing\IRouteManagerService;
use App\Services\CurlService;

/**
 * @Routes\Root("messages");
*/
class MessageController extends Controller{

    public function __construct(private IMessagesService $messageService, private IRouteManagerService $routerService, private IMessageFactory $messageFactory, private CurlService $curlService){
        
    }

    
/**
 * @Routes\Get("/getting/message");
*/
    public function MessageView(){
        $messagesReceived = $this->messageService->getMessages();
        if($messagesReceived){
            $messagesReceivedObj = [];
            foreach($messagesReceived as $msg){
                $messageObj = $this->messageFactory->criarMessagemDb($msg);
                array_push($messagesReceivedObj, $messageObj);
            }
            $this->view->bindData("messages", $messagesReceivedObj);
            $this->view->setBlock("content", "app:admin/messages_view");
        }
        $this->view->renderContent("template_site")->withStatusCode(201)->render();
    }
/**
 * @Routes\Post("/process/message");
 * @Routes\Interceptor("MiddlewareMessages");
*/
public function MessageSubmit(Request $request)
{
    $messageObj = $this->messageFactory->criarMessagemForm($request->post());
    $this->curlService->sendMessageData('http://localhost:3000/api-messages/send', $messageObj);
    if ($this->messageService->saveMessage($messageObj)) {
        $this->routerService->redirectBuild()->to("/home")->withError("successForm", "Enviado")->execute();
    } else {
        $this->routerService->redirectBuild()->to("/home")->withError("errorForm", "Não Enviado")->execute();
    };
}

}

?>