import React, { useState, useContext, useEffect } from 'react';
import { useParams, Link as RouterLink, useNavigate } from 'react-router-dom';
import { ProductContext } from '../context/ProductContext';
import { CartContext } from '../context/CartContext';
import {
  Container,
  Grid,
  Typography,
  Box,
  Rating,
  Card,
  CardContent,
  Button,
  CircularProgress,
  Alert,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  Divider,
  Link,
} from '@mui/material';

const ProductDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { product, loading, error, getProductById } = useContext(ProductContext);
  const { addToCart } = useContext(CartContext);
  const [qty, setQty] = useState(1);

  useEffect(() => {
    getProductById(id);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  const addToCartHandler = () => {
    addToCart(product, qty);
    navigate('/cart');
  };

  return (
    <Container>
      <Link component={RouterLink} to="/" sx={{ mb: 4, display: 'inline-block' }}>
        Go Back
      </Link>
      {loading ? (
        <CircularProgress />
      ) : error ? (
        <Alert severity="error">{error}</Alert>
      ) : (
        product && (
          <Grid container spacing={4}>
            <Grid item md={6}>
              <img src={product.image} alt={product.name} style={{ width: '100%' }} />
            </Grid>
            <Grid item md={6}>
                <Typography component="h1" variant="h3" gutterBottom>
                  {product.name}
                </Typography>
                <Divider sx={{ mb: 2 }} />
                <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                    <Rating value={product.rating} readOnly precision={0.5} />
                    <Typography variant="body2" color="text.secondary" sx={{ ml: 1 }}>
                    ({product.numReviews} reviews)
                    </Typography>
                </Box>
                <Typography variant="h5" sx={{ mb: 2 }}>
                    Price: ${product.price}
                </Typography>
                <Typography variant="body1" sx={{ mb: 2 }}>
                    Description: {product.description}
                </Typography>
                <Card>
                    <CardContent>
                        <Grid container spacing={2}>
                            <Grid item xs={6}>
                                <Typography>Price:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography><strong>${product.price}</strong></Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>Status:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>
                                    {product.countInStock > 0 ? 'In Stock' : 'Out Of Stock'}
                                </Typography>
                            </Grid>
                            {product.countInStock > 0 && (
                                <Grid item xs={6}>
                                    <Typography>Qty:</Typography>
                                    <FormControl fullWidth>
                                        <Select
                                            value={qty}
                                            onChange={(e) => setQty(Number(e.target.value))}
                                        >
                                            {[...Array(product.countInStock).keys()].map((x) => (
                                            <MenuItem key={x + 1} value={x + 1}>
                                                {x + 1}
                                            </MenuItem>
                                            ))}
                                        </Select>
                                    </FormControl>
                                </Grid>
                            )}
                            <Grid item xs={12}>
                                <Button
                                    onClick={addToCartHandler}
                                    variant="contained"
                                    fullWidth
                                    disabled={product.countInStock === 0}
                                >
                                    Add To Cart
                                </Button>
                            </Grid>
                        </Grid>
                    </CardContent>
                </Card>
            </Grid>
          </Grid>
        )
      )}
    </Container>
  );
};

export default ProductDetail;
