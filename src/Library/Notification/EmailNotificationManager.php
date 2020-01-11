<?php

namespace Drivezy\LaravelRecordManager\Library\Notification;

use Drivezy\LaravelRecordManager\Library\Notification\Templates\NotificationMailMessage;
use Drivezy\LaravelRecordManager\Models\EmailNotification;
use Drivezy\LaravelUtility\LaravelUtility;
use Illuminate\Support\Facades\Mail;

class EmailNotificationManager extends BaseNotification {
    /**
     * process all email notifications required for the given notification
     */
    public function process () {
        $this->processEmailNotifications();
    }

    /**
     * push email notification
     */
    private function processEmailNotifications () {
        $emailNotifications = EmailNotification::with(['active_recipients.custom_query', 'active_recipients.run_condition', 'run_condition', 'body'])->where('notification_id', $this->notification->id)->get();

        foreach ( $emailNotifications as $emailNotification ) {
            //validate run condition of the email
            if ( $this->validateRunCondition($emailNotification->run_condition) && $emailNotification->active ) {
                $this->processEmailNotification($emailNotification);
            }
        }
    }

    /**
     * send individual email notification to users
     * @param $emailNotification
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    private function processEmailNotification ($emailNotification) {
        $users = ( new NotificationUserManager($this->user_request_object) )->getTotalUsers($emailNotification->default_users, $emailNotification->active_recipients);

        $subject = LaravelUtility::parseBladeToString($emailNotification->subject, $this->data);

        $body = $emailNotification->body_id ? $emailNotification->body->script : '';
        $body = LaravelUtility::parseBladeToString($body, $this->data);

        $mailable = new NotificationMailMessage($emailNotification->template_name, $subject, $body, $this->data);

        foreach ( $users as $user ) {
            if ( !filter_var($user->email, FILTER_VALIDATE_EMAIL) ) {
                continue;
            }

            if ( !$this->validateSubscription($user, 'email') )
                continue;

            Mail::to($user)->send($mailable);
        }
    }
}

