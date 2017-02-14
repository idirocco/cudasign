<?php 

class Events
{
    /**
     * Hold the Cudasign cURL session
     * @var Library\Curl Curl Object
     */
    protected $curl;

    private $events;

    /**
     * Initialise the object load master class
     */
    public function __construct(Cudasign $master)
    {
        //associate curl class
        $this->curl = $master->curl();
        $this->events = array('document.create', 'document.update', 'document.delete', 'invite.create', 'invite.update');
    }

    /**
     * Used for creating webhooks that will be triggered when the specified event takes place.
     *
     * @param  array $data event details
     */
    public function subscribe(array $data)
    {
        if (!isset($data['event'])) {
            throw new CudasignMissingFieldError('You must include the "event" that triggers the webhook.');
        }

        if (!in_array($data['event'], $this->events)) {
            throw new CudasignMissingFieldError('You must include a valid "event" that triggers the webhook.');
        }

        if (!isset($data['callback_url'])) {
            throw new CudasignMissingFieldError('You must include "callback" URL called when event is triggered');
        }

        return $this->curl->post('event_subscription', $data, 'json');
    }

    /**
     * Delete an event subscription.
     *
     * @param  varchar   $id cudasign subscription id
     * @return array returns event details
     */
    public function delete($id)
    {
        return $this->curl->delete('event_subscription/' . $id);
    }


    /**
     * Returns the event subscriptions
     *
     * @return array returns subscriptions details
     */
    public function getSubscriptions()
    {
        return $this->curl->get('event_subscription');
    }
}
