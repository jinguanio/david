<?php
error_reporting(E_ALL);

class Service
{
    private $__action = '';
    private $__provider;

    public function __construct($query_get)
    {
        $this->__action = $this->_get_action($query_get);

        try {
            $this->__provider = new OAuthProvider();
            $this->__provider->consumerHandler(array($this,'lookupConsumer'));    
            $this->__provider->timestampNonceHandler(array($this,'timestampNonceChecker'));
            $this->__provider->tokenHandler(array($this,'tokenHandler'));
            $this->__provider->setParam('kohana_uri', NULL);  // Ignore the kohana_uri parameter
            $this->__provider->setRequestTokenPath('/v1/oauth/request_token');  // No token needed for this end point
            $this->__provider->checkOAuthRequest();
        } catch (OAuthException $E) {
            echo OAuthProvider::reportProblem($E);
            $this->oauth_error = true;
        }
    }

    private function _get_action($get)
    {
        if (isset($get['request_token'])) {
            return 'request_token';
        }

        if (isset($get['authorize'])) {
            return 'authorize';
        }

        if (isset($get['access_token'])) {
            return 'access_token';
        }
    }

    private function create_access_token()
    {
        if (function_exists('mcrypt_create_iv')) {
            $randomData = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }

        // Last resort which you probably should just get rid of:
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        return substr(hash('sha512', $randomData), 0, 40);
    }

    public function create_consumer_key()
    {
        $fp = fopen('/dev/urandom','rb');
        $entropy = fread($fp, 32);
        fclose($fp);
        // in case /dev/urandom is reusing entropy from its pool, let's add a bit more entropy
        $entropy .= uniqid(mt_rand(), true);
        $hash = sha1($entropy);  // sha1 gives us a 40-byte hash
        // The first 30 bytes should be plenty for the consumer_key
        // We use the last 10 for the shared secret
        return array(substr($hash,0,30),substr($hash,30,10));
    }

    public function lookupConsumer($provider)
    {
        $consumer = ORM::Factory("consumer", $provider->consumer_key);
        if($provider->consumer_key != $consumer->consumer_key) {
            return OAUTH_CONSUMER_KEY_UNKNOWN;
        } else if($consumer->key_status != 0) {  // 0 is active, 1 is throttled, 2 is blacklisted
            return OAUTH_CONSUMER_KEY_REFUSED;
        }
        $provider->consumer_secret = $consumer->secret;
        return OAUTH_OK;
    }

    public function oauth($action=NULL) {
        if($this->oauth_error) return;

        switch($action) {
        case 'request_token':
            $token = Token_Model::create($this->__provider->consumer_key);
            $token->save();
            // Build response with the authorization URL users should be sent to
            echo 'login_url=https://'.Kohana::config('config.site_domain').
                '/session/authorize&oauth_token='.$token->tok.
                '&oauth_token_secret='.$token->secret.
                '&oauth_callback_confirmed=true';
            break;

        case 'access_token':
            $access_token = Token_Model::create($this->__provider->consumer_key, 1);
            $access_token->save();
            $this->token->state = 2;  // The request token is marked as 'used'
            $this->token->save();
            // Now we need to find the user who authorized this request token
            $utoken = ORM::factory('utoken', $this->token->tok);
            if(!$utoken->loaded) {
                echo "oauth error - token rejected";
                break;
            }
            // And swap out the authorized request token for the access token
            $new_utoken = Utoken_Model::create(
                array('token'            => $access_token->tok,
                'user_id'         => $utoken->user_id,
                'application_id'=> $utoken->application_id,
                'access_type'   => $utoken->access_type));
            $new_utoken->save();
            $utoken->delete();
            echo "oauth_token={$access_token->tok}&oauth_token_secret={$access_token->secret}";
            break;
        }
    }

    public function tokenHandler($provider) {

        $this->token = ORM::Factory("token", $provider->token);

        if(!$this->token->loaded) {
            return OAUTH_TOKEN_REJECTED;
        } else if($this->token->type==1 && $this->token->state==1) {
            return OAUTH_TOKEN_REVOKED;
        } else if($this->token->type==0 && $this->token->state==2) {
            return OAUTH_TOKEN_USED;
        } else if($this->token->type==0 && $this->token->verifier != $provider->verifier) {
            return OAUTH_VERIFIER_INVALID;
        }

        $provider->token_secret = $this->token->secret;
        return OAUTH_OK;
    }
}

