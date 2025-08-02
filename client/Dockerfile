# Stage 1: Build the React app
FROM node:16 as builder

WORKDIR /app/client

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

# Stage 2: Serve the app with Nginx
FROM nginx:stable-alpine

# Copy the build output from the builder stage
COPY --from=builder /app/client/build /usr/share/nginx/html

# Copy the Nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]
