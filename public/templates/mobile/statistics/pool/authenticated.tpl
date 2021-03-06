<div data-role="collapsible">
  <h3>User Hashrates</h3>
{include file="statistics/pool/contributors_hashrate.tpl"}
</div>

<div data-role="collapsible">
  <h3>User Shares</h3>
{include file="statistics/pool/contributors_shares.tpl"}
</div>

<div data-role="collapsible">
  <h3>General Stats</h3>
<table>
  <tbody>
    <tr>
      <td class="leftheader">Pool Hash Rate</td>
      <td>{($GLOBAL.hashrate / 1000)|number_format:"3"} Mhash/s</td>
    </tr>
    <tr>
      <td class="leftheader">Pool Efficiency</td>
      <td>{(100 - (100 / $GLOBAL.roundshares.valid * $GLOBAL.roundshares.invalid))|number_format:"2"} %</td>
    </tr>
    <tr>
      <td class="leftheader">Current Active Workers</td>
      <td>{$GLOBAL.workers}</td>
    </tr>
    <tr>
      <td class="leftheader">Next Network Block</td>
      <td><a href="{$GLOBAL.blockexplorer}{$CURRENTBLOCK + 1}" target="_new">{$CURRENTBLOCK + 1}</a> &nbsp;&nbsp;<font size="1"> (Current: <a href="{$GLOBAL.blockexplorer}{$CURRENTBLOCK}" target="_new">{$CURRENTBLOCK})</a></font></td>
    </tr>
    <tr>
      <td class="leftheader">Last Block Found</td>
      <td><a href="{$GLOBAL.blockexplorer}{$LASTBLOCK}" target="_new">{$LASTBLOCK|default:"0"}</a></td>
    </tr>
    <tr>
      <td class="leftheader">Current Difficulty</td>
      <td><a href="{$GLOBAL.chaininfo}" target="_new"><font size="2">{$DIFFICULTY}</font></a></td>
    </tr>
    <tr>
      <td class="leftheader">Est. Avg. Time per Round</td>
      <td>{$ESTTIME|seconds_to_words}</td>
    </tr>
    <tr>
      <td class="leftheader">Est. Shares this Round</td>
      <td>{(pow(2, 32 - $GLOBAL.config.targetdiff) * $DIFFICULTY)|number_format:"0"} <font size="1">(done: {(100 / (pow(2, 32 - $GLOBAL.config.targetdiff) * $DIFFICULTY) * $GLOBAL.roundshares.valid)|number_format:"2"} %)</td>
    </tr>
    <tr>
      <td class="leftheader">Time Since Last Block</td>
      <td>{$TIMESINCELAST|seconds_to_words}</td>
    </tr>
  </tbody>
</table>
</div>

<div data-role="collapsible">
<h3>Last Blocks</h3>
{include file="statistics/blocks/small_table.tpl" ALIGN="right" SHORT=true}
</div>
