FROM node

WORKDIR /app

# Installer les dépendances
COPY package*.json ./
RUN npm install

COPY . .

# Exposer le port Vite
EXPOSE 5173

# Commande pour lancer Vite
CMD ["npm", "run", "dev", "--", "--host"]