# backend_joticius

# Terminal 1: ms-auth (8000)
cd backend_joticius/ms-auth
php -S localhost:8000 -t public

# Terminal 2: ms-conductores (8001)
cd backend_joticius/ms-conductores
php -S localhost:8001 -t public

# Terminal 3: ms-vehiculos (8002)
cd backend_joticius/ms-vehiculos
php -S localhost:8002 -t public

# Terminal 4: ms-rutas (8003)
cd backend_joticius/ms-rutas
php -S localhost:8003 -t public

# Terminal 5: ms-viajes (8004)
cd backend_joticius/ms-viajes
php -S localhost:8004 -t public