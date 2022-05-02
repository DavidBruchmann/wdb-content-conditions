# TYPO3 extension "WDB Content conditions"

This extension adds a TypoScript condition to check if content elements
with special values on a site exist.

**Important** to understand is that no content elements are returned, but
that the new condition is doing a boolean check.  
That means that above any condition the content elements have the same amount
and properties like below, **content elements are not filtered** by the condition.  

## Use case for the additional condition:
Checking for values in content elements allows to include special CSS, JS or
to configure other page- or config-related values only if it's required.  
Certainly it's also possible to change definitions for content elements
based on properties of one unreleated content element. This could be menus
but also other content elements.

@TODO: whats about operators like &&, xor, etc.?  
@TODO: whats about FlexForm values?
@TODO: test null-values
@TODO: update examples below, `tt_content(field, value, type)` example: `[ tt_content("tx_webcan_st_bt_element", 5, "int") ]`

## Examples:  

**`[tt_content("colPos") == 1]`**  
Checks if the `colPos` of a `tt_content` record on the page has the value 1.  

**`[tt_content("uid") in [12,13,14]]`**  
Checks if the `uid` of a `tt_content` record on the page has one of the values 12, 13 or 14.  

**`[tt_content("tx_myextension_field")]`**  
Checks if the field `tx_myextension_field` of a `tt_content` record on the page has any value set.

**`[tt_content("title") == "Home"]`**  
Checks if the field `title` of a `tt_content` record on the page has the value "Home".

**`[tt_content("title") == "Home" || tt_content("title") == "Homa"]`**  
Checks if the field `title` of a `tt_content` record on the page has the value "Home" or "Homa".

**`[tt_content("title") in ["Home", "Homa"]]`**  
This is the same test like above, just with different notation.
