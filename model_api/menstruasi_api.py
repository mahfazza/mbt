from flask import Flask, request, jsonify
import pickle
import pandas as pd

app = Flask(__name__)

# Load model
with open('naive_bayes_model.pkl', 'rb') as f:
    model = pickle.load(f)

# Daftar fitur lengkap sesuai model training
features = ['LengthofCycle', 'MeanCycleLength', 'EstimatedDayofOvulation', 
            'LengthofLutealPhase', 'TotalDaysofFertility', 'LengthofMenses', 
            'MeanMensesLength', 'Age', 'BMI']

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()

        # Pastikan semua fitur ada, kalau tidak ada bisa beri default nilai (misal 0 atau None)
        input_data = {}
        for feat in features:
            input_data[feat] = data.get(feat, 0)  # kamu bisa ganti default sesuai kebutuhan

        # Buat DataFrame 1 baris dengan kolom fitur lengkap
        input_df = pd.DataFrame([input_data], columns=features)

        # Prediksi dan ambil confidence (probabilitas tertinggi)
        prediction = model.predict(input_df)[0]
        proba = model.predict_proba(input_df).max()

        return jsonify({
            'prediction': int(prediction),
            'confidence': round(float(proba * 100), 2)
        })
    except Exception as e:
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(debug=True)