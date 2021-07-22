<?php
require_once realpath(dirname(__FILE__)).'/log.php';

define('URL_ETH_NODE', 'http://localhost:8545');
define('URL_TOKEN_API', 'https://api.tokenbalance.com/token/');

define('SUCCESS', 'success');

define('SUCCESS_SENT_TOKEN', "success: Send Token succeed.");

define('ERROR_REQ_METHOD', "error: The request method is incorrect.");
define('ERROR_DEST_ADR', "error: The dest address is incorrect.");
define('ERROR_SENT_AMOUNT', "error: There is no send amount.");
define('ERROR_SENT_TOKEN', "error: Failed to send Token.");

$LOG_PATH   =   'log/log.txt';

$log = new Logging();
$log->lfile($LOG_PATH);

$contract_addr = '0xfc476d1573a55f0387462b04f732a6cdc295bcc6';
$holder_addr = '0x1c2F35d669e655a023c3B1884728b70014d309ae';
$holder_addr_private = 'xxxxxx';

//check Request Method
$method = $_SERVER['REQUEST_METHOD'];

if ($method != 'POST') {
	echo ERROR_REQ_METHOD;
	die();
}

$params = $_POST;

$dest_adr = $params['adr'];
$send_amount = $params['amount'];

if (empty($dest_adr)) {
	echo ERROR_DEST_ADR;
	$log->lwrite(ERROR_DEST_ADR);
	die();
}

if ($send_amount <= 0) {
	echo ERROR_SENT_AMOUNT;
	$log->lwrite(ERROR_SENT_AMOUNT);
	die();
}

if (!isAddress($dest_adr)) {
	echo ERROR_DEST_ADR;
	$log->lwrite(ERROR_DEST_ADR);
	die();
}

unlink('script.js');

$content = 'var Web3 = require("web3");'."\r\n";
$content .= 'var Tx = require("ethereumjs-tx");'."\r\n";
$content .= 'var web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545/"));'."\r\n";

$content .= 'const main = async () => {'."\r\n";
$content .= 'var holderAddr = "'.$holder_addr.'";'."\r\n";
$content .= 'var toAddr = "'.$dest_adr.'";'."\r\n";

$content .= 'var value = '.$send_amount.';'."\r\n";
$content .= 'value = value * 10000000000000000;'."\r\n";
$content .= 'value = value.toString(16);'."\r\n";

$content .= 'var abi = [{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"tokens","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"from","type":"address"},{"name":"to","type":"address"},{"name":"tokens","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"tokenOwner","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"acceptOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"a","type":"uint256"},{"name":"b","type":"uint256"}],"name":"safeSub","outputs":[{"name":"c","type":"uint256"}],"payable":false,"stateMutability":"pure","type":"function"},{"constant":false,"inputs":[{"name":"to","type":"address"},{"name":"tokens","type":"uint256"}],"name":"transfer","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"a","type":"uint256"},{"name":"b","type":"uint256"}],"name":"safeDiv","outputs":[{"name":"c","type":"uint256"}],"payable":false,"stateMutability":"pure","type":"function"},{"constant":false,"inputs":[{"name":"spender","type":"address"},{"name":"tokens","type":"uint256"},{"name":"data","type":"bytes"}],"name":"approveAndCall","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"a","type":"uint256"},{"name":"b","type":"uint256"}],"name":"safeMul","outputs":[{"name":"c","type":"uint256"}],"payable":false,"stateMutability":"pure","type":"function"},{"constant":true,"inputs":[],"name":"newOwner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"tokenAddress","type":"address"},{"name":"tokens","type":"uint256"}],"name":"transferAnyERC20Token","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"tokenOwner","type":"address"},{"name":"spender","type":"address"}],"name":"allowance","outputs":[{"name":"remaining","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"a","type":"uint256"},{"name":"b","type":"uint256"}],"name":"safeAdd","outputs":[{"name":"c","type":"uint256"}],"payable":false,"stateMutability":"pure","type":"function"},{"constant":false,"inputs":[{"name":"_newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"payable":true,"stateMutability":"payable","type":"fallback"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_from","type":"address"},{"indexed":true,"name":"_to","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"tokens","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"tokenOwner","type":"address"},{"indexed":true,"name":"spender","type":"address"},{"indexed":false,"name":"tokens","type":"uint256"}],"name":"Approval","type":"event"}];'."\r\n";

