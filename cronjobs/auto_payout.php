#!/usr/bin/php
<?php

/*

Copyright:: 2013, Sebastian Grewe

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

 */

// Include all settings and classes
require_once('shared.inc.php');

verbose("Running auto-payouts ...");

if ($bitcoin->can_connect() !== true) {
  verbose(" unable to connect to RPC server, exiting\n");
  exit(1);
}

// Mark this job as active
$setting->setValue('auto_payout_active', 1);

// Fetch all users with setup AP
$users = $user->getAllAutoPayout();

// Quick summary
verbose(" found " . count($users) . " queued payout(s)\n");

// Go through users and run transactions
if (! empty($users)) {
  verbose("\tUserID\tUsername\tBalance\tThreshold\tAddress\t\t\t\t\tStatus\n\n");

  foreach ($users as $aUserData) {
    $aBalance = $transaction->getBalance($aUserData['id']);
    $dBalance = $aBalance['confirmed'];
    verbose("\t" . $aUserData['id'] . "\t" . $aUserData['username'] . "\t" . $dBalance . "\t" . $aUserData['ap_threshold'] . "\t\t" . $aUserData['coin_address'] . "\t");

    // Only run if balance meets threshold and can pay the potential transaction fee
    if ($dBalance > $aUserData['ap_threshold'] && $dBalance > $config['txfee']) {
      // Validate address against RPC
      try {
        $bitcoin->validateaddress($aUserData['coin_address']);
      } catch (BitcoinClientException $e) {
        verbose("VERIFY FAILED\n");
        continue;
      }

      // Send balance, fees are reduced later by RPC Server
      try {
        $bitcoin->sendtoaddress($aUserData['coin_address'], $dBalance);
      } catch (BitcoinClientException $e) {
        verbose("SEND FAILED\n");
        continue;
      }

      // Create transaction record
      if ($transaction->addTransaction($aUserData['id'], $dBalance - $config['txfee'], 'Debit_AP', NULL, $aUserData['coin_address']) && $transaction->addTransaction($aUserData['id'], $config['txfee'], 'TXFee', NULL, $aUserData['coin_address'])) {
        // Notify user via  mail
        $aMailData['email'] = $user->getUserEmail($user->getUserName($aUserData['id']));
        $aMailData['subject'] = 'Auto Payout Completed';
        $aMailData['amount'] = $dBalance;
        if (!$notification->sendNotification($aUserData['id'], 'auto_payout', $aMailData)) {
          verbose("NOTIFY FAILED\n");
        } else {
          verbose("OK\n");
        }
      } else {
        verbose("FAILED\n");
      }

    } else {
      verbose("SKIPPED\n");
    }
  }
} else {
  verbose("  no user has configured their AP > 0\n");
}

// Mark this job as inactive
$setting->setValue('auto_payout_active', 0);

?>
