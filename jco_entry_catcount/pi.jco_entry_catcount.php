<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * JCO Entry Category Count
 * 
 * @version 1.0
 * @author Jerome Coupe: port of an EE1 add-on by Erik Reagan
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported 
 */

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
	public $return_data = '';
	
	/* --------------------------------------------------------------
	* CONSTRUCTOR
	* ------------------------------------------------------------ */

	/**
	* Constructor.
	*
	* @access	public
	* @return	void
	*/
	public function __construct()
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
			$this->EE->TMPL->log_item(str_repeat('&nbsp;', 5) . '- ERROR: entry_id parameter MUST BE supplied');
			return FALSE;
		}
		else
		{
			//check that entry id exists in the DB
			$this->EE->db->select('exp_channel_titles.entry_id')
			             ->from('exp_channel_titles')
			             ->where('exp_channel_titles.entry_id', $entry_id);
			if ($this->EE->db->count_all_results() == 0)
			{
				$this->EE->TMPL->log_item(str_repeat('&nbsp;', 5) . '- ERROR: Supplied entry id was not found in the database');
				return FALSE;
			}
		}
		
		//Query
		//main part
		$this->EE->db->select('exp_category_posts.entry_id')
		             ->from('exp_category_posts')
		             ->where('exp_category_posts.entry_id', $entry_id);
		
		//count results found and return number
		$nbrresults = $this->EE->db->count_all_results();
		$this->EE->TMPL->log_item(str_repeat('&nbsp;', 5) . '- JCO ENTRY CATCOUNT RESULT: '.$nbrresults);

		return (int) $nbrresults;
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

			{if {exp:jco_entry_catcount entry_id='{entry_id}'} == 0}
				No categories assigned to this entry
			{/if}

			{if {exp:jco_entry_catcount entry_id='{entry_id}'} > 1}
				Multiple categories assigned to this entry
			{/if}

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