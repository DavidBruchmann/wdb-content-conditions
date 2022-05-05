# TYPO3 extension "WDB Content conditions"

This extension adds a TypoScript condition to check if content elements
with special values on a site exist.
Included are two conditions, **`ttContent()`** and **`tt_content[]`**:  

 1. A functional condition  
    This is verifying all content elements on a page  
    The syntax is:  
    
    **`ttContent([fieldname], [expected value], [type of value])`**  
    
    **return value: bool / array**  
    
    **Parameters:**
    
    - **`fieldname`**: the field to verify.
    - **`expected value`**: the value that shall be checked, optional.
    - **`type of value`**: the datatype of the value, optional.
    
    Due to the return value, and the limit of available operators, it's NOT
    useful to combine it with further comparisons like `a > b`. Nevertheless
    several conditions can be used together concatenated with `||` (OR)
    or `&&` (AND).  
    
    **Important** to understand is that no content elements are returned, but
    that the new condition is doing most often a boolean check.  
    That means that above any condition the content elements have the same amount
    and properties like below, **content elements are not filtered** by the condition.  

    **Note that all queries exclude deleted or hidden records without exception.**  
    
 2. An array condition to a verify single content element
    This can be used to check properties of single elements and even combine several
    to check several properties of single elements.
    The syntax is:  
    
    **`tt_content[uid][fieldname]`**  

    **return value: string / integer**  
    Return values are just what is saved in the database, this doesn't respect any
    relations to files or other records.
    
    **Array Keys:**
    
    - **`uid`**: the unique id for the `tt_content` record to verify.
    - **`fieldname `**: the field to verify.
    


## Use case for the additional condition:
Checking for values in content elements allows to include special CSS, JS or
to configure other page- or config-related values only if it's required.  
**Example:**  
```
[ttContent("list_type", "tx_myextension_pi1", "str")]
    page.includeCSS.mySlideshow = EXT:my_extension/Resources/Public/Css/mySlideshow.css
    page.includeJSFooter.mySlideshow = EXT:my_extension/Resources/Public/JavaScript/mySlideshow.js
[global]
```
**Explanation:**
- **`ttContent()`** is the name of the condition and according to the database-table with
  the same name. For beginners it might be confusing that there still exists a variable
  in TypoScript with the same name too. Nevertheless, the usage of `ttContent()` as condition
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
The condition `[ttContent("header_layout", 3, "int")]` checks in all content elements
on a page if the value 3 is saved in the field `header_layout`. Imagine there is one
record, that has this value and this record's headline shall be wrappedd additionally
to the h3-tag with the em-tag. But the code `tt_content.header.stdWrap.wrap = '<em>|<em>'`
would wrap the headlines of all content elements, no matter which value they have assigned.


@TODO: whats about operators like &&, xor, etc.?  
@TODO: whats about FlexForm values?  
@TODO: test null-values  
@TODO: update examples below, `ttContent(field, value, type)` example: `[ ttContent("tx_webcan_st_bt_element", 5, "int") ]`  

## Examples for the functional condition:  

**`[ttContent("colPos", 1, "int")]`**  
Checks if the field `colPos` of a `tt_content` record on the page has the value 1.  

**`[ttContent("subheader")]`**  
Checks if the field `subheader` of any `tt_content` record on the page is filled.  

**`[ttContent("header", "Home", "str")]`**  
Checks if the field `header` of a `tt_content` record on the page has the value "Home".

**`[ttContent("header", "Home", "str") || ttContent("header", "Homa", "str")]`**  
Checks if the field `header` of a `tt_content` record on the page has the value "Home" or "Homa".

**`[ttContent("header", "Home", "str") && ttContent("header", "Homa", "str")]`**  
Checks if the field `header` of a `tt_content` record on the page has the value "Home" and another 
`tt_content` record the value "Homa".  
This example might explain that the data are coming from a pool of records and that a conclusion if these 
are coming from the same record is usually not possible. If the requested fields are the same, combined
by an AND (&&), surely they can't come from the same record. So it's impossible with this condition,
to check if two different properties (field values) belong to the same record.

## Examples for the condition in array form:  

**`[tt_content[15]["colPos"] == 1]`**  
Checks if the field `colPos` of the `tt_content` record with the `uid` 15 on the page has the value 1. 

**`[tt_content[15]["subheader"]]`**  
Checks if the field `subheader` of the `tt_content` record with the `uid` 15 on the page is filled.  

**`[tt_content[15]["header"] == "Home"]`**  
Checks if the field `header` of the `tt_content` record with the `uid` 15 on the page has the value "Home".

**`[tt_content[15]["header"] == "Home" || tt_content[15]["subheader"] == "Welcome"]`**  
Checks if the field `header` of the `tt_content` record with the `uid` 15 on the page has the value "Home"
OR the field `subheader` the value "Welcome".

**`[tt_content[15]["header"] == "Home" && tt_content[15]["subheader"] == "Welcome"]`**  
Checks if the field `header` of the `tt_content` record with the `uid` 15 on the page has the value "Home"
AND the field `subheader` the value "Welcome".
This example shows that the data are coming from the same record and it can be be useful to have the `uid`
of a distinct record to verify something.  
String comparisons like in this example might be useful to combine with a 3rd condition of the
same kind, verifying the field `sys_language_uid` for the record-language:
**`[tt_content[15]["header"] == "Home" && tt_content[15]["subheader"] == "Welcome"] && tt_content[15]["sys_language_uid"] == 1]`**  


    @TODO:
    **`[ttContent("uid") in [12,13,14]]`**  
    Checks if the `uid` of a `tt_content` record on the page has one of the values 12, 13 or 14.  
    In this case only the first parameter is used and the condition-function returns an array of
    uids of the found content elements on the page.  
    Note, that the condition still can return false, if the uid is not in the array to check `in [12,13,14]`.

    @TODO:
    **`[ttContent("header") in ["Home", "Homa"]]`**  
    This is the same test like above, just with different notation.
    In this case only the first parameter is used and the condition-function returns an array of
    header-values of the found content elements on the page.  
    Note, that the condition still can return false, if the header is not in the array to check
    `in [["Home", "Homa"]]`.
