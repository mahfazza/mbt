const express = require('express');
const router = express.Router();
const db = require('../koneksi');
const KNN = require('ml-knn');

router.post('/analyze', (req, res) => {
  const { usia, berat_badan, tinggi_badan, lingkar_kepala, jenis_kelamin } = req.body;

  db.query(
    'SELECT * FROM who_growth_standard WHERE usia_bulan = ? AND jenis_kelamin = ?', 
    [usia, jenis_kelamin], 
    (err, results) => {
      if (err) return res.status(500).json({ error: err.message });

      if (results.length === 0) {
        return res.status(404).json({ kategori: 'Data standar tidak ditemukan untuk usia ini.' });
      }

      const trainingData = results.map(row => [row.berat_badan, row.tinggi_badan, row.lingkar_kepala]);
      const labels = results.map(row => row.kategori);

      const knn = new KNN(trainingData, labels, { k: 1 }); // pakai k:1

      const prediction = knn.predict([[berat_badan, tinggi_badan, lingkar_kepala]]);

      console.log('Input:', [berat_badan, tinggi_badan, lingkar_kepala]);
      console.log('Training Data:', trainingData);
      console.log('Labels:', labels);
      console.log('Prediction:', prediction);

      res.json({ 
        kategori: prediction[0],
        input: { berat_badan, tinggi_badan, lingkar_kepala },
        usia,
        jenis_kelamin
      });      
    }
  );
});

module.exports = router;