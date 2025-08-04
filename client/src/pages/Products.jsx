import React, { useContext, useEffect } from 'react';
import { ProductContext } from '../context/ProductContext';
import Product from '../components/Product';
import { Grid, Typography, CircularProgress, Alert } from '@mui/material';

const Products = () => {
  const { products, loading, error, getProducts, filter } = useContext(ProductContext);

  useEffect(() => {
    getProducts(filter);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filter]);

  return (
    <>
      <Typography variant="h4" component="h1" gutterBottom>
        Latest Products
      </Typography>
      {loading ? (
        <CircularProgress />
      ) : error ? (
        <Alert severity="error">{error}</Alert>
      ) : (
        <Grid container spacing={4}>
          {products.map((product) => (
            <Grid item key={product._id} xs={12} sm={6} md={4} lg={3}>
              <Product product={product} />
            </Grid>
          ))}
        </Grid>
      )}
    </>
  );
};

export default Products;
