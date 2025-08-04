const express = require('express');
const router = express.Router();
const passport = require('passport');

// Load Product model
const Product = require('../../models/Product');
// Load User model
const User = require('../../models/User');

// Admin middleware
const isAdmin = (req, res, next) => {
    if (req.user && req.user.isAdmin) {
        next();
    } else {
        res.status(401).json({ message: 'Not authorized as an admin' });
    }
};

// @route   GET api/products
// @desc    Get all products
// @access  Public
router.get('/', (req, res) => {
  const filter = {};
  if (req.query.type) {
    filter.type = req.query.type;
  }

  Product.find(filter)
    .sort({ date: -1 })
    .then(products => res.json(products))
    .catch(err => res.status(404).json({ noproductsfound: 'No products found' }));
});

// @route   GET api/products/:id
// @desc    Get product by id
// @access  Public
router.get('/:id', (req, res) => {
  Product.findById(req.params.id)
    .then(product => res.json(product))
    .catch(err =>
      res.status(404).json({ noproductfound: 'No product found with that ID' })
    );
});

// @route   POST api/products
// @desc    Create product
// @access  Private/Admin
router.post(
  '/',
  passport.authenticate('jwt', { session: false }),
  isAdmin,
  (req, res) => {
    const newProduct = new Product({
      user: req.user.id,
      name: req.body.name,
      image: req.body.image,
      brand: req.body.brand,
      category: req.body.category,
      description: req.body.description,
      price: req.body.price,
      countInStock: req.body.countInStock,
    });

    newProduct.save().then(product => res.json(product));
  }
);

// @route   PUT api/products/:id
// @desc    Update product
// @access  Private/Admin
router.put(
  '/:id',
  passport.authenticate('jwt', { session: false }),
  isAdmin,
  (req, res) => {
    Product.findById(req.params.id)
      .then(product => {
        product.name = req.body.name;
        product.price = req.body.price;
        product.description = req.body.description;
        product.image = req.body.image;
        product.brand = req.body.brand;
        product.category = req.body.category;
        product.countInStock = req.body.countInStock;

        product.save().then(product => res.json(product));
      })
      .catch(err => res.status(404).json({ productnotfound: 'No product found' }));
  }
);

// @route   DELETE api/products/:id
// @desc    Delete product
// @access  Private/Admin
router.delete(
  '/:id',
  passport.authenticate('jwt', { session: false }),
  isAdmin,
  (req, res) => {
    Product.findById(req.params.id)
      .then(product => {
        product.remove().then(() => res.json({ success: true }));
      })
      .catch(err => res.status(404).json({ productnotfound: 'No product found' }));
  }
);

module.exports = router;
