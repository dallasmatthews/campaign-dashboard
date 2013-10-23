<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Controller for contacts table
*/

class Templates extends MY_Controller
{
	//What models should we load?
	public $models = array();

	//What views are we using? Defaults to views/__CLASS__/__METHOD__
	//public $view ; //FALSE = load no view, 'view_name' = load view_name.php instead

	public function __construct()
	{
		parent::__construct();
		require_once (APPPATH . 'presenters/template_presenter.php');
		
	}

	public function index()
	{
		//$this->data['contacts'] = $this->contact->get_all();
	}

	public function grid()
	{
		# code...
	}

	public function layout_index()
	{
		# code...
	}
	
	public function show()
	{
		//Shows a blank record with the form action = create/edit
		$this->data['template'] = new Template_Presenter();
		$this->view = 'templates/layout_show';
	}

	public function create()
	{
		//Shows a blank record with the form action = create/edit
		$this->data['template'] = new Template_Presenter();
		$this->view = 'templates/layout_show';
	}
	
}