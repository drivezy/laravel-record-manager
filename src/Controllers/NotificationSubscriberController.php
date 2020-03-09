<?php

namespace Drivezy\LaravelRecordManager\Controllers;

use Drivezy\LaravelRecordManager\Models\NotificationSubscriber;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class NotificationSubscriberController
 * @package Drivezy\LaravelRecordManager\Controllers
 */
class NotificationSubscriberController extends RecordController
{
    /**
     * @var string
     */
    protected $model = NotificationSubscriber::class;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function post (Request $request)
    {
        $user = Auth::user();

        //only logged in user should be able to do it
        if ( !$user ) return invalid_operation();

        $subscription = NotificationSubscriber::firstOrNew([
            'source_type'     => md5(LaravelUtility::getUserModelFullQualifiedName()),
            'source_id'       => $user->id,
            'notification_id' => $request->notification_id,
        ]);

        $columns = ['email', 'sms', 'whatsapp', 'push'];
        foreach ( $columns as $item )
            $subscription->{$item} = $request->{$item};

        $subscription->save();

        return success_response($subscription);
    }
}
