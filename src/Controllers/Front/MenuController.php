<?php

namespace Spreadaurora\ci4_page\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Spreadaurora\ci4_page\Models\PagesModel;

class FrontPagesController extends \App\Controllers\Front\FrontController
{
    use ResponseTrait;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel  = new PagesModel();
    }
    public function index()
    {
    }

    public function show($id)
    {
    }
}
