import axios from 'axios';

const API_URL = '/api/products/';

// Get all products
const getProducts = (filter) => {
  let url = API_URL;
  if (filter && filter !== 'all') {
    url += `?type=${filter}`;
  }
  return axios.get(url);
};

// Get product by ID
const getProductById = (id) => {
  return axios.get(API_URL + id);
};

// Create a product
const createProduct = (productData, token) => {
  const config = {
    headers: {
      Authorization: token,
    },
  };
  return axios.post(API_URL, productData, config);
};

// Update a product
const updateProduct = (id, productData, token) => {
  const config = {
    headers: {
      Authorization: token,
    },
  };
  return axios.put(API_URL + id, productData, config);
};

// Delete a product
const deleteProduct = (id, token) => {
  const config = {
    headers: {
      Authorization: token,
    },
  };
  return axios.delete(API_URL + id, config);
};

const productService = {
  getProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct,
};

export default productService;
