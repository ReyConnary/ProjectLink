import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart'; 
/* mengimpor library yang dibutuhkan :
  material.dart -> komponen ui flutter
  convert.dart -> mengubah data ke format json
  http.dart -> melakukan permintaan http
  intl.dart -> format mata uang
*/

class LaporanPage extends StatefulWidget {
  const LaporanPage({super.key});

  @override
  _LaporanPageState createState() => _LaporanPageState();
}

class _LaporanPageState extends State<LaporanPage> {
  List laporan = []; // Menyimpan data laporan barang dari server
  bool loading = true; // Status untuk menampilkan indikator loading

  // URL dasar API (ganti dengan IP server jika bukan localhost)
  final String baseUrl = "http://localhost/iventory_db";

  // Formatter untuk menampilkan angka ke format mata uang Rupiah tanpa simbol
  final NumberFormat rupiahFormat =
      NumberFormat.currency(locale: 'id_ID', symbol: '', decimalDigits: 0);

  // Fungsi untuk mengambil data laporan dari API
  Future<void> fetchLaporan() async {
    try {
      final res = await http.get(Uri.parse("$baseUrl/get_barang.php"));
      print("Fetch Laporan Response: ${res.body}");

      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        if (data["status"] == "success") {
          setState(() {
            laporan = data["data"]; // Simpan data barang ke variabel laporan
            loading = false; // Hentikan loading setelah data diterima
          });
        } else {
          setState(() => loading = false);
        }
      }
    } catch (e) {
      print("Error laporan: $e"); // Jika gagal ambil data, tampilkan error di console
      setState(() => loading = false);
    }
  }

  @override
  void initState() {
    super.initState();
    fetchLaporan(); // Panggil fetchLaporan() saat halaman pertama kali dibuka
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: Text("📑 Laporan Barang"),
        backgroundColor: Colors.teal, // Warna utama AppBar
      ),
      // Tampilan utama halaman laporan
      body: loading
          ? Center(child: CircularProgressIndicator()) // Tampilkan loading
          : laporan.isEmpty
              ? Center(child: Text("Tidak ada data barang")) // Jika kosong
              : ListView.builder(
                  itemCount: laporan.length, // Jumlah item dalam laporan
                  itemBuilder: (context, index) {
                    final item = laporan[index];

                    // Format harga ke bentuk 100.000
                    String formattedHarga = rupiahFormat.format(
                      double.tryParse(item["harga"].toString()) ?? 0,
                    );

                    return Card(
                      elevation: 3, // Sedikit bayangan pada kartu
                      margin: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: Colors.teal,
                          child: Text(
                            "${index + 1}", // Nomor urut item
                            style: TextStyle(color: Colors.white),
                          ),
                        ),
                        title: Text(
                          item["nama_barang"], // Nama barang
                          style: TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            SizedBox(height: 4),
                            Text(
                              "Jumlah: ${item["jum_barang"]}", // Jumlah barang
                              style: TextStyle(color: Colors.black87),
                            ),
                            Text(
                              "Harga: Rp$formattedHarga", // Harga dalam format Rupiah
                              style: TextStyle(color: Colors.orange[800]),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}
