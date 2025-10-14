import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'home_page.dart';
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  convert.dart -> mengubah data ke format json
  http.dart -> melakukan permintaan http
  home_page.dart -> memanggil halaman utama aplikasi
*/

// Halaman untuk melakukan transaksi barang (masuk / keluar)
class HalamanTransaksi extends StatefulWidget {
  const HalamanTransaksi({super.key});

  @override
  _HalamanTransaksiState createState() => _HalamanTransaksiState();
}

class _HalamanTransaksiState extends State<HalamanTransaksi> {
  List barangList = []; // Menyimpan daftar barang dari database
  String? selectedBarang; // Barang yang dipilih user
  String? jenisTransaksi; // Jenis transaksi: masuk / keluar
  final jumlahController = TextEditingController(); // Input jumlah barang

  @override
  void initState() {
    super.initState();
    fetchBarang(); // Ambil data barang dari server saat halaman dibuka
  }

  // Fungsi untuk mengambil daftar barang dari API
  Future<void> fetchBarang() async {
    try {
      final res = await http.get(Uri.parse("http://localhost/iventory_db/get_barang.php"));
      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        if (data["status"] == "success") {
          setState(() {
            barangList = data["data"]; // Simpan data barang ke dalam list
          });
        }
      }
    } catch (e) {
      print("Error fetchBarang: $e"); // Jika gagal ambil data
    }
  }

  // Fungsi untuk menyimpan transaksi ke database
  Future<void> simpanTransaksi() async {
    // Validasi agar semua input terisi
    if (selectedBarang == null || jenisTransaksi == null || jumlahController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Harap lengkapi semua data")),
      );
      return;
    }

    // Ambil barang yang dipilih berdasarkan id
    final selectedItem = barangList.firstWhere(
      (item) => item["id_barang"].toString() == selectedBarang,
      orElse: () => null,
    );

    // Jika barang tidak ditemukan
    if (selectedItem == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Barang tidak ditemukan")),
      );
      return;
    }

    final stokSaatIni = int.tryParse(selectedItem["jum_barang"].toString()) ?? 0;
    final jumlahInput = int.tryParse(jumlahController.text) ?? 0;

    // Cek jika transaksi keluar tapi stok tidak mencukupi
    if (jenisTransaksi == "keluar" && jumlahInput > stokSaatIni) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Stok tidak cukup! Sisa stok: $stokSaatIni")),
      );
      return;
    }

    try {
      // Kirim data transaksi ke server melalui API POST
      final res = await http.post(
        Uri.parse("http://localhost/iventory_db/add_transaksi.php"),
        body: {
          "id_barang": selectedBarang,
          "jenis": jenisTransaksi,
          "jumlah": jumlahController.text,
        },
      );

      final data = json.decode(res.body);
      if (data["status"] == "success") {
        // Jika berhasil simpan transaksi
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Transaksi berhasil disimpan")),
        );
        jumlahController.clear(); // Kosongkan input
        setState(() {
          jenisTransaksi = null;
          selectedBarang = null;
        });
        fetchBarang(); // Refresh stok setelah transaksi
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Gagal menyimpan transaksi")),
        );
      }
    } catch (e) {
      print("Error simpanTransaksi: $e"); // Tangani error koneksi
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        // Tombol kembali ke HomePage dan hapus halaman sebelumnya dari stack
        leading: IconButton(
          icon: Icon(Icons.arrow_back),
          onPressed: () {
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (context) => HomePage()),
              (route) => false,
            );
          },
        ),
        title: Text("🔄 Transaksi Barang"),
        backgroundColor: Colors.indigo,
      ),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            // Dropdown pilih barang
            DropdownButtonFormField<String>(
              decoration: InputDecoration(labelText: "Pilih Barang"),
              initialValue: selectedBarang,
              items: barangList.map<DropdownMenuItem<String>>((item) {
                return DropdownMenuItem<String>(
                  value: item["id_barang"].toString(),
                  child: Text("${item["nama_barang"]} (Stok: ${item["jum_barang"] ?? '0'})"),
                );
              }).toList(),
              onChanged: (value) => setState(() => selectedBarang = value),
            ),
            SizedBox(height: 12),

            // Dropdown jenis transaksi (masuk / keluar)
            DropdownButtonFormField<String>(
              decoration: InputDecoration(labelText: "Jenis Transaksi"),
              initialValue: jenisTransaksi,
              items: [
                DropdownMenuItem(value: "masuk", child: Text("Barang Masuk")),
                DropdownMenuItem(value: "keluar", child: Text("Barang Keluar")),
              ],
              onChanged: (value) => setState(() => jenisTransaksi = value),
            ),
            SizedBox(height: 12),

            // Input jumlah barang
            TextField(
              controller: jumlahController,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                labelText: "Jumlah",
                border: OutlineInputBorder(),
              ),
            ),
            SizedBox(height: 20),

            // Tombol simpan transaksi
            ElevatedButton.icon(
              onPressed: simpanTransaksi,
              icon: Icon(Icons.save, color: Colors.indigo),
              label: Text(
                "Simpan Transaksi",
                style: TextStyle(color: Colors.indigo, fontWeight: FontWeight.bold),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                side: BorderSide(color: Colors.indigo, width: 2),
                minimumSize: Size(double.infinity, 48),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
                elevation: 3,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
