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

// Fetch our last statsrun Share ID for incremental stats
$iLastShareId = $setting->getValue('statistics_max_share_id') ? $setting->getValue('statistics_max_share_id') : 0;

// Fetch all cachable values but disable fetching from cache
$statistics->setGetCache(false);

// Verbose output
verbose("Running statistical cache updates\n");

// Since fetching from cache is disabled, overwrite our stats
verbose("  getRoundShares ...");
if (!$statistics->getRoundShares())
  verbose(" update failed");
verbose("\n  getTopContributors shares ...");
if (!$statistics->getTopContributors('shares'))
  verbose(" update failed");
verbose("\n  getTopContributors hashes ...");
if (!$statistics->getTopContributors('hashes'))
  verbose(" update failed");
verbose("\n  getCurrentHashrate ...");
if (!$statistics->getCurrentHashrate())
  verbose(" update failed");
// Admin specific statistics, we cache the global query due to slowness
// verbose("\n  getAllUserStats ...");
// if (!$statistics->getAllUserStats('%'))
//  verbose(" update failed");
verbose("\n");

// Per user share statistics based on all shares submitted
verbose("  getUserShares ...\n");
$stmt = $mysqli->prepare("SELECT DISTINCT SUBSTRING_INDEX( `username` , '.', 1 ) AS username FROM " . $share->getTableName());
if ($stmt && $stmt->execute() && $result = $stmt->get_result()) {
  while ($row = $result->fetch_assoc()) {
    verbose("    " . $row['username'] . " ...");
    // Fetch shares from cached value
    verbose(" fetch cached ...");
    $statistics->setGetCache(true);
    $aUserShares = $statistics->getUserShares($user->getUserId($row['username']));
    // Fetch newly added shares since last time we cached them
    verbose(" fetch new ...");
    $statistics->setGetCache(false);
    if (!$aNewUserShares = $statistics->getUserShares($user->getUserId($row['username']), $iLastShareId, $aUserShares)) {
      verbose(" update failed:" . $statistics->getError());
    } else {
      verbose(" done");
    }
    verbose("\n");
  }
}

// Store this runs max share ID to pick up next time
$setting->setValue('statistics_max_share_id', $iCurrentShareId);
?>
