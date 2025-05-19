const express = require('express');
const cors = require('cors'); // Tambahkan ini
const app = express();
const analyzeRoute = require('./routes/analyzeGrowth');

// Middleware
app.use(cors()); // Aktifkan CORS biar bisa akses dari browser lain (misal: localhost)
app.use(express.json());

// Route
app.use('/', analyzeRoute);

// Jalankan server
const PORT = 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});