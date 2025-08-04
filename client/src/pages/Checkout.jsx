import React, { useState, useEffect, useContext } from 'react';
import { loadStripe } from '@stripe/stripe-js';
import { Elements } from '@stripe/react-stripe-js';
import CheckoutForm from '../components/CheckoutForm';
import orderService from '../services/orderService';
import { CartContext } from '../context/CartContext';
import { AuthContext } from '../context/AuthContext';

// TODO: move this to a .env file
const stripePromise = loadStripe('your_stripe_publishable_key');

const Checkout = () => {
  const [clientSecret, setClientSecret] = useState('');
  const [orderId, setOrderId] = useState(null);
  const { cartItems } = useContext(CartContext);
  const { token } = useContext(AuthContext);

  useEffect(() => {
    const createOrderAndPaymentIntent = async () => {
      if (cartItems.length > 0) {
        try {
          // 1. Create Order
          const orderData = {
            orderItems: cartItems,
            shippingAddress: { address: '123 Main St', city: 'Anytown', postalCode: '12345', country: 'USA' }, // Placeholder
            paymentMethod: 'Stripe',
            itemsPrice: cartItems.reduce((acc, item) => acc + item.qty * item.price, 0),
            taxPrice: 0, // Placeholder
            shippingPrice: 0, // Placeholder
            totalPrice: cartItems.reduce((acc, item) => acc + item.qty * item.price, 0), // Placeholder
          };
          const orderRes = await orderService.createOrder(orderData, token);
          setOrderId(orderRes.data._id);

          // 2. Create Payment Intent
          const amount = Math.round(orderRes.data.totalPrice * 100); // Amount in cents
          const paymentIntentRes = await orderService.createPaymentIntent(amount, token);
          setClientSecret(paymentIntentRes.data.clientSecret);
        } catch (error) {
          console.error('Error creating order or payment intent', error);
        }
      }
    };

    createOrderAndPaymentIntent();
  }, [cartItems, token]);

  const appearance = {
    theme: 'stripe',
  };
  const options = {
    clientSecret,
    appearance,
  };

  return (
    <div>
      <h1>Checkout</h1>
      {clientSecret && (
        <Elements options={options} stripe={stripePromise}>
          <CheckoutForm clientSecret={clientSecret} orderId={orderId} />
        </Elements>
      )}
    </div>
  );
};

export default Checkout;
