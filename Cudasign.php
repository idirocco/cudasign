<?php

require dirname(__FILE__) . '/Library/Curl.php';
require dirname(__FILE__) . '/Library/Documents.php';
require dirname(__FILE__) . '/Library/Events.php';

class Cudasign
{
    /**
     * accessToken
     * @var string
     */
    protected $accessToken;

    /**
     * Host Url (default 'api-eval.cudasign.com')
     * @var string
     */
    protected $url;

    /**
     * Hold the Curl Object
     * @var Library\Curl Curl Object
     */
    protected $curl;

    /**
     * Placeholder attritube for the Cudasign documents class
     * @var Library\Documents Documents Object
     */
    protected $documents;

    /**
     * Placeholder attritube for the Cudasign events class
     * @var Library\Events Events Object
     */
    protected $events;

    /**
     * Set up API url and load library classes
     *
     * @param string $accessToken   API key
     * @param string $protocol protocol (default: https)
     * @param string $host     host url (default: api-eval.cudasign.com)
     */
    public function __construct($url = 'api-eval.cudasign.com')
    {
        $this->url = 'https://' . $url . '/';
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Returns the Cudasign cURL Session
     *
     * @return Library\Curl
     */
    public function curl()
    {
        if (!$this->curl){
            $this->curl = new Curl($this->url, $this->accessToken);
        }

        return $this->curl;
    }

    /**
     *
     * @return Library\Documents
     */
    public function documents()
    {
        if (!$this->documents) {
            $this->documents = new Documents($this);
        } 

        return $this->documents;
    }

    /**
     *
     * @return Library\Events
     */
    public function events()
    {
        if (!$this->events) {
            $this->events = new Events($this);
        } 

        return $this->events;
    }

    public function isAccessTokenExpired($accessToken)
    {
        $curl = new Curl($this->url, $accessToken);
        $auth = $curl->get('oauth2/token');

        return isset($auth['error']);
    }

    public function refreshToken($credentials)
    {
        $curl = new Curl($this->url, $credentials['encoded_client_credentials']);

        return $curl->post('oauth2/token', array(
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'grant_type' => 'password'
        ));
    }    

    public function refreshEventSubscription($event, $callback_url)
    {
        $already_subscribed = false;
        foreach ($this->events()->getSubscriptions()['subscriptions'] as $key => $subs) {
            if (isset($subs['event']) && $subs['event'] == $event){ 
                if ($subs['callback_url'] == $callback_url){
                    $already_subscribed = true;
                    break;
                } else {
                    $this->events()->delete($subs['id']);
                }
            }
        }

        if (!$already_subscribed) {
            $this->events()->subscribe(array(
                'event' => $event,
                'callback_url' => $callback_url
            ));
        }

        // $already_subscribed = false;
        // $curl = new Curl($this->url, $accessToken);
        // foreach ($curl->get('event_subscription') as $key => $subs) {
        //     if (isset($subs['event']) && $subs['event'] == $event && $subs['callback_url'] == $callback_url){
        //         echo "<pre>" . print_r($subs, true); exit;
        //         $already_subscribed = true;
        //         break;
        //     }
        // }

        // if (!$already_subscribed) {
        //     $result = $curl->post('event_subscription', array(
        //         'event' => $event,
        //         'callback_url' => $callback_url
        //     ));
        //     echo "<pre>" . print_r($result, true); exit;            
        // }        
    }
}
