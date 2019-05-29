<?php

namespace Drivezy\LaravelRecordManager\Jobs;

use Drivezy\LaravelRecordManager\Models\ServerDeployment;
use Drivezy\LaravelUtility\Job\BaseJob;
use Drivezy\LaravelUtility\LaravelUtility;
use Drivezy\LaravelUtility\Library\DateUtil;

/**
 * Class Ec2ServerSyncingJob
 * @package Drivezy\LaravelRecordManager\Jobs
 */
class Ec2ServerSyncingJob extends BaseJob {
    /**
     * @var int
     */
    public $inactive_threshold = 5;

    /**
     * @return bool|void
     */
    public function handle () {
        //get the time for which if not responded server should be made inactive
        $this->inactive_threshold = LaravelUtility::getProperty('server.inactive.threshold', 5);

        $client = \AWS::createClient('Ec2');
        $result = $client->describeInstances();

        foreach ( $result['Reservations'] as $reservation ) {
            foreach ( $reservation['Instances'] as $instance ) {
                if ( $instance['State']['Code'] != 16 ) continue;

                $obj = [
                    'instance_identifier' => $instance['InstanceId'] ? : '',
                    'key_name'            => $instance['KeyName'] . '.pem',
                    'private_ip'          => $instance['PrivateIpAddress'] ? : '',
                    'public_ip'           => isset($instance['PublicIpAddress']) ? $instance['PublicIpAddress'] : '',
                    'name'                => isset($instance['Tags'][0]['Value']) ? $instance['Tags'][0]['Value'] : '',
                    'public_url'          => isset($instance['PublicDnsName']) ? $instance['PublicDnsName'] : '',
                ];

                $this->setServer($obj);
            }
        };

        //any server which has not responded within 5 minutes should be made inactive
        $this->setInactiveServer();

    }

    /**
     * @param $record
     */
    private function setServer ($record) {
        $servers = ServerDeployment::where('private_ip', $record['private_ip'])->get();

        foreach ( $servers as $server ) {
            $record['active'] = DateUtil::getDateTimeDifference($server->last_ping_time, DateUtil::getDateTime()) > $this->inactive_threshold * 60 ? false : true;
            $server->update($record);
        }
    }

    /**
     * Inactivate all servers who have not responded in last 5 minutes
     */
    private function setInactiveServer () {
        ServerDeployment::where('last_ping_time', '<', DateUtil::getPastTime($this->inactive_threshold))->update(['active' => false]);
    }

}
