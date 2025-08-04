const express = require('express');
const router = express.Router();
const passport = require('passport');
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY || 'your_stripe_secret_key');

// Load Order model
const Order = require('../../models/Order');

// @route   POST api/orders
// @desc    Create new order
// @access  Private
router.post(
  '/',
  passport.authenticate('jwt', { session: false }),
  async (req, res) => {
    const {
      orderItems,
      shippingAddress,
      paymentMethod,
      itemsPrice,
      taxPrice,
      shippingPrice,
      totalPrice,
    } = req.body;

    if (orderItems && orderItems.length === 0) {
      res.status(400);
      throw new Error('No order items');
    } else {
      const order = new Order({
        orderItems,
        user: req.user.id,
        shippingAddress,
        paymentMethod,
        itemsPrice,
        taxPrice,
        shippingPrice,
        totalPrice,
      });

      const createdOrder = await order.save();

      res.status(201).json(createdOrder);
    }
  }
);

// @route   POST api/orders/create-payment-intent
// @desc    Create a payment intent
// @access  Private
router.post(
    '/create-payment-intent',
    passport.authenticate('jwt', { session: false }),
    async (req, res) => {
        const { amount } = req.body;

        try {
            const paymentIntent = await stripe.paymentIntents.create({
                amount: amount, // amount in cents
                currency: 'usd',
            });
            res.send({
                clientSecret: paymentIntent.client_secret,
            });
        } catch (error) {
            res.status(400).send({
                error: {
                    message: error.message,
                },
            });
        }
    }
);

module.exports = router;
