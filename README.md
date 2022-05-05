# TYPO3 extension "WDB Content conditions"

This extension adds a TypoScript condition to check if content elements
with special values on a site exist.

**Important** to understand is that no content elements are returned, but
that the new condition is doing a boolean check.  
That means that above any condition the content elements have the same amount
and properties like below, **content elements are not filtered** by the condition.  
**Note that all queries in this extension exclude deleted or hidden records without exception.**  

## Use case for the additional condition:
Checking for values in content elements allows to include special CSS, JS or
to configure other page- or config-related values only if it's required.  
**Example:**  
```
[tt_content("list_type", "tx_myextension_pi1", "str")]
    page.includeCSS.mySlideshow = EXT:my_extension/Resources/Public/Css/mySlideshow.css
    page.includeJSFooter.mySlideshow = EXT:my_extension/Resources/Public/JavaScript/mySlideshow.js
[global]
```
**Explanation:**
- **`tt_content()`** is the name of the condition and according to the database-table with
  the same name. For beginners it might be confusing that there still exists a variable
  in TypoScript with the same name too. Nevertheless, the usage of `tt_content()` as condition
  is the only one with round brackets, because it's a function.
- The first parameter **`"list_type"`** is the name of the field that shall be checked.
  Naming conventions for this field name are defined by the database and must not include
  spaces, minus, brackets or many other characters.  
- The second parameter **"tx_myextension_pi1"** is the value that is supposed to be found
  as value for a content element in the field **`"list_type"`**.  
  Naming conventions for this value are defined by TYPO3 and must not include
  spaces, brackets or many other characters.  
- The third parameter is the data type of the value and used to assign a constant
  for DBAL (the framework to access the database). It can be `"int"`, `"str"`, `"bool"` or more.
  Important is that this should be `"int"` for integer values, everything else is not so
  important and can be `"str"`.  
  For some fields with boolean values like `hidden` as example, the value `"int"` for this
  parameter can be used too, as TYPO3 stores boolean values usually as integers (0 or 1).  


### Special use case: manipulating content rendering
Certainly it's also possible to change definitions for rendering of content elements
based on properties of one (perhaps even unreleated) content element. This could be
menus but also other content elements. Nevertheless, as neither the content elements
nor the rendering definitions of a page are filtered, those changes would
relate to all content elements on the page.  
**Example:**  
The condition `[tt_content("header_layout", 3, "int")]` checks in all content elements
on a page if the value 3 is saved in the field `header_layout`. Imagine there is one
record, that has this value and this record's headline shall be wrappedd additionally
to the h3-tag with the em-tag. But the code `tt_content.header.stdWrap.wrap = '<em>|<em>'`
would wrap the headlines of all content elements, no matter which value they have assigned.


@TODO: whats about operators like &&, xor, etc.?  
@TODO: whats about FlexForm values?
@TODO: test null-values
@TODO: update examples below, `tt_content(field, value, type)` example: `[ tt_content("tx_webcan_st_bt_element", 5, "int") ]`

## Examples:  

**`[tt_content("colPos", 1, "int")]`**  
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
