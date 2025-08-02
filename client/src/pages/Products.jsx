import React, { useContext, useEffect } from 'react';
import { ProductContext } from '../context/ProductContext';
import Product from '../components/Product';

const Products = () => {
  const { products, loading, error, getProducts } = useContext(ProductContext);

  useEffect(() => {
    getProducts();
  }, []);

  return (
    <div>
      <h1>Latest Products</h1>
      {loading ? (
        <h2>Loading...</h2>
      ) : error ? (
        <h3>{error}</h3>
      ) : (
        <div className="row">
          {products.map(product => (
            <div key={product._id} className="col-sm-12 col-md-6 col-lg-4 col-xl-3">
              <Product product={product} />
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default Products;
