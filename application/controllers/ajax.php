<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * A controller dedicated to outputting ajax
 * Set the table in the first URI segment, then the fields ot retrieve in the next.
 * Pass any where conditionsa via $_GET 
 * 
 * E.g. to return all TASK contact_action records belonging to contact 343 you'd pass:
 * 	site.com/ajax/contact_actions/id/action_type/action_title?action_type=task&contact_id=343
 * 	
 */

class Ajax extends MY_Controller
{

	//We don't need a presenter here
	protected $_presenter = FALSE;

	public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
	}

	public function _remap($table, $params = array())
	{
	    //Load the model
	    $model_name = singular($table) . '_model';
	    $this->load->model($model_name, 'm');
	    
	    //Extract the method & load it (pass through the params)
	    $method = $params[0];
	    if (method_exists($this, $method))
	    {
	        return call_user_func_array(array($this, $method), $params);
	    }
	    show_404();
	}
	
	/**
	 * Return a JSON array.
	 *
	 * Use the URI segments ot define the fields and the $_GET array to define the where
	 * conditions:
	 *
	 * e.g. domain.com/ajax/contacts/id/first_name?owner_id=222
	 */
	public function get_table()
	{
		//is there a join passed?
		if (isset($_GET['join']))
		{
			$params = explode(',', $_GET['join']);
			//dump($params);
			$join = array(
              	'table' => $params[0],
              	'fk' => $params[1],
              	);

			$where['deleted'] = 0;
			$where['owner_id'] = OWNER_ID;

			unset($_GET['join']);

		}
		else $join = FALSE;

		//Now get the data (using datatables library)
		$cols = $this->set_cols(TRUE);
		$where = $_GET;
		$output = $this->m->get_datatables_ajax($cols, $where, $join);

		echo $output;
	}


	/**
	 * Return a JSON array ready for the typeahead function.
	 *
	 * Use the URI segments ot define the fields and the $_GET array to define the where
	 * conditions:
	 *
	 * e.g. domain.com/ajax/contacts/id/first_name?owner_id=222
	 */
	public function typeahead_contacts()
	{
		$cols = $this->set_cols();

		//set up thew wildcard search
		$like = array();

		if (count($_GET))
		{
			
			//Has the user typed 2 names?
			if (strpos($_GET['q'], ' '))
			{
				//YES... search for both names
				$q = explode(' ', $_GET['q']);
				$this->m->set_like(array('first_name' => $q[0]));
				$this->m->set_like(array('last_name' => $q[1]));
			} 
			else
			{
				//NO... just search for input as either first or last
				$q = $_GET['q'];
				$this->m->set_like(array('first_name' => $q));
				$this->m->set_like(array('last_name' => $q), TRUE);
			}

		}
		//set the cols & sort order
		$this->m->set_select('ajax', $cols);
		$this->m->order_by('first_name');

		$q = $this->m->get_all();
		
		//Now format the array into datums (https://github.com/twitter/typeahead.js#datum)
		$dataset = array();;
		foreach ($q as $o)
		{
			$datum = array();
			// dump($o);
			$datum['value'] = $o->first_name . ' ' . $o->last_name;
			if ($o->postal_code) $datum['value'] .= ' (' . strtoupper($o->postal_code) . ')';
			$datum['tokens'] = array($o->id, $o->first_name, $o->last_name);
			$datum['id'] = $o->id;
			// $datum['postal_code'] = $o->postal_code;

			$dataset[] = $datum;
			//
		}		
//dump(json_encode($dataset));
// $this->output->enable_profiler(TRUE);

		echo json_encode($dataset);			
	}



	public function typeahead()
	{
		$cols = $this->set_cols();

		//set up thew wildcard search
		$like = array();

		if (count($_GET))
		{
			foreach ($_GET as $k => $v)
			{
				$this->m->set_like(array($k => $v), TRUE);
			}

		}
		die(dump($this->m));

		//$where = $_GET;

		//set the cols
		$this->m->set_select('ajax', $cols);
		$this->m->order_by('first_name');
		
		echo json_encode($this->m->get_many_like($like));
		//do the query

		//Send to the datatables library
		//echo $this->m->get_datatables_ajax($cols, $where);
	}

	/**
	 * Set the columns for the query. They are passed as URI paramaters
	 * //e.g. /ajax/table_name/col1/col2/col3/col4
	 * @param boolean $csv Output as a comma sep list (FALSE = array)
	 */
	protected function set_cols($csv = FALSE)
	{
		$cols = array_slice($this->uri->rsegment_array(), 3);
		if ($csv) return implode(',', $cols);
		else return $cols;
	}




	


}