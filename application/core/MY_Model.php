<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This extends Jamie Rumbelow's base model
 */

class MY_Model extends CI_Model
{

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */

    /**
     * This model's default database table. Automatically
     * guessed by pluralising the model name.
     */
    protected $_table;
    
    /**
     * This model's default columns. Set these in the model. 
     * Note: if you don't set them then queries default to SELECT (*)
     */
    protected $_cols = array('single_record' => FALSE, 'multiple_record' => FALSE);
    
    /**
     * The database connection object. Will be set to the default
     * connection. This allows individual models to use different DBs 
     * without overwriting CI's global $this->db connection.
     */
    public $_database;

    /**
     * This model's default primary key or unique identifier.
     * Used by the get(), update() and delete() functions.
     */
    protected $primary_key = 'id';

    /**
     * Support for soft deletes and this model's 'deleted' key
     */
    protected $soft_delete = TRUE;
    protected $soft_delete_key = 'deleted';
    protected $_temporary_with_deleted = FALSE;
    protected $_temporary_only_deleted = FALSE;

    protected $_return_type = 'as_object';
    /**
     * The various callbacks available to the model. Each are
     * simple lists of method names (methods will be run on $this).
     */
    //protected $before_create = array('created_at', 'updated_at', 'set_client_id');
    protected $before_create = array('created_at', 'updated_at');
    //protected $before_create = array('created_at', 'updated_at', 'set_owner_id');
    protected $after_create = array();
    protected $before_update = array('updated_at');
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callback_parameters = array();

    /**
     * Protected, non-modifiable attributes
     */
    protected $protected_attributes = array('id', 'owner_id');

    //Gets a row count when doing get_all();
    public $row_count = FALSE;

    public $return_object = FALSE;




####### deleteme ############################################################
    /**
     * Relationship arrays. Use flat strings for defaults or string
     * => array to customise the class name and primary key
     */
    // protected $belongs_to = array();
    // protected $has_many = array();

    // protected $_with = array();




    /**
     * An array of validation rules. This needs to be the same format
     * as validation rules passed to the Form_validation library.
     */
    protected $validate = array();

    /**
     * Optionally skip the validation. Used in conjunction with
     * skip_validation() to skip data validation for any future calls.
     */
    protected $skip_validation = FALSE;

    /**
     * By default we return our results as objects. If we need to override
     * this, we can, or, we could use the `as_array()` and `as_object()` scopes.
     */
    protected $return_type = 'object';
    protected $_temporary_return_type = NULL;



####### deleteme ############################################################
    /*protected function _set_owner_id()
    {
        //get owner_id from the PHP session & set it as condition
        $owner_id = $_SESSION['owner_id'] = 11110;  //******************** fix this!!***
        $this->_database->where('owner_id', $owner_id);
    }*/



  /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Initialise the model, tie into the CodeIgniter superobject and
     * try our best to guess the table name.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('inflector');

        $this->_fetch_table();
        $this->_database = $this->db;

        array_unshift($this->before_create, 'protect_attributes');
        array_unshift($this->before_update, 'protect_attributes');

        $this->_temporary_return_type = $this->return_type;

