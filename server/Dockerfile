# Use an official Node.js runtime as a parent image
FROM node:16

# Set the working directory in the container
WORKDIR /app/server

# Copy package.json and package-lock.json
COPY package*.json ./

# Install app dependencies
RUN npm install

# Copy the rest of the application's code
COPY . .

# Make port 5000 available to the world outside this container
EXPOSE 5000

# Run the app when the container launches
CMD [ "npm", "start" ]
