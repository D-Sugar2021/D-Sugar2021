const request = require('supertest');
const express = require('express');
const mongoose = require('mongoose');
const passport = require('passport');
const users = require('../../routes/api/users');
const User = require('../../models/User');

// Setup express app
const app = express();
app.use(express.urlencoded({ extended: false }));
app.use(express.json());
app.use(passport.initialize());
require('../../config/passport')(passport);
app.use('/api/users', users);

describe('Auth Routes Integration Test', () => {
  const testUser = {
    name: 'Test User',
    email: 'test@example.com',
    password: 'password123',
  };

  it('should register a new user successfully', async () => {
    const res = await request(app)
      .post('/api/users/register')
      .send(testUser);

    expect(res.statusCode).toEqual(200);
    expect(res.body.name).toBe(testUser.name);
    expect(res.body.email).toBe(testUser.email);
    expect(res.body).toHaveProperty('password'); // The hashed password
  });

  it('should not register a user with an existing email', async () => {
    // First, register a user
    await request(app).post('/api/users/register').send(testUser);

    // Then, try to register again with the same email
    const res = await request(app)
      .post('/api/users/register')
      .send(testUser);

    expect(res.statusCode).toEqual(400);
    expect(res.body.email).toBe('Email already exists');
  });

  it('should login a registered user and return a JWT token', async () => {
    // Register the user first
    await request(app).post('/api/users/register').send(testUser);

    const res = await request(app)
      .post('/api/users/login')
      .send({
        email: testUser.email,
        password: testUser.password,
      });

    expect(res.statusCode).toEqual(200);
    expect(res.body.success).toBe(true);
    expect(res.body).toHaveProperty('token');
    expect(res.body.token.startsWith('Bearer ')).toBe(true);
  });

  it('should not login with incorrect password', async () => {
    await request(app).post('/api/users/register').send(testUser);

    const res = await request(app)
      .post('/api/users/login')
      .send({
        email: testUser.email,
        password: 'wrongpassword',
      });

    expect(res.statusCode).toEqual(400);
    expect(res.body.password).toBe('Password incorrect');
  });
});