$content .= 'var xcczContractAddress = "'.$contract_addr.'";'."\r\n";
$content .= 'var tokenContract = new web3.eth.Contract(abi, xcczContractAddress, {from: holderAddr});'."\r\n";

$content .= 'count = await web3.eth.getTransactionCount(holderAddr);'."\r\n";

$content .= 'var privKey = Buffer.from("'.$holder_addr_private.'", "hex");'."\r\n";

$content .= 'var gasPrice = await web3.eth.getGasPrice();'."\r\n";
$content .= 'var gasLimit = 210000;'."\r\n";

$content .= 'var rawTransaction = {
		"from": holderAddr,
		"nonce": web3.utils.toHex(count),
		"gasPrice": web3.utils.toHex(gasPrice),
		"gasLimit": web3.utils.toHex(gasLimit),
		"to": xcczContractAddress,
		"value": "0x0",
		"data": tokenContract.methods.transfer(toAddr, value).encodeABI()
	};'."\r\n";


$content .= 'var tx = new Tx(rawTransaction);'."\r\n";
$content .= 'tx.sign(privKey);'."\r\n";
$content .= 'var serializedTx = tx.serialize();'."\r\n";

$content .= 'result = await web3.eth.sendSignedTransaction("0x" + serializedTx.toString("hex"));'."\r\n";
$content .= "console.log(JSON.stringify(result, null, '\t'));"."\r\n";
$content .= '}'."\r\n";
$content .= 'main();'."\r\n";

file_put_contents('script.js', $content);

$command = 'node script.js';
$result = shell_exec($command);

$response = json_decode($result, true);

if (!empty($response['transactionHash'])) {
	echo $response['transactionHash'];
	$msg = SUCCESS_SENT_TOKEN . '[ txHash: ' . $response['transactionHash'] . '] ';
	$log->lwrite($msg);
	die();
}

echo ERROR_SENT_TOKEN;
$log->lwrite(ERROR_SENT_TOKEN);
die();

// check ETH address valid
function isAddress($address) {
    if (!preg_match('/^(0x)?[0-9a-f]{40}$/i',$address)) {
        // check if it has the basic requirements of an address
        return false;
    } elseif (!preg_match('/^(0x)?[0-9a-f]{40}$/',$address) || preg_match('/^(0x)?[0-9A-F]{40}$/',$address)) {
        // If it's all small caps or all all caps, return true
        return true;
    } else {
        // Otherwise check each case
        return isChecksumAddress($address);
    }
}

// check ETH address valid
function isChecksumAddress($address) {
    // Check each case
    $address = str_replace('0x','',$address);
    $addressHash = hash('sha3',strtolower($address));
    $addressArray=str_split($address);
    $addressHashArray=str_split($addressHash);

    for($i = 0; $i < 40; $i++ ) {
        // the nth letter should be uppercase if the nth digit of casemap is 1
        if ((intval($addressHashArray[$i], 16) > 7 && strtoupper($addressArray[$i]) !== $addressArray[$i]) || (intval($addressHashArray[$i], 16) <= 7 && strtolower($addressArray[$i]) !== $addressArray[$i])) {
            return false;
        }
    }
    return true;
}

function getTokenInfo($contract_addr, $eth_addr){
	// Get cURL resource
	$curl = curl_init();

	$request_url = URL_TOKEN_API . $contract_addr . '/' . $eth_addr;
	$data = file_get_contents($request_url);
	$data = json_decode($data, true);
	 
	//Print the data out onto the page.
	return $data;
}

?>
