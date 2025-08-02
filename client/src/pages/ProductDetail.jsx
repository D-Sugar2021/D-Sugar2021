import React, { useState, useContext, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { ProductContext } from '../context/ProductContext';
import { CartContext } from '../context/CartContext';

const ProductDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { product, loading, error, getProductById } = useContext(ProductContext);
  const { addToCart } = useContext(CartContext);
  const [qty, setQty] = useState(1);

  useEffect(() => {
    getProductById(id);
  }, [id]);

  const addToCartHandler = () => {
    addToCart(product, qty);
    navigate('/cart');
  };

  return (
    <div>
      <Link to="/" className="btn btn-light my-3">
        Go Back
      </Link>
      {loading ? (
        <h2>Loading...</h2>
      ) : error ? (
        <h3>{error}</h3>
      ) : (
        product && (
          <div className="row">
            <div className="col-md-6">
              <img src={product.image} alt={product.name} className="img-fluid" />
            </div>
            <div className="col-md-3">
              <ul className="list-group list-group-flush">
                <li className="list-group-item">
                  <h3>{product.name}</h3>
                </li>
                <li className="list-group-item">
                  {product.rating} from {product.numReviews} reviews
                </li>
                <li className="list-group-item">Price: ${product.price}</li>
                <li className="list-group-item">
                  Description: {product.description}
                </li>
              </ul>
            </div>
            <div className="col-md-3">
              <div className="card">
                <ul className="list-group list-group-flush">
                  <li className="list-group-item">
                    <div className="row">
                      <div className="col">Price:</div>
                      <div className="col">
                        <strong>${product.price}</strong>
                      </div>
                    </div>
                  </li>
                  <li className="list-group-item">
                    <div className="row">
                      <div className="col">Status:</div>
                      <div className="col">
                        {product.countInStock > 0 ? 'In Stock' : 'Out Of Stock'}
                      </div>
                    </div>
                  </li>
                  {product.countInStock > 0 && (
                    <li className="list-group-item">
                      <div className="row">
                        <div className="col">Qty</div>
                        <div className="col">
                          <select
                            value={qty}
                            onChange={(e) => setQty(Number(e.target.value))}
                          >
                            {[...Array(product.countInStock).keys()].map((x) => (
                              <option key={x + 1} value={x + 1}>
                                {x + 1}
                              </option>
                            ))}
                          </select>
                        </div>
                      </div>
                    </li>
                  )}
                  <li className="list-group-item">
                    <button
                      onClick={addToCartHandler}
                      className="btn btn-primary btn-block"
                      type="button"
                      disabled={product.countInStock === 0}
                    >
                      Add To Cart
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        )
      )}
    </div>
  );
};

export default ProductDetail;
