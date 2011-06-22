<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'JCO Entry Category Count',
  'pi_version' =>'1.0',
  'pi_author' =>'Jerome Coupe: port of an EE1 add-on by Erik Reagan',
  'pi_author_url' => 'http://twitter.com/jeromecoupe/',
  'pi_description' => 'Returns the number of categories for a given entry.',
  'pi_usage' => Jco_entry_catcount::usage()
  );


class Jco_entry_catcount {
	
	/* --------------------------------------------------------------
	* RETURNED DATA
	* ------------------------------------------------------------ */
	/**
	* Data returned from the plugin.
	*
	* @access	public
	* @var string
	*/
	var $return_data = '';
	
	/* --------------------------------------------------------------
	* CONSTRUCTOR
	* ------------------------------------------------------------ */

	/**
	* Constructor.
	*
	* @access	public
	* @return	void
	*/
	function __construct()
	{
		$this->EE =& get_instance();
		$this->return_data = $this->Count_entry_categories();
	}
	
	
	/**
	* Compatibility for prior to EE 2.1.3
	*
	* @access public
	* @return void
	* method first seen used by Stephen Lewis (https://github.com/experience/you_are_here.ee2_addon)
	*/
	public function Jco_entry_catcount()
	{
		$this->__construct();
	}
	
	/* --------------------------------------------------------------
	* USED FUNCTIONS
	* ------------------------------------------------------------ */
	
	/**
	* Return number of items in category.
	*
	* @access	public
	* @return	mixed: integer, boolean
	*/
	public function Count_entry_categories()
	{
		//Get parameters and set defaults if parameter not provided
		$entry_id = $this->EE->TMPL->fetch_param('entry_id', '');
		
		//check entry id
		if ($entry_id == "")
		{
			return "ERROR: entry_id parameter MUST BE supplied";
		}
		else
		{
			//check that entry id exists in the DB
			$this->EE->db->select('exp_channel_titles.entry_id');
			$this->EE->db->from('exp_channel_titles');
			$this->EE->db->where('exp_channel_titles.entry_id', $entry_id);
			$entry_exists = $this->EE->db->count_all_results();
			if ($entry_exists == 0)
			{
				return "Supplied entry id was not found in the database";
			}
		}
		
		//Query
		//main part
		$this->EE->db->select('exp_category_posts.entry_id');
		$this->EE->db->from('exp_category_posts');
		$this->EE->db->where('exp_category_posts.entry_id', $entry_id);
		
		//count results found and return number
		return $this->EE->db->count_all_results();
	}
	
	/* --------------------------------------------------------------
	* PLUGIN USAGE
	* ------------------------------------------------------------ */

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	public function usage()
	{
		ob_start(); 
		?>

			Description:
	
			Returns the number of categories for a given entry
			
			* Entry ID has to be supplied
			* returns 0 if no categories are found
	
			------------------------------------------------------
			
			Examples:
			{exp:jco_entry_catcount entry_id="3"}
			{exp:jco_entry_catcount entry_id="{entry_id}"}
	
			Returns
			3
	
			------------------------------------------------------
			
			Parameters:
	
			entry_id="1" : Mandatory
			The id for the entry that you want to output the number of categories for
		
		<?php
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}
	  // END

	}


/* End of file pi.jco_entry_catcount.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.jco_entry_catcount.php */