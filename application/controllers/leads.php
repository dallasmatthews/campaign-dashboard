<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Controller for contacts table
*/

class Leads extends MY_Controller
{
/* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */

    /**
     * These vars can overwrite the default ones set in MY_Controller
     *
     * NOTE: Set the scope as 'protected' here
     */
    
    protected $_models = array(
        'contact_action' => array(
            'where' => array(
                array('lead_id' => '%id%'),
                ),
            ),
            // 'join' => array(
            //  array(
            //      'table' => 'users',
            //      'join_on' => 'users.id=contacts.user_id',
            //      'join_type' => '',
            //      'join_fields' => array('tags.tag_id', 'tags.tag_id')
            //      ),
            //'other' => array(   //ensure this is a valid active record method
                // 'limit' => 4,
                // ),         
                // ),
        
              
        );

    // protected $_layout = FALSE;  //Defaults to 'application' - override here with false or another name
    
    // protected $_view_settings = array();     //Defaults to 'application' - override here with false or another name
    
    // protected $_presenter = FALSE;   //Defaults to $this->main_model - override here with false or another name
    
    // protected $_main_model = FALSE;  //Defaults to class name, but overwrite or set to FALSE

    

    /* --------------------------------------------------------------
     * METHODS
     * ------------------------------------------------------------ */

    /**
     * These methods are defined in MY_Controller. You can extend them (return parent::{method_name}() ) or over-ride them by defning a new method here.
     *
     */
    
    public function __construct()
    {
        parent::__construct();
    }

    public function show_board()
    {
        //Get all current leads
        $attr = array(
            'join' => array(
             array(
                 'table' => 'contacts',
                 'join_on' => 'contacts.id=leads.contact_id',
                 'join_type' => '',
                 'join_fields' => array('contacts.first_name', 'contacts.last_name', 'contacts.org_name')
                 ),
            'id_as_key' => TRUE,
            ),
        );
        $q = $this->m->list_records($attr);
        $sorted_q = array();
        
        //Sort them into stages
        foreach ($q as $id => $row)
        {
            $sorted_q[$row['lead_stage']][$id] = $row;
        }
        $this->q->sorted_by_type = $sorted_q;

        parent::index();
    }
	
}