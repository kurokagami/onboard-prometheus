<?php
namespace App\Framework\Session;

interface ISessionManager{

    public function set($key, $value);

    public function get($key);

    public function isset($key);

    public function remove($key);

    public function destroy();
}
