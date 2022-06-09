<?php

namespace App\View;
use App\Base\View\BaseView;

require_once viewDir . '/base/BaseView.php';

class StudentView extends BaseView
{
    function viewAddForm()
    {
        $this->includeViewFile('addForm'); 
    }

	function viewDefault(): void {
	}
	
    
	function __construct()
    {
        parent::__construct(__DIR__, ['addForm' => 'addForm.php']);
	}
}
