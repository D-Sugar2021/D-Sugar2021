import React, { createContext, useReducer, useEffect } from 'react';
import authService from '../services/authService';
import jwt_decode from 'jwt-decode';

// Initial state
const initialState = {
  isAuthenticated: false,
  user: null,
  token: localStorage.getItem('token'),
};

// Create context
export const AuthContext = createContext(initialState);

// Reducer
const authReducer = (state, action) => {
  switch (action.type) {
    case 'LOGIN_SUCCESS':
      localStorage.setItem('token', action.payload.token);
      return {
        ...state,
        isAuthenticated: true,
        user: jwt_decode(action.payload.token),
        token: action.payload.token,
      };
    case 'REGISTER_SUCCESS':
        return {
            ...state,
            // You might want to automatically log in the user after registration
            // For now, we'll just return the state
        };
    case 'LOGOUT':
      localStorage.removeItem('token');
      return {
        ...state,
        isAuthenticated: false,
        user: null,
        token: null,
      };
    default:
      return state;
  }
};

// Provider component
export const AuthProvider = ({ children }) => {
  const [state, dispatch] = useReducer(authReducer, initialState);

  // On initial load, check for token and update auth state
  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
        try {
            const decodedToken = jwt_decode(token);
            // Check if token is expired
            if (decodedToken.exp * 1000 < Date.now()) {
                dispatch({ type: 'LOGOUT' });
            } else {
                dispatch({ type: 'LOGIN_SUCCESS', payload: { token } });
            }
        } catch (error) {
            // If token is invalid, logout
            dispatch({ type: 'LOGOUT' });
        }
    }
  }, []);


  // Actions
  const login = async (userData) => {
    try {
      const res = await authService.login(userData);
      dispatch({
        type: 'LOGIN_SUCCESS',
        payload: res.data,
      });
    } catch (err) {
      console.error(err);
    }
  };

  const register = async (userData) => {
    try {
      await authService.register(userData);
      dispatch({
        type: 'REGISTER_SUCCESS',
      });
    } catch (err) {
      console.error(err);
    }
  };

  const logout = () => {
    dispatch({ type: 'LOGOUT' });
  };

  return (
    <AuthContext.Provider
      value={{
        ...state,
        login,
        register,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};
