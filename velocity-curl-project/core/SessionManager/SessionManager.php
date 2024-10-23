<?php
namespace Framework\Session;

use App\Framework\Session\ISessionManager;

final class SessionManager implements ISessionManager{

    public function __construct(){
        //Inicia a sessão
        session_start();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function remove($key){
        if (isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public function isset($key){
        return isset($_SESSION[$key]) ? true : false;
    }

    public function destroy() {
        session_destroy();
    }

    public function reset(){
        session_unset();
        session_destroy();
    }
}
