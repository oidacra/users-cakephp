<?php
use Cake\Core\Plugin;
use Cake\Core\Configure;

Configure::load('Acciona/Users.users');
collection((array)Configure::read('Users.config'))->each(function ($file) {
    Configure::load($file);
});

Plugin::load('ADmad/JwtAuth');
