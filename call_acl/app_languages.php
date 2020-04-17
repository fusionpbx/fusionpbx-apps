<?php
#This file was last reorganized on 19th of September 2017 08:54:24 AM UTC

$text['title-call_acl']['en-us'] = "Call ACL";

$text['label-call_acl_order']['en-us'] = "Order";

$text['label-call_acl_name']['en-us'] = "Name";

$text['label-call_acl_source']['en-us'] = "Source";

$text['label-call_acl_destination']['en-us'] = "Destination";

$text['label-call_acl_action']['en-us'] = "Action";

$text['label-call_acl_enabled']['en-us'] = "Enabled";


$text['label-edit-add']['en-us'] = "Add ACL rule";

$text['label-edit-edit']['en-us'] = "Edit ACL rule";

$text['label-add-note']['en-us'] = "Add access control rule. Rules are applied in order. If order for rules are same, they applied in random order. If none of rules are matched - allow call";

$text['label-edit-note']['en-us'] = "Edit access control rule. Rules are applied in order. If order for rules are same, they applied in random order. If none of rules are matched - allow call";

$text['label-add-complete']['en-us'] = "Add Complete";

$text['label-update-complete']['en-us'] = "Update Complete";

$text['label-reject']['en-us'] = "Reject";

$text['label-allow']['en-us'] = "Allow";

$text['description-call_acl']['en-us'] = "Access Control List for calls within domain. Default rule - allow";

$text['description-call_acl_name']['en-us'] = "Enter the name for this rule";

$text['description-call_acl_source']['en-us'] = "Enter the template for source number to match. Refer below for templates description";

$text['description-call_acl_destination']['en-us'] = "Enter the template for destination number to match. Refer below for templates description";

$text['description-call_acl_action']['en-us'] = "Allow or reject(block) this call";

$text['description-call_acl_enabled']['en-us'] = "Enable or disable this rule";

$text['description-call_acl_order']['en-us'] = "Select order number for this rule";



$text['description-call_acl_templates']['en-us'] = "Templates support ranges and wildcards. Actually it's limited regexes<br>";
$text['description-call_acl_templates']['en-us'] .= "[A-B] matches any digit within A-B range<br>";
$text['description-call_acl_templates']['en-us'] .= "'X' matches any digit one time<br>";
$text['description-call_acl_templates']['en-us'] .= "'*' matches anything<br>";
$text['description-call_acl_templates']['en-us'] .= "'^'  matches start of the string<br>";
$text['description-call_acl_templates']['en-us'] .= "'$'  matches end of the string<br>";
$text['description-call_acl_templates']['en-us'] .= "Examples:<br>";
$text['description-call_acl_templates']['en-us'] .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;^[1-2]X matches numbers 10 - 29 <br>";
$text['description-call_acl_templates']['en-us'] .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;^XXX or ^XXX*$ matches numbers with 3 digits and more<br>";
$text['description-call_acl_templates']['en-us'] .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;^XXX$ matches numbers with exact 3 digits <br>";
$text['description-call_acl_templates']['en-us'] .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;^*[45]$ matches numbers that ends with 4 or 5 <br>";
?>
