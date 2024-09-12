<?php

namespace App\Framework;

interface IRequest{
  
    public function getMethod();

    public function payload();

    public function getUrl();

    public function getTarget();

    public function file($fileName);

    public function get($name);

    public function post($name);
}
