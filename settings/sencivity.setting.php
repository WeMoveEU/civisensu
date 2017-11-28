<?php

return array(
  'sensu_url' => array(
    'group_name' => 'Sencivity settings',
    'group' => 'sencivity',
    'name' => 'sensu_url',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => 'http://localhost:4567',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => 'Sensu URL',
    'description' => 'Root URL of the Sensu API, without trailing slash',
    'help_text' => 'Root URL of the Sensu API, without trailing slash',
  ),
  'sensu_client' => array(
    'group_name' => 'Sencivity settings',
    'group' => 'sencivity',
    'name' => 'sensu_client',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => 'sencivity',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => 'Sensu client name',
    'description' => 'Name of the Sensu client reporting check results and metrics',
    'help_text' => 'Name of the Sensu client reporting check results and metrics',
  ),
);

