<?php
include('../models/WaveParse.php');
include('../audio/customer-channel.txt');
include('../audio/user-channel.txt');
/**
 * Class WaveParse parses the file for channel.txt.
 */
class WaveFormController
{
    public function Wave()
    {
        $customer = new WaveParse();
        $customer->setFileName('customer-channel.txt');
        $customerArray         = $customer->getFileData();
        $customerLongMonologue = $customer->calculateDuration($customerArray);

        $user = new WaveParse();
        $user->setFileName('user-channel.txt');
        $userArray          = $user->getFileData();
        $userLongMonologue  = $user->calculateDuration($userArray);
        $userTalkPercentage = $user->calculatePercentage($userArray, $customerArray);

        $аrray = [
            'longest_user_monologue'     => $customerLongMonologue,
            'longest_customer_monologue' => $userLongMonologue,
            'user_talk_percentage'       => $userTalkPercentage,
            'user'                       => $userArray,
            'customer'                   => $customerArray,
        ];
        return json_encode($аrray);
    }
}