const mongoose = require('mongoose');
const User = require('../../models/User');

describe('User Model Test', () => {
  it('should create & save a user successfully', async () => {
    const userData = {
      name: 'Test User',
      email: 'test@example.com',
      password: 'password123',
    };
    const validUser = new User(userData);
    const savedUser = await validUser.save();

    // Object Id should be defined when saved successfully.
    expect(savedUser._id).toBeDefined();
    expect(savedUser.name).toBe(userData.name);
    expect(savedUser.email).toBe(userData.email);
    expect(savedUser.password).toBe(userData.password);
    expect(savedUser.isAdmin).toBe(false);
  });

  it('should fail if user is missing a required field', async () => {
    let err;
    try {
      const incompleteUser = new User({ name: 'Test User' });
      await incompleteUser.save();
    } catch (error) {
      err = error;
    }
    expect(err).toBeInstanceOf(mongoose.Error.ValidationError);
    expect(err.errors.email).toBeDefined();
    expect(err.errors.password).toBeDefined();
  });
});
