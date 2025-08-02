import axios from 'axios';

const API_URL = '/api/orders/';

// Create a new order
const createOrder = (orderData, token) => {
  const config = {
    headers: {
      'Content-Type': 'application/json',
      Authorization: token,
    },
  };
  return axios.post(API_URL, orderData, config);
};

// Create a payment intent
const createPaymentIntent = (amount, token) => {
  const config = {
    headers: {
      'Content-Type': 'application/json',
      Authorization: token,
    },
  };
  return axios.post(API_URL + 'create-payment-intent', { amount }, config);
};

const orderService = {
  createOrder,
  createPaymentIntent,
};

export default orderService;
