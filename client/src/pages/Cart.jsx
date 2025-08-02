import React, { useContext } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { CartContext } from '../context/CartContext';

const Cart = () => {
  const { cartItems, addToCart, removeFromCart } = useContext(CartContext);
  const navigate = useNavigate();

  const removeFromCartHandler = (id) => {
    removeFromCart(id);
  };

  const checkoutHandler = () => {
    navigate('/login?redirect=/shipping');
  };

  return (
    <div className="row">
      <div className="col-md-8">
        <h1>Shopping Cart</h1>
        {cartItems.length === 0 ? (
          <div>
            Your cart is empty <Link to="/">Go Back</Link>
          </div>
        ) : (
          <ul className="list-group list-group-flush">
            {cartItems.map((item) => (
              <li key={item.product} className="list-group-item">
                <div className="row">
                  <div className="col-md-2">
                    <img src={item.image} alt={item.name} className="img-fluid rounded" />
                  </div>
                  <div className="col-md-3">
                    <Link to={`/product/${item.product}`}>{item.name}</Link>
                  </div>
                  <div className="col-md-2">${item.price}</div>
                  <div className="col-md-2">
                    <select
                      value={item.qty}
                      onChange={(e) =>
                        addToCart(item, Number(e.target.value))
                      }
                    >
                      {[...Array(item.countInStock).keys()].map((x) => (
                        <option key={x + 1} value={x + 1}>
                          {x + 1}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div className="col-md-2">
                    <button
                      type="button"
                      className="btn btn-light"
                      onClick={() => removeFromCartHandler(item.product)}
                    >
                      <i className="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </li>
            ))}
          </ul>
        )}
      </div>
      <div className="col-md-4">
        <div className="card">
          <ul className="list-group list-group-flush">
            <li className="list-group-item">
              <h2>
                Subtotal ({cartItems.reduce((acc, item) => acc + item.qty, 0)})
                items
              </h2>
              $
              {cartItems
                .reduce((acc, item) => acc + item.qty * item.price, 0)
                .toFixed(2)}
            </li>
            <li className="list-group-item">
              <button
                type="button"
                className="btn btn-primary btn-block"
                disabled={cartItems.length === 0}
                onClick={checkoutHandler}
              >
                Proceed To Checkout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default Cart;
