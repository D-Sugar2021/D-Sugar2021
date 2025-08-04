import React, { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

const AdminRoute = ({ children }) => {
  const { isAuthenticated, user } = useContext(AuthContext);

  if (isAuthenticated && user && user.isAdmin) {
    return children;
  } else {
    return <Navigate to="/login" />;
  }
};

export default AdminRoute;
