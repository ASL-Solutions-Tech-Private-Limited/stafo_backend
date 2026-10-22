FROM node:20-alpine

RUN apk add --no-cache bash curl

WORKDIR /var/www/chat-server

# Copy package manifests
COPY chat-server/package*.json ./

RUN npm install

COPY docker/node/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 6001

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["node", "server.js"]
