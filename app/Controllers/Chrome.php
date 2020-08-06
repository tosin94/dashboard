<?php namespace App\Controllers;

use App\Libraries\GroceryCrud;

class Chrome extends BaseController
{

	public function dashboard()
	{
        $crud = new GroceryCrud();
        $chrome = new \App\Models\ChromeBookModel();
        $chrome->get_csv();

        $crud->setTable('chrome_book');
        $crud->unsetDelete();
        $output = $crud->render();
        //var_dump($output);
		return $this->_exampleOutput($output);
    }

    private function _exampleOutput($output = null) {
        return view('dashboard', (array)$output);
    }

    

	//--------------------------------------------------------------------

}
