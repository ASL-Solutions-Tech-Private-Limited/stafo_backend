module.exports = {
  apps: [
    {
      name: 'stafo-chat',
      script: 'server.js',
      cwd: '/var/www/php-apps/stafo/chat-server',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '200M',
      env: {
        NODE_ENV: 'production',
        CHAT_PORT: 6001
      },
      error_file: '/root/.pm2/logs/stafo-chat-error.log',
      out_file: '/root/.pm2/logs/stafo-chat-out.log'
    }
  ]
};
