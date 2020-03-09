<?php

namespace Drivezy\LaravelRecordManager\Library\Listeners;

use Drivezy\LaravelRecordManager\Models\MailLog;
use Drivezy\LaravelRecordManager\Models\MailRecipient;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Storage;

/**
 * Class MailMessageListener
 * @package Drivezy\LaravelRecordManager\Library\Listeners
 */
class MailMessageListener
{

    /**
     * @param MessageSent $messageSent
     */
    public function handle (MessageSent $messageSent)
    {
        $users = [];
        $primaryUser = null;
        foreach ( $messageSent->message->getTo() as $key => $item ) {
            $primaryUser = $primaryUser ? $primaryUser : $key;

            array_push($users, [
                'type'  => 'to',
                'email' => $key,
                'name'  => $item,
            ]);
        }

        $cc = $messageSent->message->getCc();
        if ( is_array($cc) ) {
            foreach ( $messageSent->message->getCc() as $key => $item ) {
                array_push($users, [
                    'type'  => 'cc',
                    'email' => $key,
                    'name'  => $item,
                ]);
            }
        }

        $bcc = $messageSent->message->getBcc();
        if ( is_array($bcc) ) {
            foreach ( $messageSent->message->getBcc() as $key => $item ) {
                array_push($users, [
                    'type'  => 'bc',
                    'email' => $key,
                    'name'  => $item,
                ]);
            }
        }

        $fileName = strtotime('now') . '-' . LaravelUtility::generateRandomAlphabets(10) . '.html';

        $path = $userClass = config('utility.s3_bucket') . '/mails//';
        Storage::disk('s3')->put($path . $fileName, $messageSent->message->getBody(), 'public');

        $mail = new MailLog();

        $mail->subject = $messageSent->message->getSubject();
        $mail->primary_recipient = $primaryUser;
        $mail->body = $fileName;

        $mail->save();

        foreach ( $users as $user ) {
            $recipient = new MailRecipient();

            $recipient->mail_id = $mail->id;
            $recipient->type = $user['type'];
            $recipient->name = $user['name'];
            $recipient->email_id = $user['email'];

            $recipient->save();
        }
    }
}
