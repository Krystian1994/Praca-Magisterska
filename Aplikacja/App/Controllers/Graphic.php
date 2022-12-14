<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

class Graphic extends Authenticated{
    protected function before(){
        parent::before();
        $this->user = Auth::getUser();
    }

    public function newAction(){
        View::renderTemplate('Graphic/graphic.html', [
            'user' => $this->user,
        ]);
    }
}
