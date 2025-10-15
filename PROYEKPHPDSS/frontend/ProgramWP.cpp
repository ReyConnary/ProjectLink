// Tugas matkul DSS pertemuan 5 metode Weighted Product
	// Kelompok 9:
	// Rey Connary Karnadi					(825230059)
	// Marsha Alessandra Gisselese Wansaga	(825230060)
	// Felix Bunandar						(825230067)
	// Audelia Franetta						(825230164)
	// Cantika Dian Farhana					(825230184)

#include <iostream>
#include <iomanip>
#include <cmath>
#include <string>
#include <limits>
using namespace std;

int main() {
    int alternatif, kriteria;   // jumlah alternatif & kriteria
    cout << "=== DSS - Metode Weighted Product (WP) ===\n\n";
    cout << "Masukkan jumlah alternatif: ";
    cin  >> alternatif;
    cout << "Masukkan jumlah kriteria : ";
    cin  >> kriteria;

    // Batasan aman supaya array bisa terpakai
    if (alternatif > 50 || kriteria > 50) {
        cout << "[Error] Maksimum jumlah alternatif dan kriteria adalah 50.\n";
        return 1;
    }

    // Deklarasi struktur data
    string altNama[50], kritNama[50];
    int    Cost[50];          // cost = 1, benefit = 0
    double bobot[50];           // bobot kriteria (akan dinormalisasi)
    double matriksX[50][50];    // matriks keputusan 
    double matriksR[50][50];    // matriks normalisasi
    double nilaiAkhir[50];      // nilai akhir WP

    // Input nama alternatif
    cin.ignore(numeric_limits<streamsize>::max(), '\n');
    cout << "\n--- Nama alternatif ---\n";
    for (int i = 0; i < alternatif; i++) {
        cout << "Nama alternatif A" << (i+1) << ": ";
        getline(cin, altNama[i]);
        if (altNama[i].empty())
            altNama[i] = "A" + string(1, char('1' + i)); // A1..A9
    }

    // Input kriteria: nama, bobot, jenis
    double totalBobot = 0.0;
    cout << "\n--- Data Kriteria ---\n";
    for (int j = 0; j < kriteria; j++) {
        cout << "Nama kriteria C" << (j+1) << ": ";
        getline(cin, kritNama[j]);
        if (kritNama[j].empty())
            kritNama[j] = "C" + string(1, char('1' + j));

        cout << "  Bobot (desimal atau persen): ";
        cin  >> bobot[j];

        cout << "  Jenis (B untuk benefit, C untuk cost): ";
        char t; cin >> t;
        Cost[j] = (t == 'C' || t == 'c') ? 1 : 0;

        totalBobot += bobot[j];
        cin.ignore(numeric_limits<streamsize>::max(), '\n');
    }

    // Normalisasi bobot agar total = 1 ()desimal atau persen
    if (totalBobot <= 0.0) {
        cout << "\n[Error] Total bobot tidak boleh 0.\n";
        return 1;
    }
    if (fabs(totalBobot - 1.0) > 1e-9) {
        for (int j = 0; j < kriteria; j++) bobot[j] /= totalBobot;
        cout << "\n[Info] Bobot dinormalisasi agar total = 1.\n";
    }

    // Input matriks keputusan (nilai asli)
    cout << "\n--- Matriks Keputusan ---\n";
    for (int i = 0; i < alternatif; i++) {
        cout << ">> " << altNama[i] << "\n";
        for (int j = 0; j < kriteria; j++) {
            cout << "  " << kritNama[j] << ": ";
            cin  >> matriksX[i][j];
        }
    }

    // Cari max & min tiap kriteria (untuk normalisasi)
    double kolomMax[50], kolomMin[50];
    for (int j = 0; j < kriteria; j++) {
        kolomMax[j] = -1e300;   // nilai awal untuk cari maksimum
        kolomMin[j] =  1e300;   // nilai awal untuk cari minimum
        for (int i = 0; i < alternatif; i++) {
            if (matriksX[i][j] > kolomMax[j]) kolomMax[j] = matriksX[i][j];
            if (matriksX[i][j] < kolomMin[j]) kolomMin[j] = matriksX[i][j];
        }
    }

    // Proses normalisasi matriks R
    for (int i = 0; i < alternatif; i++) {
        for (int j = 0; j < kriteria; j++) {
            if (Cost[j]) {
                matriksR[i][j] = (matriksX[i][j] == 0.0) ? 0.0 : (kolomMin[j] / matriksX[i][j]); // cost
            } else {
                matriksR[i][j] = (kolomMax[j] == 0.0) ? 0.0 : (matriksX[i][j] / kolomMax[j]);     // benefit
            }
        }
    }

    // Tampilkan matriks R
    cout << fixed << setprecision(4);	//Format dengan 4 desimal
    cout << "\n=== Matriks Normalisasi (R) ===\n";
    cout << setw(15) << "Alternatif";
    for (int j = 0; j < kriteria; j++) cout << setw(12) << kritNama[j];
    cout << "\n";
    for (int i = 0; i < alternatif; i++) {
        cout << setw(15) << altNama[i];
        for (int j = 0; j < kriteria; j++) cout << setw(12) << matriksR[i][j];
        cout << "\n";
    }

    // Hitung nilai akhir tiap alternatif dengan perkalian nilai normalisasi berpangkat bobot
    for (int i = 0; i < alternatif; i++) {
        long double produk = 1.0L;
        for (int j = 0; j < kriteria; j++) {
            long double r = (matriksR[i][j] <= 0.0 ? 1e-18L : (long double)matriksR[i][j]); // guard 0^w
            produk *= pow(r, (long double)bobot[j]);	// kali dengan r_ij^w_j
        }
        nilaiAkhir[i] = (double)produk;
    }

    // Ranking dengan selection sort pada array indeks
    int peringkat[50];
    for (int i = 0; i < alternatif; i++) peringkat[i] = i;
    for (int a = 0; a < alternatif; a++) {
        int best = a;
        for (int b = a+1; b < alternatif; b++) {
            if (nilaiAkhir[peringkat[b]] > nilaiAkhir[peringkat[best]]) best = b;
        }
        if (best != a) { int tmp = peringkat[a]; peringkat[a] = peringkat[best]; peringkat[best] = tmp; }
    }

    // Tampilkan bobot & hasil ranking
    cout << "\n=== Bobot (setelah normalisasi) ===\n";
    for (int j = 0; j < kriteria; j++) {
        cout << " - " << kritNama[j]
             << " [" << (Cost[j] ? "cost" : "benefit") << "] : "
             << bobot[j] << "\n";
    }

    cout << "\n=== Nilai WP & Ranking ===\n";
    cout << setw(6) << "Rank" << setw(15) << "Alternatif" << setw(15) << "Nilai Akhir\n";
    for (int r = 0; r < alternatif; r++) {
        int i = peringkat[r];
        cout << setw(6)  << (r+1)
             << setw(15) << altNama[i]
             << setw(15) << nilaiAkhir[i] << "\n";
    }

	// =====================================
	// Tambahan output machine-readable untuk PHP
	// =====================================
	
	// Matriks X (nilai mentah)
	for (int i = 0; i < alternatif; i++) {
	    cout << "[MATRIXX]," << altNama[i];
	    for (int j = 0; j < kriteria; j++)
	        cout << "," << matriksX[i][j];
	    cout << "\n";
	}
	
	// Matriks R (normalisasi)
	for (int i = 0; i < alternatif; i++) {
	    cout << "[MATRIXR]," << altNama[i];
	    for (int j = 0; j < kriteria; j++)
	        cout << "," << matriksR[i][j];
	    cout << "\n";
	}
	
	// Hasil Akhir
	for (int r = 0; r < alternatif; r++) {
	    int i = peringkat[r];
	    cout << "[RESULT]," << (r+1) << "," 
	         << altNama[i] << "," 
	         << nilaiAkhir[i] << "\n";
	}
	
	return 0;

}

