<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * JCO Entry Category Count
 *
 * @version 1.2
 * @author Jerome Coupe: port of an EE1 add-on by Erik Reagan
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

$plugin_info = array(
  'pi_name' => 'JCO Entry Category Count',
  'pi_version' =>'1.2',
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
	public $return_data = "";

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

		// get value for parameters and set default
		$entry_id 			= $this->EE->TMPL->fetch_param('entry_id',FALSE);
		$category_group_id 	= $this->EE->TMPL->fetch_param('category_group_id',FALSE);
		$site 				= $this->EE->config->item('site_id');

		//set category_group control variables to false by default
		$catgroup_isnumeric = FALSE;
		$catgroup_exists 	= FALSE;

		// check if valid category_group_id passed
		if ($category_group_id !== FALSE)
		{
			//is there a NOT clause ?
			if (strpos($category_group_id, "not") === 0)
			{
				$catgroup_notclause = TRUE;
				$category_group_id = substr($category_group_id, 4);
				$category_group_id = explode('|', $category_group_id);
			}
			else
			{
				$catgroup_notclause = FALSE;
				$category_group_id = explode('|', $category_group_id);
			}

			// check if each element of the array is a number
			foreach ($category_group_id as $value)
			{
				if (is_numeric($value))
				{
					$catgroup_isnumeric = TRUE;
				}
				else
				{
					$this->EE->TMPL->log_item(str_repeat("&nbsp;", 5) . "- ERROR (jco_entry_catcount): invalid category_group_id supplied: category group ids must be numeric");
					return FALSE;
				}
			}

			//check if each element of the array matches a category in the database
			if ($this->_category_group_exists($category_group_id, $site) === TRUE)
			{
				$catgroup_exists = TRUE;
			}
			else
			{
				$this->EE->TMPL->log_item(str_repeat("&nbsp;", 5) . "- ERROR (jco_entry_catcount): invalid category_group_id supplied: category group ids not found in database for the current site");
				return FALSE;
			}
		}

		// check if entry_id passed
		if ($entry_id !== FALSE)
		{
			// check if entry_id is valid
			if (is_numeric($entry_id) && $this->_entry_exists($entry_id))
			{
				// Build Query checking how many categories are associated with the entry
				// (uses category group as well if exists, defaults to current site)
				$this->EE->db->select('category_posts.entry_id')
							 ->from('category_posts')
					    	 ->join('categories', 'categories.cat_id = category_posts.cat_id')
							 ->where('categories.site_id', $site)
							 ->where('category_posts.entry_id', $entry_id);

				// add part of query to limit category group if needed
				if ($catgroup_isnumeric === TRUE && $catgroup_exists == TRUE)
				{
					if ($catgroup_notclause === TRUE)
					{
						$this->EE->db->where_not_in('categories.group_id', $category_group_id);
					}
					else
					{
						$this->EE->db->where_in('categories.group_id', $category_group_id);
					}
				}

				// get results from query
				$results = $this->EE->db->count_all_results();

				// put results in array with just one row and assign the number of categories to nbrcategories variable
				$variables[0] = array(
					'nbrcategories' 	=>	(int) $results
				);

				// parse result array
				return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
			}
			else
			{
				$this->EE->TMPL->log_item(str_repeat("&nbsp;", 5) . "- ERROR (jco_entry_catcount): invalid entry_id");
				return FALSE;
			}
		}
		else
		{
			$this->EE->TMPL->log_item(str_repeat("&nbsp;", 5) . "- ERROR (jco_entry_catcount): entry_id parameter must be supplied");
			return FALSE;
		}
	}

	/**
	* Check if entry_id exists in DB
	*
	* @access	private
	* @return	boolean
	*/
	private function _entry_exists($entry_id)
	{

		//query
		$this->EE->db->select('entry_id')
					 ->from('channel_titles')
					 ->where('entry_id', $entry_id);

		//if 1 result found set to TRUE, otherwise send to FALSE
		$results = ($this->EE->db->count_all_results() == 1) ? TRUE : FALSE;

		//return TRUE or FALSE
		return $results;

	}

	/**
	* Check if category_group exists in DB
	*
	* @access	private
	* @return	boolean
	*/
	private function _category_group_exists($category_group_array, $site)
	{

		//query
		$this->EE->db->select('group_id')
					 ->from('category_groups')
					 ->where('site_id', $site)
					 ->where_in('group_id', $category_group_array);

		//set result to TRUE if nbr of results found match the length of passed arrays
		$results_from_db 		= $this->EE->db->count_all_results();
		$catgroup_array_items 	= count($category_group_array);

		$results = ($results_from_db == $catgroup_array_items) ? TRUE : FALSE;

		return $results;

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
			------------------------------------------------------

			Returns the number of categories for a given entry

			* Entry ID has to be supplied
			* returns 0 if no categories are found


			Examples:
			------------------------------------------------------

			Simple:
			{exp:jco_entry_catcount entry_id="3"}{nbrcategories}{/exp:jco_entry_catcount}
			{exp:jco_entry_catcount entry_id="{entry_id}"}{nbrcategories}{/exp:jco_entry_catcount}

			Limited to category group(s):
			{exp:jco_entry_catcount entry_id="3" category_group_id="1"}{nbrcategories}{/exp:jco_entry_catcount}
			{exp:jco_entry_catcount entry_id="3" category_group_id="1|2"}{nbrcategories}{/exp:jco_entry_catcount}
			{exp:jco_entry_catcount entry_id="3" category_group_id="not 2"}{nbrcategories}{/exp:jco_entry_catcount}

			Using Conditionals:
			{exp:jco_entry_catcount entry_id="{entry_id}"}
				{if nbrcategories == 0}
					No categories assigned to this entry
				{/if}
			{/exp:jco_entry_catcount}

			{exp:jco_entry_catcount entry_id="{entry_id}"}
				{if nbrcategories > 0}
					Multiple categories assigned to this entry
				{/if}
			{/exp:jco_entry_catcount}


			Parameters:
			------------------------------------------------------

			entry_id="1" : Mandatory
			The id for the entry that you want to output the number of categories for

			category_group_id="1": Optional
			The id of the category group you want to limit the query to
			You can use piped list: category_group_id="1|2"
			You can use not clause: category_group_id="not 2"

		<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	  // END

}


/* End of file pi.jco_entry_catcount.php */
/* Location: ./system/expressionengine/third_party/plugin_name/pi.jco_entry_catcount.php */