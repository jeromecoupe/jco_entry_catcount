#Description

Returns the number of categories for a given entry
* Entry ID has to be supplied
* returns 0 if no categories are found

#Examples

##Simple

	{exp:jco_entry_catcount entry_id="3"}{nbrcategories}{/exp:jco_entry_catcount}
	{exp:jco_entry_catcount entry_id="{entry_id}"}{nbrcategories}{/exp:jco_entry_catcount}

##Limited to category group(s):

	{exp:jco_entry_catcount entry_id="3" category_group_id="1"}{nbrcategories}{/exp:jco_entry_catcount}
	{exp:jco_entry_catcount entry_id="3" category_group_id="1|2"}{nbrcategories}{/exp:jco_entry_catcount}
	{exp:jco_entry_catcount entry_id="3" category_group_id="not 2"}{nbrcategories}{/exp:jco_entry_catcount}

##Using Conditionals

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

#Parameters

*entry_id="1" : Mandatory*

The id for the entry that you want to output the number of categories for

*category_group_id="1": Optional*

The id of the category group you want to limit the query to
* You can use piped list: category_group_id="1|2"
* You can use not clause: category_group_id="not 2"