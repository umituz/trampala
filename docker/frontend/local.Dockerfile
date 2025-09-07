# Optimized Nuxt.js Development Dockerfile
FROM node:20-alpine

WORKDIR /app

# Create non-root user first
ARG HOST_UID=1001
ARG HOST_GID=1001
RUN addgroup --system --gid $HOST_GID nodejs && \
    adduser --system --uid $HOST_UID nuxtjs

# Set environment variables
ARG PROJECT_NAME
ENV PROJECT_NAME=${PROJECT_NAME} \
    NODE_ENV=development \
    NUXT_TELEMETRY_DISABLED=1 \
    WATCHPACK_POLLING=true \
    CHOKIDAR_USEPOLLING=true \
    CHOKIDAR_INTERVAL=1000 \
    PORT=3000 \
    HOST="0.0.0.0"

# Install system dependencies for better performance
RUN apk add --no-cache \
    libc6-compat \
    dumb-init

# Copy source code
COPY --chown=nuxtjs:nodejs nuxt/ .

# Create necessary directories and set permissions
RUN mkdir -p .nuxt .output node_modules && \
    chown -R nuxtjs:nodejs /app

# Switch to non-root user
USER nuxtjs

# Expose port
EXPOSE 3000

# Use dumb-init for proper signal handling
ENTRYPOINT ["dumb-init", "--"]

# Install dependencies and start development server
CMD ["sh", "-c", "npm install && npm run dev"]