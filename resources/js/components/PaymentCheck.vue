<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-5 rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold text-center mb-4">Deposit</h1>
        <form @submit.prevent="submitDeposit">
            <div class="mb-4">
                <label for="currency" class="block text-sm font-medium text-gray-700">Currency::  </label>
                <select v-model="currency" id="currency" @change="generateAddress" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    <option value="BNB">BNB</option>
                    <option value="ETH">Ethereum</option>
                    <option value="TRON">TRON</option>
                    <option value="BTC">BTC</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700">Amount::  </label>
                <input type="number" v-model="amount" id="amount" placeholder="Enter amount" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required />
            </div>
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700">Deposit Address::  </label>
                <input type="text" v-model="address" id="address" placeholder="Generated address" style="width:400px" class=" mt-1 block w-full border border-gray-300 rounded-md h-100 py-3 px-3" readonly />
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-2 rounded-md hover:bg-blue-600">Create Deposit</button>
        </form>
        <div v-if="successMessage" class="mt-4 text-green-500 text-center">{{ successMessage }}</div>
        <div v-if="errorMessage" class="mt-4 text-red-500 text-center">{{ errorMessage }}</div>
    </div>
</template>

<script>
import axios from 'axios';
import { generateAddress } from '../utils/addressGenerator'; // Import your address generation utility

export default {
    data() {
        return {
            amount: '',
            currency: 'BNB',
            address: '',
            successMessage: '',
            errorMessage: ''
        };
    },
    methods: {
        generateAddress() {
            this.address = generateAddress(this.currency);
            this.checkForDeposit(); // Start checking for deposits when address is generated
        },
        async checkForDeposit() {
            this.successMessage = '';
            this.errorMessage = '';

            // Use setInterval to check for deposits every 10 seconds
            const interval = setInterval(async () => {
                try {
                    const response = await axios.get(`/api/check-deposit?address=${this.address}&currency=${this.currency}`);
                    // const response = await axios.get('http://localhost:8000/api/check-deposit?address=0x05B7152a037e9e2078190a374E3698aF87335756&currency=BNB');
        
                    if (response.data) {
                        this.successMessage = "Deposit Successfully!";
                        clearInterval(interval); // Stop checking once a deposit is detected
                    }
                    else{
                        this.successMessage = response.data;
                    }
                } catch (error) {
                    console.error('Error checking deposit:', error);
                }
            }, 10000); // Check every 10 seconds
        },
        async submitDeposit() {
            // You can keep this method for any other logic if needed
        }
    },
    mounted() {
        this.generateAddress();
    }
};
</script>