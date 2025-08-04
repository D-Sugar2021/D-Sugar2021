import React, { useContext } from 'react';
import { Link as RouterLink, useNavigate } from 'react-router-dom';
import { CartContext } from '../context/CartContext';
import {
  Container,
  Grid,
  Typography,
  Box,
  Card,
  CardContent,
  Button,
  List,
  ListItem,
  ListItemText,
  ListItemAvatar,
  Avatar,
  IconButton,
  Select,
  MenuItem,
  FormControl,
  Link,
  Alert
} from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';

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
    <Container>
      <Typography variant="h4" component="h1" gutterBottom>
        Shopping Cart
      </Typography>
      <Grid container spacing={4}>
        <Grid item md={8}>
          {cartItems.length === 0 ? (
            <Alert severity="info">
              Your cart is empty. <Link component={RouterLink} to="/">Go Back</Link>
            </Alert>
          ) : (
            <List>
              {cartItems.map((item) => (
                <ListItem key={item.product} divider>
                  <ListItemAvatar>
                    <Avatar src={item.image} alt={item.name} variant="square" />
                  </ListItemAvatar>
                  <ListItemText
                    primary={<Link component={RouterLink} to={`/product/${item.product}`}>{item.name}</Link>}
                    secondary={`$${item.price}`}
                  />
                  <FormControl sx={{ m: 1, minWidth: 70 }}>
                    <Select
                      value={item.qty}
                      onChange={(e) =>
                        addToCart({ ...item, product: { _id: item.product, name: item.name, image: item.image, price: item.price, countInStock: item.countInStock } }, Number(e.target.value))
                      }
                    >
                      {[...Array(item.countInStock).keys()].map((x) => (
                        <MenuItem key={x + 1} value={x + 1}>
                          {x + 1}
                        </MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                  <IconButton edge="end" aria-label="delete" onClick={() => removeFromCartHandler(item.product)}>
                    <DeleteIcon />
                  </IconButton>
                </ListItem>
              ))}
            </List>
          )}
        </Grid>
        <Grid item md={4}>
          <Card>
            <CardContent>
              <Typography variant="h5" component="div" gutterBottom>
                Subtotal ({cartItems.reduce((acc, item) => acc + item.qty, 0)}) items
              </Typography>
              <Typography variant="h6">
                $
                {cartItems
                  .reduce((acc, item) => acc + item.qty * item.price, 0)
                  .toFixed(2)}
              </Typography>
              <Button
                type="button"
                variant="contained"
                fullWidth
                disabled={cartItems.length === 0}
                onClick={checkoutHandler}
                sx={{ mt: 2 }}
              >
                Proceed To Checkout
              </Button>
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Container>
  );
};

export default Cart;
