// ProgramSAW.cpp (diperbarui: mendukung Benefit/Cost dan output machine-readable)
// Compile: g++ ProgramSAW.cpp -o ProgramSAW.exe (Windows) atau sesuai toolchain
#include <iostream>
#include <string>
#include <iomanip>
#include <cmath>
#include <sstream>
#include <limits>
using namespace std;

// Pembulatan ke bawah (floor) untuk tampilan
string pembulatan(double value, int decimal = 2) {
    double faktor = pow(10, decimal);
    double trunc = floor(value * faktor) / faktor;
    ostringstream out;
    out << fixed << setprecision(decimal) << trunc;
    return out.str();
}

int main() {
    int kritNum;
    cout << "Masukkan jumlah kriteria: ";
    if (!(cin >> kritNum)) return 1;
    cin.ignore(numeric_limits<streamsize>::max(), '\n');

    string kritNama[50];
    double kritBobot[50];
    int kritStatus[50]; // 0 = Benefit, 1 = Cost

    double totalBobot = 0.0;
    for (int j = 0; j < kritNum; j++) {
        cout << "Nama kriteria ke-" << (j+1) << ": ";
        getline(cin, kritNama[j]);

        cout << "Bobot kriteria (desimal atau persen): ";
        cin >> kritBobot[j];

        cout << "Jenis (B untuk benefit, C untuk cost): ";
        char t; cin >> t;
        kritStatus[j] = (t == 'C' || t == 'c') ? 1 : 0;

        totalBobot += kritBobot[j];
        cin.ignore(numeric_limits<streamsize>::max(), '\n');
    }

    // Normalisasi bobot agar total = 1
    if (totalBobot <= 0.0) {
        cout << "\n[Error] Total bobot tidak boleh 0.\n";
        return 1;
    }
    if (fabs(totalBobot - 1.0) > 1e-9) {
        for (int j = 0; j < kritNum; j++) kritBobot[j] /= totalBobot;
        cout << "\n[Info] Bobot dinormalisasi agar total = 1.\n";
    }

    int altNum;
    cout << "\nMasukkan jumlah alternatif: ";
    cin >> altNum;
    cin.ignore(numeric_limits<streamsize>::max(), '\n');

    string altNama[50];
    double altNilai[50][50];

    for (int i = 0; i < altNum; i++) {
        cout << "Nama alternatif ke-" << (i+1) << ": ";
        getline(cin, altNama[i]);
        for (int j = 0; j < kritNum; j++) {
            cout << "Nilai " << kritNama[j] << " untuk " << altNama[i] << ": ";
            cin >> altNilai[i][j];
        }
        cin.ignore(numeric_limits<streamsize>::max(), '\n');
    }

    // Cetak Matriks X (tampilan manusia)
    cout << "\n=== Matriks X (Keputusan) ===\n";
    cout << setw(15) << "Alternatif";
    for (int j = 0; j < kritNum; j++) cout << setw(12) << kritNama[j];
    cout << "\n";
    for (int i = 0; i < altNum; i++) {
        cout << setw(15) << altNama[i];
        for (int j = 0; j < kritNum; j++) cout << setw(12) << altNilai[i][j];
        cout << "\n";
    }

    // Cari max & min tiap kriteria
    double kritMax[50], kritMin[50];
    for (int j = 0; j < kritNum; j++) {
        kritMax[j] = altNilai[0][j];
        kritMin[j] = altNilai[0][j];
        for (int i = 1; i < altNum; i++) {
            if (altNilai[i][j] > kritMax[j]) kritMax[j] = altNilai[i][j];
            if (altNilai[i][j] < kritMin[j]) kritMin[j] = altNilai[i][j];
        }
    }

    // Hitung normalisasi R
    double normNilai[50][50];
    for (int i = 0; i < altNum; i++) {
        for (int j = 0; j < kritNum; j++) {
            if (kritStatus[j] == 0) { // Benefit
                normNilai[i][j] = (kritMax[j] == 0.0) ? 0.0 : (altNilai[i][j] / kritMax[j]);
            } else { // Cost
                normNilai[i][j] = (altNilai[i][j] == 0.0) ? 0.0 : (kritMin[j] / altNilai[i][j]);
            }
        }
    }

    cout << "\n=== Matriks R (Normalisasi) ===\n";
    cout << setw(15) << "Alternatif";
    for (int j = 0; j < kritNum; j++) cout << setw(12) << kritNama[j];
    cout << "\n";
    cout << fixed << setprecision(4);
    for (int i = 0; i < altNum; i++) {
        cout << setw(15) << altNama[i];
        for (int j = 0; j < kritNum; j++) cout << setw(12) << normNilai[i][j];
        cout << "\n";
    }

    // Hitung skor akhir SAW (V)
    double skor[50];
    for (int i = 0; i < altNum; i++) {
        skor[i] = 0.0;
        for (int j = 0; j < kritNum; j++) {
            skor[i] += normNilai[i][j] * kritBobot[j];
        }
    }

    // Ranking (descending)
    int idx[50];
    for (int i = 0; i < altNum; i++) idx[i] = i;
    for (int i = 0; i < altNum - 1; i++) {
        for (int j = i + 1; j < altNum; j++) {
            if (skor[idx[j]] > skor[idx[i]]) swap(idx[i], idx[j]);
        }
    }

    cout << "\n=== Hasil Akhir SAW ===\n";
    cout << setw(6) << "Rank" << setw(20) << "Alternatif" << setw(15) << "Nilai Akhir\n";
    for (int r = 0; r < altNum; r++) {
        int i = idx[r];
        cout << setw(6) << (r+1) << setw(20) << altNama[i] << setw(15) << pembulatan(skor[i], 4) << "\n";
    }

    // Machine-readable outputs untuk PHP
    // MATRIXX
    for (int i = 0; i < altNum; i++) {
        cout << "[MATRIXX]," << altNama[i];
        for (int j = 0; j < kritNum; j++) cout << "," << altNilai[i][j];
        cout << "\n";
    }
    // MATRIXR
    for (int i = 0; i < altNum; i++) {
        cout << "[MATRIXR]," << altNama[i];
        for (int j = 0; j < kritNum; j++) cout << "," << normNilai[i][j];
        cout << "\n";
    }
    // RESULT
    for (int r = 0; r < altNum; r++) {
        int i = idx[r];
        cout << "[RESULT]," << (r+1) << "," << altNama[i] << "," << pembulatan(skor[i], 4) << "\n";
    }

    return 0;
}

