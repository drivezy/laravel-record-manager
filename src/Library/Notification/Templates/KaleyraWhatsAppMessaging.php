<?php

namespace Drivezy\LaravelRecordManager\Library\Notification\Templates;

use Drivezy\LaravelRecordManager\Models\WhatsAppMessage;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * Class KaleyraWhatsAppMessaging
 * @package Drivezy\LaravelRecordManager\Library\Notification\Templates
 */
class KaleyraWhatsAppMessaging
{

    /**
     * @var null
     */
    private static $key = null;
    /**
     * @var null
     */
    private static $from = null;
    /**
     * @var null
     */
    private static $country_code = null;

    /**
     * KaleyraWhatsAppMessaging constructor.
     * @param WhatsAppMessage $message
     */
    public function __construct (WhatsAppMessage $message)
    {
        self::init();

        $this->message = $message;
        $this->message->gateway = self::class;
    }


    /**
     * @param $template
     * @param $params
     * @return WhatsAppMessage|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function process ($template, $params)
    {
        if ( !( self::$key && self::$from ) ) return;
        $this->sendTextWhatsAppMessage($template, $params);

        return $this->message;

    }

    /**
     * @param Request $request
     * @return WhatsAppMessage
     */
    public function callback (Request $request)
    {
        //check if status is present in the request object
        if ( !$request->has('status') ) return [];

        $this->message->{strtolower($request->status)} = DateUtil::getDateTime($request->timestamp);

        return $this->message->tracking_code;
    }

    /**
     * @param $template
     * @param $params
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendTextWhatsAppMessage ($template, $params)
    {
        //setup the url on which kaleyra will send the callback
        $callbackUrl = config('app.url') . '/whatsAppCallback/' . $this->message->id . '-' . strtotime($this->message->created_at);

        //creating json body against the actual message
        $body = json_encode([
            'to'       => self::$country_code . $this->message->mobile,
            'type'     => 'template',
            'template' => [
                "namespace"     => "4ead37ff_b4ea_4165_8480_bee781b451ee",
                "template_name" => "$template",
                "policy"        => "deterministic",
                "lang_code"     => "en",
                "params"        => $params,
                "ttl"           => "86400",
            ],
            "callback" => "$callbackUrl",
        ]);

        //setup default params against the main body
        $params = [
            'from'    => '' . self::$from . '',
            'method'  => 'wa',
            'format'  => 'json',
            'api_key' => '' . self::$key . '',
            'body'    => $body,
        ];

        //push request to the remote server of kaleyra
        $client = new Client(['base_uri' => 'https://global.kaleyra.com/']);
        $response = $client->request('POST', 'api/v4/', [
            'form_params' => $params,
        ]);

        //check response from the remote server
        if ( $response->getStatusCode() != 200 ) return false;

        //decode the response of kaleyra and then setup message accordingly
        $response = json_decode($response->getBody(), true);
        if ( $response['status'] == 'OK' ) {
            $this->message->tracking_code = $response['data'][0]['id'];
        }
    }

    /**
     * save the final version of the message before logging out of the system
     */
    public function __destruct ()
    {
        $this->message->save();
    }

    /**
     * set up the defaults for the whatsapp
     * to be initialized only once so that each time this is not repeated
     */
    private static function init ()
    {
        //check if the key and from is already set for the session
        if ( self::$key && self::$from ) return;

        //fetch the key and from the property table
        self::$key = LaravelUtility::getProperty('notification.whatsApp.key', false);
        self::$from = LaravelUtility::getProperty('notification.whatsApp.from.mobile', false);

        //if either of them is missing then dont do anything
        if ( !( self::$key && self::$from ) ) return;

        //decrypt the values against the params
        self::$key = Crypt::decrypt(self::$key);
        self::$from = Crypt::decrypt(self::$from);

        //get the default country code of the user
        self::$country_code = LaravelUtility::getProperty('notification.whatsApp.country.code', '91');
    }
}