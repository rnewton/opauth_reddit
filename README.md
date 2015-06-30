Opauth-Reddit
=============
[Opauth][1] strategy for Reddit authentication.

Implemented based on https://github.com/reddit/reddit/wiki/OAuth2

Opauth is a multi-provider authentication framework for PHP.

Getting started
----------------
1. Install Opauth-Reddit:
   ```bash
   composer require rnewton/opauth_reddit
   ```

2. Create a Reddit app https://www.reddit.com/prefs/apps/
   - Make sure that redirect URI is set to actual OAuth 2.0 callback URL, usually `http://path_to_opauth/reddit/oauth2callback`


3. Configure Opauth-Reddit strategy.

4. Direct user to `http://path_to_opauth/reddit` to authenticate


Strategy configuration
----------------------

Required parameters:

```php
<?php
'Reddit' => array(
    'key' => 'YOUR CLIENT ID',
    'secret' => 'YOUR CLIENT SECRET'
)
```

License
---------
Opauth-Reddit is MIT Licensed  
Copyright © 2015 Robert Newton

[1]: https://github.com/uzyn/opauth
