#Description:
Returns the number of categories for a given entry

* Entry ID has to be supplied
* returns 0 if no categories are found

#Examples:
	{exp:jco_entry_catcount entry_id="3"}
	{exp:jco_entry_catcount entry_id="{entry_id}"}

#Parameters:

`entry_id="1"` : Mandatory
The id for the entry that you want to output the number of categories for