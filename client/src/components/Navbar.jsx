import React, { useContext } from 'react';
import { Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { CartContext } from '../context/CartContext';

const Navbar = () => {
  const { isAuthenticated, logout, user } = useContext(AuthContext);
  const { cartItems } = useContext(CartContext);

  const authLinks = (
    <ul>
      <li>
        <Link to="/cart">
          <i className="fas fa-shopping-cart"></i> Cart{' '}
          {cartItems.length > 0 && (
            <span className="badge">{cartItems.reduce((acc, item) => acc + item.qty, 0)}</span>
          )}
        </Link>
      </li>
      <li>
        <span>Hello, {user && user.name}</span>
      </li>
      <li>
        <a onClick={logout} href="#!">
          Logout
        </a>
      </li>
    </ul>
  );

  const guestLinks = (
    <ul>
      <li>
        <Link to="/cart">
          <i className="fas fa-shopping-cart"></i> Cart{' '}
          {cartItems.length > 0 && (
            <span className="badge">{cartItems.reduce((acc, item) => acc + item.qty, 0)}</span>
          )}
        </Link>
      </li>
      <li>
        <Link to="/register">Register</Link>
      </li>
      <li>
        <Link to="/login">Login</Link>
      </li>
    </ul>
  );

  return (
    <nav>
      <h1>
        <Link to="/">MERN E-Commerce</Link>
      </h1>
      {isAuthenticated ? authLinks : guestLinks}
    </nav>
  );
};

export default Navbar;
