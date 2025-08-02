import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Navbar from './components/Navbar';
import Products from './pages/Products';
import ProductDetail from './pages/ProductDetail';
import Cart from './pages/Cart';
import Register from './pages/Register';
import Login from './pages/Login';
import Checkout from './pages/Checkout';
import PrivateRoute from './components/PrivateRoute';
import AdminRoute from './components/AdminRoute';
import ProductList from './pages/admin/ProductList';
import ProductEdit from './pages/admin/ProductEdit';
import ProductCreate from './pages/admin/ProductCreate';
import { AuthProvider } from './context/AuthContext';
import { ProductProvider } from './context/ProductContext';
import { CartProvider } from './context/CartContext';
import './App.css';

const App = () => {
  return (
    <AuthProvider>
      <ProductProvider>
        <CartProvider>
          <Router>
            <Navbar />
            <main className="py-3">
              <div className="container">
                <Routes>
                  {/* Public Routes */}
                  <Route path="/" element={<Products />} />
                  <Route path="/product/:id" element={<ProductDetail />} />
                  <Route path="/cart" element={<Cart />} />
                  <Route path="/register" element={<Register />} />
                  <Route path="/login" element={<Login />} />

                  {/* Private Routes */}
                  <Route
                    path="/shipping"
                    element={
                      <PrivateRoute>
                        <Checkout />
                      </PrivateRoute>
                    }
                  />

                  {/* Admin Routes */}
                  <Route
                    path="/admin/productlist"
                    element={
                      <AdminRoute>
                        <ProductList />
                      </A>
                    }
                  />
                  <Route
                    path="/admin/product/:id/edit"
                    element={
                      <AdminRoute>
                        <ProductEdit />
                      </AdminRoute>
                    }
                  />
                  <Route
                    path="/admin/product/create"
                    element={
                      <AdminRoute>
                        <ProductCreate />
                      </AdminRoute>
                    }
                  />
                </Routes>
              </div>
            </main>
          </Router>
        </CartProvider>
      </ProductProvider>
    </AuthProvider>
  );
};

export default App;