        //Check $_cols is set up properly
        

        


    }

    /*
        Ensure that we only GET the owner's records, plus set the owner_id when inserting
        (Note, the owner_id is protected under $protect_attributes, so this is why we need this) 
     */
    private function _set_owner_id($query_type = 'get', $data = array())
    {
        //Is it a get query?
        if ($query_type === 'insert')
        {
            // $data['owner_id'] = OWNER_ID;
            $data['owner_id'] = $this->owner_id;
            return $data;
        } 
        else $this->_database->where($this->_table . '.owner_id', $this->owner_id);
        // else $this->_database->where($this->_table . '.owner_id', OWNER_ID);
        
    }



    /* --------------------------------------------------------------
     * CRUD INTERFACE
     * ------------------------------------------------------------ */

    /**
     * Fetch a single record based on the primary key. Returns an object.
     */
    public function get($primary_value, $where = FALSE)
    {
        
        $this->trigger('before_get');

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

      
        //Set the columns
        $this->set_select('single');

        //Set the where portion    
        if (is_array($where))
            $this->_database->select($where);

        //Just perform for this client's records: 
        $this->_set_owner_id();

        $row = $this->_database->where($this->_table .'.'.$this->primary_key, $primary_value)
                        ->get($this->_table)
                        ->{$this->_return_type()}();

        $this->_temporary_return_type = $this->return_type;

        $row = $this->trigger('after_get', $row);

        // $this->_with = array();
        return $row;
    }

    /**
     * Fetch a single record based on an arbitrary WHERE call. Can be
     * any valid value to $this->_database->where().
     */
    public function get_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        $this->trigger('before_get');

         //Decide what fields to retrieve (set up in each model)
        // if (is_array($this->_cols['single_record']))
        //     $this->_database->select(array_values($this->_cols['single_record']));

        //Just perform for this client's records: 
        $this->_set_owner_id();

        $row = $this->_database->get($this->_table)
                        ->{$this->_return_type()}();
        $this->_temporary_return_type = $this->return_type;

        $row = $this->trigger('after_get', $row);

        $this->_with = array();
        return $row;
    }

    /**
     * [get_contacts_records description]
     * @param  int $id         This is the id of the currentcontact 
     * @param  array  $sort_array The sort array - by default sorts by id
     * @param  string $col        column name to match with id (defaults to 'contact_id')
     * @return object             Object of results
     */
    public function get_contacts_records($id, $sort_array = array('id' => 'DESC'), $col = 'contact_id')
    {
        return $this->as_array()->order_by($sort_array)->get_many_by($this->_table .'.'.$col, $id);
    }


    /**
     * Fetch an array of records based on an array of primary values.
     */
    public function get_many($values)
    {
        /*if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }*/

        $this->_database->where_in($this->_table .'.'.$this->primary_key, $values);

        return $this->get_all();
    }

    /**
     * Fetch an array of records based on an arbitrary WHERE call.
     */
    public function get_many_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        /*if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }*/

        return $this->get_all();
    }
 /**
     * Fetch an array of records based on an arbitrary LIKE call.
     */
    // public function get_many_like()
    // {
    //     $where = func_get_args();
    //     $this->_set_where($where);

    //     if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
    //     {
    //         $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
    //     }

    //     return $this->get_all();
    // }

    /**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all()
    {
        $this->trigger('before_get');
        
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        //Set the columns
        if ( ! count($this->_database->ar_select)) $this->set_select('multiple');

       
        //Just perform for this client's records: 
        $this->_set_owner_id();
// dump($this->_return_type(1));
// 

        if ($this->return_object !== FALSE)
        {
            $result = $this->_database->get($this->_table);
        }
        else 
        {
            $result = $this->_database->get($this->_table)->{$this->_return_type(1)}();
            $this->_temporary_return_type = $this->return_type;

            foreach ($result as $key => &$row)
            {
                $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
            }
        }
        

        //$this->_with = array();
        return $result;
    }


    /**
     * Performs the passed SQL. NOTE: We can't use any of the $this->database methods that alter the query as its overidden by the $sql, so we need to append them
     */
    public function do_query($sql, $limit = FALSE, $offset = FALSE)
    {        
        //Set up the 'WHERE deleted=' clause...
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $sql .= ' WHERE ' . $this->soft_delete_key . '=' . (int)$this->_temporary_only_deleted;
        }

        //Just perform for this client's records: 
        $this->_set_owner_id();

        //Set up any limits
        if (is_int($limit) && is_int($offset))
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        //$result = $this->_database->query($sql);

        // dump($this->_database->last_query());

        return $this->_database->query($sql);
    }







    /**
     * Insert a new row into the table. $data should be an associative array
     * of data to be inserted. Returns newly created ID.
     */
    public function insert($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $data = $this->trigger('before_create', $data);

            //Just perform for this client's records: 
            $data = $this->_set_owner_id('insert', $data);

            $this->_database->insert($this->_table, $data);
            $insert_id = $this->_database->insert_id();

            $this->trigger('after_create', $insert_id);

            return $insert_id;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Insert multiple rows into the table. Returns an array of multiple IDs.
     */
    public function insert_many($data, $skip_validation = FALSE)
    {
        $ids = array();

        foreach ($data as $key => $row)
        {
            //Just perform for this client's records: 
            $this->_set_owner_id('insert', $data);

            $ids[] = $this->insert($row, $skip_validation, ($key == count($data) - 1));
        }

        return $ids;
    }

    /**
     * Updated a record based on the primary value.
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $result = $this->_database->where($this->primary_key, $primary_value)
                               ->set($data)
                               ->update($this->_table);

            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Update many records, based on an array of primary values.
     */
    public function update_many($primary_values, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $result = $this->_database->where_in($this->primary_key, $primary_values)
                               ->set($data)
                               ->update($this->_table);

            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Updated a record based on an arbitrary WHERE clause.
     */
    public function update_by()
    {
        $args = func_get_args();
        $data = array_pop($args);

        $data = $this->trigger('before_update', $data);

        if ($this->validate($data) !== FALSE)
        {
            $this->_set_where($args);
            $result = $this->_database->set($data)
                               ->update($this->_table);
            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Update all records
     */
    public function update_all($data)
    {
        $data = $this->trigger('before_update', $data);
        $result = $this->_database->set($data)
                           ->update($this->_table);
        $this->trigger('after_update', array($data, $result));

        return $result;
    }

    /**
     * Delete a row from the table by the primary value
     */
    public function delete($id)
    {
        $this->trigger('before_delete', $id);

        $this->_database->where($this->primary_key, $id);

        //Just perform for this client's records: 
        $this->_set_owner_id();

        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete a row from the database table by an arbitrary WHERE clause
     */
    public function delete_by()
    {
        $where = func_get_args();

        $where = $this->trigger('before_delete', $where);

        $this->_set_where($where);

        //Just perform for this client's records: 
        $this->_set_owner_id();


        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete many rows from the database table by multiple primary values
     */
    public function delete_many($primary_values)
    {
        $primary_values = $this->trigger('before_delete', $primary_values);

        $this->_database->where_in($this->primary_key, $primary_values);

        //Just perform for this client's records: 
        $this->_set_owner_id();

        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }


    /**
     * Truncates the table
     */
    /*public function truncate()
    {
        $result = $this->_database->truncate($this->_table);

        return $result;
    }
    */

    /* --------------------------------------------------------------
     * RELATIONSHIPS
     * ------------------------------------------------------------ */

   
    /* --------------------------------------------------------------
     * UTILITY METHODS
     * ------------------------------------------------------------ */

    /**
     * Retrieve and generate a form_dropdown friendly array
     */
    
    /**
     * Fetch a count of rows based on an arbitrary WHERE call.
     */
    public function count_by()
    {
        $where = func_get_args();
        $this->_set_where($where);

        //Just perform for this client's records: 
        $this->_set_owner_id();
        
        //Exclude deleted records
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }

        return $this->_database->count_all_results($this->_table);
    }

    /**
     * Fetch a total count of rows, disregarding any previous conditions
     */
    public function count_all()
    {
        //Just perform for this client's records: 
        $this->_set_owner_id();

        //Exclude deleted records
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->_table . '.' . $this->soft_delete_key, (bool)$this->_temporary_only_deleted);
        }
        
        return $this->_database->count_all($this->_table);
    }

    /**
     * Fetch a total count of rows for the last query only
     */
    public function count_all_owner_records($table  = 'contacts', $where = array())
    {
        //Set up the WHERE statements
        $this->_set_owner_id();

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where($this->soft_delete_key, FALSE);
        }
        
        if (count($where)) $this->_database->where($where);
        $this->_database->from($table);

        //return the number of records counted
        return $this->_database->count_all_results();
    }

    /**
     * Tell the class to skip the insert validation
     */
    public function skip_validation()
    {
        $this->skip_validation = TRUE;
        return $this;
    }

    /**
     * Get the skip validation status
     */
    public function get_skip_validation()
    {
        return $this->skip_validation;
    }

    /**
     * Return the next auto increment of the table. Only tested on MySQL.
     */
    public function get_next_id()
    {
        return (int) $this->_database->select('AUTO_INCREMENT')
            ->from('information_schema.TABLES')
            ->where('TABLE_NAME', $this->_table)
            ->where('TABLE_SCHEMA', $this->_database->database)->get()->row()->AUTO_INCREMENT;
    }

    /**
     * Getter for the table name
     */
    public function table()
    {
        return $this->_table;
    }

    /* --------------------------------------------------------------
     * GLOBAL SCOPES
     * ------------------------------------------------------------ */

    /**
     * Return the next call as an array rather than an object
     */
    public function as_array()
    {
        $this->_temporary_return_type = 'array';
        return $this;
    }

    /**
     * Return the next call as an object rather than an array
     */
    public function as_object()
    {
        $this->_temporary_return_type = 'object';
        return $this;
    }

    /**
     * Don't care about soft deleted rows on the next call
     */
    public function with_deleted()
    {
        $this->_temporary_with_deleted = TRUE;
        return $this;
    }
    
    /**
     * Only get deleted rows on the next call
     */
    public function only_deleted()
    {
        $this->_temporary_only_deleted = TRUE;
        return $this;
    }

    /* --------------------------------------------------------------
     * OBSERVERS
     * ------------------------------------------------------------ */

    /**
     * MySQL DATETIME created_at and updated_at
     */
    public function created_at($row)
    {
        if (is_object($row))
        {
            $row->created_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['created_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    public function updated_at($row)
    {
        if (is_object($row))
        {
            $row->updated_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['updated_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Serialises data for you automatically, allowing you to pass
     * through objects and let it handle the serialisation in the background
     */
    public function serialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            $row[$column] = serialize($row[$column]);
        }

        return $row;
    }

    public function unserialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            if (is_array($row))
            {
                $row[$column] = unserialize($row[$column]);
            }
            else
            {
                $row->$column = unserialize($row->$column);
            }
        }

        return $row;
    }

    /**
     * Protect attributes by removing them from $row array
     */
    public function protect_attributes($row)
    {
        foreach ($this->protected_attributes as $attr)
        {
            if (is_object($row))
            {
                unset($row->$attr);
            }
            else
            {
                unset($row[$attr]);
            }
        }

        return $row;
    }

    /* --------------------------------------------------------------
     * QUERY BUILDER DIRECT ACCESS METHODS
     * ------------------------------------------------------------ */

    /**
     * A wrapper to $this->_database->order_by()
     */
    public function order_by($criteria = FALSE, $order = 'ASC')
    {
        //If not criteria passed then just use the default defined in the model
        if ( ! $criteria ) $criteria = $this->_sort;

        if ( is_array($criteria) )
        {
            foreach ($criteria as $key => $value)
            {
                $this->_database->order_by($key, $value);
            }
        }
        else
        {
            $this->_database->order_by($criteria, $order);
        }
        return $this;
    }

    /**
     * A wrapper to $this->_database->limit()
     */
    public function limit($limit, $offset = 0)
    {
        $this->_database->limit($limit, $offset);
        echo "<p>Limit is $limit and offset = $offset </p>";
        return $this;
    }

    /* --------------------------------------------------------------
     * INTERNAL METHODS
     * ------------------------------------------------------------ */

    /**
     * Trigger an event and call its observers. Pass through the event name
     * (which looks for an instance variable $this->event_name), an array of
     * parameters to pass through and an optional 'last in interation' boolean
     */
    public function trigger($event, $data = FALSE, $last = TRUE)
    {
        if (isset($this->$event) && is_array($this->$event))
        {
            foreach ($this->$event as $method)
            {
                if (strpos($method, '('))
                {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                    $method = $matches[1];
                    $this->callback_parameters = explode(',', $matches[3]);
                }

                $data = call_user_func_array(array($this, $method), array($data, $last));
            }
        }

        return $data;
    }

    /**
     * Run validation on the passed data
     */
    public function validate($data)
    {
        if($this->skip_validation)
        {
            return $data;
        }
        
        if(!empty($this->validate))
        {
            foreach($data as $key => $val)
            {
                $_POST[$key] = $val;
            }

            $this->load->library('form_validation');

            if(is_array($this->validate))
            {
                $this->form_validation->set_rules($this->validate);

                if ($this->form_validation->run() === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                if ($this->form_validation->run($this->validate) === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
        }
        else
        {
            return $data;
        }
    }

    /**
     * Guess the table name by pluralising the model name
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL)
        {
            $this->_table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
        }
    }

    

    /**
     * Set LIKE parameters, cleverly
     */
    public function set_like($params, $or = FALSE)
    {
        // if (is_array($params) && ! $or)
        // {
        //     $this->_database->or_like($params);
        // }
        // elseif (is_array($params))
        // {
        //     $this->_database->or_like($params);
        // }
        if (is_array($params))
        {
            $this->_database->or_like($params);
        }


    }

    /**
     * Return the method name for the current return type
     */
    protected function _return_type($multi = FALSE)
    {
        $method = ($multi) ? 'result' : 'row';
        return $this->_temporary_return_type == 'array' ? $method . '_array' : $method;
    }

    /**
     * Returns an ajax output from the current model ready for Datatables to consume
     */
    public function get_datatables_ajax($cols, $where = array(), $join = array())
    {
        if ( !isset($where['deleted'])) $where['deleted'] = 0;
        if ( !isset($where['owner_id'])) $where['owner_id'] = $this->owner_id;
        // if ( !isset($where['owner_id'])) $where['owner_id'] = OWNER_ID;

        $this->load->library('datatables');

        //if $join has been passed...
        if ($join)
        {
            $this->datatables->join($join['join_table'], $join['join_fk'] = $join['join_pk']);
        }
        
        //Set the rest of the query
        $this->datatables->select($cols)
        ->from($this->_table)
       // ->order_by('id')
        ->where($where);

        return $this->datatables->generate();
    }

    /**
     * Sets the columns to return (used mainly in the Ajax class)
     * @param string $type either multiple_record or single_record (this determines the fields to get in either get_all(), or get($id) respectively)    
     * @param array $cols a comma separated list of columns to return
     */
    public function set_select($type = 'single', $cols = FALSE)
    {
    // Allow us to overwrite the default cols

        if ( ! $cols ) $cols = $this->_cols;

        switch ($type) {
            case 'ajax':
            $cols = array_values($cols);
            break;
            
            case 'join':
            $cols = array_values($cols);
            break;
            
            case 'single':
            case 'multiple':
            if (count($cols[$type . '_record']) >= 1)
            {
                $cols = array_values($cols[$type . '_record']);
                break;
            }

            default:
            $cols = $this->_database->list_fields($this->_table);
            break;
        }

       //loop through and prepend with this tablename (ignore those already prepended)
       foreach ($cols as $k => $col)
       {
            if ( ! strpos($col, '.'))
            {
                $cols[$k] = $this->_table . '.' . $col;
            }
       }

       $this->_database->select($cols, FALSE);

       // if ($type == 'ajax')
       // {
       //       $this->_database->select(array_values($cols));
       //       
       // }

       // //If no columns are set, then we're getting them all
       // if ( ! count($cols[$type]))
       // {
       //      $cols[$type] = $this->_database->list_fields($this->_table);
       // }

       // //Now add in the table name
       // $proper_cols = array();
       // foreach ( $cols[$type] as $k => $col )
       // {
       //      $proper_cols[] = $this->_table . '.' . $col;
       // }

       // //Do we have any join cols?
       // if ( isset($this->_cols['join_fields']) && isset($this->_join['join_table']) )
       // {
       //      foreach ($this->_cols['join_fields'] as $k => $col)
       //      {
       //          $proper_cols[] = $col;
       //      }
       // }

       // $this->_database->select(array_values($proper_cols));
   }
   
   protected function _join($table, $join_statement, $join_type = NULL)
   {
       $this->_database->join($table, $join_statement, $join_type);
   }



    /**
     * Wrapper for the group_by() active record method
     * @param  mixed $params Can be an array of col names or a single col 
     * @return none         
     */
    // public function group_by($params)
    // {
        
    //     return $this->_database->group_by($params);
    // }





    public function join_by()
    {       

        //Have we set a join table?
        if (isset($this->_join['join_table']))
        {
            $join_table = $this->_join['join_table'];

            //Set up the default foreign key (overide in model)
            if ( ! isset($this->_foreign_key['join_table']) )
            {
                $foreign_key = singular($join_table) . '_id';
            }
            else $foreign_key = $this->_foreign_key['join_table'];

            //Set up join type
            if ( ! isset($this->_join['join_type']) )
            {
                $join_type = 'LEFT';
            }
            else $join_type = $this->_join['join_type'];

            //add in extra cols to select
            //$this->_cols['join_fields']

            //Construct the join
            $j = $join_table . '.id';
            $j .= '=' . $this->_table . '.' . $foreign_key;
            $this->_database->join($join_table, $j, $join_type);
        }
        
        return $this;
    }















################## New imporved MY_MODEL!!!

// Vars to set up:
// $_protected sort 




    public function list_records($attr = array())
    {        
        //Set up the where and join statements
        if (count($attr))
        {
            //Set the where statement(s)
            if (isset($attr['where']))
            {
                foreach ($attr['where'] as $w)
                {
                    foreach ($w as $k => $v)
                    {
                        //Replace placeholders with actual values form the query
                        if (strpos($v, '%') !== FALSE)
                        {
                            $col = str_replace('%', '', $v);
                            $w[$k] = $this->q->$col;
                            
                            // $w[$k] = $this->q->{$this->main_model}->$col;
                        }
                    }
                    $this->_set_where(array($w));
                }
            }

            //Set the where statement(s)
            if (isset($attr['join']))
            {
                foreach ($attr['join'] as $j)
                {
                    $this->_set_join($j);
                }
            }

            //Set any other query settings, e.g. LIMIT etc 
            ////May need to have some verification here that $k is a valid command?
            if (isset($attr['other']))
            {
                foreach ($attr['other'] as $k => $v)
                {
                    $this->_database->{$k}($v);
                }
            }
        }

        //Set the select statement
        $this->_set_select('multiple');

        //Set the order_by statement
        //order_by()->
        
        $q = $this->as_array()->get_all();
        if (isset($attr['id_as_key']) && $attr['id_as_key'] !== FALSE)
        {
            $t = array(); //temp array
            foreach ($q as $k => $array)
            {
                $t[$array['id']] = $array;
            }
            $q = $t;
        }
        
        return $q;
        
    }

     /**
     * Updated a record based on the primary value.
     */
    public function update_record($primary_value, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $result = $this->_database->where($this->primary_key, $primary_value)
                               ->set($data)
                               ->update($this->_table);

            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Insert a new row into the table. $data should be an associative array
     * of data to be inserted. Returns newly created ID.
     */
    public function insert_record($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {
            $data = $this->trigger('before_create', $data);

            //Just perform for this client's records: 
            $data = $this->_set_owner_id('insert', $data);

            $this->_database->insert($this->_table, $data);
            $insert_id = $this->_database->insert_id();

            $this->trigger('after_create', $insert_id);

            return $insert_id;
        }
        else
        {
            return FALSE;
        }
    }


    /**
     * Delete a row from the table by the primary value
     */
    public function delete_record($id)
    {
        $this->trigger('before_delete', $id);

        $this->_database->where($this->primary_key, $id);

        //Just perform for this client's records: 
        $this->_set_owner_id();

        $result = $this->_database->update($this->_table, array('deleted' => 1));

        $this->trigger('after_delete', $result);

        return $result;
    }

    protected function toggle_value($col)
    {
        $q = $this->get($id);

// dump('The existing value is '. $q->$col);

        //Set default and get current value
      $new_value = 1;
      if ($q->$col == 1)
      {
        $new_value = 0;
      }

        //insert new_value
      $this->update($id, array($col => $new_value));
      $q->$col = $new_value;
// dump('The changed value is '.$new_value);
      return $q;
    }


    //do saved search
    public function do_saved_search($attr = array(), $result_type = 'array')
    {
        //If $attr is an object, turn it into an array
        if (is_object($attr)) $attr = (array) $attr;
        
        //Is attr empty?
        if ( is_array($attr) && ! count($attr)) return FALSE;

        //Set up the query
        $q;
        $query_components = array('select', 'join', 'where', 'like', 'order_by', 'group_by', 'having');
        
        foreach ($query_components as $comp)
        {
            if (isset($attr[$comp]) && ! empty($attr[$comp]))
            {
                $this->_database->{$comp}($attr[$comp]);
            }
        }

        if ($result_type == 'count') return count($this->get_all());//Is this a straight count?
        if ($result_type == 'export')
        {
            $this->return_object = TRUE;
            return $this->get_all();  
        } 

        //Have we passed a limit/offset?
        if (isset($attr['limit']))
        {
            $offset = '';
            if ( isset($attr['offset'])) $offset = $attr['offset'];
            $this->_database->limit($attr['limit'], $offset);
        }

        //Do query
        $q = $this->as_array()->get_all();
        
        //Make the keyfo the results array the id of the record
       if (isset($attr['id_as_key']) && $attr['id_as_key'] !== FALSE)
        {
            $t = array(); //temp array
            foreach ($q as $k => $array)
            {
                $t[$array['id']] = $array;
            }
            $q = $t;
        }
        
        return $q;
    }










    protected function _set_join($join_array)
   {
       if (
        isset($join_array['table']) 
        && isset($join_array['join_on']) 
        && isset($join_array['join_type'])
        ) $this->_database->join($join_array['table'], $join_array['join_on'], $join_array['join_type']);

        if (count($join_array['join_fields'])) $this->set_select('join', $join_array['join_fields']);
   }

   /**
     * Set WHERE parameters
     */
    protected function _set_where($params)
    {
        if (count($params) == 1)
        {
            $this->_database->where($params[0]);
        }
        else if(count($params) == 2)
        {
            $this->_database->where($params[0], $params[1]);
        }
        else if(count($params) == 3)
        {
            $this->_database->where($params[0], $params[1], $params[2]);
        }
        else
        {
            $this->_database->where($params);
        }
    }

    /**
     * Sets the columns to return (used mainly in the Ajax class)
     * @param string $type either multiple_record or single_record (this determines the fields to get in either get_all(), or get($id) respectively)    
     * @param array $cols a comma separated list of columns to return
     */
    protected function _set_select($type = 'single', $cols = FALSE)
    {
    // Allow us to overwrite the default cols

        if ( ! $cols ) $cols = $this->_cols;

        switch ($type) {
            case 'ajax':
            $cols = array_values($cols);
            break;

            case 'join':
            $cols = array_values($cols);
            break;
           
            case 'single':
            case 'multiple':
            if (count($cols[$type . '_record']) >= 1)
            {
                $cols = array_values($cols[$type . '_record']);
                break;
            }

            default:
            $cols = $this->_database->list_fields($this->_table);
            break;
        }

       //loop through and prepend with this tablename (ignore those already with a period on them)
       foreach ($cols as $k => $col)
       {
            if ( ! strpos($col, '.'))
            {
                $cols[$k] = $this->_table . '.' . $col;
            }
       }

       $this->_database->select($cols, FALSE);
    }







}