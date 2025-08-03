import React, { createContext, useReducer } from 'react';
import productService from '../services/productService';

// Initial state
const initialState = {
  products: [],
  product: null,
  loading: true,
  error: null,
  filter: 'all', // 'all', 'good', 'service'
};

// Create context
export const ProductContext = createContext(initialState);

// Reducer
const productReducer = (state, action) => {
  switch (action.type) {
    case 'GET_PRODUCTS_SUCCESS':
      return {
        ...state,
        products: action.payload,
        loading: false,
      };
    case 'GET_PRODUCT_SUCCESS':
      return {
        ...state,
        product: action.payload,
        loading: false,
      };
    case 'SET_FILTER':
        return {
            ...state,
            filter: action.payload,
            loading: true, // Set loading to true when filter changes
        };
    case 'PRODUCT_ERROR':
      return {
        ...state,
        error: action.payload,
        loading: false,
      };
    default:
      return state;
  }
};

// Provider component
export const ProductProvider = ({ children }) => {
  const [state, dispatch] = useReducer(productReducer, initialState);

  // Actions
  const getProducts = async (filter) => {
    try {
      const res = await productService.getProducts(filter);
      dispatch({
        type: 'GET_PRODUCTS_SUCCESS',
        payload: res.data,
      });
    } catch (err) {
      dispatch({
        type: 'PRODUCT_ERROR',
        payload: err.response.data,
      });
    }
  };

  const getProductById = async (id) => {
    try {
      const res = await productService.getProductById(id);
      dispatch({
        type: 'GET_PRODUCT_SUCCESS',
        payload: res.data,
      });
    } catch (err) {
      dispatch({
        type: 'PRODUCT_ERROR',
        payload: err.response.data,
      });
    }
  };

  const setFilter = (filter) => {
      dispatch({ type: 'SET_FILTER', payload: filter });
  }

  // You can add create, update, delete actions here later

  return (
    <ProductContext.Provider
      value={{
        ...state,
        getProducts,
        getProductById,
        setFilter,
      }}
    >
      {children}
    </ProductContext.Provider>
  );
};
