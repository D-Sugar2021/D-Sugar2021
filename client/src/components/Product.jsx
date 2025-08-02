import React from 'react';
import { Link } from 'react-router-dom';

const Product = ({ product }) => {
  return (
    <div className="card">
      <Link to={`/product/${product._id}`}>
        <img src={product.image} className="card-img-top" alt={product.name} />
      </Link>
      <div className="card-body">
        <Link to={`/product/${product._id}`}>
          <h5 className="card-title">{product.name}</h5>
        </Link>
        <div className="my-3">
          {/* We can add a Rating component here later */}
          {product.rating} from {product.numReviews} reviews
        </div>
        <h5 className="card-text">${product.price}</h5>
      </div>
    </div>
  );
};

export default Product;
