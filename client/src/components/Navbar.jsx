import React, { useContext } from 'react';
import { Link as RouterLink, useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { CartContext } from '../context/CartContext';
import { ProductContext } from '../context/ProductContext';
import {
    AppBar,
    Toolbar,
    Typography,
    Button,
    Box,
    IconButton,
    Badge,
    ButtonGroup
} from '@mui/material';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';

const Navbar = () => {
  const { isAuthenticated, logout, user } = useContext(AuthContext);
  const { cartItems } = useContext(CartContext);
  const { filter, setFilter } = useContext(ProductContext);
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  const cartItemCount = cartItems.reduce((acc, item) => acc + item.qty, 0);

  return (
    <AppBar position="static" color="default" elevation={0} sx={{ borderBottom: (theme) => `1px solid ${theme.palette.divider}` }}>
      <Toolbar sx={{ flexWrap: 'wrap' }}>
        <Typography variant="h6" color="inherit" noWrap sx={{ flexGrow: 1, textDecoration: 'none' }} component={RouterLink} to="/">
          MugenCommerce
        </Typography>

        <ButtonGroup variant="text" aria-label="text button group" sx={{ mr: 3 }}>
            <Button onClick={() => setFilter('all')} disabled={filter === 'all'}>All</Button>
            <Button onClick={() => setFilter('good')} disabled={filter === 'good'}>Goods</Button>
            <Button onClick={() => setFilter('service')} disabled={filter === 'service'}>Services</Button>
        </ButtonGroup>

        <Box sx={{ display: 'flex', alignItems: 'center' }}>
          <IconButton component={RouterLink} to="/cart" color="inherit" sx={{ mr: 2 }}>
            <Badge badgeContent={cartItemCount} color="secondary">
              <ShoppingCartIcon />
            </Badge>
          </IconButton>
          {isAuthenticated ? (
            <>
              <Typography sx={{ mr: 2 }}>
                Hello, {user && user.name}
              </Typography>
              <Button onClick={handleLogout} variant="outlined">
                Logout
              </Button>
            </>
          ) : (
            <>
              <Button component={RouterLink} to="/login" sx={{ my: 1, mx: 1.5 }}>
                Login
              </Button>
              <Button component={RouterLink} to="/register" variant="contained">
                Sign Up
              </Button>
            </>
          )}
        </Box>
      </Toolbar>
    </AppBar>
  );
};

export default Navbar;
