<?php 

return [

  'default' => [

    // The server alias
    'alias'   => 'the-server-alias-here',

    // The server scheme, usually 'http'
    'schema'  => 'http',
    
    // The server host/ip
    'host'    => 'localhost',
    
    // The server port, usually 7474
    'port'    => 7474,
    
    // If the server is secured (Make sure to set this to true in production env)
    'auth'    => false,
    
    // The server credentials
    'user'    => null,
    'pass'    => null,
    
    'timeout' => 25,
    
  ],

];