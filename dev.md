# Workorders Integration

## Custom HTML Forms

NOTE: This feature is under development. Some functionality is not fully supported.

Sometimes you just need to build a complex HTML form by hand because a GUI form builder just doesn't cut it. The form POST processor can receive your form data and create a new workorder.

### Custom Form Requirements

NOTE: Your form will need to be defined before attempting to post data to the POST processor.

Required Form POST Data Fields (key=value)

* post_type=qryWORKORDER-add_workorder_qry
* form-xml-schema=<xml_schema>
* form-description=Desc from the form definition
* form-name=Form name from the form definition
* form-id=<form_definition_id>

Your custom HTML form will need to include the above hidden fields along with your custom form fields which were defined in the schema.

* <input_field_id>=<input_field_value>
