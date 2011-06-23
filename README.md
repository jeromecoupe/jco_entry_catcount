#Description:
Returns the number of categories for a given entry

* Entry ID has to be supplied
* returns 0 if no categories are found

#Examples:

	{exp:jco_entry_catcount entry_id="3"}

	{if {exp:jco_entry_catcount entry_id='{entry_id}'} == 0}
		No categories assigned to this entry
	{/if}

	{if {exp:jco_entry_catcount entry_id='{entry_id}'} > 1}
		Multiple categories assigned to this entry
	{/if}

#Parameters:

`entry_id="1"` : Mandatory
The id for the entry that you want to output the number of categories for