<?php
class DefaultController
{
    public function index()
    {
        header('Location: /webbanhang/Product/home');
        exit;
    }

    public function apiDemo()
    {
        include 'app/views/shares/api_demo.php';
    }
}
