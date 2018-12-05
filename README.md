This is helper file for Codeigniter that you can add to your project and load it

There is function "send_push" that have 3 argument :
token_or_topic : can be token or topic
payload
expire : per second notification alive

And function "get_token_info"
That return information about your token

Tip:
Please add firebase api key in your config file in application/config/config.php
$config['firebase_api'] = 'AI_';
