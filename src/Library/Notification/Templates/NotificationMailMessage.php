<?php

namespace Drivezy\LaravelRecordManager\Library\Notification\Templates;

/**
 * Class NotificationMailMessage
 * @package Drivezy\LaravelRecordManager\Library\Notification
 */
class NotificationMailMessage extends BaseMailable
{
    /**
     * @var
     */
    public $template;
    /**
     * @var string
     */
    public $subject;
    /**
     * @var string
     */
    public $body;
    /**
     * @var array
     */
    public $data;


    /**
     * NotificationMailMessage constructor.
     * @param $template
     * @param string $subject
     * @param string $body
     * @param array $data
     */
    public function __construct ($template, string $subject, string $body, $data = [])
    {
        $this->template = $template;
        $this->subject = $subject;
        $this->body = $body;
        $this->data = $data;

    }

    /**
     * @return NotificationMailMessage
     */
    public function build ()
    {
        return $this->view($this->template)
            ->subject($this->subject);
    }


}
