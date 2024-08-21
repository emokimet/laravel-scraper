// resources/js/utils/addressGenerator.js

import { ethers } from "ethers";
//import { bitcoin } from "bitcoinjs-lib";
//const ltc = require('litecoinjs-lib');

export function generateAddress(currency) {
    switch (currency) {
        case 'BNB':
            // BNB address generation can be similar to Ethereum
            //const bnbwallet = ethers.Wallet.createRandom();
            //return bnbwallet.address; // Example only
            return "0x49957F2309a8Bc730EeBc7a9c71714399700DeA9";       
        case 'ETH':
            //const ehterwallet = ethers.Wallet.createRandom();
            //return ehterwallet.address; // Valid Ethereum address
            return "0x49957F2309a8Bc730EeBc7a9c71714399700DeA9";
        case 'TRON':
            //const ltcKeyPair = ltc.ECPair.makeRandom();
            //const { address } = ltc.payments.p2pkh({ pubkey: ltcKeyPair.publicKey });
            //return address; // Valid Litecoin address
            return "TSPBGkYywqgkroQ5PHcuYgkSQQv1ArGRgj";
        case 'BTC':
            //const btcKeyPair = bitcoin.ECPair.makeRandom();
            //const { address: btcAddress } = bitcoin.payments.p2pkh({ pubkey: btcKeyPair.publicKey });
            //return btcAddress; // Valid Bitcoin address*/
            return "TSPBGkYywqgkroQ5PHcuYgkSQQv1ArGRgj";
        default:
            return '';
    }
}

