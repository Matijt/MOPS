<?php
/**
 * Created by PhpStorm.
 * User: Matias
 * Date: 09/02/2017
 * Time: 12:29 AM
 */

namespace backend\controllers;


class calculadora
{

    public function sumar($uno,$dos){
        return $uno+$dos;
    }

    public function restar($uno,$dos){
        return $uno-$dos;
    }

    public function multiplicar($uno,$dos){
        return $uno*$dos;
    }

    public function dividir($uno,$dos){
        return $uno/$dos;
    }
}