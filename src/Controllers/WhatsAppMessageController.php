<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\WhatsAppMessage;
use Illuminate\Http\Request;

/**
 * Class WhatsAppMessageController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class WhatsAppMessageController extends RecordController
{
    /**
     * @var string
     */
    protected $model = WhatsAppMessage::class;

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallbackUrl (Request $request, $id)
    {
        //get the id and the datetime against the message
        $splits = explode('-', $id);

        $message = WhatsAppMessage::find($splits[0]);

        //if no records of the message is there, then simply exit
        if ( !$message ) return success_response([]);

        //check if the created at flag matches with the id
        if ( strtotime($message->created_at) != $splits[1] )
            return success_response([]);

        //send request to the message gateway to process the callback
        $gateway = $message->gateway;

        return success_response(( new $gateway($message) )->callback($request));
    }
}